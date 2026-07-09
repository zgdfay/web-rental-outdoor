<script>
    import Navbar from '@/components/layout/navbar.svelte';
    import Footer from '@/components/layout/footer.svelte';
    import Hero from '@/components/section/hero-section.svelte';
    import Categories from '@/components/section/categories-section.svelte';
    import FeaturedGear from '@/components/section/featured-gear-section.svelte';
    import WhyUs from '@/components/section/why-us-section.svelte';

    let { featured_gears = [] } = $props();
</script>

<svelte:head>
    <title>Outdoor Species - Premium Outdoor Equipment</title>
    <meta name="description" content="Rent high-quality outdoor gear for your next adventure. Camping, hiking, climbing, and more." />
</svelte:head>

<div class="min-h-screen bg-white dark:bg-black selection:bg-emerald-500 selection:text-white">
    <Navbar />

    <main>
        <Hero />
        
        <div class="relative z-10">
            <Categories />
            <FeaturedGear gears={featured_gears} />
            <WhyUs />
            
            <!-- Newsletter / CTA Section -->
            <section class="py-24 px-6 bg-white dark:bg-black border-t border-zinc-100 dark:border-zinc-900 overflow-hidden relative">
                <div class="max-w-4xl mx-auto text-center space-y-8 relative z-10">
                    <h2 class="text-4xl md:text-5xl font-bold tracking-tight text-zinc-900 dark:text-white">
                        STAY IN THE <span class="text-primary">LOOP</span>
                    </h2>
                    <p class="text-zinc-500 dark:text-zinc-400 max-w-lg mx-auto leading-relaxed">
                        Join our community of outdoor enthusiasts. Get exclusive gear guides, adventure stories, and early access to new rental arrivals.
                    </p>
                    
                    <form class="flex flex-col sm:flex-row gap-4 max-w-md mx-auto">
                        <input 
                            type="email" 
                            placeholder="Enter your email" 
                            class="flex-1 px-6 py-4 rounded-full bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 focus:outline-none focus:ring-2 focus:ring-emerald-500/50 transition-all dark:text-white"
                        />
                        <button class="px-8 py-4 bg-primary hover:bg-primary/90 text-primary-foreground font-bold rounded-full transition-all whitespace-nowrap">
                            Subscribe
                        </button>
                    </form>
                    
                    <p class="text-[10px] text-zinc-400 uppercase tracking-[0.2em] font-medium">
                        NO SPAM. JUST PURE ADVENTURE.
                    </p>
                </div>

                <!-- Decorative blur -->
                <div class="absolute -bottom-20 -left-20 size-80 bg-emerald-500/10 blur-[120px] rounded-full"></div>
                <div class="absolute -top-20 -right-20 size-80 bg-emerald-400/5 blur-[120px] rounded-full"></div>
            </section>
        </div>
    </main>

    <Footer />
</div>
