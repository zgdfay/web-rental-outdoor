<script>
    import Navbar from '@/components/layout/navbar.svelte';
    import Footer from '@/components/layout/footer.svelte';
    import { Search, Filter, Star, SlidersHorizontal, ChevronDown, X, Package } from 'lucide-svelte';
    import { Link } from '@inertiajs/svelte';


    // Shadcn Components
    import { Input } from "@/components/ui/input";
    import { Checkbox } from "@/components/ui/checkbox";
    import { Slider } from "@/components/ui/slider";
    import { Badge } from "@/components/ui/badge";
    import * as Select from "@/components/ui/select/index.js";
    import { Button } from "@/components/ui/button/index.js";

    // Mockup data for catalog
    let { gears = [] } = $props();

    const categories = [
        { name: 'Hiking', count: gears.filter(g => g.category === 'Hiking').length },
        { name: 'Camping', count: gears.filter(g => g.category === 'Camping').length },
        { name: 'Climbing', count: gears.filter(g => g.category === 'Climbing').length },
        { name: 'Accessories', count: gears.filter(g => g.category === 'Accessories').length },
    ];

    const allTags = ['Popular', 'New', 'Top Rated', 'Lightweight', 'Essential'];

    // State (Svelte 5 Runes)
    let searchQuery = $state('');
    let selectedCategories = $state([]);
    let priceRange = $state([500000]); // Max price in IDR
    let selectedTags = $state([]);
    let sortBy = $state('Recommended');

    function formatCurrency(amount) {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0
        }).format(amount);
    }

    // Reactive Filtering (Svelte 5 Derived)
    const filteredProducts = $derived.by(() => {
        let filtered = gears.filter(product => {
            // Search
            if (searchQuery && !product.name.toLowerCase().includes(searchQuery.toLowerCase())) return false;
            
            // Categories
            if (selectedCategories.length > 0 && !selectedCategories.includes(product.category)) return false;

            // Price
            if (product.price_per_day > priceRange[0]) return false;

            return true;
        });

        // Sorting
        return filtered.sort((a, b) => {
            if (sortBy === 'Price: Low to High') return a.price_per_day - b.price_per_day;
            if (sortBy === 'Price: High to Low') return b.price_per_day - a.price_per_day;
            return 0;
        });
    });

    // Toggle Handlers
    function toggleCategory(cat) {
        if (selectedCategories.includes(cat)) {
            selectedCategories = selectedCategories.filter(c => c !== cat);
        } else {
            selectedCategories = [...selectedCategories, cat];
        }
    }

    function toggleTag(tag) {
        if (selectedTags.includes(tag)) {
            selectedTags = selectedTags.filter(t => t !== tag);
        } else {
            selectedTags = [...selectedTags, tag];
        }
    }

    function clearFilters() {
        selectedCategories = [];
        selectedTags = [];
        priceRange = [500000];
        searchQuery = '';
        sortBy = 'Recommended';
    }

    const hasFilters = $derived(selectedCategories.length > 0 || selectedTags.length > 0 || priceRange[0] < 100 || searchQuery);
</script>

<svelte:head>
    <title>Catalog - Outdoor Species</title>
</svelte:head>

