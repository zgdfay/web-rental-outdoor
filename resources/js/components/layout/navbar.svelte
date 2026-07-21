<script>
    import { ShoppingBag, Search, User, Menu, X, Plus, Minus, ArrowRight, Package } from 'lucide-svelte';
    import { onMount } from 'svelte';
    import { fly, fade } from 'svelte/transition';
    import { page, Link } from '@inertiajs/svelte';
    import { cart } from '@/stores/cart.svelte.js';
    import * as Dialog from "@/components/ui/dialog/index.js";
    import { Button } from "@/components/ui/button/index.js";

    let isScrolled = $state(false);
    let isCartOpen = $state(false);

    const navLinks = [
        { name: 'Catalog', href: '/catalog' },
        { name: 'Camping', href: '/camping' },
        { name: 'Hiking', href: '/hiking' },
        { name: 'Climbing', href: '/climbing' },
        { name: 'FAQ', href: '/faq' },
    ];

    onMount(() => {
        const handleScroll = () => {
            isScrolled = window.scrollY > 20;
        };
        window.addEventListener('scroll', handleScroll);
        return () => window.removeEventListener('scroll', handleScroll);
    });

    function formatCurrency(amount) {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0
        }).format(amount || 0);
    }
</script>

<nav 
    class="fixed top-0 left-0 right-0 z-50 transition-all duration-300 {isScrolled ? 'bg-white/90 dark:bg-zinc-900/90 backdrop-blur-md py-4 shadow-sm' : 'bg-transparent py-6'}"
