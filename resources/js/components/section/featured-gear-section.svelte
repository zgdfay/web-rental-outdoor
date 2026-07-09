<script>
    import { Star, Package, ArrowRight } from 'lucide-svelte';
    import { Link } from '@inertiajs/svelte';

    let { gears = [] } = $props();

    function formatCurrency(amount) {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0
        }).format(amount);
    }
</script>

<section class="py-24 px-6 bg-white dark:bg-zinc-950">
    <div class="max-w-7xl mx-auto">
        <div class="text-center mb-16 space-y-4">
            <h2 class="text-4xl font-bold tracking-tight text-zinc-900 dark:text-white">TOP-RATED RENTALS</h2>
            <div class="h-1 w-20 bg-primary mx-auto"></div>
            <p class="text-zinc-500 max-w-xl mx-auto">
                Discover our most trusted equipment, professionally maintained and ready for your next big adventure.
            </p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
            {#each gears as gear}
                <div class="group bg-white dark:bg-zinc-900 rounded-3xl overflow-hidden border border-zinc-100 dark:border-zinc-800 hover:border-primary/20 transition-all flex flex-col h-full {gear.stock === 0 ? 'opacity-60 grayscale' : 'shadow-xl shadow-zinc-200/50 dark:shadow-none'}">
                <div class="relative aspect-square overflow-hidden bg-zinc-50">
                        {#if gear.image_path}
                            <img 
                                src="/storage/{gear.image_path}" 
                                alt={gear.name} 
                                class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700"
                            />
                        {:else}
                            <div class="w-full h-full flex items-center justify-center text-zinc-200">
                                <Package size={64} />
                            </div>
                        {/if}
                        
                        <!-- Badges -->
                        <div class="absolute top-4 left-4">
                            <span class="px-3 py-1 bg-white/90 dark:bg-zinc-900/90 backdrop-blur-md rounded-full text-[10px] font-bold uppercase tracking-widest text-zinc-900 dark:text-white border border-zinc-200 dark:border-zinc-700">
                                {gear.category}
                            </span>
                        </div>
                    </div>

                    <div class="p-6 flex flex-col flex-1 gap-4">
                        <div class="space-y-2">
                            <div class="flex items-center gap-2 text-xs font-medium text-amber-500">
                                <Star size={14} fill="currentColor" />
                                <span>Featured</span>
                            </div>
                            <h3 class="font-bold text-lg text-zinc-900 dark:text-white leading-tight min-h-[3rem] line-clamp-2">
                                {gear.name}
                            </h3>
                        </div>

                        <div class="mt-auto flex items-end justify-between">
                            <div class="flex flex-col">
                                <span class="text-[10px] font-bold uppercase tracking-widest flex items-center gap-1.5 mb-1 {gear.stock > 0 ? 'text-zinc-400' : 'text-rose-500'}">
                                    Available: {gear.stock} units
                                </span>
                                <span class="text-xl font-black text-zinc-900 dark:text-white leading-none">{formatCurrency(gear.price_per_day)}<span class="text-[10px] font-medium text-zinc-400 ml-1">/day</span></span>
                            </div>
                             <Link
                                href={`/booking?id=${gear.id}&name=${encodeURIComponent(gear.name)}&price=${gear.price_per_day}&stock=${gear.stock}&image_path=${encodeURIComponent(gear.image_path || '')}`} 
                                disabled={gear.stock === 0}
                                class="inline-flex items-center justify-center px-5 py-3 bg-primary text-white font-semibold uppercase text-xs rounded-xl transition-all hover:bg-primary/90 {gear.stock === 0 ? 'bg-zinc-200 text-zinc-400 dark:bg-zinc-800 pointer-events-none' : ''}">
                                {gear.stock > 0 ? 'Rent Now' : 'Out of Stock'}
                            </Link>
                        </div>
                    </div>
                </div>
            {/each}
        </div>

        {#if gears.length === 0}
            <div class="p-20 text-center border-2 border-dashed border-zinc-100 rounded-[40px]">
                <p class="text-zinc-400 font-medium">No featured gear available at the moment.</p>
            </div>
        {/if}

        <div class="mt-16 text-center">
            <Link href="/catalog" class="inline-flex items-center gap-3 px-10 py-4 bg-zinc-900 text-white font-bold rounded-full hover:bg-zinc-800 transition-all uppercase tracking-widest text-sm cursor-pointer shadow-xl shadow-zinc-900/20">
                Explore Full Catalog
                <ArrowRight size={18} />
            </Link>
        </div>
    </div>
</section>
