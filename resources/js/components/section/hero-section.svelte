<script>
    import { ArrowRight, Play } from 'lucide-svelte';
</script>

<section class="relative h-screen w-full overflow-hidden flex items-center">
    <!-- Background Image -->
    <div class="absolute inset-0 z-0">
        <img 
            src="/assets/images/hero-banner.webp" 
            alt="Outdoor Adventure" 
            class="w-full h-full object-cover scale-105"
        />
        <div class="absolute inset-0 bg-gradient-to-r from-zinc-950/80 via-zinc-950/40 to-transparent"></div>
    </div>

    <div class="relative z-10 max-w-7xl mx-auto px-6 w-full pt-20">
        <div class="max-w-2xl space-y-8">

            <h1 class="text-6xl md:text-8xl font-bold text-white leading-[0.9] tracking-wide">
                LIFE IS <br/> BETTER <br/> <span class="text-primary">OUTSIDE</span>
            </h1>

            <p class="text-lg text-zinc-300 max-w-lg leading-relaxed font-light">
                Premium outdoor gear rentals for those who prefer the trail over the attic. Quality equipment, sustainably shared, delivered to your door.
            </p>

            <div class="flex flex-wrap items-center gap-6 pt-4">
                <button class="px-8 py-4 bg-primary hover:bg-primary/90 text-primary-foreground font-semibold rounded-full flex items-center gap-3 transition-all transform hover:scale-105 shadow-xl shadow-primary/40">
                    Rent Now <ArrowRight size={20} />
                </button>
                
                <button class="flex items-center gap-3 text-white font-medium group">
                    <div class="size-12 rounded-full border border-white/20 flex items-center justify-center group-hover:bg-white/10 transition-all">
                        <Play size={18} fill="white" />
                    </div>
                    <span>Watch Story</span>
                </button>
            </div>

            <!-- Stats -->
            <div class="grid grid-cols-3 gap-8 pt-12 border-t border-white/10 max-w-md">
                <div>
                    <div class="text-2xl font-bold text-white">500+</div>
                    <div class="text-xs text-zinc-400 uppercase tracking-widest">Premium Items</div>
                </div>
                <div>
                    <div class="text-2xl font-bold text-white">12k+</div>
                    <div class="text-xs text-zinc-400 uppercase tracking-widest">Adventures</div>
                </div>
                <div>
                    <div class="text-2xl font-bold text-white">4.9/5</div>
                    <div class="text-xs text-zinc-400 uppercase tracking-widest">Rating</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scroll Indicator -->
    <div class="absolute bottom-10 left-1/2 -translate-x-1/2 flex flex-col items-center gap-4 text-white/40">
        <span class="text-[10px] uppercase tracking-[0.3em] font-bold">Scroll</span>
        <div class="w-px h-12 bg-gradient-to-b from-white/40 to-transparent"></div>
    </div>
</section>
