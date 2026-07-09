<script>
    import { Link } from '@inertiajs/svelte';
    import Navbar from '@/components/layout/navbar.svelte';
    import Footer from '@/components/layout/footer.svelte';
    import { Compass, Tent, Flame, Coffee, ArrowRight, Mountain, Trees } from 'lucide-svelte';
    import { onMount } from 'svelte';
    import { fade, fly } from 'svelte/transition';

    let isVisible = $state(false);
    onMount(() => {
        isVisible = true;
    });
</script>

<svelte:head>
    <title>Camping Stories - Outdoor Species</title>
</svelte:head>

<div class="bg-zinc-950 text-white selection:bg-primary selection:text-white overflow-hidden">
    <Navbar />

    <!-- Hero Section: The Night -->
    <section class="relative min-h-screen flex flex-col items-center justify-center pt-20 px-6">
        <div class="absolute inset-0 z-0">
            <img 
                src="/assets/images/camping-hero.png" 
                alt="Camping under stars" 
                class="w-full h-full object-cover brightness-[0.6]"
            />
            <div class="absolute inset-0 bg-gradient-to-b from-zinc-950/40 via-transparent to-zinc-950"></div>
        </div>

        <div class="relative z-10 max-w-4xl text-center space-y-8">
            {#if isVisible}

                
                <h1 in:fly={{ y: 50, duration: 1000, delay: 200 }} class="text-6xl md:text-8xl font-black leading-none">
                    BEYOND THE <br />
                    <span class="text-primary italic">STRETCHED SKY</span>
                </h1>
                
                <p in:fly={{ y: 50, duration: 1000, delay: 400 }} class="text-lg md:text-2xl text-zinc-300 max-w-2xl mx-auto font-light leading-relaxed">
                    It starts with the silence. A quiet so profound it rings in your ears, replaced only by the crackle of wood and the whispers of ancient pines.
                </p>

                <div in:fade={{ duration: 1000, delay: 800 }} class="pt-8">
                    <button class="group flex flex-col items-center gap-4 mx-auto cursor-pointer">
                        <span class="text-xs font-bold uppercase tracking-widest text-zinc-500 group-hover:text-primary transition-colors">Scroll to Wander</span>
                        <div class="w-px h-16 bg-gradient-to-b from-primary to-transparent animate-bounce"></div>
                    </button>
                </div>
            {/if}
        </div>
    </section>

    <!-- Story Section 1: The Escape -->
    <section class="py-32 px-6 relative">
        <div class="max-w-7xl mx-auto grid lg:grid-cols-2 gap-20 items-center">
            <div class="space-y-10 order-2 lg:order-1">
                <div class="size-16 rounded-2xl bg-zinc-900 flex items-center justify-center border border-zinc-800 shadow-2xl">
                    <Trees class="text-primary" size={32} />
                </div>
                <h2 class="text-5xl font-black tracking-tight leading-tight">
                    Where the city noise <br /> 
                    <span class="text-zinc-500">fades into memory.</span>
                </h2>
                <div class="space-y-6 text-zinc-400 text-lg leading-relaxed font-medium">
                    <p>
                        We left the asphalt at dawn, chasing the horizon until the signal bars vanished. Camping isn't just about sleeping outdoors; it's about reclaiming the territory of your own mind.
                    </p>
                    <p>
                        Every step deeper into the forest is a layer of routine falling away. Here, the only notifications you receive are the shift in the wind and the changing hue of the mountain peaks.
                    </p>
                </div>
                
                <div class="pt-6 grid grid-cols-2 gap-8 border-t border-zinc-900">
                    <div class="space-y-2">
                        <span class="text-primary font-black text-3xl">2.4k</span>
                        <p class="text-xs uppercase font-bold tracking-widest text-zinc-500">Elevation Reached</p>
                    </div>
                    <div class="space-y-2">
                        <span class="text-primary font-black text-3xl">0</span>
                        <p class="text-xs uppercase font-bold tracking-widest text-zinc-500">Distractions</p>
                    </div>
                </div>
            </div>

            <div class="relative order-1 lg:order-2 group">
                <div class="absolute -inset-4 bg-primary/10 rounded-[2.5rem] blur-2xl group-hover:bg-primary/20 transition-all"></div>
                <div class="relative overflow-hidden rounded-[2rem] border border-zinc-800 aspect-[4/5]">
                    <img 
                        src="https://images.unsplash.com/photo-1523987355523-c7b5b0dd90a7?q=80&w=2070&auto=format&fit=crop" 
                        alt="Forest walk" 
                        class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110"
                    />
                    <div class="absolute inset-0 bg-gradient-to-t from-zinc-950 via-transparent to-transparent"></div>
                    <div class="absolute bottom-8 left-8">
                        <p class="text-sm font-bold uppercase tracking-[0.2em] text-white">Path to Serenity</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Parallax-ish Break: The Flame -->
    <section class="h-[70vh] relative overflow-hidden flex items-center justify-center">
        <img 
            src="https://images.unsplash.com/photo-1517824806704-9040b037703b?q=80&w=2070&auto=format&fit=crop" 
            alt="Campfire close up" 
            class="absolute inset-0 w-full h-full object-cover opacity-60"
        />
        <div class="absolute inset-0 bg-zinc-950/60 backdrop-blur-[2px]"></div>
        <div class="relative z-10 text-center space-y-6">
            <Flame size={64} class="text-primary mx-auto animate-pulse" />
            <h2 class="text-4xl md:text-6xl font-black tracking-tight italic">
                "In every walk with nature, one receives <br /> far more than he seeks."
            </h2>
            <p class="text-zinc-400 font-bold uppercase tracking-widest">— John Muir</p>
        </div>
    </section>

    <!-- Story Section 2: The Morning -->
    <section class="py-32 px-6">
        <div class="max-w-7xl mx-auto grid lg:grid-cols-2 gap-20 items-center">
            <div class="relative group">
                <div class="absolute -inset-4 bg-primary/10 rounded-[2.5rem] blur-2xl group-hover:bg-primary/20 transition-all"></div>
                <div class="relative overflow-hidden rounded-[2rem] border border-zinc-800 aspect-square">
                    <img 
                        src="/assets/images/camping-morning.png" 
                        alt="Morning coffee" 
                        class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110"
                    />
                    <div class="absolute inset-0 bg-gradient-to-tr from-zinc-950/60 via-transparent to-transparent"></div>
                </div>
            </div>

            <div class="space-y-10">
                <div class="size-16 rounded-2xl bg-zinc-900 flex items-center justify-center border border-zinc-800 shadow-2xl">
                    <Coffee class="text-primary" size={32} />
                </div>
                <h2 class="text-5xl font-black tracking-tight leading-tight">
                    Morning Brew <br /> 
                    <span class="text-zinc-500">at World's End.</span>
                </h2>
                <div class="space-y-6 text-zinc-400 text-lg leading-relaxed font-medium">
                    <p>
                        There is no coffee quite like the one brewed over a stove at 6,000 feet. The steam dances with the morning mist, and for a few minutes, the whole world is just you and the heat between your hands.
                    </p>
                    <p>
                        This is why we go. Not for the hardship, but for the clarity. For the moment when the first ray of sun hits the tent fabric and you realize you have everything you need in a single backpack.
                    </p>
                </div>
                
                <div class="pt-4">
                    <Link 
                        href="/catalog" 
                        class="inline-flex items-center gap-3 px-8 py-4 bg-primary hover:bg-primary/90 text-white font-bold rounded-2xl transition-all shadow-xl shadow-primary/20 group"
                    >
                        Prepare Your Story
                        <ArrowRight size={20} class="transition-transform group-hover:translate-x-1" />
                    </Link>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-32 px-6 bg-zinc-900/50">
        <div class="max-w-5xl mx-auto text-center space-y-12">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-zinc-800 border border-zinc-700 text-zinc-400 text-xs font-bold uppercase tracking-widest">
                The Journey Begins
            </div>
            <h2 class="text-5xl md:text-7xl font-black tracking-tight">
                WRITE YOUR OWN <br />
                <span class="text-primary italic">ADVENTURE.</span>
            </h2>
            <p class="text-zinc-400 text-xl max-w-2xl mx-auto leading-relaxed">
                We provide the gear, you provide the courage. Explore our curated selection of professional camping equipment.
            </p>
            <div class="flex flex-wrap justify-center gap-4 pt-6">
                <Link href="/catalog?category=Tents" class="px-8 py-4 bg-zinc-800 hover:bg-zinc-700 border border-zinc-700 rounded-2xl font-bold transition-all flex items-center gap-2">
                    <Tent size={18} class="text-primary" />
                    Premium Tents
                </Link>
                <Link href="/catalog?category=Backpacks" class="px-8 py-4 bg-zinc-800 hover:bg-zinc-700 border border-zinc-700 rounded-2xl font-bold transition-all flex items-center gap-2">
                    <Compass size={18} class="text-primary" />
                    Rugged Packs
                </Link>
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
