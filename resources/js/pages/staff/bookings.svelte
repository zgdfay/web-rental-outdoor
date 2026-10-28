<script>
    import Sidebar from '@/components/layout/Sidebar.svelte';
    import { router } from '@inertiajs/svelte';
    import { CalendarClock, CheckCircle, PackageCheck, RotateCcw, Search, Filter, ShieldCheck, User, Phone, MapPin, CreditCard, Info, Eye, Printer, Bluetooth, MessageSquare } from 'lucide-svelte';
    import { fly, fade } from 'svelte/transition';
    import * as Select from "@/components/ui/select/index.js";
    import * as Dialog from "@/components/ui/dialog/index.js";
    import { Button } from "@/components/ui/button/index.js";

    let { bookings = [] } = $props();

    let searchQuery = $state('');
    let statusFilter = $state('all');
    let selectedBooking = $state(null);
    let confirmDialog = $state({
        open: false,
        title: '',
        message: '',
        onConfirm: null
    });
    
    let printingBooking = $state(null);
    let bluetoothDevice = $state(null);
    let returnModal = $state({
        open: false,
        booking: null,
        condition: 'good',
        damage_fee: 0,
        damage_notes: '',
        lateDays: 0,
        lateFine: 0,
        submitting: false
    });

    const filteredBookings = $derived.by(() => {
        return bookings.filter(booking => {
            const search = searchQuery.toLowerCase();
            const matchesSearch = (booking.customer_name || '').toLowerCase().includes(search) || 
                                 (booking.gear?.name || '').toLowerCase().includes(search) ||
                                 booking.id.toString().includes(search);
            
            const matchesStatus = statusFilter === 'all' || booking.status === statusFilter;
            
            return matchesSearch && matchesStatus;
        });
    });

    function showConfirm(title, message, action) {
        confirmDialog = {
            open: true,
            title,
            message,
            onConfirm: action
        };
    }

    function handleConfirm() {
        if (confirmDialog.onConfirm) confirmDialog.onConfirm();
        confirmDialog.open = false;
    }

    function validateBooking(id) {
        showConfirm(
            'Verify Documents', 
            'Are you sure the KTP and payment transfer are valid?', 
            () => router.post(`/staff/bookings/${id}/validate`)
        );
    }

    async function printReceipt(booking) {
        if (bluetoothDevice) {
            try {
                await printViaBluetooth(booking);
                return;
            } catch (error) {
                console.error("Bluetooth print failed", error);
                alert("Gagal print via Bluetooth: " + error.message + ". Dialihkan ke Print Browser...");
            }
        }
        
        // Fallback to window.print()
        printingBooking = booking;
        setTimeout(() => {
            window.print();
            setTimeout(() => { printingBooking = null; }, 500);
        }, 100);
    }

    async function getPrinterCharacteristic() {
        if (!bluetoothDevice.gatt.connected) {
            await bluetoothDevice.gatt.connect();
        }

        const services = await bluetoothDevice.gatt.getPrimaryServices();
        if (services.length === 0) throw new Error("Service printer tidak ditemukan");
        
        for (const service of services) {
            const characteristics = await service.getCharacteristics();
            const characteristic = characteristics.find(c => c.properties.write || c.properties.writeWithoutResponse);
            if (characteristic) return characteristic;
        }
        throw new Error("Karakteristik write tidak ditemukan pada printer ini");
    }

    async function sendBluetoothData(characteristic, dataArray) {
        const maxChunk = 100;
        for (let i = 0; i < dataArray.length; i += maxChunk) {
            const chunk = new Uint8Array(dataArray.slice(i, i + maxChunk));
            await characteristic.writeValue(chunk);
        }
    }

    async function printViaBluetooth(booking) {
        const targetCharacteristic = await getPrinterCharacteristic();
        const encoder = new TextEncoder();


        const escInit = [0x1B, 0x40];
        const escAlignCenter = [0x1B, 0x61, 1];
        const escAlignLeft = [0x1B, 0x61, 0];
        const escBoldOn = [0x1B, 0x45, 1];
        const escBoldOff = [0x1B, 0x45, 0];
        const lf = [0x0A];

        let payload = [];
        payload.push(...escInit);
        payload.push(...escAlignCenter);
        payload.push(...escBoldOn);
        payload.push(...encoder.encode("OUTDOOR SPECIES\n"));
        payload.push(...escBoldOff);
        payload.push(...encoder.encode("Rental Alat Outdoor\n"));
        payload.push(...encoder.encode("================================\n"));
        payload.push(...lf);

        payload.push(...escAlignLeft);
        payload.push(...encoder.encode(`No       : #${booking.id}\n`));
        payload.push(...encoder.encode(`Tanggal  : ${new Date().toLocaleDateString('id-ID')}\n`));
        payload.push(...encoder.encode(`Customer : ${booking.customer_name}\n`));
        payload.push(...encoder.encode("================================\n"));
        
        payload.push(...escBoldOn);
        payload.push(...encoder.encode("DETAIL SEWA\n"));
        payload.push(...escBoldOff);
        const qty = booking.quantity || 1;
        payload.push(...encoder.encode(`${booking.gear?.name || 'Item Sewa'}\n`));
        payload.push(...encoder.encode(`  Qty      : x${qty}\n`));
        payload.push(...encoder.encode(`  Periode  : ${formatSimpleDate(booking.start_date)} - ${formatDueDate(booking)}\n`));
        payload.push(...encoder.encode(`  Durasi   : ${booking.rental_days} Hari\n`));
        payload.push(...encoder.encode("================================\n"));

        const pelunasan = Math.max(0, (booking.total_price || 0) - (booking.dp_amount || 0));
        const sisaBalance = (booking.status === 'handed_over' || booking.status === 'returned' || booking.remaining_balance <= 0) ? 0 : (booking.remaining_balance || 0);
        const damageFee = Number(booking.damage_fee) || 0;
        const totalFine = Number(booking.fine_amount) || 0;
        const lateFine = Math.max(0, totalFine - damageFee);
        const grandTotal = (booking.total_price || 0) + totalFine;

        payload.push(...escBoldOn);
        payload.push(...encoder.encode("RINCIAN PEMBAYARAN\n"));
        payload.push(...escBoldOff);
        payload.push(...encoder.encode(`Biaya Sewa  : ${formatRupiah(booking.total_price)}\n`));
        payload.push(...encoder.encode(`DP Masuk    : ${formatRupiah(booking.dp_amount)}\n`));
        payload.push(...encoder.encode(`Pelunasan   : ${formatRupiah(pelunasan)}\n`));
        
        if (damageFee > 0) {
            payload.push(...encoder.encode(`Biaya Rusak : ${formatRupiah(damageFee)}\n`));
        }
        if (booking.damage_notes) {
            payload.push(...encoder.encode(`Ket. Rusak  : ${booking.damage_notes.substring(0, 30)}\n`));
        }
        if (lateFine > 0) {
            payload.push(...encoder.encode(`Denda Telat : ${formatRupiah(lateFine)}\n`));
        }
        
        payload.push(...encoder.encode("--------------------------------\n"));
        payload.push(...escBoldOn);
        payload.push(...encoder.encode(`TOTAL AKHIR : ${formatRupiah(grandTotal)}\n`));
        payload.push(...escBoldOff);
        payload.push(...encoder.encode(`Sisa Tagihan: ${formatRupiah(sisaBalance)}\n`));
        payload.push(...encoder.encode("--------------------------------\n"));
        payload.push(...escBoldOn);
        const statusText = booking.status === 'returned' ? 'SELESAI (RETURNED)' : 'LUNAS (HANDED OVER)';
        payload.push(...encoder.encode(`STATUS      : ${statusText}\n`));
        payload.push(...escBoldOff);
        payload.push(...encoder.encode("================================\n"));
        
        payload.push(...lf);
        payload.push(...escAlignCenter);
        payload.push(...encoder.encode("Terima kasih telah menyewa\n"));
        payload.push(...encoder.encode("di Outdoor Species.\n"));
        payload.push(...encoder.encode("Kembalikan barang tepat waktu\n"));
        payload.push(...encoder.encode("untuk menghindari denda.\n"));
        
        // Feed paper
        payload.push(...lf, ...lf, ...lf, ...lf);

        await sendBluetoothData(targetCharacteristic, payload);
    }

    async function testPrintViaBluetooth() {
        if (!bluetoothDevice) return;
        try {
            const targetCharacteristic = await getPrinterCharacteristic();
            const encoder = new TextEncoder();
            const escInit = [0x1B, 0x40];
            const escAlignCenter = [0x1B, 0x61, 1];
            const lf = [0x0A];

            let payload = [];
            payload.push(...escInit, ...escAlignCenter);
            payload.push(...encoder.encode("TEST PRINT BERHASIL!\n"));
            payload.push(...encoder.encode("Printer siap digunakan.\n"));
            payload.push(...lf, ...lf, ...lf, ...lf);

            await sendBluetoothData(targetCharacteristic, payload);
        } catch (error) {
            console.error("Test print failed", error);
            alert("Gagal test print: " + error.message);
        }
    }

    async function connectBluetoothPrinter() {
        if (!navigator.bluetooth) {
            alert('Browser Anda tidak mendukung Web Bluetooth. Gunakan Chrome/Edge di PC atau Android.');
            return;
        }
        
        try {
            const device = await navigator.bluetooth.requestDevice({
                acceptAllDevices: true,
                optionalServices: ['000018f0-0000-1000-8000-00805f9b34fb', 'e7810a71-73ae-499d-8c15-faa9aef0c3f2'] // UUID umum printer thermal
            });
            bluetoothDevice = device;
            alert(`Berhasil pairing ke: ${device.name || 'Printer Bluetooth'}`);
        } catch (error) {
            if (error.name !== 'NotFoundError') {
                alert("Bluetooth Error: " + error.message);
            }
        }
    }

    function handoverItems(booking) {
        showConfirm(
            'Confirm Handover', 
            'Confirm gear handover to customer? Sisa sewa akan dilunasi dan struk akan dicetak.', 
            () => router.post(`/staff/bookings/${booking.id}/handover`, {}, {
                onSuccess: () => {
                    const updated = { ...booking, status: 'handed_over', remaining_balance: 0 };
                    printReceipt(updated);
                }
            })
        );
    }

    function openReturnModal(booking) {
        const dueDate = getDueDate(booking);
        let lateDays = 0;
        let lateFine = 0;
        if (dueDate && new Date() > dueDate) {
            const diffTime = Math.abs(new Date() - dueDate);
            lateDays = Math.max(1, Math.ceil(diffTime / (1000 * 60 * 60 * 24)));
            const price = booking.gear ? booking.gear.price_per_day : 0;
            lateFine = lateDays * (price * 1.5);
        }

        returnModal = {
            open: true,
            booking,
            condition: 'good',
            damage_fee: 0,
            damage_notes: '',
            lateDays,
            lateFine,
            submitting: false
        };
    }

    function submitReturn() {
        if (!returnModal.booking) return;
        returnModal.submitting = true;
        const b = returnModal.booking;
        const damageFee = returnModal.condition === 'damaged' ? (Number(returnModal.damage_fee) || 0) : 0;
        const damageNotes = returnModal.condition === 'damaged' ? returnModal.damage_notes : null;

        router.post(`/staff/bookings/${b.id}/return`, {
            damage_fee: damageFee,
            damage_notes: damageNotes
        }, {
            onSuccess: () => {
                const totalFine = (returnModal.lateFine || 0) + damageFee;
                const updated = {
                    ...b,
                    status: 'returned',
                    remaining_balance: 0,
                    fine_amount: totalFine,
                    damage_fee: damageFee,
                    damage_notes: damageNotes
                };
                returnModal.open = false;
                printReceipt(updated);
            },
            onFinish: () => {
                returnModal.submitting = false;
            }
        });
    }

    function getStatusStyle(status) {
        switch(status) {
            case 'pending': return 'bg-amber-50 text-amber-600 border-amber-100';
            case 'validated': return 'bg-blue-50 text-blue-600 border-blue-100';
            case 'handed_over': return 'bg-zinc-900 text-white border-zinc-900';
            case 'returned': return 'bg-emerald-50 text-emerald-600 border-emerald-100';
            default: return 'bg-zinc-50 text-zinc-500 border-zinc-100';
        }
    }

    function formatCurrency(amount) {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0
        }).format(amount);
    }

    function formatRupiah(amount) {
        return 'Rp ' + new Intl.NumberFormat('id-ID', { minimumFractionDigits: 0 }).format(amount || 0);
    }

    function formatSimpleDate(dateStr) {
        if (!dateStr) return '-';
        return new Date(dateStr).toLocaleDateString('id-ID', { day: 'numeric', month: 'short' });
    }

    function getDueDate(booking) {
        if (!booking.start_date) return null;
        const date = new Date(booking.start_date);
        date.setDate(date.getDate() + booking.rental_days);
        return date;
    }

    function isOverdue(booking) {
        if (booking.status !== 'handed_over' || !booking.start_date) return false;
        const dueDate = getDueDate(booking);
        return new Date() > dueDate;
    }

    function getWaUrl(booking) {
        let p = (booking.customer_phone || '').replace(/[^0-9]/g, '');
        if (p.startsWith('0')) p = '62' + p.substring(1);
        
        const name = booking.customer_name || 'Kak';
        const gearName = booking.gear?.name || 'Alat Outdoor';
        const qty = booking.quantity || 1;
        
        let text = `Halo kak *${name}*, dari *Outdoor Species*.\n\n`;
        
        if (booking.status === 'pending' || booking.status === 'validated') {
            text += `Pemesanan sewa alat *${gearName} (${qty}x)* sudah dikonfirmasi dan siap untuk diambil di toko kami. Mohon membawa KTP asli dan sisa pelunasan saat pengambilan ya!`;
        } else if (booking.status === 'handed_over') {
            const dueDate = booking.start_date ? new Date(new Date(booking.start_date).getTime() + (booking.rental_days * 86400000)).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }) : 'segera';
            if (isOverdue(booking)) {
                text = `🚨 *OUTDOOR SPECIES - PERINGATAN KETERLAMBATAN (H+1)* 🚨\n\nHalo kak *${name}*,\nMasa penyewaan untuk alat *${gearName} (${qty}x)* telah *MELEWATI JATUH TEMPO* sejak ${dueDate}.\n\nSaat ini denda keterlambatan berjalan. Mohon untuk segera mengembalikan perlengkapan ke toko hari ini ya. Terima kasih!`;
            } else {
                text += `Masa sewa untuk alat *${gearName} (${qty}x)* berakhir pada *${dueDate}*. Mohon untuk dikembalikan tepat waktu agar terhindar dari denda keterlambatan ya. Terima kasih!`;
            }
        } else if (booking.status === 'returned') {
            text += `Terima kasih telah mengembalikan alat *${gearName} (${qty}x)* ke Outdoor Species. Sampai jumpa di petualangan berikutnya!`;
        } else {
            text += `Mengenai pemesanan sewa alat *${gearName} (${qty}x)* Anda.`;
        }
        
        return `https://wa.me/${p}?text=${encodeURIComponent(text)}`;
    }

    function formatDueDate(booking) {
        const date = getDueDate(booking);
        if (!date) return '-';
        return date.toLocaleDateString('id-ID', { day: 'numeric', month: 'short' });
    }
