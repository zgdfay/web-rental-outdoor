<script>
    import { Link } from '@inertiajs/svelte';
    import Navbar from '@/components/layout/navbar.svelte';
    import Footer from '@/components/layout/footer.svelte';
    import { Search, ChevronDown, MessageCircle, Phone, Mail, HelpCircle, Package, ShieldCheck, CreditCard, User } from 'lucide-svelte';
    import { slide } from 'svelte/transition';

    let searchQuery = $state("");
    let activeCategory = $state("All");

    const categories = [
        { name: "All" },
        { name: "Rental", icon: Package },
        { name: "Payments", icon: CreditCard },
        { name: "Safety", icon: ShieldCheck },
        { name: "Account", icon: User },
    ];

    const faqs = [
        {
            category: "Rental",
            question: "How long can I rent the gear for?",
            answer: "Our standard rental periods range from 3 days to 14 days. If you need equipment for a longer expedition, please contact our support team for a custom quote."
        },
        {
            category: "Rental",
            question: "Is there a security deposit required?",
            answer: "Yes, we require a small security deposit for high-value items like professional tents and climbing racks. This deposit is fully refunded once the gear is returned in good condition."
        },
        {
            category: "Payments",
            question: "What payment methods do you accept?",
            answer: "We accept all major credit cards, PayPal, and digital wallets like Apple Pay and Google Pay. For local rentals, we also support bank transfers."
        },
        {
            category: "Safety",
            question: "Is the climbing gear safety-certified?",
            answer: "Absolutely. All our climbing and safety equipment is UIAA and CE certified. We conduct rigorous inspections and strength tests after every single rental."
        },
        {
            category: "Account",
            question: "Can I change my rental dates after booking?",
            answer: "You can modify your booking up to 48 hours before the start date, subject to equipment availability. Simply log in to your account and navigate to 'Active Rentals'."
        },
        {
            category: "Safety",
            question: "What happens if gear gets damaged?",
            answer: "Minor wear and tear is expected. However, significant damage or loss may incur a repair or replacement fee. We offer 'Damage Protection' insurance at checkout for peace of mind."
        }
    ];

    let openIndex = $state(null);

    function toggleFaq(index) {
        openIndex = openIndex === index ? null : index;
    }

    let filteredFaqs = $derived(
        faqs.filter(faq => {
            const matchesSearch = faq.question.toLowerCase().includes(searchQuery.toLowerCase()) || 
                                 faq.answer.toLowerCase().includes(searchQuery.toLowerCase());
            const matchesCategory = activeCategory === "All" || faq.category === activeCategory;
            return matchesSearch && matchesCategory;
        })
    );
</script>

<svelte:head>
    <title>FAQ - Outdoor Species</title>
</svelte:head>

