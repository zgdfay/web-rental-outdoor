<script>
    import { ArrowUpRight } from 'lucide-svelte';

    const categories = [
        {
            title: 'Camping',
            count: '124 items',
            image: '/assets/images/camping-1.webp',
            size: 'md:col-span-2'
        },
        {
            title: 'Hiking',
            count: '86 items',
            image: '/assets/images/hiking-1.webp',
            size: 'md:col-span-1'
        },
        {
            title: 'Climbing',
            count: '42 items',
            image: '/assets/images/climbing-1.webp',
            size: 'md:col-span-1'
        },
        {
            title: 'Winter Sports',
            count: '58 items',
            image: '/assets/images/winter-sport-1.webp',
            size: 'md:col-span-2'
        }
    ];
</script>

<section class="py-24 px-6 bg-zinc-50 dark:bg-black">
    <div class="max-w-7xl mx-auto">
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-8 mb-16">
            <div class="space-y-4">
                <h2 class="text-4xl md:text-5xl font-bold tracking-tight text-zinc-900 dark:text-white leading-[1.1]">
                    GEAR FOR EVERY <br/> <span class="text-primary">JOURNEY</span>
                </h2>
                <p class="text-zinc-500 dark:text-zinc-400 max-w-md">
                    Whether you're scaling peaks or camping in the valley, we have the specialized equipment you need for a safe and memorable trip.
                </p>
            </div>
            <a href="/catalog" class="text-sm font-bold uppercase tracking-widest text-zinc-900 dark:text-white flex items-center gap-2 group border-b-2 border-primary pb-1 self-start md:self-auto transition-all hover:text-primary">
                Explore All Catalog <ArrowUpRight size={18} class="group-hover:translate-x-1 group-hover:-translate-y-1 transition-transform" />
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            {#each categories as category}
                <div class="relative group overflow-hidden rounded-3xl aspect-[4/3] {category.size}">
                    <img 
                        src={category.image} 
                        alt={category.title} 
                        class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110"
                        loading="lazy"
                    />
                    <div class="absolute inset-0 bg-gradient-to-t from-zinc-950/80 via-zinc-950/20 to-transparent"></div>
                    
                    <div class="absolute bottom-0 left-0 p-8 w-full flex justify-between items-end">
                        <div class="space-y-1">
                            <h3 class="text-2xl font-bold text-white">{category.title}</h3>
                            <p class="text-white/60 text-sm font-medium">{category.count}</p>
                        </div>
                        <button class="size-12 rounded-full bg-white/10 backdrop-blur-md border border-white/20 flex items-center justify-center text-white opacity-0 group-hover:opacity-100 transition-all transform translate-y-4 group-hover:translate-y-0">
                            <ArrowUpRight size={20} />
                        </button>
                    </div>

                    <a href="#" class="absolute inset-0"></a>
                </div>
            {/each}
        </div>
    </div>
</section>
