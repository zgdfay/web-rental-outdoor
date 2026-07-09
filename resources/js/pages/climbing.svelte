<script>
    import { Link } from '@inertiajs/svelte';
    import Navbar from '@/components/layout/navbar.svelte';
    import Footer from '@/components/layout/footer.svelte';
    import { Anchor, ArrowRight, Activity, ShieldCheck, Target, Cloud, Zap } from 'lucide-svelte';
    import { onMount } from 'svelte';
    import { fade, fly } from 'svelte/transition';

    let isVisible = $state(false);
    onMount(() => {
        isVisible = true;
    });
</script>

<svelte:head>
    <title>Climbing Stories - Outdoor Species</title>
</svelte:head>

<div class="bg-zinc-950 text-white selection:bg-primary selection:text-white overflow-hidden font-sans">
    <Navbar />

    <!-- Hero Section: The Vertical -->
    <section class="relative min-h-screen flex flex-col items-center justify-center pt-20 px-6">
        <div class="absolute inset-0 z-0">
            <img 
                src="/assets/images/climbing-hero.png" 
                alt="Climber on rock face" 
                class="w-full h-full object-cover brightness-[0.7]"
            />
            <div class="absolute inset-0 bg-gradient-to-b from-zinc-950/40 via-transparent to-zinc-950"></div>
        </div>

        <div class="relative z-10 max-w-4xl text-center space-y-8">
            {#if isVisible}
               
                
                <h1 in:fly={{ y: 30, duration: 1000, delay: 200 }} class="text-7xl md:text-9xl font-black leading-none">
                    DEFIANCE OF <br />
                    <span class="text-primary italic">GRAVITY.</span>
                </h1>
                
                <p in:fly={{ y: 30, duration: 1000, delay: 400 }} class="text-xl md:text-3xl text-zinc-300 max-w-2xl mx-auto font-light leading-relaxed">
                    It's not about the strength of your arms, but the clarity of your focus. Every crimp, every smear, every breath is a calculated move toward the sky.
                </p>

                <div in:fade={{ duration: 1000, delay: 800 }} class="pt-10">
                    <button class="group flex flex-col items-center gap-4 mx-auto cursor-pointer">
                        <span class="text-xs font-bold uppercase tracking-widest text-zinc-500 group-hover:text-primary transition-colors">Start the Line</span>
                        <div class="w-px h-16 bg-gradient-to-b from-primary to-transparent animate-bounce"></div>
                    </button>
                </div>
            {/if}
        </div>
    </section>

    <!-- Story Section 1: The First Pitch -->
    <section class="py-32 px-6 relative">
        <div class="max-w-7xl mx-auto grid lg:grid-cols-2 gap-24 items-center">
            <div class="space-y-12 order-2 lg:order-1">
                <div class="size-16 rounded-2xl bg-zinc-900 flex items-center justify-center border border-zinc-800 shadow-2xl">
                    <Target class="text-primary" size={32} />
                </div>
                <h2 class="text-6xl font-black tracking-tight leading-tight">
                    Precision over <br /> 
                    <span class="text-zinc-600">brute force.</span>
                </h2>
                <div class="space-y-8 text-zinc-400 text-xl leading-relaxed">
                    <p>
                        The rock doesn't care about your ego. It only responds to technique and trust. On a vertical face, the world shrinks to the next four inches of granite.
                    </p>
                    <p>
                        We find a rhythm between the chalk dust and the clicking of carabiners. It's a high-stakes dance where your partner is your lifeline and your gear is your insurance against the void.
                    </p>
                </div>
                
                <div class="pt-8 flex items-center gap-12">
                    <div class="space-y-1">
                        <p class="text-4xl font-black text-white">5.12a</p>
                        <p class="text-xs uppercase font-bold tracking-widest text-zinc-500">Grade Mastered</p>
                    </div>
                    <div class="w-px h-12 bg-zinc-800"></div>
                    <div class="space-y-1">
                        <p class="text-4xl font-black text-white">850m</p>
                        <p class="text-xs uppercase font-bold tracking-widest text-zinc-500">Vertical Wall</p>
                    </div>
                </div>
            </div>

            <div class="relative order-1 lg:order-2">
                <div class="absolute -inset-10 bg-primary/5 rounded-full blur-[100px]"></div>
                <div class="relative overflow-hidden rounded-3xl border border-zinc-800 aspect-[4/5] group">
                    <img 
                        src="https://images.unsplash.com/photo-1522163182402-834f871fd851?q=80&w=2006&auto=format&fit=crop" 
                        alt="Climber's hands" 
                        class="w-full h-full object-cover transition-transform duration-1000 group-hover:scale-110"
                    />
                    <div class="absolute inset-0 bg-gradient-to-t from-zinc-950 via-transparent to-transparent opacity-60"></div>
                    <div class="absolute bottom-8 left-8">
                        <p class="text-sm font-bold uppercase tracking-[0.2em] text-white italic">The Grip of Reality</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Mid Section: The Suspension -->
    <section class="h-[60vh] relative overflow-hidden flex items-center justify-center group">
        <img 
            src="https://images.unsplash.com/photo-1544084944-15269ec7b5a0?q=80&w=2070&auto=format&fit=crop" 
            alt="Hanging belay view" 
            class="absolute inset-0 w-full h-full object-cover transition-transform duration-[10s] group-hover:scale-125"
        />
        <div class="absolute inset-0 bg-zinc-950/70 backdrop-blur-sm"></div>
        <div class="relative z-10 text-center space-y-8 px-6">
            <Anchor size={48} class="text-primary mx-auto opacity-50" />
            <blockquote class="text-3xl md:text-5xl font-medium tracking-tight leading-tight italic text-zinc-200 max-w-4xl">
                "Climbing is not a battle with the elements, nor as the victory over the rock. It is a battle with oneself."
            </blockquote>
            <span class="block text-xs font-bold uppercase tracking-[0.4em] text-zinc-500">— Walter Bonatti</span>
        </div>
    </section>

    <!-- Story Section 2: The Gear -->
    <section class="py-32 px-6">
        <div class="max-w-7xl mx-auto grid lg:grid-cols-2 gap-24 items-center">
            <div class="relative group">
                <div class="absolute -inset-10 bg-primary/5 rounded-full blur-[100px]"></div>
                <div class="relative overflow-hidden rounded-3xl border border-zinc-800 aspect-square">
                    <img 
                        src="https://images.unsplash.com/photo-1533560223595-364239b03940?q=80&w=2070&auto=format&fit=crop" 
                        alt="Climbing gear" 
                        class="w-full h-full object-cover transition-transform duration-1000 group-hover:scale-105"
                    />
                    <div class="absolute inset-0 bg-zinc-950/10"></div>
                </div>
            </div>

            <div class="space-y-12">
                <div class="size-16 rounded-2xl bg-zinc-900 flex items-center justify-center border border-zinc-800 shadow-2xl">
                    <ShieldCheck class="text-primary" size={32} />
                </div>
                <h2 class="text-6xl font-black tracking-tight leading-tight">
                    Trust your gear, <br /> 
                    <span class="text-zinc-600">trust yourself.</span>
                </h2>
                <div class="space-y-8 text-zinc-400 text-xl leading-relaxed">
                    <p>
                        When you're hundreds of feet off the deck, your equipment is more than just metal and nylon. It's a promise. A promise that you can push your limits safely.
                    </p>
                    <p>
                        From the friction of your rubber to the dynamic stretch of your rope, every piece of kit from Outdoor Species is selected to ensure that your only concern is the next move.
                    </p>
                </div>
                
                <div class="pt-6">
                    <Link 
                        href="/catalog" 
                        class="inline-flex items-center gap-3 px-10 py-5 bg-primary hover:bg-primary/90 text-white font-black uppercase tracking-widest text-xs rounded-2xl transition-all shadow-2xl shadow-primary/30 group"
                    >
                        Prepare the Rack
                        <ArrowRight size={18} class="transition-transform group-hover:translate-x-1" />
                    </Link>
                </div>
            </div>
        </div>
    </section>

    <!-- Metrics Section: The Grit -->
    <section class="py-32 px-6 border-t border-zinc-900 bg-zinc-950 relative overflow-hidden">
        <div class="absolute bottom-0 left-0 size-[500px] bg-primary/5 blur-[120px] translate-y-1/2 -translate-x-1/2 rounded-full"></div>
        <div class="max-w-7xl mx-auto grid md:grid-cols-3 gap-12 relative z-10">
            <div class="p-10 rounded-3xl bg-zinc-900/40 border border-zinc-800 hover:border-primary/30 transition-colors group">
                <div class="size-12 rounded-xl bg-primary/10 flex items-center justify-center mb-6 group-hover:bg-primary transition-colors">
                    <Cloud class="text-primary group-hover:text-white" size={24} />
                </div>
                <h3 class="text-xl font-bold mb-4">Vertical Reach</h3>
                <p class="text-zinc-500 leading-relaxed">Access the most remote walls and conquer heights that few have ever seen.</p>
            </div>
            <div class="p-10 rounded-3xl bg-zinc-900/40 border border-zinc-800 hover:border-primary/30 transition-colors group">
                <div class="size-12 rounded-xl bg-primary/10 flex items-center justify-center mb-6 group-hover:bg-primary transition-colors">
                    <ShieldCheck class="text-primary group-hover:text-white" size={24} />
                </div>
                <h3 class="text-xl font-bold mb-4">Safety First</h3>
                <p class="text-zinc-500 leading-relaxed">Top-tier protection gear tested to the highest international standards.</p>
            </div>
            <div class="p-10 rounded-3xl bg-zinc-900/40 border border-zinc-800 hover:border-primary/30 transition-colors group">
                <div class="size-12 rounded-xl bg-primary/10 flex items-center justify-center mb-6 group-hover:bg-primary transition-colors">
                    <Zap class="text-primary group-hover:text-white" size={24} />
                </div>
                <h3 class="text-xl font-bold mb-4">Pure Power</h3>
                <p class="text-zinc-500 leading-relaxed">Lightweight and durable equipment designed for maximum performance on the rock.</p>
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