</script>

<div class="flex bg-zinc-50 min-h-screen font-sans selection:bg-primary selection:text-white print:hidden">
    <Sidebar role="staff" />

    <main class="flex-grow p-10">
        <div class="max-w-6xl mx-auto space-y-10">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div class="space-y-1">
                    <h1 class="text-2xl font-bold tracking-tight text-zinc-900">Bookings</h1>
                    <p class="text-sm text-zinc-500">Validate orders and manage equipment handovers.</p>
                </div>
                <div class="flex items-center gap-2">
                    {#if bluetoothDevice}
                        <button 
                            onclick={testPrintViaBluetooth}
                            class="flex items-center gap-2 px-4 py-2 bg-zinc-100 text-zinc-900 rounded-lg text-sm font-semibold hover:bg-zinc-200 transition-colors border border-zinc-200"
                        >
                            <Printer size={16} />
                            Test Print
                        </button>
                    {/if}
                    <button 
                        onclick={connectBluetoothPrinter}
                        class="flex items-center gap-2 px-4 py-2 bg-blue-50 text-blue-600 rounded-lg text-sm font-semibold hover:bg-blue-100 transition-colors border border-blue-200"
                    >
                        <Bluetooth size={16} />
                        {bluetoothDevice ? 'Bluetooth Connected' : 'Connect Bluetooth'}
                    </button>
                </div>
            </div>

            <!-- Main Content Area -->
            <div class="bg-white border border-zinc-200 rounded-xl overflow-hidden shadow-sm">
                <div class="px-6 py-4 border-b border-zinc-100 flex items-center justify-between bg-zinc-50/50">
                    <div class="flex items-center gap-4">
                        <div class="relative w-80 group">
                            <Search class="absolute left-3 top-1/2 -translate-y-1/2 text-zinc-400 group-focus-within:text-zinc-900 transition-colors" size={16} />
                            <input 
                                type="text" 
                                bind:value={searchQuery}
                                placeholder="Search customer, gear, or ID..." 
                                class="w-full h-10 bg-white border border-zinc-200 rounded-md pl-10 pr-4 focus:border-zinc-400 transition-all outline-none text-sm" 
                            />
                        </div>

                        <Select.Root type="single" bind:value={statusFilter}>
                            <Select.Trigger class="w-[180px] h-10 bg-white border border-zinc-200 rounded-md px-3 text-sm font-medium">
                                {statusFilter === 'all' ? 'All Status' : statusFilter.replace('_', ' ').toUpperCase()}
                            </Select.Trigger>
                            <Select.Content class="z-50">
                                <Select.Item value="all">All Status</Select.Item>
                                <Select.Item value="pending">Pending</Select.Item>
                                <Select.Item value="validated">Validated</Select.Item>
                                <Select.Item value="handed_over">Handed Over</Select.Item>
                                <Select.Item value="returned">Returned</Select.Item>
                            </Select.Content>
                        </Select.Root>
                    </div>
                </div>

                <!-- Table -->
                <div class="w-full overflow-x-auto">
                    <table class="w-full text-left whitespace-nowrap min-w-max">
                        <thead>
                            <tr class="bg-zinc-50/50 border-b border-zinc-100">
                                <th class="px-6 py-4 text-xs font-semibold text-zinc-500">Customer & Gear</th>
                                <th class="px-6 py-4 text-xs font-semibold text-zinc-500 text-center">Duration</th>
                                <th class="px-6 py-4 text-xs font-semibold text-zinc-500 text-center">Rental Period</th>
                                <th class="px-6 py-4 text-xs font-semibold text-zinc-500 text-center">Status</th>
                                <th class="px-6 py-4 text-xs font-semibold text-zinc-500">Payment</th>
                                <th class="px-6 py-4 text-xs font-semibold text-zinc-500 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-100">
                        {#each filteredBookings as booking}
                            <tr class="hover:bg-zinc-50/30 transition-colors group">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-4">
                                        <div class="size-10 rounded-lg bg-zinc-100 flex items-center justify-center border border-zinc-200 overflow-hidden text-zinc-400">
                                            <User size={18} />
                                        </div>
                                        <div class="space-y-0.5">
                                            <p class="text-sm font-semibold text-zinc-900">{booking.customer_name}</p>
                                            <div class="flex items-center gap-2">
                                                <p class="text-[10px] text-primary font-bold uppercase tracking-wider">{booking.gear?.name || 'Item Sewa'}</p>
                                                <span class="text-[9px] font-black bg-zinc-100 text-zinc-500 px-1.5 py-0.5 rounded border border-zinc-200">
                                                    QTY: {booking.quantity || 1}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <p class="text-sm font-semibold text-zinc-900">{booking.rental_days} <span class="text-zinc-400 font-medium">Days</span></p>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex flex-col items-center gap-1">
                                        <p class="text-xs font-semibold text-zinc-900 whitespace-nowrap">
                                            {formatSimpleDate(booking.start_date)} — {formatDueDate(booking)}
                                        </p>
                                        {#if isOverdue(booking)}
                                            <span class="px-1.5 py-0.5 bg-red-50 text-red-600 text-[8px] font-black uppercase tracking-tighter rounded border border-red-100 animate-pulse">
                                                Overdue
                                            </span>
                                        {/if}
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="px-2.5 py-0.5 rounded-full border text-[10px] font-bold uppercase tracking-wider whitespace-nowrap {getStatusStyle(booking.status)}">
                                        {booking.status.replace('_', ' ')}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="space-y-1">
                                        {#if booking.status === 'handed_over' || booking.status === 'returned' || booking.remaining_balance <= 0}
                                            <div class="flex items-center gap-1.5">
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-black bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                    LUNAS
                                                </span>
                                                <span class="text-xs font-semibold text-zinc-500">Balance: <span class="text-zinc-900 font-bold">Rp 0</span></span>
                                            </div>
                                            {#if booking.fine_amount > 0}
                                                <p class="text-[10px] font-bold text-red-600 uppercase tracking-tight">Total Denda: {formatCurrency(booking.fine_amount)}</p>
                                            {:else}
                                                <p class="text-[10px] font-bold text-emerald-600 uppercase tracking-tight">Pelunasan Selesai</p>
                                            {/if}
                                            {#if booking.damage_notes}
                                                <p class="text-[9px] font-bold text-red-700 bg-red-50 border border-red-200 px-1.5 py-0.5 rounded w-fit max-w-[200px] truncate" title={booking.damage_notes}>
                                                    ⚠️ {booking.damage_notes}
                                                </p>
                                            {/if}
                                        {:else}
                                            <p class="text-xs font-semibold text-zinc-500">Sisa Tagihan: <span class="text-zinc-900 font-bold">{formatCurrency(booking.remaining_balance)}</span></p>
                                            <p class="text-[10px] font-bold text-amber-600 uppercase tracking-tight">Belum Lunas (Bayar di Toko)</p>
                                        {/if}
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a 
                                            href={getWaUrl(booking)}
                                            target="_blank"
                                            rel="noopener noreferrer"
                                            class="flex items-center gap-1 px-2.5 py-1.5 bg-[#25D366] text-white rounded-md text-[10px] font-bold uppercase tracking-wider hover:bg-[#20bd5a] transition-all shadow-sm shrink-0"
                                            title="Chat WhatsApp dengan Template Otomatis"
                                        >
                                            <MessageSquare size={12} />
                                            WA
                                        </a>

                                        <Button 
                                            variant="ghost" 
                                            size="icon"
                                            class="size-8 text-zinc-400 hover:text-zinc-900"
                                            onclick={() => selectedBooking = booking}
                                        >
                                            <Eye size={16} />
                                        </Button>

                                        {#if booking.status === 'pending'}
                                            <button 
                                                onclick={() => validateBooking(booking.id)}
                                                class="flex items-center gap-1.5 px-3 py-1.5 bg-blue-600 text-white rounded-md text-[10px] font-bold uppercase tracking-wider hover:bg-blue-700 transition-all"
                                            >
                                                <ShieldCheck size={12} />
                                                Validate
                                            </button>
                                        {:else if booking.status === 'validated'}
                                            <button 
                                                onclick={() => handoverItems(booking)}
                                                class="flex items-center gap-1.5 px-3 py-1.5 bg-zinc-900 text-white rounded-md text-[10px] font-bold uppercase tracking-wider hover:bg-zinc-800 transition-all"
                                            >
                                                <PackageCheck size={12} />
                                                Handover
                                            </button>
                                        {:else if booking.status === 'handed_over'}
                                            <button 
                                                onclick={() => printReceipt(booking)}
                                                class="flex items-center gap-1.5 px-3 py-1.5 bg-zinc-100 text-zinc-900 rounded-md text-[10px] font-bold uppercase tracking-wider hover:bg-zinc-200 transition-all border border-zinc-200"
                                                title="Cetak Struk"
                                            >
                                                <Printer size={12} />
                                                Print Struk
                                            </button>
                                            <button 
                                                onclick={() => openReturnModal(booking)}
                                                class="flex items-center gap-1.5 px-3 py-1.5 bg-primary text-white rounded-md text-[10px] font-bold uppercase tracking-wider hover:bg-primary/90 transition-all shadow-xs"
                                            >
                                                <RotateCcw size={12} />
                                                Return
                                            </button>
                                        {:else}
                                            <button 
                                                onclick={() => printReceipt(booking)}
                                                class="flex items-center gap-1.5 px-3 py-1.5 bg-zinc-100 dark:bg-zinc-800 text-zinc-900 dark:text-white rounded-md text-[10px] font-bold uppercase tracking-wider hover:bg-zinc-200 dark:hover:bg-zinc-700 transition-all border border-zinc-200 dark:border-zinc-700"
                                                title="Cetak Struk Kembali"
                                            >
                                                <Printer size={12} />
                                                Print Struk
                                            </button>
                                            <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest px-2">Completed</span>
                                        {/if}
                                    </div>
                                </td>
                            </tr>
                        {/each}
                    </tbody>
                </table>
            </div>

                {#if bookings.length === 0}
                    <div class="p-20 text-center space-y-4">
                        <div class="size-20 bg-zinc-50 rounded-full flex items-center justify-center mx-auto border border-zinc-100 text-zinc-200">
                            <CalendarClock size={40} />
                        </div>
                        <div class="space-y-1">
                            <p class="font-black text-zinc-900 uppercase">No bookings yet</p>
                            <p class="text-sm text-zinc-400 font-medium">Orders will appear here once users make a booking.</p>
                        </div>
                    </div>
                {/if}
            </div>
        </div>
    </main>

    <!-- Document Viewer Dialog -->
    <Dialog.Root open={!!selectedBooking} onOpenChange={(open) => !open && (selectedBooking = null)}>
        <Dialog.Content class="max-w-4xl">
            <Dialog.Header>
                <Dialog.Title>Booking Documents - #{selectedBooking?.id}</Dialog.Title>
                <Dialog.Description>
                    Verify the identity and payment transfer for {selectedBooking?.customer_name}.
                </Dialog.Description>
            </Dialog.Header>
            
            <div class="grid md:grid-cols-2 gap-6 mt-6">
                <!-- KTP -->
                <div class="space-y-3">
                    <p class="text-xs font-bold text-zinc-500 uppercase tracking-widest flex items-center gap-2">
                        <User size={14} /> Identity Card (KTP)
                    </p>
                    <div class="aspect-[3/2] bg-zinc-100 rounded-xl border border-zinc-200 overflow-hidden group relative">
                        {#if selectedBooking?.ktp_path}
                            <img src={`/storage/${selectedBooking.ktp_path}`} alt="KTP" class="w-full h-full object-cover" />
                            <a href={`/storage/${selectedBooking.ktp_path}`} target="_blank" class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center text-white text-xs font-bold uppercase tracking-widest">
                                Open Full Size
                            </a>
                        {:else}
                            <div class="w-full h-full flex items-center justify-center text-zinc-400">No Image Uploaded</div>
                        {/if}
                    </div>
                </div>

                <!-- Transfer Proof -->
                <div class="space-y-3">
                    <p class="text-xs font-bold text-zinc-500 uppercase tracking-widest flex items-center gap-2">
                        <CreditCard size={14} /> Transfer Proof
                    </p>
                    <div class="aspect-[3/2] bg-zinc-100 rounded-xl border border-zinc-200 overflow-hidden group relative">
                        {#if selectedBooking?.transfer_proof_path}
                            <img src={`/storage/${selectedBooking.transfer_proof_path}`} alt="Transfer Proof" class="w-full h-full object-cover" />
                            <a href={`/storage/${selectedBooking.transfer_proof_path}`} target="_blank" class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center text-white text-xs font-bold uppercase tracking-widest">
                                Open Full Size
                            </a>
                        {:else}
                            <div class="w-full h-full flex items-center justify-center text-zinc-400">No Image Uploaded</div>
                        {/if}
                    </div>
                </div>
            </div>

            <Dialog.Footer class="mt-8">
                <Button variant="outline" onclick={() => selectedBooking = null}>Close Preview</Button>
                {#if selectedBooking?.status === 'pending'}
                    <Button onclick={() => { validateBooking(selectedBooking.id); selectedBooking = null; }}>
                        Approve & Validate
                    </Button>
                {/if}
            </Dialog.Footer>
        </Dialog.Content>
    </Dialog.Root>

    <!-- Confirmation AlertDialog -->
    <Dialog.Root bind:open={confirmDialog.open}>
        <Dialog.Content class="max-w-[400px]">
            <Dialog.Header>
                <Dialog.Title>{confirmDialog.title}</Dialog.Title>
                <Dialog.Description class="text-sm">
                    {confirmDialog.message}
                </Dialog.Description>
            </Dialog.Header>
            <Dialog.Footer class="mt-6 flex gap-2">
                <Button variant="outline" class="flex-grow" onclick={() => confirmDialog.open = false}>Cancel</Button>
                <Button variant="default" class="flex-grow bg-zinc-900" onclick={handleConfirm}>Confirm</Button>
            </Dialog.Footer>
        </Dialog.Content>
    </Dialog.Root>

    <!-- Return & Inspection Dialog -->
    <Dialog.Root bind:open={returnModal.open}>
        <Dialog.Content class="max-w-[480px]">
            <Dialog.Header>
                <Dialog.Title class="flex items-center gap-2 text-zinc-900 font-bold">
                    <RotateCcw size={18} class="text-primary" />
                    Pemeriksaan & Pengembalian Alat
                </Dialog.Title>
                <Dialog.Description class="text-xs text-zinc-500">
                    Inspeksi kelengkapan & kondisi #{returnModal.booking?.id} ({returnModal.booking?.gear?.name || 'Alat Outdoor'}) milik {returnModal.booking?.customer_name}.
                </Dialog.Description>
            </Dialog.Header>

            <div class="space-y-4 my-2 text-sm">
                <!-- Info Keterlambatan -->
                {#if returnModal.lateDays > 0}
                    <div class="p-3 bg-red-50 border border-red-200 rounded-xl space-y-1">
                        <div class="flex justify-between items-center text-red-700 font-bold text-xs">
                            <span>Terlambat {returnModal.lateDays} Hari</span>
                            <span>{formatRupiah(returnModal.lateFine)}</span>
                        </div>
                        <p class="text-[11px] text-red-600">Denda keterlambatan 1.5x tarif sewa otomatis dihitung.</p>
                    </div>
                {:else}
                    <div class="p-3 bg-emerald-50 border border-emerald-200 rounded-xl flex items-center justify-between text-emerald-800 text-xs font-semibold">
                        <span>Status Waktu: Tepat Waktu</span>
                        <span class="font-bold">Denda Telat: Rp 0</span>
                    </div>
                {/if}

                <!-- Pilihan Kondisi Barang -->
                <div class="space-y-2">
                    <label class="text-xs font-bold text-zinc-700 uppercase tracking-wider">Kondisi Fisik Perlengkapan</label>
                    <div class="grid grid-cols-2 gap-3">
                        <button 
                            type="button"
                            onclick={() => { returnModal.condition = 'good'; returnModal.damage_fee = 0; returnModal.damage_notes = ''; }}
                            class="p-3 rounded-xl border text-left transition-all {returnModal.condition === 'good' ? 'border-emerald-600 bg-emerald-50 ring-2 ring-emerald-500/20 text-emerald-900 font-bold' : 'border-zinc-200 hover:bg-zinc-50 text-zinc-600'}"
                        >
                            <span class="block text-xs uppercase tracking-wider">🟢 Normal / Lengkap</span>
                            <span class="text-[11px] font-normal text-zinc-500 mt-0.5 block">Tidak ada kerusakan</span>
                        </button>
                        <button 
                            type="button"
                            onclick={() => { returnModal.condition = 'damaged'; }}
                            class="p-3 rounded-xl border text-left transition-all {returnModal.condition === 'damaged' ? 'border-red-600 bg-red-50 ring-2 ring-red-500/20 text-red-900 font-bold' : 'border-zinc-200 hover:bg-zinc-50 text-zinc-600'}"
                        >
                            <span class="block text-xs uppercase tracking-wider">🔴 Rusak / Hilang</span>
                            <span class="text-[11px] font-normal text-zinc-500 mt-0.5 block">Perlu ganti rugi/catatan</span>
                        </button>
                    </div>
                </div>

                <!-- Form Kerusakan jika 'damaged' -->
                {#if returnModal.condition === 'damaged'}
                    <div class="p-4 bg-zinc-50 rounded-xl border border-zinc-200 space-y-3">
                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-zinc-700">Biaya Ganti Rugi / Perbaikan (Rp)</label>
                            <input 
                                type="number" 
                                min="0" 
                                step="1000"
                                bind:value={returnModal.damage_fee}
                                placeholder="e.g. 50000" 
                                class="w-full h-10 bg-white border border-zinc-200 rounded-lg px-3 text-sm font-bold text-zinc-900 focus:border-red-400 outline-none"
                            />
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-zinc-700">Catatan Kerusakan / Komponen Hilang</label>
                            <textarea 
                                bind:value={returnModal.damage_notes}
                                rows="2"
                                placeholder="Contoh: Frame tenda patah 1 ruas, pasak kurang 2 pcs"
                                class="w-full bg-white border border-zinc-200 rounded-lg p-2.5 text-xs text-zinc-900 focus:border-red-400 outline-none leading-relaxed"
                            ></textarea>
                        </div>
                    </div>
                {/if}

                <!-- Total Tagihan Denda -->
                <div class="border-t border-dashed border-zinc-200 pt-3 flex justify-between items-center">
                    <div>
                        <p class="text-xs font-bold text-zinc-900">Total Denda & Ganti Rugi</p>
                        <p class="text-[10px] text-zinc-400 font-medium">Denda telat + Biaya kerusakan</p>
                    </div>
                    <span class="text-lg font-black {(returnModal.lateFine + (returnModal.condition === 'damaged' ? Number(returnModal.damage_fee) || 0 : 0)) > 0 ? 'text-red-600' : 'text-zinc-900'}">
                        {formatRupiah((returnModal.lateFine || 0) + (returnModal.condition === 'damaged' ? Number(returnModal.damage_fee) || 0 : 0))}
                    </span>
                </div>
            </div>

            <Dialog.Footer class="mt-4 flex gap-2">
                <Button variant="outline" class="flex-grow" onclick={() => returnModal.open = false}>Batal</Button>
                <Button 
                    variant="default" 
                    class="flex-grow bg-primary text-white hover:bg-primary/90" 
                    disabled={returnModal.submitting}
                    onclick={submitReturn}
                >
                    {returnModal.submitting ? 'Memproses...' : 'Proses Return & Cetak'}
                </Button>
            </Dialog.Footer>
        </Dialog.Content>
    </Dialog.Root>
</div>

{#if printingBooking}
<div class="hidden print:block bg-white text-black p-6 font-mono text-xs max-w-[300px] mx-auto">
    <!-- Logo & Header -->
    <div class="text-center mb-4">
        <img src="/assets/logo/logo-outdoor-species.png" alt="Logo" class="w-20 h-20 mx-auto mb-2 object-contain" />
        <h2 class="text-lg font-bold tracking-tight">OUTDOOR SPECIES</h2>
        <p class="text-[10px] mt-0.5">Rental Alat Outdoor</p>
    </div>
    
    <!-- Info Booking -->
    <div class="border-t border-b border-dashed border-black py-3 mb-3 space-y-1">
        <div class="flex justify-between">
            <span>No. Booking</span>
            <span class="font-bold">#{printingBooking.id}</span>
        </div>
        <div class="flex justify-between">
            <span>Tanggal</span>
            <span>{new Date().toLocaleDateString('id-ID')}</span>
        </div>
        <div class="flex justify-between">
            <span>Customer</span>
            <span class="font-bold">{printingBooking.customer_name}</span>
        </div>
    </div>

    <!-- Detail Sewa -->
    <div class="mb-3">
        <div class="font-bold mb-1.5 text-[10px] uppercase tracking-wider">Detail Sewa</div>
        <div class="flex justify-between font-bold">
            <span>{printingBooking.gear?.name || 'Item Sewa'}</span>
            <span>x{printingBooking.quantity || 1}</span>
        </div>
        <div class="flex justify-between mt-1">
            <span>Periode</span>
            <span>{formatSimpleDate(printingBooking.start_date)} - {formatDueDate(printingBooking)}</span>
        </div>
        <div class="flex justify-between mt-0.5">
            <span>Durasi</span>
            <span>{printingBooking.rental_days} Hari</span>
        </div>
    </div>

    <!-- Pembayaran -->
    <div class="border-t border-dashed border-black pt-3 mb-3 space-y-1">
        <div class="font-bold mb-1.5 text-[10px] uppercase tracking-wider">Rincian Pembayaran</div>
        <div class="flex justify-between">
            <span>Biaya Sewa</span>
            <span>{formatRupiah(printingBooking.total_price)}</span>
        </div>
        <div class="flex justify-between">
            <span>DP Masuk</span>
            <span>{formatRupiah(printingBooking.dp_amount)}</span>
        </div>
        <div class="flex justify-between">
            <span>Pelunasan Toko</span>
            <span>{formatRupiah(Math.max(0, (printingBooking.total_price || 0) - (printingBooking.dp_amount || 0)))}</span>
        </div>
        {#if printingBooking.damage_fee && printingBooking.damage_fee > 0}
            <div class="flex justify-between text-red-600">
                <span>Biaya Kerusakan</span>
                <span class="font-bold">{formatRupiah(printingBooking.damage_fee)}</span>
            </div>
        {/if}
        {#if printingBooking.damage_notes}
            <div class="text-[9px] text-red-600 italic">
                <span>Ket. Rusak: {printingBooking.damage_notes}</span>
            </div>
        {/if}
        {#if printingBooking.fine_amount && printingBooking.fine_amount > 0}
            {@const lateFine = Math.max(0, (printingBooking.fine_amount || 0) - (printingBooking.damage_fee || 0))}
            {#if lateFine > 0}
                <div class="flex justify-between text-amber-600">
                    <span>Denda Keterlambatan</span>
                    <span>{formatRupiah(lateFine)}</span>
                </div>
            {/if}
        {/if}

        <div class="border-t border-dashed border-black my-1.5"></div>
        {@const grandTotal = (printingBooking.total_price || 0) + (printingBooking.fine_amount || 0)}
        <div class="flex justify-between font-bold text-sm">
            <span>TOTAL AKHIR</span>
            <span>{formatRupiah(grandTotal)}</span>
        </div>
        <div class="flex justify-between text-xs text-zinc-600 font-medium">
            <span>Sisa Tagihan</span>
            <span class="font-bold">{(printingBooking.status === 'handed_over' || printingBooking.status === 'returned' || printingBooking.remaining_balance <= 0) ? 'Rp 0 (LUNAS)' : formatRupiah(printingBooking.remaining_balance)}</span>
        </div>

        <div class="border-t border-dashed border-black my-1.5"></div>
        <div class="flex justify-between font-bold text-sm">
            <span>STATUS</span>
            <span>{printingBooking.status === 'returned' ? 'SELESAI (RETURNED)' : 'LUNAS'}</span>
        </div>
    </div>

    <!-- Footer -->
    <div class="text-center mt-4 text-[10px] border-t border-black pt-3">
        <p class="font-bold">Terima kasih telah menyewa</p>
        <p class="font-bold">di Outdoor Species.</p>
        <p class="mt-1">Kembalikan barang tepat waktu</p>
        <p>untuk menghindari denda.</p>
    </div>
</div>
{/if}