<div class="bg-zinc-950 text-white selection:bg-primary selection:text-white min-h-screen flex flex-col overflow-hidden">
    <Navbar />

    <main class="flex-grow pt-32 pb-20 px-6">
        <!-- Hero Header -->
        <div class="max-w-4xl mx-auto text-center space-y-6 mb-16">
            <h1 class="text-5xl md:text-7xl font-black">
                FREQUENTLY ASKED <br />
                <span class="text-primary italic">QUESTIONS</span>
            </h1>
            <p class="text-zinc-500 text-lg max-w-2xl mx-auto">
                Everything you need to know about our gear, rentals, and the adventure ahead. Can't find what you're looking for? Reach out to us.
            </p>

            <!-- Search Bar -->
            <div class="max-w-3xl mx-auto pt-10">
                <div class="relative group">
                    <div class="absolute -inset-1 bg-primary/20 rounded-full blur-xl opacity-0 group-focus-within:opacity-100 transition-opacity"></div>
                    <Search class="absolute left-6 top-1/2 -translate-y-1/2 text-zinc-500 group-focus-within:text-primary transition-colors" size={22} />
                    <input 
                        type="text" 
                        bind:value={searchQuery}
                        placeholder="Search for questions (e.g. 'deposit', 'safety')..."
                        class="relative w-full h-16 pl-16 pr-8 bg-zinc-900/50 border border-zinc-800 rounded-full focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all outline-none text-zinc-100 placeholder:text-zinc-600 font-medium"
                    />
                </div>
            </div>
        </div>

        <!-- Categories -->
        <div class="max-w-5xl mx-auto mb-12">
            <div class="flex flex-wrap justify-center gap-3">
                {#each categories as category}
                    <button 
                        onclick={() => activeCategory = category.name}
                        class="flex items-center gap-2 px-6 py-3 rounded-xl border font-bold transition-all {activeCategory === category.name ? 'bg-primary border-primary text-white shadow-lg shadow-primary/20' : 'bg-zinc-900 border-zinc-800 text-zinc-500 hover:border-zinc-700 hover:text-zinc-300'}"
                    >
                        <category.icon size={18} />
                        {category.name}
                    </button>
                {/each}
            </div>
        </div>

        <!-- FAQ List -->
        <div class="max-w-3xl mx-auto space-y-4">
            {#if filteredFaqs.length > 0}
                {#each filteredFaqs as faq, i}
                    <div class="group">
                        <button 
                            onclick={() => toggleFaq(i)}
                            class="w-full p-6 text-left bg-zinc-900/50 border border-zinc-800 hover:border-zinc-700 rounded-2xl transition-all flex items-start justify-between gap-4 {openIndex === i ? 'border-primary/50 bg-zinc-900' : ''}"
                        >
                            <div class="space-y-1">
                                <span class="text-[10px] font-black uppercase tracking-widest text-primary">{faq.category}</span>
                                <h3 class="text-lg font-bold text-zinc-200">{faq.question}</h3>
                            </div>
                            <div class="mt-1 transition-transform duration-300 {openIndex === i ? 'rotate-180' : ''}">
                                <ChevronDown class="text-zinc-600 group-hover:text-primary" size={20} />
                            </div>
                        </button>
                        
                        {#if openIndex === i}
                            <div transition:slide={{ duration: 300 }} class="overflow-hidden">
                                <div class="p-6 pt-2 text-zinc-400 leading-relaxed text-md font-medium">
                                    {faq.answer}
                                </div>
                            </div>
                        {/if}
                    </div>
                {/each}
            {:else}
                <div class="text-center py-20 space-y-4">
                    <div class="size-16 bg-zinc-900 rounded-2xl flex items-center justify-center mx-auto border border-zinc-800">
                        <Search class="text-zinc-700" size={32} />
                    </div>
                    <p class="text-zinc-500 font-medium italic">No questions found matching your search.</p>
                </div>
            {/if}
        </div>

        <!-- Contact Support -->
        <div class="max-w-5xl mx-auto mt-32 p-12 rounded-[2.5rem] bg-gradient-to-br from-zinc-900 to-zinc-950 border border-zinc-800 relative overflow-hidden">
            <div class="absolute top-0 right-0 size-[400px] bg-primary/5 blur-[100px] -translate-y-1/2 translate-x-1/2 rounded-full"></div>
            
            <div class="relative z-10 grid md:grid-cols-2 gap-12 items-center">
                <div class="space-y-6">
                    <h2 class="text-4xl font-black tracking-tight leading-tight">
                        Still have <br />
                        <span class="text-primary italic">questions?</span>
                    </h2>
                    <p class="text-zinc-500 font-medium">
                        Our experts are ready to help you plan your next expedition. Reach out via your preferred channel.
                    </p>
                    <div class="flex flex-col gap-4">
                        <div class="flex items-center gap-4 text-zinc-300 font-bold">
                            <div class="size-10 rounded-lg bg-zinc-800 flex items-center justify-center border border-zinc-700">
                                <Phone size={18} class="text-primary" />
                            </div>
                            +1 (800) OUTDOOR
                        </div>
                        <div class="flex items-center gap-4 text-zinc-300 font-bold">
                            <div class="size-10 rounded-lg bg-zinc-800 flex items-center justify-center border border-zinc-700">
                                <Mail size={18} class="text-primary" />
                            </div>
                            support@outdoorspecies.com
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4">
                    <a 
                        href="https://wa.me/18006883667" 
                        target="_blank"
                        class="w-full p-6 bg-primary hover:bg-primary/90 text-white font-black uppercase tracking-widest text-xs rounded-2xl transition-all shadow-xl shadow-primary/20 flex items-center justify-center gap-3 group"
                    >
                        <MessageCircle size={20} />
                        Open WhatsApp
                    </a>
                    <div class="text-center">
                        <span class="text-[10px] font-black uppercase tracking-widest text-zinc-600 italic">Available 24/7 for active rentals</span>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <Footer />
</div>
