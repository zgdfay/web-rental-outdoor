<script>
    import { Link, useForm, router } from '@inertiajs/svelte';
    import Navbar from '@/components/layout/navbar.svelte';
    import Footer from '@/components/layout/footer.svelte';
    import { User, Phone, Mail, MapPin, CreditCard, Upload, CheckCircle2, ChevronLeft, Calendar, CalendarRange, ArrowRight, ShieldCheckIcon, X } from 'lucide-svelte';
    import { fly, fade } from 'svelte/transition';

    // Assume these are passed from the catalog selection
    let { product: initialProduct = { name: "Osprey Aether 65", price: "Rp 12k/hari" } } = $props();

    let product = $state({ ...initialProduct });
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
        if (stock !== null) product.stock = parseInt(stock);
        if (image_path) product.image_path = image_path;
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
    });

    let ktpPreview = $state(null);
    let transferPreview = $state(null);

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
    const calculatedDP = $derived(Math.ceil((totalPrice * 0.3) / 1000) * 1000); // 30% DP rounded to nearest 1k

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
                                        <p class="text-xs font-bold uppercase tracking-wider text-zinc-400 ml-1">Upload DP Transfer Proof</p>
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

                            <!-- Price Breakdown -->
                            <div class="space-y-3 pt-6 border-t border-zinc-100">
                                <div class="flex justify-between text-xs">
                                    <span class="text-zinc-500 font-medium">Daily Rate</span>
                                    <span class="text-zinc-900 font-bold">RP {product.price}</span>
                                </div>
                                <div class="flex justify-between text-xs">
                                    <span class="text-zinc-500 font-medium">Units Booked</span>
                                    <span class="text-zinc-900 font-bold">{$form.quantity} Unit(s)</span>
                                </div>
                                <div class="flex justify-between text-xs">
                                    <span class="text-zinc-500 font-medium">Total Price</span>
                                    <span class="text-zinc-900 font-black">{formatCurrency(totalPrice)}</span>
                                </div>
                            </div>

                            <!-- Payment Info -->
                            <div class="space-y-6 pt-6 border-t border-zinc-100">
                                <div class="p-6 bg-zinc-50 rounded-2xl border border-zinc-200 space-y-5 relative overflow-hidden">
                                    <div class="absolute top-0 right-0 p-3">
                                        <CreditCard size={32} class="text-zinc-200/50" />
                                    </div>
                                    <div class="space-y-1">
                                        <h5 class="text-[10px] font-black uppercase tracking-[0.2em] text-zinc-400">Transfer To</h5>
                                        <p class="text-xs font-bold text-zinc-700">{bankDetails.bank}</p>
                                    </div>
                                    <div class="space-y-1">
                                        <p class="text-2xl font-black text-zinc-900 tracking-tight">{bankDetails.accountNumber}</p>
                                        <p class="text-[10px] font-black text-zinc-400 uppercase italic">A/N: {bankDetails.accountHolder}</p>
                                    </div>
                                    <div class="pt-2 flex items-center justify-between">
                                        <div class="px-4 py-2 rounded-xl bg-primary text-white flex flex-col shadow-lg shadow-primary/20">
                                            <span class="text-[8px] font-black uppercase tracking-widest opacity-80">Down Payment (30%)</span>
                                            <span class="text-sm font-black tracking-tight">{formatCurrency(calculatedDP)}</span>
                                        </div>
                                    </div>
                                </div>
                                <p class="text-[10px] text-zinc-400 leading-relaxed font-medium italic text-center">
                                    *Note: Secure your gear with DP. The rest is paid on pickup.
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
