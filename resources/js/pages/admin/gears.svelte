<script>
    import Sidebar from '@/components/layout/Sidebar.svelte';
    import { useForm, router } from '@inertiajs/svelte';
    import { Package, Plus, Search, Filter, Edit3, Trash2, MoreVertical, Loader2 } from 'lucide-svelte';
    import { fly, fade } from 'svelte/transition';
    import * as Select from "@/components/ui/select/index.js";

    let { gears = [] } = $props();

    const form = useForm({
        name: '',
        description: '',
        category: 'Hiking',
        price_per_day: '',
        stock: '',
        is_featured: false,
        image: null,
    });

    let imagePreview = $state(null);

    let isAdding = $state(false);
    let editingId = $state(null);

    // Filter state
    let searchQuery = $state('');
    let categoryFilter = $state('All');

    const filteredGears = $derived.by(() => {
        return gears.filter(gear => {
            const matchesSearch = gear.name.toLowerCase().includes(searchQuery.toLowerCase()) || 
                               gear.description?.toLowerCase().includes(searchQuery.toLowerCase());
            const matchesCategory = categoryFilter === 'All' || gear.category === categoryFilter;
            return matchesSearch && matchesCategory;
        });
    });

    function handleSubmit(e) {
        e.preventDefault();
        if (editingId) {
            $form.transform((data) => ({
                ...data,
                _method: 'put',
            })).post(`/admin/gears/${editingId}`, {
                onSuccess: () => {
                    isAdding = false;
                    editingId = null;
                    $form.reset();
                    imagePreview = null;
                }
            });
        } else {
            $form.post('/admin/gears', {
                onSuccess: () => {
                    isAdding = false;
                    $form.reset();
                    imagePreview = null;
                }
            });
        }
    }

    function editGear(gear) {
        editingId = gear.id;
        $form.name = gear.name;
        $form.description = gear.description;
        $form.category = gear.category;
        $form.price_per_day = gear.price_per_day;
        $form.stock = gear.stock;
        $form.is_featured = !!gear.is_featured;
        $form.image = null;
        imagePreview = gear.image_path ? `/storage/${gear.image_path}` : null;
        isAdding = true;
    }

    function deleteGear(id) {
        if (confirm('Are you sure you want to delete this gear?')) {
            router.delete(`/admin/gears/${id}`);
        }
    }

    function formatCurrency(amount) {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0
        }).format(amount);
    }
</script>

