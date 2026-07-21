<script>
    import { Link } from '@inertiajs/svelte';
    import { CheckCircle2, ChevronLeft, ShieldCheckIcon, InfoIcon, MapPinIcon, MessageSquare } from 'lucide-svelte';
    import { fly } from 'svelte/transition';

    let { product_name, rental_days, quantity, remaining_balance, name, phone, address, booked_items = [] } = $props();

    const today = new Date();
    const returnDate = new Date();
    returnDate.setDate(today.getDate() + parseInt(rental_days));

    function formatDate(date) {
        return date.toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
    }

    function formatCurrency(amount) {
        if (!amount) return 'RP 0';
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0
        }).format(amount).replace('Rp', 'RP');
    }

    const waPhone = $derived(() => {
        let p = (phone || '').replace(/[^0-9]/g, '');
        if (p.startsWith('0')) p = '62' + p.substring(1);
        return p;
    });

    const waText = $derived(() => {
        const itemsText = (booked_items && booked_items.length > 0)
            ? booked_items.map(i => `${i.name} (${i.quantity}x)`).join(', ')
            : `${product_name} (${quantity || 1}x)`;
        return `🏕️ *OUTDOOR SPECIES - KONFIRMASI BOOKING* 🏕️\n\n` +
               `Halo kak *${name}*,\n` +
               `Terima kasih telah memesan alat outdoor di toko kami!\n\n` +
               `📦 *Alat Dipesan*: ${itemsText}\n` +
               `⏱️ *Durasi Sewa*: ${rental_days} Hari\n` +
               `📅 *Ambil*: ${formatDate(today)}\n` +
               `📅 *Kembali*: ${formatDate(returnDate)}\n` +
               `💰 *Sisa Pelunasan*: *${formatCurrency(remaining_balance)}* (Dibayar saat pengambilan alat)\n\n` +
               `Mohon membawa KTP Asli saat pengambilan alat di toko Outdoor Species.`;
    });
</script>

<svelte:head>
    <title>Booking Success - Outdoor Species</title>
</svelte:head>

