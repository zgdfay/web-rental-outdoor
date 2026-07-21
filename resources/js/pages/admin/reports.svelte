<script>
    import Sidebar from '@/components/layout/Sidebar.svelte';
    import { router } from '@inertiajs/svelte';
    import { TrendingUp, Printer, Download, Calendar, Search, Filter, DollarSign, CreditCard, AlertTriangle, CheckCircle2, RotateCcw, Package } from 'lucide-svelte';
    import { fly, fade } from 'svelte/transition';
    import * as Select from "@/components/ui/select/index.js";

    let { bookings = [], stats = {}, filters = {} } = $props();

    let startDate = $state(filters.start_date || '');
    let endDate = $state(filters.end_date || '');
    let statusFilter = $state(filters.status || 'all');
    let searchQuery = $state('');

    const filteredBookings = $derived.by(() => {
        return bookings.filter(b => {
            const matchesSearch = !searchQuery || 
                b.customer_name?.toLowerCase().includes(searchQuery.toLowerCase()) ||
                b.gear?.name?.toLowerCase().includes(searchQuery.toLowerCase()) ||
                String(b.id).includes(searchQuery);
            return matchesSearch;
        });
    });

    function applyFilters() {
        router.get('/admin/reports', {
            start_date: startDate,
            end_date: endDate,
            status: statusFilter === 'all' ? '' : statusFilter
        }, { preserveState: true, preserveScroll: true });
    }

    function resetFilters() {
        startDate = '';
        endDate = '';
        statusFilter = 'all';
        searchQuery = '';
        router.get('/admin/reports', {}, { preserveState: true, preserveScroll: true });
    }

    function exportToCSV() {
        const headers = ['ID Booking', 'Tanggal', 'Customer', 'No HP', 'Alamat', 'Item Sewa', 'Durasi (Hari)', 'Total Harga', 'DP', 'Pelunasan', 'Denda', 'Status'];
        const rows = filteredBookings.map(b => [
            `#${b.id}`,
            new Date(b.created_at).toLocaleDateString('id-ID'),
            `"${(b.customer_name || '').replace(/"/g, '""')}"`,
            `"${b.customer_phone || ''}"`,
            `"${(b.customer_address || '').replace(/"/g, '""')}"`,
            `"${(b.gear?.name || '').replace(/"/g, '""')} (x${b.quantity || 1})"`,
            b.rental_days || 1,
            b.total_price || 0,
            b.dp_amount || 0,
            b.remaining_balance || 0,
            b.fine_amount || 0,
            b.status || ''
        ]);

        let csvContent = "data:text/csv;charset=utf-8," 
            + headers.join(",") + "\n"
            + rows.map(e => e.join(",")).join("\n");

        const encodedUri = encodeURI(csvContent);
        const link = document.createElement("a");
        link.setAttribute("href", encodedUri);
        link.setAttribute("download", `Laporan_Pendapatan_OutdoorSpecies_${new Date().toISOString().slice(0,10)}.csv`);
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }

    function printReport() {
        window.print();
    }

    function formatCurrency(amount) {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0
        }).format(amount || 0);
    }
</script>

<svelte:head>
    <title>Laporan Pendapatan - Outdoor Species</title>
</svelte:head>

