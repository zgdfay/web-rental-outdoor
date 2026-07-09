<script>
    import { ShieldCheck, Truck, RefreshCcw, Leaf } from 'lucide-svelte';

    const features = [
        {
            icon: ShieldCheck,
            title: 'Professional Quality',
            desc: 'We only stock industry-leading brands maintained to the highest safety and cleanliness standards.'
        },
        {
            icon: Truck,
            title: 'Express Delivery',
            desc: 'Get your gear delivered to your home or directly to the trailhead. Adventure when you want it.'
        },
        {
            icon: RefreshCcw,
            title: 'Easy Returns',
            desc: 'No more storage worries. Just pack it up and let us handle the cleaning and maintenance.'
        },
        {
            icon: Leaf,
            title: 'Eco-Friendly',
            desc: 'By sharing gear, we reduce waste and production impact. Adventure sustainably with us.'
        }
    ];
</script>

<section class="py-24 px-6 bg-zinc-950">
    <div class="max-w-7xl mx-auto">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-20 items-center">
            <div class="space-y-12">
                <div class="space-y-6">
                    <h2 class="text-4xl md:text-5xl font-bold text-white leading-tight">
                        ADVENTURE MORE, <br/> <span class="text-primary italic font-serif">OWN LESS</span>
                    </h2>
                    <p class="text-zinc-100/60 max-w-md leading-relaxed">
                        We believe that everyone should have access to the wilderness without needing a garage full of gear. GearRental is your shared inventory for the planet.
                    </p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-10">
                    {#each features as feature}
                        <div class="space-y-4">
                            <div class="size-12 rounded-2xl bg-primary/10 border border-primary/20 flex items-center justify-center text-primary shadow-lg">
                                <svelte:component this={feature.icon} size={24} />
                            </div>
                            <h4 class="text-white font-bold text-lg">{feature.title}</h4>
                            <p class="text-sm text-zinc-100/50 leading-relaxed">
                                {feature.desc}
                            </p>
                        </div>
                    {/each}
                </div>
            </div>

            <div class="relative hidden lg:block">
                <!-- Visual Composition -->
                <div class="relative z-10 aspect-[3/4] rounded-[48px] overflow-hidden shadow-2xl overflow-hidden">
                    <img 
                        src="https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?auto=format&fit=crop&q=80&w=800" 
                        alt="Outdoor Lifestyle" 
                        class="w-full h-full object-cover"
                    />
                    <div class="absolute inset-0 bg-black/20"></div>
                </div>
                
                <!-- Floating Card -->
                <div class="absolute -bottom-10 -left-10 z-20 bg-white p-8 rounded-3xl shadow-3xl max-w-xs space-y-4">
                    <div class="flex items-center gap-4">
                        <div class="size-10 rounded-full bg-primary/10 flex items-center justify-center text-primary">
                            <Leaf size={20} />
                        </div>
                        <span class="font-bold text-zinc-900">Carbon Neutral</span>
                    </div>
                    <p class="text-sm text-zinc-500 leading-relaxed italic">
                        "Every rental contributes to reforesting protected wilderness areas across the country."
                    </p>
                </div>

                <!-- Decorative elements -->
                <div class="absolute -top-10 -right-10 size-40 bg-primary/20 blur-3xl rounded-full"></div>
                <div class="absolute top-1/2 -left-20 size-60 bg-primary/10 blur-[100px] rounded-full"></div>
            </div>
        </div>
    </div>
</section>
