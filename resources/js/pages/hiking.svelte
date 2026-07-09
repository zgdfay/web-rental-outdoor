<script>
    import { Link } from '@inertiajs/svelte';
    import Navbar from '@/components/layout/navbar.svelte';
    import Footer from '@/components/layout/footer.svelte';
    import { Compass, Mountain, ArrowRight, Wind, Map, Heart, Zap } from 'lucide-svelte';
    import { onMount } from 'svelte';
    import { fade, fly } from 'svelte/transition';

    let isVisible = $state(false);
    onMount(() => {
        isVisible = true;
    });
</script>

<svelte:head>
    <title>Hiking Stories - Outdoor Species</title>
</svelte:head>

<div class="bg-zinc-950 text-white selection:bg-primary selection:text-white overflow-hidden font-sans">
    <Navbar />

    <!-- Hero Section: The Summit -->
    <section class="relative min-h-screen flex flex-col items-center justify-center pt-20 px-6">
        <div class="absolute inset-0 z-0">
            <img 
                src="/assets/images/hiking-hero.png" 
                alt="Hiker at summit" 
                class="w-full h-full object-cover brightness-[0.7]"
            />
            <div class="absolute inset-0 bg-gradient-to-b from-zinc-950/30 via-transparent to-zinc-950"></div>
        </div>

        <div class="relative z-10 max-w-4xl text-center space-y-8">
            {#if isVisible}
                    
                    <h1 in:fly={{ y: 30, duration: 1000, delay: 200 }} class="text-7xl md:text-9xl font-black leading-none">
                    ABOVE THE <br />
                    <span class="text-primary italic">THIN AIR</span>
                </h1>
                
                <p in:fly={{ y: 30, duration: 1000, delay: 400 }} class="text-xl md:text-3xl text-zinc-300 max-w-2xl mx-auto font-light leading-relaxed">
                    The peak is just a point. The journey is the thin air, the burning lungs, and the rhythmic sound of boots on stone.
                </p>

                <div in:fade={{ duration: 1000, delay: 800 }} class="pt-10">
                    <button class="group flex flex-col items-center gap-4 mx-auto cursor-pointer">
                        <span class="text-xs font-bold uppercase tracking-widest text-zinc-500 group-hover:text-primary transition-colors">Start Climbing</span>
                        <div class="w-px h-16 bg-gradient-to-b from-primary to-transparent animate-bounce"></div>
                    </button>
                </div>
            {/if}
        </div>
    </section>

    <!-- Story Section 1: The First Step -->
    <section class="py-32 px-6 relative">
        <div class="max-w-7xl mx-auto grid lg:grid-cols-2 gap-24 items-center">
            <div class="space-y-12 order-2 lg:order-1">
                <div class="size-16 rounded-2xl bg-zinc-900 flex items-center justify-center border border-zinc-800 shadow-2xl">
                    <Map class="text-primary" size={32} />
                </div>
                <h2 class="text-6xl font-black tracking-tight leading-tight">
                    Every ridge is <br /> 
                    <span class="text-zinc-600">a new perspective.</span>
                </h2>
                <div class="space-y-8 text-zinc-400 text-xl leading-relaxed">
                    <p>
                        We started while the world was still gray, headlamps cutting through the morning mist. In the mountains, time is measured not in hours, but in vertical gain and the slow reveal of the landscape.
                    </p>
                    <p>
                        Hiking is the art of endurance. It's the conversation you have with yourself when the trail gets steep and your pack feels heavier. It's where you find what you're truly made of.
                    </p>
                </div>
                
                <div class="pt-8 flex items-center gap-12">
                    <div class="space-y-1">
                        <p class="text-4xl font-black text-white">12.4</p>
                        <p class="text-xs uppercase font-bold tracking-widest text-zinc-500">Miles Conquered</p>
                    </div>
                    <div class="w-px h-12 bg-zinc-800"></div>
                    <div class="space-y-1">
                        <p class="text-4xl font-black text-white">1,500m</p>
                        <p class="text-xs uppercase font-bold tracking-widest text-zinc-500">Elevation Gain</p>
                    </div>
                </div>
            </div>

            <div class="relative order-1 lg:order-2">
                <div class="absolute -inset-10 bg-primary/5 rounded-full blur-[100px]"></div>
                <div class="relative overflow-hidden rounded-3xl border border-zinc-800 aspect-[4/5] group">
                    <img 
                        src="/assets/images/hiking-detail.png" 
                        alt="Hiking boot close up" 
                        class="w-full h-full object-cover transition-transform duration-1000 group-hover:scale-110"
                    />
                    <div class="absolute inset-0 bg-gradient-to-t from-zinc-950 via-transparent to-transparent opacity-60"></div>
                    <div class="absolute top-8 right-8">
                        <div class="px-4 py-2 rounded-full bg-black/40 backdrop-blur-md border border-white/10 text-[10px] font-bold uppercase tracking-widest text-white">
                            Gear: Salomon Quest 4
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Mid Section: The Feeling -->
    <section class="py-40 bg-zinc-900/30 relative overflow-hidden">
        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-full h-px bg-gradient-to-r from-transparent via-zinc-800 to-transparent"></div>
        <div class="max-w-4xl mx-auto text-center px-6 space-y-12">
            <Wind size={48} class="text-primary mx-auto opacity-50" />
            <blockquote class="text-4xl md:text-5xl font-medium tracking-tight leading-tight italic text-zinc-200">
                "The mountains are calling and I must go, and I will work on while I can, studying incessantly."
            </blockquote>
            <div class="flex items-center justify-center gap-4">
                <div class="w-8 h-px bg-zinc-700"></div>
                <span class="text-xs font-bold uppercase tracking-[0.4em] text-zinc-500">John Muir, 1873</span>
                <div class="w-8 h-px bg-zinc-700"></div>
            </div>
        </div>
        <div class="absolute bottom-0 left-1/2 -translate-x-1/2 w-full h-px bg-gradient-to-r from-transparent via-zinc-800 to-transparent"></div>
    </section>

    <!-- Story Section 2: The View -->
    <section class="py-32 px-6">
        <div class="max-w-7xl mx-auto grid lg:grid-cols-2 gap-24 items-center">
            <div class="relative group order-2 lg:order-1">
                <div class="absolute -inset-10 bg-primary/5 rounded-full blur-[100px]"></div>
                <div class="relative overflow-hidden rounded-3xl border border-zinc-800 aspect-square">
                    <img 
                        src="https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?q=80&w=2070&auto=format&fit=crop" 
                        alt="Mountain range" 
                        class="w-full h-full object-cover transition-transform duration-1000 group-hover:scale-105"
                    />
                    <div class="absolute inset-0 bg-zinc-950/20 group-hover:bg-transparent transition-colors"></div>
                </div>
            </div>

            <div class="space-y-12 order-1 lg:order-2">
                <div class="size-16 rounded-2xl bg-zinc-900 flex items-center justify-center border border-zinc-800 shadow-2xl">
                    <Mountain class="text-primary" size={32} />
                </div>
                <h2 class="text-6xl font-black tracking-tight leading-tight">
                    Above the clouds, <br /> 
                    <span class="text-zinc-600">below the sky.</span>
                </h2>
                <div class="space-y-8 text-zinc-400 text-xl leading-relaxed">
                    <p>
                        There's a specific moment on a hike—usually when you've reached the highest saddle—where the world opens up. The valley below looks like a different planet, and the air tastes like ice and history.
                    </p>
                    <p>
                        It's not about conquering the mountain. You don't conquer mountains. You just survive them, and in return, they give you a moment of absolute clarity that nothing in the city can provide.
                    </p>
                </div>
                
                <div class="pt-6">
                    <Link 
                        href="/catalog" 
                        class="inline-flex items-center gap-3 px-10 py-5 bg-primary hover:bg-primary/90 text-white font-black uppercase tracking-widest text-xs rounded-2xl transition-all shadow-2xl shadow-primary/30 group"
                    >
                        Equip Your Journey
                        <ArrowRight size={18} class="transition-transform group-hover:translate-x-1" />
                    </Link>
                </div>
            </div>
        </div>
    </section>

    <!-- Metrics Section: The Grit -->
    <section class="py-32 px-6 border-t border-zinc-900 bg-zinc-950 relative overflow-hidden">
        <div class="absolute top-0 right-0 size-[500px] bg-primary/5 blur-[120px] -translate-y-1/2 translate-x-1/2 rounded-full"></div>
        <div class="max-w-7xl mx-auto grid md:grid-cols-3 gap-12 relative z-10">
            <div class="p-10 rounded-3xl bg-zinc-900/40 border border-zinc-800 hover:border-primary/30 transition-colors group">
                <div class="size-12 rounded-xl bg-primary/10 flex items-center justify-center mb-6 group-hover:bg-primary transition-colors">
                    <Heart class="text-primary group-hover:text-white" size={24} />
                </div>
                <h3 class="text-xl font-bold mb-4">Unyielding Spirit</h3>
                <p class="text-zinc-500 leading-relaxed">The mental toughness required to push past the fatigue and reach the summit.</p>
            </div>
            <div class="p-10 rounded-3xl bg-zinc-900/40 border border-zinc-800 hover:border-primary/30 transition-colors group">
                <div class="size-12 rounded-xl bg-primary/10 flex items-center justify-center mb-6 group-hover:bg-primary transition-colors">
                    <Wind class="text-primary group-hover:text-white" size={24} />
                </div>
                <h3 class="text-xl font-bold mb-4">Altitude Prep</h3>
                <p class="text-zinc-500 leading-relaxed">Adapting to the thin air and unpredictable mountain weather with the right gear.</p>
            </div>
            <div class="p-10 rounded-3xl bg-zinc-900/40 border border-zinc-800 hover:border-primary/30 transition-colors group">
                <div class="size-12 rounded-xl bg-primary/10 flex items-center justify-center mb-6 group-hover:bg-primary transition-colors">
                    <Compass class="text-primary group-hover:text-white" size={24} />
                </div>
                <h3 class="text-xl font-bold mb-4">Pathfinding</h3>
                <p class="text-zinc-500 leading-relaxed">Navigating through the unknown and finding beauty in the most rugged terrain.</p>
            </div>
        </div>
    </section>

    <Footer />
</div>

<style>
    :global(html) {
        scroll-behavior: smooth;
    }
</style>