<div class="flex bg-zinc-50 min-h-screen font-sans selection:bg-primary selection:text-white print:bg-white print:block">
    <div class="print:hidden">
        <Sidebar role="admin" />
    </div>

    <main class="flex-grow p-10 print:p-0">
        <div class="max-w-7xl mx-auto space-y-8">
            <!-- Header -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 print:hidden">
                <div class="space-y-1">
                    <h1 class="text-2xl font-bold tracking-tight text-zinc-900">Laporan Pendapatan</h1>
                    <p class="text-sm text-zinc-500">Pantau transaksi, omset penyewaan, pelunasan, dan denda keterlambatan.</p>
                </div>
                <div class="flex items-center gap-3">
                    <button 
                        type="button"
                        onclick={exportToCSV}
                        class="flex items-center gap-2 bg-white border border-zinc-200 text-zinc-800 px-4 py-2.5 rounded-lg font-semibold text-xs uppercase tracking-wider hover:bg-zinc-50 transition-all shadow-sm"
                    >
                        <Download size={16} />
                        Export CSV
                    </button>
                    <button 
                        type="button"
                        onclick={printReport}
                        class="flex items-center gap-2 bg-zinc-900 text-white px-5 py-2.5 rounded-lg font-semibold text-xs uppercase tracking-wider hover:bg-zinc-800 transition-all shadow-sm"
                    >
                        <Printer size={16} />
                        Cetak Laporan
                    </button>
                </div>
            </div>

            <!-- Print Only Header -->
            <div class="hidden print:block border-b border-zinc-300 pb-6 mb-6">
                <h1 class="text-3xl font-black uppercase">OUTDOOR SPECIES - LAPORAN PENDAPATAN</h1>
                <p class="text-sm text-zinc-600 mt-1">Dicetak pada: {new Date().toLocaleDateString('id-ID', { dateStyle: 'full' })}</p>
                {#if startDate && endDate}
                    <p class="text-xs text-zinc-500">Periode: {startDate} s/d {endDate}</p>
                {/if}
            </div>

            <!-- Stats Overview -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                <div class="bg-white border border-zinc-200 p-6 rounded-2xl shadow-sm space-y-2 relative overflow-hidden">
                    <div class="flex items-center justify-between">
                        <p class="text-[10px] font-black uppercase tracking-widest text-zinc-400">Total Omset Sewa</p>
                        <div class="size-8 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center">
                            <DollarSign size={16} />
                        </div>
                    </div>
                    <p class="text-2xl font-black text-zinc-900">{formatCurrency(stats.total_revenue)}</p>
                    <p class="text-[11px] text-zinc-400 font-medium">Dari transaksi terkonfirmasi/selesai</p>
                </div>

                <div class="bg-white border border-zinc-200 p-6 rounded-2xl shadow-sm space-y-2 relative overflow-hidden">
                    <div class="flex items-center justify-between">
                        <p class="text-[10px] font-black uppercase tracking-widest text-zinc-400">Total DP Terkumpul</p>
                        <div class="size-8 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center">
                            <CreditCard size={16} />
                        </div>
                    </div>
                    <p class="text-2xl font-black text-zinc-900">{formatCurrency(stats.total_dp)}</p>
                    <p class="text-[11px] text-zinc-400 font-medium">Uang muka sewa masuk (30%)</p>
                </div>

                <div class="bg-white border border-zinc-200 p-6 rounded-2xl shadow-sm space-y-2 relative overflow-hidden">
                    <div class="flex items-center justify-between">
                        <p class="text-[10px] font-black uppercase tracking-widest text-zinc-400">Total Pelunasan</p>
                        <div class="size-8 rounded-full bg-indigo-50 text-indigo-600 flex items-center justify-center">
                            <CheckCircle2 size={16} />
                        </div>
                    </div>
                    <p class="text-2xl font-black text-zinc-900">{formatCurrency(stats.total_remaining)}</p>
                    <p class="text-[11px] text-zinc-400 font-medium">Sisa tagihan sewa dilunasi</p>
                </div>

                <div class="bg-white border border-zinc-200 p-6 rounded-2xl shadow-sm space-y-2 relative overflow-hidden">
                    <div class="flex items-center justify-between">
                        <p class="text-[10px] font-black uppercase tracking-widest text-zinc-400">Denda Keterlambatan</p>
                        <div class="size-8 rounded-full bg-amber-50 text-amber-600 flex items-center justify-center">
                            <AlertTriangle size={16} />
                        </div>
                    </div>
                    <p class="text-2xl font-black {stats.total_fines > 0 ? 'text-amber-600' : 'text-zinc-900'}">{formatCurrency(stats.total_fines)}</p>
                    <p class="text-[11px] text-zinc-400 font-medium">Dari {stats.completed_rentals || 0} penyewaan selesai</p>
                </div>
            </div>

            <!-- Toolbar Filters -->
            <div class="bg-white border border-zinc-200 rounded-2xl p-5 shadow-sm space-y-4 print:hidden">
                <div class="flex flex-wrap items-end gap-4">
                    <div class="space-y-1.5 min-w-[150px]">
                        <label class="text-xs font-bold text-zinc-600 uppercase tracking-wider">Mulai Tanggal</label>
                        <input 
                            type="date" 
                            bind:value={startDate}
                            class="w-full h-10 bg-zinc-50 border border-zinc-200 rounded-lg px-3 text-sm font-medium focus:border-zinc-400 outline-none"
                        />
                    </div>

                    <div class="space-y-1.5 min-w-[150px]">
                        <label class="text-xs font-bold text-zinc-600 uppercase tracking-wider">Sampai Tanggal</label>
                        <input 
                            type="date" 
                            bind:value={endDate}
                            class="w-full h-10 bg-zinc-50 border border-zinc-200 rounded-lg px-3 text-sm font-medium focus:border-zinc-400 outline-none"
                        />
                    </div>

                    <div class="space-y-1.5 min-w-[180px]">
                        <label class="text-xs font-bold text-zinc-600 uppercase tracking-wider">Status Transaksi</label>
                        <Select.Root type="single" bind:value={statusFilter}>
                            <Select.Trigger class="w-full h-10 bg-zinc-50 border border-zinc-200 rounded-lg px-3 text-sm font-medium">
                                {statusFilter === 'all' ? 'Semua Status' : statusFilter === 'returned' ? 'Selesai (Returned)' : statusFilter === 'handed_over' ? 'Sedang Disewa' : statusFilter}
                            </Select.Trigger>
                            <Select.Content class="z-50">
                                <Select.Item value="all">Semua Status</Select.Item>
                                <Select.Item value="returned">Selesai (Returned)</Select.Item>
                                <Select.Item value="handed_over">Sedang Disewa (Handed Over)</Select.Item>
                                <Select.Item value="validated">Siap Diambil (Validated)</Select.Item>
                                <Select.Item value="pending">Menunggu Validasi</Select.Item>
                            </Select.Content>
                        </Select.Root>
                    </div>

                    <div class="flex-grow space-y-1.5 min-w-[220px]">
                        <label class="text-xs font-bold text-zinc-600 uppercase tracking-wider">Cari Customer / Gear / ID</label>
                        <div class="relative">
                            <Search class="absolute left-3.5 top-1/2 -translate-y-1/2 text-zinc-400" size={16} />
                            <input 
                                type="text" 
                                bind:value={searchQuery}
                                placeholder="Cari nama, alat, atau #ID..." 
                                class="w-full h-10 bg-zinc-50 border border-zinc-200 rounded-lg pl-10 pr-4 text-sm font-medium focus:border-zinc-400 outline-none"
                            />
                        </div>
                    </div>

                    <div class="flex gap-2">
                        <button 
                            type="button"
                            onclick={applyFilters}
                            class="h-10 px-5 bg-primary text-white rounded-lg font-bold text-xs uppercase tracking-wider hover:bg-primary/90 transition-all shadow-md shadow-primary/20"
                        >
                            Filter
                        </button>
                        {#if startDate || endDate || statusFilter !== 'all' || searchQuery}
                            <button 
                                type="button"
                                onclick={resetFilters}
                                class="h-10 px-4 bg-zinc-100 text-zinc-600 rounded-lg font-bold text-xs uppercase tracking-wider hover:bg-zinc-200 transition-all"
                            >
                                Reset
                            </button>
                        {/if}
                    </div>
                </div>
            </div>

            <!-- Table Section -->
            <div class="bg-white border border-zinc-200 rounded-2xl overflow-hidden shadow-sm">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-zinc-50/80 border-b border-zinc-200 text-[11px] font-black uppercase tracking-wider text-zinc-500">
                                <th class="px-6 py-4">ID / Tanggal</th>
                                <th class="px-6 py-4">Customer</th>
                                <th class="px-6 py-4">Alat Sewa</th>
                                <th class="px-6 py-4">Durasi</th>
                                <th class="px-6 py-4">Total Sewa</th>
                                <th class="px-6 py-4">DP (30%)</th>
                                <th class="px-6 py-4">Pelunasan</th>
                                <th class="px-6 py-4">Denda</th>
                                <th class="px-6 py-4 text-right">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-100">
                            {#each filteredBookings as b}
                                <tr class="hover:bg-zinc-50/50 transition-colors text-sm">
                                    <td class="px-6 py-4.5 whitespace-nowrap">
                                        <p class="font-black text-zinc-900">#{b.id}</p>
                                        <p class="text-xs font-medium text-zinc-400">{new Date(b.created_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' })}</p>
                                    </td>
                                    <td class="px-6 py-4.5">
                                        <p class="font-bold text-zinc-900">{b.customer_name || '-'}</p>
                                        <p class="text-xs text-zinc-400">{b.customer_phone || '-'}</p>
                                    </td>
                                    <td class="px-6 py-4.5">
                                        <div class="flex items-center gap-3">
                                            <div class="size-9 rounded-lg bg-zinc-100 flex items-center justify-center overflow-hidden border border-zinc-200 shrink-0">
                                                {#if b.gear?.image_path}
                                                    <img src="/storage/{b.gear.image_path}" class="w-full h-full object-cover" />
                                                {:else}
                                                    <Package size={16} class="text-zinc-400" />
                                                {/if}
                                            </div>
                                            <div>
                                                <p class="font-bold text-zinc-900 text-xs">{b.gear?.name || 'Unknown Gear'}</p>
                                                <p class="text-[11px] text-zinc-500">Qty: {b.quantity || 1} unit</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4.5 whitespace-nowrap">
                                        <span class="px-2.5 py-1 bg-zinc-100 text-zinc-800 rounded-md text-xs font-bold">{b.rental_days} Hari</span>
                                    </td>
                                    <td class="px-6 py-4.5 whitespace-nowrap font-black text-zinc-900">
                                        {formatCurrency(b.total_price)}
                                    </td>
                                    <td class="px-6 py-4.5 whitespace-nowrap font-bold text-zinc-600">
                                        {formatCurrency(b.dp_amount)}
                                    </td>
                                    <td class="px-6 py-4.5 whitespace-nowrap font-bold text-zinc-800">
                                        {formatCurrency(b.remaining_balance)}
                                    </td>
                                    <td class="px-6 py-4.5 whitespace-nowrap font-black {b.fine_amount > 0 ? 'text-amber-600' : 'text-zinc-300'}">
                                        {b.fine_amount > 0 ? formatCurrency(b.fine_amount) : '-'}
                                    </td>
                                    <td class="px-6 py-4.5 text-right whitespace-nowrap">
                                        {#if b.status === 'returned'}
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 text-xs font-black uppercase tracking-wider border border-emerald-200/60">
                                                <CheckCircle2 size={12} /> Selesai
                                            </span>
                                        {:else if b.status === 'handed_over'}
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-blue-50 text-blue-700 text-xs font-black uppercase tracking-wider border border-blue-200/60">
                                                Sedang Disewa
                                            </span>
                                        {:else if b.status === 'validated'}
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-50 text-amber-700 text-xs font-black uppercase tracking-wider border border-amber-200/60">
                                                Siap Diambil
                                            </span>
                                        {:else}
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-zinc-100 text-zinc-600 text-xs font-black uppercase tracking-wider border border-zinc-200">
                                                {b.status}
                                            </span>
                                        {/if}
                                    </td>
                                </tr>
                            {/each}
                        </tbody>
                    </table>
                </div>

                {#if filteredBookings.length === 0}
                    <div class="py-20 text-center space-y-3">
                        <div class="size-16 rounded-full bg-zinc-100 flex items-center justify-center mx-auto text-zinc-400">
                            <TrendingUp size={32} />
                        </div>
                        <p class="font-bold text-zinc-800 text-sm">Tidak ada riwayat transaksi</p>
                        <p class="text-xs text-zinc-400">Coba sesuaikan filter rentang tanggal atau kata kunci pencarian Anda.</p>
                    </div>
                {/if}
            </div>
        </div>
    </main>
</div>

<style>
    @media print {
        @page {
            size: A4 landscape;
            margin: 15mm;
        }
        body {
            background: white !important;
            color: black !important;
        }
    }
</style>