>
    <div class="max-w-7xl mx-auto px-6 flex items-center justify-between">
        <!-- Logo -->
        <a href="/" class="flex items-center gap-2 group">
            <div class="size-10 flex items-center justify-center rounded-lg group-hover:bg-primary/10 transition-colors">
                <img src='/assets/logo/logo-outdoor-species.png' alt='logo-outdoor-species'/>
            </div>
            <span class="text-xl font-bold tracking-tight {isScrolled ? 'text-zinc-900 dark:text-white' : 'text-white'}">
                OUTDOOR <span class="text-primary">SPECIES</span>
            </span>
        </a>

        <!-- Desktop Navigation -->
        <div class="hidden md:flex items-center gap-8">
            {#each navLinks as link}
                <a 
                    href={link.href}
                    class="text-sm font-bold transition-all relative py-1 {$page.url === link.href ? 'text-primary' : (isScrolled ? 'text-zinc-600 hover:text-primary dark:text-zinc-400 dark:hover:text-primary' : 'text-white/80 hover:text-white')}"
                >
                    {link.name}
                    {#if $page.url === link.href}
                        <div class="absolute -bottom-1 left-0 right-0 h-0.5 bg-primary rounded-full" in:fly={{ y: 5, duration: 300 }}></div>
                    {/if}
                </a>
            {/each}
        </div>

        <!-- Desktop & Mobile Actions -->
        <div class="flex items-center gap-4">
            <!-- Cart Button -->
            <button 
                type="button"
                onclick={() => isCartOpen = true}
                class="relative flex items-center gap-2 px-4 py-2 rounded-full border transition-all {isScrolled ? 'border-zinc-200 text-zinc-900 hover:bg-zinc-50 dark:border-zinc-800 dark:text-white' : 'border-white/20 text-white hover:bg-white/10'}"
            >
                <ShoppingBag size={18} />
                <span class="text-sm font-bold hidden sm:inline">Keranjang</span>
                {#if cart.totalItems > 0}
                    <span class="absolute -top-1.5 -right-1.5 size-6 bg-primary text-white text-[11px] font-black rounded-full flex items-center justify-center shadow-md shadow-primary/30 animate-pulse">
                        {cart.totalItems}
                    </span>
                {/if}
            </button>

            <a 
                href="/login" 
                class="hidden md:flex items-center gap-2 px-5 py-2 rounded-full border transition-all {isScrolled ? 'border-zinc-200 text-zinc-900 hover:bg-zinc-50 dark:border-zinc-800 dark:text-white' : 'border-white/20 text-white hover:bg-white/10'}"
            >
                <User size={18} />
                <span class="text-sm font-medium">Account</span>
            </a>

            <!-- Mobile Menu Toggle -->
            <button class="md:hidden {isScrolled ? 'text-zinc-900 dark:text-white' : 'text-white'} ml-1">
                <Menu size={24} />
            </button>
        </div>
    </div>
</nav>

<!-- Cart Modal / Sheet -->
<Dialog.Root bind:open={isCartOpen}>
    <Dialog.Content class="max-w-[480px] p-6 rounded-3xl overflow-hidden bg-white dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 shadow-2xl">
        <Dialog.Header>
            <Dialog.Title class="flex items-center justify-between text-xl font-black uppercase tracking-tight text-zinc-900 dark:text-white">
                <div class="flex items-center gap-2.5">
                    <ShoppingBag size={22} class="text-primary" />
                    <span>Keranjang Sewa</span>
                </div>
                {#if cart.totalItems > 0}
                    <span class="px-3 py-1 bg-primary/10 text-primary text-xs font-bold rounded-full">
                        {cart.totalItems} Unit
                    </span>
                {/if}
            </Dialog.Title>
            <Dialog.Description class="text-xs text-zinc-500">
                Pilih atau sesuaikan jumlah alat outdoor sebelum melanjutkan ke pemesanan.
            </Dialog.Description>
        </Dialog.Header>

        <div class="mt-6 max-h-[60vh] overflow-y-auto space-y-4 pr-1 divide-y divide-zinc-100 dark:divide-zinc-900">
            {#if cart.items.length === 0}
                <div class="py-16 text-center space-y-4">
                    <div class="size-16 rounded-full bg-zinc-100 dark:bg-zinc-900 flex items-center justify-center mx-auto text-zinc-400">
                        <ShoppingBag size={32} strokeWidth={1.5} />
                    </div>
                    <div class="space-y-1">
                        <p class="font-bold text-zinc-800 dark:text-white text-sm">Keranjang Anda masih kosong</p>
                        <p class="text-xs text-zinc-400">Jelajahi katalog dan tambahkan alat yang ingin disewa.</p>
                    </div>
                    <Button 
                        onclick={() => { isCartOpen = false; }} 
                        variant="outline" 
                        class="rounded-full text-xs font-bold px-6"
                    >
                        Tutup & Cari Alat
                    </Button>
                </div>
            {:else}
                {#each cart.items as item}
                    <div class="pt-4 first:pt-0 flex items-center gap-4 group">
                        <div class="size-16 rounded-2xl bg-zinc-100 dark:bg-zinc-900 flex items-center justify-center overflow-hidden border border-zinc-200 dark:border-zinc-800 shrink-0">
                            {#if item.image_path}
                                <img src="/storage/{item.image_path}" alt={item.name} class="w-full h-full object-cover" />
                            {:else}
                                <Package size={24} class="text-zinc-400" />
                            {/if}
                        </div>

                        <div class="flex-grow min-w-0 space-y-1">
                            <p class="text-xs font-black uppercase tracking-wider text-primary">{item.category}</p>
                            <h4 class="font-bold text-zinc-900 dark:text-white text-sm truncate">{item.name}</h4>
                            <p class="text-xs font-bold text-zinc-500">{formatCurrency(item.price_per_day)} <span class="text-[10px] font-normal">/hari</span></p>
                        </div>

                        <div class="flex items-center gap-2 shrink-0">
                            <button 
                                type="button"
                                onclick={() => cart.updateQuantity(item.id, item.quantity - 1)}
                                class="size-8 rounded-full bg-zinc-100 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 flex items-center justify-center hover:bg-zinc-200 transition-colors"
                            >
                                <Minus size={14} class="text-zinc-600 dark:text-zinc-400" />
                            </button>
                            <span class="w-6 text-center font-bold text-sm text-zinc-900 dark:text-white">{item.quantity}</span>
                            <button 
                                type="button"
                                onclick={() => cart.updateQuantity(item.id, item.quantity + 1)}
                                disabled={item.quantity >= (item.stock || 99)}
                                class="size-8 rounded-full bg-zinc-100 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 flex items-center justify-center hover:bg-zinc-200 transition-colors disabled:opacity-40"
                            >
                                <Plus size={14} class="text-zinc-600 dark:text-zinc-400" />
                            </button>
                            <button 
                                type="button"
                                onclick={() => cart.removeItem(item.id)}
                                class="size-8 rounded-full text-zinc-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-950/30 flex items-center justify-center transition-colors ml-1"
                            >
                                <X size={16} />
                            </button>
                        </div>
                    </div>
                {/each}
            {/if}
        </div>

        {#if cart.items.length > 0}
            <div class="mt-6 pt-6 border-t border-zinc-100 dark:border-zinc-900 space-y-4">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-zinc-500 uppercase tracking-wider">Total Tarif Per Hari</span>
                    <span class="text-xl font-black text-zinc-900 dark:text-white">{formatCurrency(cart.totalDailyRate)}</span>
                </div>

                <div class="flex gap-3 pt-2">
                    <button 
                        type="button"
                        onclick={() => cart.clearCart()}
                        class="px-4 py-3 rounded-2xl bg-zinc-100 dark:bg-zinc-900 text-zinc-600 dark:text-zinc-400 text-xs font-bold uppercase tracking-wider hover:bg-zinc-200 transition-all"
                    >
                        Kosongkan
                    </button>
                    <Link 
                        href="/booking" 
                        onclick={() => isCartOpen = false}
                        class="flex-grow px-6 py-3.5 bg-primary text-white font-black uppercase tracking-widest text-xs rounded-2xl hover:bg-primary/90 transition-all shadow-xl shadow-primary/20 flex items-center justify-center gap-2 group"
                    >
                        Checkout Sekarang
                        <ArrowRight size={16} class="transition-transform group-hover:translate-x-1" />
                    </Link>
                </div>
            </div>
        {/if}
    </Dialog.Content>
</Dialog.Root>