<div class="bg-zinc-50 text-zinc-900 selection:bg-primary selection:text-white min-h-screen flex flex-col font-sans">

    <main class="flex-grow pt-12 pb-20 px-6 flex items-center justify-center">
        <div class="w-full max-w-4xl mx-auto">
            <div class="bg-white border border-zinc-200 rounded-[32px] p-10 md:p-16 text-center space-y-10 shadow-2xl shadow-zinc-200/50" in:fly={{ y: 50, duration: 800 }}>
                <div class="flex flex-col items-center space-y-6">
                    <div class="size-24 rounded-full bg-green-50 flex items-center justify-center text-green-600 border border-green-100 shadow-inner">
                        <CheckCircle2 size={48} />
                    </div>
                    <div class="space-y-2">
                        <h1 class="text-4xl font-black tracking-tight text-zinc-900 leading-none uppercase">Booking Success!</h1>
                        <p class="text-zinc-500 font-semibold uppercase text-xs">No. Booking: #BO-{Math.floor(Math.random() * 90000) + 10000}</p>
                    </div>
                </div>

                <div class="grid md:grid-cols-2 gap-8 text-left">
                    <div class="p-8 bg-zinc-50 rounded-3xl border border-zinc-100 space-y-6 relative overflow-hidden flex flex-col justify-between">
                        <div class="absolute top-0 right-0 p-4 opacity-10 rotate-12">
                            <ShieldCheckIcon size={80} />
                        </div>
                        <div>
                            <h3 class="text-xs font-black uppercase tracking-widest text-primary mb-6">Booking Summary</h3>
                            <div class="space-y-4">
                                <div class="flex justify-between border-b border-zinc-200/50 pb-3">
                                    <span class="text-sm text-zinc-500 font-medium">Name</span>
                                    <span class="text-sm text-zinc-900 font-bold">{name}</span>
                                </div>
                                <div class="flex justify-between border-b border-zinc-200/50 pb-3">
                                    <span class="text-sm text-zinc-500 font-medium">Phone</span>
                                    <span class="text-sm text-zinc-900 font-bold">{phone}</span>
                                </div>
                                <div class="flex justify-between border-b border-zinc-200/50 pb-3">
                                    <span class="text-sm text-zinc-500 font-medium">Items Booked</span>
                                    <div class="text-right">
                                        {#if booked_items && booked_items.length > 0}
                                            <div class="space-y-1">
                                                {#each booked_items as item}
                                                    <p class="text-sm text-zinc-900 font-bold">{item.name} <span class="text-primary text-xs">({item.quantity}x)</span></p>
                                                {/each}
                                            </div>
                                        {:else}
                                            <span class="text-sm text-zinc-900 font-bold">{product_name} ({quantity || 1}x)</span>
                                        {/if}
                                    </div>
                                </div>
                                <div class="flex justify-between border-b border-zinc-200/50 pb-3">
                                    <span class="text-sm text-zinc-500 font-medium">Total Units</span>
                                    <span class="text-sm text-zinc-900 font-bold">{quantity || 1} Unit(s)</span>
                                </div>
                                <div class="flex justify-between border-b border-zinc-200/50 pb-3">
                                    <span class="text-sm text-zinc-500 font-medium">Rental Date</span>
                                    <span class="text-sm text-zinc-900 font-bold">{formatDate(today)}</span>
                                </div>
                                <div class="flex justify-between border-b border-zinc-200/50 pb-3">
                                    <span class="text-sm text-zinc-500 font-medium">Return Date</span>
                                    <span class="text-sm text-zinc-900 font-bold text-primary">{formatDate(returnDate)}</span>
                                </div>
                                <div class="flex justify-between border-b border-zinc-200/50 pb-3">
                                    <span class="text-sm text-zinc-500 font-medium">Duration</span>
                                    <span class="text-sm text-zinc-900 font-bold">{rental_days} Days</span>
                                </div>
                                <div class="flex justify-between pt-2">
                                    <span class="text-sm text-zinc-500 font-medium">Remaining</span>
                                    <span class="text-lg text-zinc-900 font-black">{formatCurrency(remaining_balance)}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-6 flex flex-col">
                        <div class="flex-grow bg-blue-50/50 border border-blue-100 rounded-3xl p-8 space-y-6 flex flex-col items-center justify-center text-center">
                            <div class="size-14 rounded-2xl bg-blue-100 flex items-center justify-center text-blue-600">
                                <MapPinIcon size={28} />
                            </div>
                            <div class="space-y-2">
                                <h3 class="text-xs font-black uppercase tracking-widest text-blue-600">Pickup Location</h3>
                                <p class="text-sm text-zinc-600 font-bold leading-relaxed">
                                    OUTDOOR SPECIES INDONESIA<br/>
                                    Jl. Veteran No. 08<br/>
                                    Malang, East Java
                                </p>
                            </div>
                        </div>

                        <div class="bg-primary/5 border border-primary/10 rounded-3xl p-8 space-y-3">
                            <p class="text-sm font-black text-primary uppercase flex items-center gap-2">
                                <InfoIcon size={16} />
                                IMPORTANT
                            </p>
                            <p class="text-[13px] text-zinc-600 leading-relaxed font-medium">
                                Please <b>screenshot or take a photo</b> of this page. Bring it to our store to verify your booking and pick up your gear.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="pt-4 flex flex-col sm:flex-row items-center justify-center gap-4">
                    <a 
                        href={`https://wa.me/${waPhone()}?text=${encodeURIComponent(waText())}`}
                        target="_blank"
                        rel="noopener noreferrer"
                        class="inline-flex items-center gap-2.5 px-8 py-5 bg-[#25D366] text-white rounded-2xl font-black uppercase tracking-[0.1em] text-[11px] hover:bg-[#20bd5a] transition-all shadow-xl shadow-[#25D366]/20 w-full sm:w-auto justify-center"
                    >
                        <MessageSquare size={18} />
                        Buka Salinan di WhatsApp
                    </a>
                    <Link href="/catalog" class="inline-flex px-10 py-5 bg-zinc-900 text-white rounded-2xl font-black uppercase tracking-[0.1em] text-[11px] hover:bg-zinc-800 transition-all shadow-xl shadow-zinc-900/20 w-full sm:w-auto justify-center">
                        Return to Catalog
                    </Link>
                </div>
            </div>
        </div>
    </main>
</div>