<div class="min-h-screen bg-white dark:bg-black selection:bg-primary/20 selection:text-primary">
    <div class="relative bg-zinc-950 overflow-hidden">
        <!-- Hero Background Image -->
        <div class="absolute inset-0 z-0">
            <img 
                src="/assets/images/bg-run.jpg" 
                alt="Gear Catalog Hero" 
                class="w-full h-full object-cover opacity-60 scale-105" 
            />
        </div>

        <div class="relative z-20">
            <Navbar />
            
            <div class="max-w-7xl mx-auto px-6 pt-40 pb-24 md:pt-48 md:pb-32">
                <div class="max-w-2xl">
                    <h1 class="text-5xl md:text-7xl font-black tracking-tighter text-white mb-6 leading-[0.9]">
                        GEAR <span class="text-primary">CATALOG</span>
                    </h1>
                    <p class="text-zinc-300 text-lg md:text-xl max-w-xl leading-relaxed font-medium">
                        Explore our curated selection of high-performance outdoor equipment. Engineered for durability, designed for adventure.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <main class="max-w-7xl mx-auto px-6 py-12">
        <div class="flex flex-col lg:flex-row gap-8 items-start">
            
            <!-- Sidebar Filters -->
            <aside class="w-full lg:w-64 shrink-0 space-y-8">
                <!-- Mobile Filter Button -->
                <button class="lg:hidden w-full flex items-center justify-between px-4 py-3 bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl text-zinc-900 dark:text-white font-medium">
                    <div class="flex items-center gap-2">
                        <SlidersHorizontal size={18} />
                        Filters
                    </div>
                    <ChevronDown size={18} />
                </button>

                <div class="hidden lg:block space-y-8">
                    <!-- Categories -->
                    <div>
                        <h3 class="text-lg font-bold text-zinc-900 dark:text-white mb-4 border-b border-zinc-100 dark:border-zinc-900 pb-2">Categories</h3>
                        <div class="space-y-3 mt-4">
                            {#each categories as category}
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <Checkbox 
                                            id={`cat-${category.name}`} 
                                            checked={selectedCategories.includes(category.name)}
                                            onCheckedChange={() => toggleCategory(category.name)}
                                        />
                                        <label for={`cat-${category.name}`} class="text-sm font-medium text-zinc-600 dark:text-zinc-400 cursor-pointer select-none">
                                            {category.name}
                                        </label>
                                    </div>
                                    <Badge variant="outline" class="text-xs text-zinc-400 bg-zinc-100 dark:bg-zinc-900 border-none">{category.count}</Badge>
                                </div>
                            {/each}
                        </div>
                    </div>

                    <!-- Price Range -->
                    <div>
                        <h3 class="text-lg font-bold text-zinc-900 dark:text-white mb-4 border-b border-zinc-100 dark:border-zinc-900 pb-2">Price up to</h3>
                        <div class="space-y-6 mt-6">
                            <Slider bind:value={priceRange} max={500000} step={1000}  />
                            <div class="flex items-center justify-between text-[10px] font-black uppercase tracking-widest text-primary">
                                <span>Rp 0</span>
                                <span>{formatCurrency(priceRange[0])}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Tags -->
                    <!-- <div>
                        <h3 class="text-lg font-bold text-zinc-900 dark:text-white mb-4 border-b border-zinc-100 dark:border-zinc-900 pb-2">Tags</h3>
                        <div class="flex flex-wrap gap-2 mt-4">
                            {#each allTags as tag}
                                <Badge 
                                    variant={selectedTags.includes(tag) ? "default" : "outline"}
                                    class="cursor-pointer"
                                    onclick={() => toggleTag(tag)}
                                >
                                    {tag}
                                </Badge>
                            {/each}
                        </div>
                    </div> -->
                </div>
            </aside>

            <!-- Main Content Area -->
            <div class="flex-1 w-full space-y-8">
                <!-- Search and Toolbar -->
                <div class="flex flex-col sm:flex-row gap-4 items-center justify-between">
                    <div class="flex items-center gap-3 w-full max-w-xl">
                        <div class="relative flex-1 max-w-md">
                            <Search size={18} class="absolute left-4 top-1/2 -translate-y-1/2 text-zinc-400 z-10" />
                            <Input 
                                bind:value={searchQuery}
                                type="text" 
                                placeholder="Search gear..." 
                                class="w-full pl-11 rounded-full h-12 bg-zinc-50 dark:bg-zinc-900 border-zinc-200 dark:border-zinc-800 focus:ring-primary/20 transition-all"
                            />
                        </div>
                        
                        {#if hasFilters}
                            <Button 
                                variant="ghost" 
                                onclick={clearFilters}
                                class="text-zinc-500 hover:text-primary hover:bg-primary/10 rounded-full h-12 px-6 font-bold transition-all shrink-0"
                            >
                                Clear All
                            </Button>
                        {/if}
                    </div>
                    
                    <div class="flex items-center gap-2 shrink-0 self-start sm:self-auto w-full sm:w-auto">
                        <span class="text-sm font-medium text-zinc-500">Sort by:</span>
                        <Select.Root bind:value={sortBy} type="single">
                            <Select.Trigger class="w-[180px] h-12 px-5 bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-full font-bold focus:ring-primary/20 transition-all">
                                {sortBy || "Sort By"}
                            </Select.Trigger>
                            <Select.Content>
                                <Select.Item value="Recommended">Recommended</Select.Item>
                                <Select.Item value="Price: Low to High">Price: Low to High</Select.Item>
                                <Select.Item value="Price: High to Low">Price: High to Low</Select.Item>
                                <Select.Item value="Highest Rated">Highest Rated</Select.Item>
                            </Select.Content>
                        </Select.Root>
                    </div>
                </div>

                <!-- Active Filters -->
                {#if selectedCategories.length > 0 || selectedTags.length > 0 || priceRange[0] < 100 || searchQuery}
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="text-xs font-bold text-zinc-400 uppercase tracking-widest mr-2">Locating:</span>
                        
                        {#each selectedCategories as cat}
                            <Badge variant="secondary" class="font-bold flex items-center gap-1 cursor-pointer" onclick={() => toggleCategory(cat)}>
                                {cat} <X size={12} class="ml-1 opacity-70" />
                            </Badge>
                        {/each}

                        {#each selectedTags as tag}
                            <Badge variant="secondary" class="font-bold flex items-center gap-1 cursor-pointer" onclick={() => toggleTag(tag)}>
                                {tag} <X size={12} class="ml-1 opacity-70" />
                            </Badge>
                        {/each}

                        {#if priceRange[0] < 100}
                            <Badge variant="secondary" class="font-bold cursor-pointer" onclick={() => priceRange = [100]}>
                                Under Rp {priceRange[0]}k <X size={12} class="ml-1 opacity-70 inline" />
                            </Badge>
                        {/if}


                    </div>
                {/if}

                <!-- Products Grid -->
                {#if filteredProducts.length === 0}
                    <div class="text-center py-24 bg-zinc-50 dark:bg-zinc-900/20 rounded-3xl border border-zinc-100 dark:border-zinc-800">
                        <Search size={40} class="mx-auto text-zinc-300 mb-4" />
                        <h3 class="text-lg font-bold text-zinc-900 dark:text-white">No gear found</h3>
                        <p class="text-zinc-500 mt-2">Try adjusting your filters or search query.</p>
                        <button onclick={clearFilters} class="mt-6 font-bold text-primary hover:underline">Clear all filters</button>
                    </div>
                {:else}
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                        {#each filteredProducts as product}
                            <div class="group flex flex-col h-full bg-zinc-50/50 dark:bg-zinc-900/30 rounded-3xl border border-zinc-100 dark:border-zinc-800 overflow-hidden transition-all hover:shadow-lg cursor-pointer {product.stock === 0 ? 'opacity-60 grayscale scale-[0.98]' : ''}">
                                <div class="relative aspect-square overflow-hidden bg-zinc-100/50">
                                    {#if product.image_path}
                                        <img 
                                            src="/storage/{product.image_path}" 
                                            alt={product.name} 
                                            class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700"
                                        />
                                    {:else}
                                        <div class="w-full h-full flex items-center justify-center text-zinc-300">
                                            <Package size={48} />
                                        </div>
                                    {/if}
                                    
                                    {#if product.is_featured}
                                        <div class="absolute top-4 left-4">
                                            <Badge variant="default" class="rounded-full shadow-lg border border-white/20 bg-amber-500 hover:bg-amber-600">
                                                Featured
                                            </Badge>
                                        </div>
                                    {/if}

                                    {#if product.stock === 0}
                                        <div class="absolute inset-0 bg-black/40 flex items-center justify-center backdrop-blur-[2px]">
                                            <span class="px-6 py-2 bg-white text-black font-black uppercase tracking-widest text-xs rounded-full shadow-2xl">Sold Out</span>
                                        </div>
                                    {/if}
                                </div>

                                <div class="p-6 flex flex-col flex-1 gap-4">
                                    <div class="space-y-2">
                                        <div class="flex items-center gap-2 text-xs font-medium text-zinc-400">
                                            <span>{product.category}</span>
                                        </div>
                                        <h3 class="font-bold text-lg text-zinc-900 dark:text-white leading-tight min-h-[3rem] line-clamp-2">
                                            {product.name}
                                        </h3>
                                    </div>

                                    <div class="mt-auto flex items-end justify-between">
                                        <div class="flex flex-col">
                                            <span class="text-[10px] font-bold uppercase tracking-widest flex items-center gap-1.5 mb-1 {product.stock > 0 ? 'text-zinc-400' : 'text-red-500'}">
                                                Available: {product.stock} units
                                            </span>
                                            <span class="text-xl font-black text-zinc-900 dark:text-white">{formatCurrency(product.price_per_day)}</span>
                                        </div>
                                        <Link
                                            href={`/booking?id=${product.id}&name=${encodeURIComponent(product.name)}&price=${product.price_per_day}&stock=${product.stock}&image_path=${encodeURIComponent(product.image_path || '')}`} 
                                            disabled={product.stock === 0}
                                            class="inline-flex items-center justify-center px-6 py-3 bg-primary text-white font-semibold uppercase text-xs rounded-xl transition-all hover:bg-primary/90 {product.stock === 0 ? 'bg-zinc-200 text-zinc-400 dark:bg-zinc-800 pointer-events-none' : ''}">
                                            {product.stock > 0 ? 'Rent' : 'N/A'}
                                        </Link>
                                    </div>
                                </div>
                            </div>
                        {/each}
                    </div>
                {/if}

                <!-- Pagination (Hidden if insufficient items) -->
                {#if filteredProducts.length > 0}
                    <div class="flex justify-center items-center gap-2 pt-12 border-t border-zinc-100 dark:border-zinc-900">
                        <button disabled class="size-10 rounded-full border border-zinc-200 dark:border-zinc-800 flex items-center justify-center text-zinc-400 opacity-50 cursor-not-allowed transition-all">&larr;</button>
                        <button class="size-10 rounded-full bg-primary text-primary-foreground font-bold flex items-center justify-center shadow-lg shadow-primary/20">1</button>
                        <button disabled class="size-10 rounded-full border border-zinc-200 dark:border-zinc-800 flex items-center justify-center text-zinc-400 opacity-50 cursor-not-allowed transition-all">&rarr;</button>
                    </div>
                {/if}
            </div>
        </div>
    </main>

    <Footer />
</div>
