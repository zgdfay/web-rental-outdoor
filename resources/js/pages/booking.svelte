<script>
    import { Link, useForm, router } from '@inertiajs/svelte';
    import Navbar from '@/components/layout/navbar.svelte';
    import Footer from '@/components/layout/footer.svelte';
    import { User, Phone, Mail, MapPin, CreditCard, Upload, CheckCircle2, ChevronLeft, Calendar, CalendarRange, ArrowRight, ShieldCheckIcon, X, Copy, Check } from 'lucide-svelte';
    import { fly, fade } from 'svelte/transition';

    // Assume these are passed from the catalog selection
    let { product: initialProduct = { name: "Osprey Aether 65", price: "Rp 12k/hari" } } = $props();
    
    let product = $state({ 
        stock: 1, // Explicitly declare to ensure deep Svelte 5 reactivity tracking
        image_path: '',
        ...initialProduct 
    });
    const isSoldOut = $derived(product.stock <= 0);

    // Sync with query params if available (for client-side navigation)
    $effect(() => {
        const urlParams = new URLSearchParams(window.location.search);
        const id = urlParams.get('id');
        const name = urlParams.get('name');
        const price = urlParams.get('price');
        const stock = urlParams.get('stock');
        const image_path = urlParams.get('image_path');
        
        if (id) $form.gear_id = id;
        if (name) product.name = name;
        if (price) product.price = price;
        
        // Fix for "mentok": avoid parseInt("undefined") which returns NaN
        if (stock !== null && stock !== 'undefined' && stock !== '') {
            const parsedStock = parseInt(stock);
            if (!isNaN(parsedStock)) {
                product.stock = parsedStock;
            }
        } else if (product.stock === undefined) {
            product.stock = 1; // Default to 1 to avoid reactivity tracking issues
        }
        
        if (image_path && image_path !== 'undefined') product.image_path = image_path;
    });

    const form = useForm({
        gear_id: '',
        name: '',
        phone: '',
        email: '',
        address: '',
        ktp_file: null,
        transfer_file: null,
        rental_days: 1,
        quantity: 1,
        dp_percentage: 30,
    });

    let ktpPreview = $state(null);
    let transferPreview = $state(null);
    let copiedField = $state(null);

    function copyToClipboard(text, field) {
        if (navigator.clipboard) {
            navigator.clipboard.writeText(text);
            copiedField = field;
            setTimeout(() => {
                if (copiedField === field) copiedField = null;
            }, 2000);
        }
    }

    function handleFileChange(e, field) {
        const file = e.target.files[0];
        if (file) {
            $form[field] = file;
            const reader = new FileReader();
            reader.onload = (e) => {
                if (field === 'ktp_file') ktpPreview = e.target.result;
                else transferPreview = e.target.result;
            };
            reader.readAsDataURL(file);
        }
    }

    function removeFile(field) {
        $form[field] = null;
        if (field === 'ktp_file') ktpPreview = null;
        else transferPreview = null;
    }

    function handleSubmit(e) {
        e.preventDefault();
        $form.post('/bookings');
    }

    const bankDetails = {
        bank: "Bank Central Asia (BCA)",
        accountNumber: "8820 9182 1102",
        accountHolder: "Outdoor Species Indonesia"
    };

    const priceNumeric = $derived(() => {
        const val = product.price.toString().replace(/[^0-9]/g, '');
        return parseInt(val) || 0;
    });

    const totalPrice = $derived(priceNumeric() * $form.rental_days * $form.quantity);

    // Macam pilihan skema pembayaran DP
    const dpTiers = [
        { percentage: 30, label: 'DP 30%', desc: 'Standar Minimum', badge: 'Standar' },
        { percentage: 50, label: 'DP 50%', desc: 'Setengah Biaya', badge: 'Disarankan' },
        { percentage: 100, label: 'Lunas 100%', desc: 'Bayar Penuh', badge: 'Bebas Antre' },
    ];

    function calculateTierAmount(percentage) {
        if (percentage >= 100) return totalPrice;
        return Math.ceil((totalPrice * (percentage / 100)) / 1000) * 1000;
    }

    const calculatedDP = $derived(
        $form.dp_percentage >= 100
            ? totalPrice
            : Math.ceil((totalPrice * ($form.dp_percentage / 100)) / 1000) * 1000
    );

    const remainingBalance = $derived(Math.max(0, totalPrice - calculatedDP));

    function formatCurrency(amount) {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0
        }).format(amount).replace('Rp', 'RP');
    }

    const isFormValid = $derived(
        !isSoldOut &&
        $form.name.trim() !== '' &&
        $form.phone.trim() !== '' &&
        $form.address.trim() !== '' &&
        $form.ktp_file !== null &&
        $form.transfer_file !== null
    );
