<script>
    import { ShoppingBag, Search, User, Menu } from 'lucide-svelte';
    import { onMount } from 'svelte';
    import { fly } from 'svelte/transition';
    import { page } from '@inertiajs/svelte';

    let isScrolled = $state(false);

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
</script>

<nav 
    class="fixed top-0 left-0 right-0 z-50 transition-all duration-300 {isScrolled ? 'bg-white/80 dark:bg-zinc-900/80 backdrop-blur-md py-4 shadow-sm' : 'bg-transparent py-6'}"
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

        <!-- Desktop Actions -->
        <div class="hidden md:flex items-center gap-6">
            <a 
                href="/login" 
                class="flex items-center gap-2 px-5 py-2 rounded-full border transition-all {isScrolled ? 'border-zinc-200 text-zinc-900 hover:bg-zinc-50 dark:border-zinc-800 dark:text-white' : 'border-white/20 text-white hover:bg-white/10'}"
            >
                <User size={18} />
                <span class="text-sm font-medium">Account</span>
            </a>
        </div>

        <!-- Mobile Menu Toggle -->
        <button class="md:hidden {isScrolled ? 'text-zinc-900 dark:text-white' : 'text-white'}">
            <Menu size={24} />
        </button>
    </div>
</nav>