<div class="flex bg-zinc-50 min-h-screen font-sans selection:bg-primary selection:text-white">
    <Sidebar role="admin" />

    <main class="flex-grow p-10">
        <div class="max-w-6xl mx-auto space-y-10">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div class="space-y-1">
                    <h1 class="text-2xl font-bold tracking-tight text-zinc-900">Inventory</h1>
                    <p class="text-sm text-zinc-500">Manage your outdoor equipment and stock levels.</p>
                </div>
                <button 
                    onclick={() => { isAdding = true; editingId = null; $form.reset(); }}
                    class="flex items-center gap-2 bg-zinc-900 text-white px-5 py-2.5 rounded-lg font-semibold text-sm hover:bg-zinc-800 transition-all shadow-sm"
                >
                    <Plus size={16} />
                    Add Gear
                </button>
            </div>

            <!-- Stats Overview (Minimalist) -->
            <div class="grid grid-cols-4 gap-6">
                <div class="bg-white border border-zinc-200 p-5 rounded-xl shadow-sm space-y-1">
                    <p class="text-[10px] font-semibold uppercase tracking-wider text-zinc-400">Total Items</p>
                    <p class="text-2xl font-bold text-zinc-900">{gears.length}</p>
                </div>
                <div class="bg-white border border-zinc-200 p-5 rounded-xl shadow-sm space-y-1">
                    <p class="text-[10px] font-semibold uppercase tracking-wider text-zinc-400">In Stock</p>
                    <p class="text-2xl font-bold text-zinc-900">{gears.reduce((acc, gear) => acc + gear.stock, 0)}</p>
                </div>
            </div>

            <!-- Main Content Area -->
            <div class="bg-white border border-zinc-200 rounded-xl overflow-hidden shadow-sm">
                <!-- Toolbar -->
                <div class="px-6 py-4 border-b border-zinc-100 flex items-center justify-between bg-zinc-50/50">
                    <div class="relative w-80 group">
                        <Search class="absolute left-3 top-1/2 -translate-y-1/2 text-zinc-400 group-focus-within:text-zinc-900 transition-colors" size={16} />
                        <input 
                            type="text" 
                            bind:value={searchQuery}
                            placeholder="Filter gears..." 
                            class="w-full h-10 bg-white border border-zinc-200 rounded-md pl-10 pr-4 focus:border-zinc-400 transition-all outline-none text-sm" 
                        />
                    </div>
                    <div class="flex items-center gap-3">
                        <Select.Root type="single" bind:value={categoryFilter}>
                            <Select.Trigger class="w-40 h-10 bg-white border border-zinc-200 rounded-md px-3 focus:border-zinc-400 transition-all outline-none text-sm font-medium">
                                {categoryFilter === 'All' ? 'All Categories' : categoryFilter}
                            </Select.Trigger>
                            <Select.Content class="z-50">
                                <Select.Item value="All">All Categories</Select.Item>
                                <Select.Item value="Hiking">Hiking</Select.Item>
                                <Select.Item value="Camping">Camping</Select.Item>
                                <Select.Item value="Climbing">Climbing</Select.Item>
                                <Select.Item value="Accessories">Accessories</Select.Item>
                            </Select.Content>
                        </Select.Root>
                    </div>
                </div>

                <!-- Table -->
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-zinc-50/50 border-b border-zinc-100">
                                <th class="px-6 py-4 text-xs font-semibold text-zinc-500">Gear</th>
                                <th class="px-6 py-4 text-xs font-semibold text-zinc-500">Category</th>
                                <th class="px-6 py-4 text-xs font-semibold text-zinc-500">Price/Day</th>
                                <th class="px-6 py-4 text-xs font-semibold text-zinc-500">Stock</th>
                                <th class="px-6 py-4 text-xs font-semibold text-zinc-500 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-100">
                            {#each filteredGears as gear}
                                <tr class="hover:bg-zinc-50/30 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-4">
                                            <div class="size-10 rounded-md bg-zinc-100 flex items-center justify-center border border-zinc-200 text-zinc-400 overflow-hidden">
                                                {#if gear.image_path}
                                                    <img src="/storage/{gear.image_path}" class="size-full object-cover" />
                                                {:else}
                                                    <Package size={20} />
                                                {/if}
                                            </div>
                                            <div class="min-w-0">
                                                <p class="text-sm font-semibold text-zinc-900 truncate max-w-[200px]">{gear.name}</p>
                                                <p class="text-[11px] text-zinc-400 truncate max-w-[200px]">{gear.description || 'No description provided'}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="px-2 py-0.5 bg-zinc-100 text-zinc-600 rounded text-[10px] font-semibold uppercase tracking-wider">{gear.category}</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <p class="text-sm font-semibold text-zinc-900">{formatCurrency(gear.price_per_day)}</p>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-2">
                                            <div class="size-1.5 rounded-full {gear.stock > 0 ? 'bg-emerald-500' : 'bg-rose-500'}"></div>
                                            <p class="text-sm font-medium text-zinc-900">{gear.stock} units</p>
                                            {#if gear.is_featured}
                                                <span class="ml-2 px-1.5 py-0.5 bg-amber-50 text-amber-600 rounded text-[9px] font-black uppercase tracking-widest border border-amber-100">Featured</span>
                                            {/if}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex items-center justify-end gap-1">
                                            <button onclick={() => editGear(gear)} class="p-2 text-zinc-400 hover:text-zinc-900 hover:bg-zinc-100 rounded-md transition-all">
                                                <Edit3 size={16} />
                                            </button>
                                            <button onclick={() => deleteGear(gear.id)} class="p-2 text-zinc-400 hover:text-rose-600 hover:bg-rose-50 rounded-md transition-all">
                                                <Trash2 size={16} />
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            {/each}
                        </tbody>
                    </table>
                </div>

                {#if gears.length === 0}
                    <div class="p-20 text-center space-y-4">
                        <div class="size-16 bg-zinc-50 rounded-full flex items-center justify-center mx-auto border border-zinc-100 text-zinc-300">
                            <Package size={32} />
                        </div>
                        <div class="space-y-1">
                            <p class="text-sm font-semibold text-zinc-900">No gears found</p>
                            <p class="text-xs text-zinc-500">Start by adding your first equipment.</p>
                        </div>
                    </div>
                {/if}
            </div>
        </div>
    </main>

    <!-- Modal Form (Shadcn Style) -->
    {#if isAdding}
        <div class="fixed inset-0 z-[100] flex items-center justify-center p-6" transition:fade>
            <button class="absolute inset-0 bg-zinc-950/20 backdrop-blur-[2px] cursor-default" onclick={() => isAdding = false}></button>
            <div class="relative w-full max-w-lg bg-white rounded-xl shadow-2xl border border-zinc-200" in:fly={{ y: 20 }}>
                <div class="p-8 border-b border-zinc-100 bg-zinc-50/50">
                    <h2 class="text-lg font-bold text-zinc-900">{editingId ? 'Edit Gear' : 'Add New Gear'}</h2>
                    <p class="text-xs text-zinc-500 mt-1">Provide equipment details for your inventory.</p>
                </div>

                <form onsubmit={handleSubmit} class="p-8 space-y-6">
                    <div class="space-y-4">
                        <div class="space-y-2">
                            <label class="text-xs font-semibold text-zinc-700">Gear Image</label>
                            <div class="flex items-center gap-4">
                                <div class="size-20 rounded-lg bg-zinc-50 border-2 border-dashed border-zinc-200 flex items-center justify-center text-zinc-400 overflow-hidden">
                                    {#if imagePreview}
                                        <img src={imagePreview} class="size-full object-cover" />
                                    {:else}
                                        <Package size={24} />
                                    {/if}
                                </div>
                                <div class="flex-grow space-y-1">
                                    <input 
                                        type="file" 
                                        accept="image/*"
                                        id="gear-image"
                                        onchange={(e) => {
                                            const file = e.target.files[0];
                                            if (file) {
                                                $form.image = file;
                                                imagePreview = URL.createObjectURL(file);
                                            }
                                        }}
                                        class="hidden"
                                    />
                                    <label 
                                        for="gear-image" 
                                        class="inline-block px-4 py-2 bg-white border border-zinc-200 rounded-md text-xs font-semibold text-zinc-700 hover:bg-zinc-50 hover:border-zinc-300 cursor-pointer transition-all"
                                    >
                                        Choose Image
                                    </label>
                                    <p class="text-[10px] text-zinc-400">JPG, PNG or WEBP. Max 2MB.</p>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <label class="text-xs font-semibold text-zinc-700">Gear Name</label>
                                <input type="text" bind:value={$form.name} required placeholder="e.g. Osprey Aether 65" class="w-full h-10 bg-white border border-zinc-200 rounded-md px-4 focus:border-zinc-400 transition-all outline-none text-sm font-medium" />
                            </div>
                            <div class="space-y-2">
                                <label class="text-xs font-semibold text-zinc-700">Category</label>
                                <Select.Root type="single" bind:value={$form.category}>
                                    <Select.Trigger class="w-full h-10 bg-white border border-zinc-200 rounded-md px-3 focus:border-zinc-400 transition-all outline-none text-sm font-medium">
                                        {$form.category || "Select Category"}
                                    </Select.Trigger>
                                    <Select.Content class="z-[200]">
                                        <Select.Item value="Hiking">Hiking</Select.Item>
                                        <Select.Item value="Camping">Camping</Select.Item>
                                        <Select.Item value="Climbing">Climbing</Select.Item>
                                        <Select.Item value="Accessories">Accessories</Select.Item>
                                    </Select.Content>
                                </Select.Root>
                            </div>
                        </div>
                        <div class="space-y-2">
                            <label class="text-xs font-semibold text-zinc-700">Description</label>
                            <textarea bind:value={$form.description} rows="3" placeholder="Features and condition..." class="w-full bg-white border border-zinc-200 rounded-md p-4 focus:border-zinc-400 transition-all outline-none text-sm font-medium leading-relaxed"></textarea>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <label class="text-xs font-semibold text-zinc-700">Price per Day</label>
                                <div class="relative">
                                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-zinc-400 text-xs font-bold">Rp</span>
                                    <input type="number" bind:value={$form.price_per_day} required placeholder="12000" class="w-full h-10 bg-white border border-zinc-200 rounded-md pl-10 pr-4 focus:border-zinc-400 transition-all outline-none text-sm font-medium" />
                                </div>
                            </div>
                            <div class="space-y-2">
                                <label class="text-xs font-semibold text-zinc-700">Initial Stock</label>
                                <input type="number" bind:value={$form.stock} required placeholder="10" class="w-full h-10 bg-white border border-zinc-200 rounded-md px-4 focus:border-zinc-400 transition-all outline-none text-sm font-medium" />
                            </div>
                        </div>

                        <!-- Featured Toggle -->
                        <div class="flex items-center justify-between p-4 bg-zinc-50 rounded-lg border border-zinc-200">
                            <div class="space-y-0.5">
                                <p class="text-xs font-semibold text-zinc-900">Featured Gear</p>
                                <p class="text-[10px] text-zinc-500">Show this item in the featured section of the homepage.</p>
                            </div>
                            <button 
                                type="button"
                                onclick={() => $form.is_featured = !$form.is_featured}
                                class="w-10 h-6 rounded-full transition-colors relative {$form.is_featured ? 'bg-zinc-900' : 'bg-zinc-200'}"
                            >
                                <div class="absolute top-1 left-1 size-4 bg-white rounded-full transition-transform {$form.is_featured ? 'translate-x-4' : 'translate-x-0'}"></div>
                            </button>
                        </div>
                    </div>

                    <div class="pt-4 flex justify-end gap-3">
                        <button type="button" onclick={() => isAdding = false} class="px-4 py-2 text-sm font-semibold text-zinc-600 hover:text-zinc-900 transition-colors">Cancel</button>
                        <button type="submit" disabled={$form.processing} class="px-6 py-2 bg-zinc-900 text-white font-semibold text-sm rounded-md hover:bg-zinc-800 transition-all flex items-center gap-2">
                            {#if $form.processing}
                                <Loader2 class="animate-spin" size={16} />
                                Saving...
                            {:else}
                                Save Item
                            {/if}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    {/if}
</div>