</script>

<svelte:head>
    <title>Booking Form - Outdoor Species</title>
</svelte:head>

<div class="bg-zinc-50 text-zinc-900 selection:bg-primary selection:text-white min-h-screen flex flex-col font-sans">
    <!-- Sticky Header -->
    <header class="sticky top-0 z-50 bg-white/80 backdrop-blur-md border-b border-zinc-200 py-4 px-6">
        <div class="max-w-6xl mx-auto flex items-center justify-between">
            <Link href="/catalog" class="flex items-center gap-2 text-zinc-500 hover:text-zinc-900 transition-all font-bold text-xs uppercase tracking-widest group">
                <div class="size-8 rounded-full bg-white flex items-center justify-center border border-zinc-200 group-hover:border-primary/50 transition-colors shadow-sm">
                    <ChevronLeft size={16} class="group-hover:text-primary transition-colors" />
                </div>
                Back to Store
            </Link>
            <div class="flex items-center gap-2">
                <ShieldCheckIcon class="text-green-600" size={20} />
                <span class="text-[10px] font-black uppercase text-green-600">Secure Checkout</span>
            </div>
        </div>
    </header>

    <main class="flex-grow pt-12 pb-20 px-6">
        <div class="max-w-6xl mx-auto">
            
                <div class="grid lg:grid-cols-3 gap-12">
                    
                    <!-- Left: Form Content -->
                    <div class="lg:col-span-2 space-y-12">
                        <div class="space-y-4">
                            <h1 class="text-5xl font-black tracking-tight leading-none uppercase">RENTAL BOOKING</h1>
                            <p class="text-zinc-500 font-medium">Complete the form below to secure your outdoor equipment.</p>
                        </div>

                        <form onsubmit={handleSubmit} class="space-y-10">
                            <!-- Section: Personal Data -->
                            <div class="space-y-6">
                                <h3 class="text-sm font-black uppercase tracking-widest text-zinc-400 flex items-center gap-3">
                                    Personal Information
                                </h3>
                                <div class="grid md:grid-cols-2 gap-5">
                                    <div class="space-y-1.5">
                                        <label for="name" class="text-[11px] font-black uppercase tracking-wider text-zinc-400 ml-1">Full Name</label>
                                        <div class="relative group">
                                            <User class="absolute left-4 top-1/2 -translate-y-1/2 text-zinc-300 group-focus-within:text-primary transition-colors" size={18} />
                                            <input type="text" id="name" bind:value={$form.name} placeholder="e.g. John Doe" class="w-full h-14 bg-white border border-zinc-200 rounded-xl pl-12 pr-4 focus:border-primary/50 focus:ring-4 focus:ring-primary/5 transition-all outline-none text-zinc-900 font-medium" required />
                                        </div>
                                    </div>
                                    <div class="space-y-1.5">
                                        <label for="phone" class="text-[11px] font-black uppercase tracking-wider text-zinc-400 ml-1">WhatsApp Number</label>
                                        <div class="relative group">
                                            <Phone class="absolute left-4 top-1/2 -translate-y-1/2 text-zinc-300 group-focus-within:text-primary transition-colors" size={18} />
                                            <input 
                                                type="tel" 
                                                id="phone" 
                                                bind:value={$form.phone} 
                                                oninput={(e) => $form.phone = e.target.value.replace(/\D/g, '')}
                                                inputmode="numeric"
                                                placeholder="e.g. 0812345678" 
                                                class="w-full h-14 bg-white border border-zinc-200 rounded-xl pl-12 pr-4 focus:border-primary/50 focus:ring-4 focus:ring-primary/5 transition-all outline-none text-zinc-900 font-medium" 
                                                required 
                                            />
                                        </div>
                                    </div>
                                    <div class="space-y-1.5 md:col-span-2">
                                        <label for="address" class="text-[11px] font-black uppercase tracking-wider text-zinc-400 ml-1">Home Address</label>
                                        <div class="relative group">
                                            <MapPin class="absolute left-4 top-4.5 text-zinc-300 group-focus-within:text-primary transition-colors" size={18} />
                                            <textarea id="address" bind:value={$form.address} rows="3" placeholder="Enter your complete home address for delivery..." class="w-full bg-white border border-zinc-200 rounded-xl pl-12 pr-4 py-4 focus:border-primary/50 focus:ring-4 focus:ring-primary/5 transition-all outline-none text-zinc-900 font-medium leading-relaxed" required></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Section: Document Upload -->
                            <div class="space-y-6">
                                <h3 class="text-sm font-black uppercase tracking-widest text-zinc-400 flex items-center gap-3">
                                    Identity & Verification
                                </h3>
                                <div class="grid md:grid-cols-2 gap-8">
                                    <!-- KTP Upload -->
                                    <div class="space-y-3">
                                        <p class="text-xs font-bold uppercase tracking-wider text-zinc-400 ml-1">Upload KTP (Identity Card)</p>
                                        {#if ktpPreview}
                                            <div class="relative w-full h-48 rounded-2xl overflow-hidden border border-zinc-200 group" in:fade>
                                                <img src={ktpPreview} alt="KTP Preview" class="w-full h-full object-cover" />
                                                <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center backdrop-blur-[2px]">
                                                    <button 
                                                        type="button"
                                                        onclick={() => removeFile('ktp_file')}
                                                        class="p-3 bg-red-500 text-white rounded-full hover:bg-red-600 transition-all transform hover:scale-110 shadow-xl"
                                                    >
                                                        <X size={20} />
                                                    </button>
                                                </div>
                                            </div>
                                        {:else}
                                            <label class="flex flex-col items-center justify-center w-full h-48 border-2 border-dashed border-zinc-200 rounded-2xl cursor-pointer hover:bg-white hover:border-primary/50 transition-all group overflow-hidden relative shadow-sm">
                                                <div class="flex flex-col items-center justify-center pt-5 pb-6 space-y-2">
                                                    <Upload class="text-zinc-300 group-hover:text-primary transition-colors" size={24} />
                                                    <p class="text-xs text-zinc-500 font-bold uppercase tracking-widest">Select Image</p>
                                                </div>
                                                <input type="file" class="hidden" accept="image/*" onchange={(e) => handleFileChange(e, 'ktp_file')} />
                                            </label>
                                        {/if}
                                    </div>
                                    <!-- Transfer Upload -->
                                    <div class="space-y-3">
                                        <div class="flex items-center justify-between ml-1">
                                            <p class="text-xs font-bold uppercase tracking-wider text-zinc-400">Upload Bukti Transfer</p>
                                            <span class="text-[10px] font-black text-primary bg-primary/10 px-2.5 py-0.5 rounded-full">
                                                Transfer: {formatCurrency(calculatedDP)}
                                            </span>
                                        </div>
                                        {#if transferPreview}
                                            <div class="relative w-full h-48 rounded-2xl overflow-hidden border border-zinc-200 group" in:fade>
                                                <img src={transferPreview} alt="Transfer Proof Preview" class="w-full h-full object-cover" />
                                                <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center backdrop-blur-[2px]">
                                                    <button 
                                                        type="button"
                                                        onclick={() => removeFile('transfer_file')}
                                                        class="p-3 bg-red-500 text-white rounded-full hover:bg-red-600 transition-all transform hover:scale-110 shadow-xl"
                                                    >
                                                        <X size={20} />
                                                    </button>
                                                </div>
                                            </div>
                                        {:else}
                                            <label class="flex flex-col items-center justify-center w-full h-48 border-2 border-dashed border-zinc-200 rounded-2xl cursor-pointer hover:bg-white hover:border-primary/50 transition-all group overflow-hidden relative shadow-sm">
                                                <div class="flex flex-col items-center justify-center pt-5 pb-6 space-y-2">
                                                    <CreditCard class="text-zinc-300 group-hover:text-primary transition-colors" size={24} />
                                                    <p class="text-xs text-zinc-500 font-bold uppercase tracking-widest">Select Image</p>
                                                </div>
                                                <input type="file" class="hidden" accept="image/*" onchange={(e) => handleFileChange(e, 'transfer_file')} />
                                            </label>
                                        {/if}
                                    </div>
                                </div>
                            </div>

                            <button 
                                type="submit" 
                                disabled={!isFormValid}
                                class="w-full h-20 bg-primary hover:bg-primary/90 text-white font-black uppercase tracking-[0.2em] text-sm rounded-2xl transition-all shadow-2xl shadow-primary/20 flex items-center justify-center gap-4 group disabled:bg-zinc-200 disabled:text-zinc-400 disabled:shadow-none disabled:cursor-not-allowed"
                            >
                                {#if isSoldOut}
                                    Item Sold Out
                                {:else if isFormValid}
                                    Confirm & Book Gear
                                    <ArrowRight class="transition-transform group-hover:translate-x-1" />
                                {:else}
                                    Complete Form to Book
                                {/if}
                            </button>
                        </form>
                    </div>

                    <!-- Right: Summary & Payment Info -->
                    <div class="lg:col-span-1 space-y-8">
                        <div class="bg-white border border-zinc-200 rounded-3xl p-8 space-y-8 sticky top-32 shadow-sm">
                            <h2 class="text-xs font-black uppercase tracking-widest text-zinc-500 border-b border-zinc-100 pb-4">Rental Summary</h2>
                            
                            <!-- Selected Product -->
                            <div class="flex items-center gap-4">
                                <div class="size-16 rounded-2xl bg-zinc-50 flex items-center justify-center border border-zinc-200 overflow-hidden">
                                    {#if product.image_path}
                                        <img src="/storage/{product.image_path}" alt={product.name} class="w-full h-full object-cover" />
                                    {:else}
                                        <CalendarRange class="text-primary" size={24} />
                                    {/if}
                                </div>
                                <div class="space-y-1">
                                    <h4 class="font-bold text-zinc-900 leading-tight {isSoldOut ? 'opacity-50' : ''}">{product.name}</h4>
                                    <p class="text-xs font-black text-primary uppercase tracking-widest">RP {product.price}</p>
                                </div>
                            </div>

                            <!-- Duration Selection -->
                            <div class="space-y-4">
                                <div class="flex justify-between items-center">
                                    <label class="text-[11px] font-black uppercase tracking-wider text-zinc-400">Duration</label>
                                    <span class="text-xs font-bold text-primary bg-primary/5 px-3 py-1 rounded-full">{$form.rental_days} Days</span>
                                </div>
                                <input 
                                    type="range" 
                                    min="1" 
                                    max="14" 
                                    bind:value={$form.rental_days} 
                                    disabled={isSoldOut}
                                    style="background: linear-gradient(to right, #fb923c 0%, #fb923c {($form.rental_days - 1) / 13 * 100}%, #f4f4f5 {($form.rental_days - 1) / 13 * 100}%, #f4f4f5 100%)"
                                    class="w-full h-1.5 rounded-lg appearance-none cursor-pointer accent-primary transition-all disabled:opacity-30 disabled:cursor-not-allowed" 
                                />
                                {#if isSoldOut}
                                    <p class="text-[10px] text-red-500 font-bold uppercase tracking-widest text-center mt-2">Unavailable: Stock Empty</p>
                                {/if}
                            </div>

                            <!-- Quantity Selection -->
                            <div class="space-y-4 pt-6 border-t border-zinc-100">
                                <div class="flex justify-between items-center">
                                    <div class="space-y-0.5">
                                        <label class="text-[11px] font-black uppercase tracking-wider text-zinc-400">Quantity</label>
                                        <p class="text-[9px] text-zinc-400 font-bold uppercase">Stock: {product.stock || 0} left</p>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <button 
                                            type="button"
                                            onclick={() => $form.quantity = Math.max(1, $form.quantity - 1)}
                                            class="size-8 rounded-full border border-zinc-200 flex items-center justify-center hover:bg-zinc-50 transition-colors disabled:opacity-30"
                                            disabled={$form.quantity <= 1}
                                        >
                                            <span class="text-zinc-500 font-bold">-</span>
                                        </button>
                                        <span class="text-xs font-bold text-zinc-900 w-4 text-center">{$form.quantity}</span>
                                        <button 
                                            type="button"
                                            onclick={() => $form.quantity = Math.min(product.stock || 1, $form.quantity + 1)}
                                            class="size-8 rounded-full border border-zinc-200 flex items-center justify-center hover:bg-zinc-50 transition-colors disabled:opacity-30"
                                            disabled={$form.quantity >= (product.stock || 1)}
                                        >
                                            <span class="text-zinc-500 font-bold">+</span>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Macam Pilihan Skema DP -->
                            <div class="space-y-3 pt-6 border-t border-zinc-100">
                                <div class="flex justify-between items-center">
                                    <div class="space-y-0.5">
                                        <label class="text-[11px] font-black uppercase tracking-wider text-zinc-400">Pilihan Skema DP</label>
                                        <p class="text-[9px] text-zinc-400 font-medium">Pilih besaran uang muka yang ingin dibayar</p>
                                    </div>
                                    <span class="text-[10px] font-black text-primary bg-primary/10 px-2.5 py-0.5 rounded-full uppercase tracking-wider">
                                        {$form.dp_percentage === 100 ? 'Lunas 100%' : `DP ${$form.dp_percentage}%`}
                                    </span>
                                </div>
                                <div class="grid grid-cols-3 gap-2">
                                    {#each dpTiers as tier}
                                        <button
                                            type="button"
                                            onclick={() => $form.dp_percentage = tier.percentage}
                                            class="p-2.5 rounded-2xl border text-left transition-all relative flex flex-col justify-between {$form.dp_percentage === tier.percentage 
                                                ? 'border-primary bg-primary/5 ring-2 ring-primary/20 shadow-sm' 
                                                : 'border-zinc-200 bg-white hover:border-zinc-300 hover:bg-zinc-50/50'}"
                                        >
                                            <div class="space-y-1">
                                                <div class="flex items-center justify-between">
                                                    <span class="text-[10px] font-black uppercase tracking-wider {$form.dp_percentage === tier.percentage ? 'text-primary' : 'text-zinc-500'}">
                                                        {tier.label}
                                                    </span>
                                                    {#if $form.dp_percentage === tier.percentage}
                                                        <span class="size-1.5 rounded-full bg-primary animate-pulse"></span>
                                                    {/if}
                                                </div>
                                                <p class="text-[11px] font-black text-zinc-900 leading-tight">
                                                    {formatCurrency(calculateTierAmount(tier.percentage))}
                                                </p>
                                            </div>
                                            <span class="text-[8px] font-bold mt-2 px-1.5 py-0.5 rounded w-fit {$form.dp_percentage === tier.percentage ? 'bg-primary text-white shadow-xs' : 'bg-zinc-100 text-zinc-500'}">
                                                {tier.badge}
                                            </span>
                                        </button>
                                    {/each}
                                </div>
                            </div>

                            <!-- Price Breakdown -->
                            <div class="space-y-3 pt-6 border-t border-zinc-100">
                                <div class="flex justify-between text-xs">
                                    <span class="text-zinc-500 font-medium">Tarif Sewa Harian</span>
                                    <span class="text-zinc-900 font-bold">RP {product.price} / hari</span>
                                </div>
                                <div class="flex justify-between text-xs">
                                    <span class="text-zinc-500 font-medium">Durasi Sewa</span>
                                    <span class="text-zinc-900 font-bold">{$form.rental_days} Hari</span>
                                </div>
                                <div class="flex justify-between text-xs">
                                    <span class="text-zinc-500 font-medium">Jumlah Unit</span>
                                    <span class="text-zinc-900 font-bold">{$form.quantity} Unit</span>
                                </div>
                                <div class="flex justify-between text-xs pb-1">
                                    <span class="text-zinc-500 font-medium">Total Biaya Sewa</span>
                                    <span class="text-zinc-900 font-black">{formatCurrency(totalPrice)}</span>
                                </div>

                                <div class="pt-3 border-t border-dashed border-zinc-200 space-y-2">
                                    <div class="flex justify-between items-center text-xs">
                                        <div class="flex items-center gap-1.5">
                                            <span class="text-zinc-900 font-bold">Total DP yang Harus Dibayar</span>
                                            <span class="text-[9px] font-black text-primary bg-primary/10 px-1.5 py-0.5 rounded">
                                                {$form.dp_percentage}%
                                            </span>
                                        </div>
                                        <span class="text-primary font-black text-sm">{formatCurrency(calculatedDP)}</span>
                                    </div>
                                    <div class="flex justify-between items-center text-xs">
                                        <span class="text-zinc-500 font-medium">Sisa Pelunasan di Toko</span>
                                        {#if remainingBalance > 0}
                                            <span class="text-zinc-800 font-black">{formatCurrency(remainingBalance)}</span>
                                        {:else}
                                            <span class="text-green-600 font-black bg-green-50 px-2 py-0.5 rounded-md text-[10px]">LUNAS (Rp 0)</span>
                                        {/if}
                                    </div>
                                </div>
                            </div>

                            <!-- Payment Info -->
                            <div class="space-y-6 pt-6 border-t border-zinc-100">
                                <div class="p-6 bg-zinc-50 rounded-2xl border border-zinc-200 space-y-5 relative overflow-hidden shadow-inner">
                                    <div class="absolute top-0 right-0 p-3 opacity-10">
                                        <CreditCard size={48} class="text-zinc-900" />
                                    </div>
                                    <div class="space-y-1">
                                        <h5 class="text-[10px] font-black uppercase tracking-[0.2em] text-zinc-400">Transfer Pembayaran Ke</h5>
                                        <p class="text-xs font-bold text-zinc-700">{bankDetails.bank}</p>
                                    </div>
                                    <div class="space-y-1">
                                        <div class="flex items-center gap-2">
                                            <p class="text-2xl font-black text-zinc-900 tracking-tight font-mono">{bankDetails.accountNumber}</p>
                                            <button 
                                                type="button" 
                                                onclick={() => copyToClipboard(bankDetails.accountNumber.replace(/\s+/g, ''), 'account')}
                                                class="size-8 rounded-lg border border-zinc-200 bg-white hover:bg-zinc-100 flex items-center justify-center text-zinc-600 transition-colors shadow-xs"
                                                title="Salin No. Rekening"
                                            >
                                                {#if copiedField === 'account'}
                                                    <Check size={14} class="text-green-600" />
                                                {:else}
                                                    <Copy size={14} />
                                                {/if}
                                            </button>
                                        </div>
                                        <p class="text-[10px] font-black text-zinc-400 uppercase italic">A/N: {bankDetails.accountHolder}</p>
                                    </div>
                                    <div class="pt-2">
                                        <div class="p-4 rounded-xl bg-primary text-white space-y-1.5 shadow-lg shadow-primary/20">
                                            <div class="flex items-center justify-between">
                                                <span class="text-[9px] font-black uppercase tracking-widest opacity-90">
                                                    Total Transfer ({$form.dp_percentage === 100 ? 'LUNAS 100%' : `DP ${$form.dp_percentage}%`})
                                                </span>
                                                <button 
                                                    type="button" 
                                                    onclick={() => copyToClipboard(calculatedDP.toString(), 'amount')}
                                                    class="text-[9px] font-bold uppercase tracking-wider bg-white/20 hover:bg-white/30 px-2 py-0.5 rounded transition-colors flex items-center gap-1"
                                                >
                                                    {#if copiedField === 'amount'}
                                                        <Check size={12} /> Tersalin
                                                    {:else}
                                                        <Copy size={12} /> Salin
                                                    {/if}
                                                </button>
                                            </div>
                                            <div class="text-2xl font-black tracking-tight">{formatCurrency(calculatedDP)}</div>
                                            {#if remainingBalance > 0}
                                                <p class="text-[10px] opacity-85 pt-1 border-t border-white/20 leading-relaxed">
                                                    Sisa <b>{formatCurrency(remainingBalance)}</b> dilunasi saat pengambilan alat di toko.
                                                </p>
                                            {:else}
                                                <p class="text-[10px] opacity-95 pt-1 border-t border-white/20 leading-relaxed text-emerald-100 font-semibold">
                                                    ✓ Pembayaran Lunas! Tidak ada biaya tambahan saat pengambilan alat.
                                                </p>
                                            {/if}
                                        </div>
                                    </div>
                                </div>
                                <p class="text-[10px] text-zinc-400 leading-relaxed font-medium italic text-center">
                                    *Pastikan transfer sesuai nominal DP di atas untuk mempercepat verifikasi oleh staff kami.
                                </p>
                            </div>
                        </div>
                    </div>

                </div>
        </div>
    </main>
</div>

<style>
    input::placeholder, textarea::placeholder {
        color: #d4d4d8;
        font-weight: 500;
        font-size: 0.85rem;
    }
</style>
