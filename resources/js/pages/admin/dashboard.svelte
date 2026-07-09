<script>
    import Sidebar from '@/components/layout/Sidebar.svelte';
    import { LayoutDashboard, TrendingUp, Users, Package, Clock } from 'lucide-svelte';

    let { stats = { total_revenue: 0, customers: 0, active_rentals: 0, inventory: 0 }, recent_bookings = [] } = $props();

    function formatCurrency(amount) {
        if (amount >= 1000000) {
            return `Rp ${(amount / 1000000).toFixed(1)}M`;
        }
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            maximumFractionDigits: 0
        }).format(amount);
    }

    function timeAgo(dateString) {
        const date = new Date(dateString);
        const now = new Date();
        const diffInSeconds = Math.floor((now - date) / 1000);
        
        if (diffInSeconds < 60) return `${diffInSeconds}s ago`;
        if (diffInSeconds < 3600) return `${Math.floor(diffInSeconds / 60)}m ago`;
        if (diffInSeconds < 86400) return `${Math.floor(diffInSeconds / 3600)}h ago`;
        return `${Math.floor(diffInSeconds / 86400)}d ago`;
    }
</script>

<div class="flex bg-zinc-50 min-h-screen font-sans selection:bg-primary selection:text-white">
    <Sidebar role="admin" />

    <main class="flex-grow p-10">
        <div class="max-w-6xl mx-auto space-y-10">
            <div class="space-y-1">
                <h1 class="text-2xl font-bold tracking-tight text-zinc-900">Dashboard</h1>
                <p class="text-sm text-zinc-500">Welcome back, here is what's happening today.</p>
            </div>

            <div class="grid grid-cols-4 gap-6">
                <div class="bg-white border border-zinc-200 p-6 rounded-xl shadow-sm space-y-3">
                    <div class="flex items-center justify-between">
                        <p class="text-xs font-medium text-zinc-500">Total Revenue</p>
                        <TrendingUp size={16} class="text-zinc-400" />
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-zinc-900">{formatCurrency(stats.total_revenue)}</p>
                        <p class="text-[10px] text-zinc-400 font-medium tracking-tight">Lifetime earnings</p>
                    </div>
                </div>

                <div class="bg-white border border-zinc-200 p-6 rounded-xl shadow-sm space-y-3">
                    <div class="flex items-center justify-between">
                        <p class="text-xs font-medium text-zinc-500">Customers</p>
                        <Users size={16} class="text-zinc-400" />
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-zinc-900">{stats.customers}</p>
                        <p class="text-[10px] text-zinc-400 font-medium">Registered users</p>
                    </div>
                </div>

                <div class="bg-white border border-zinc-200 p-6 rounded-xl shadow-sm space-y-3">
                    <div class="flex items-center justify-between">
                        <p class="text-xs font-medium text-zinc-500">Active Rentals</p>
                        <Clock size={16} class="text-zinc-400" />
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-zinc-900">{stats.active_rentals}</p>
                        <p class="text-[10px] text-orange-600 font-medium tracking-tight">Currently out with users</p>
                    </div>
                </div>

                <div class="bg-white border border-zinc-200 p-6 rounded-xl shadow-sm space-y-3">
                    <div class="flex items-center justify-between">
                        <p class="text-xs font-medium text-zinc-500">Inventory</p>
                        <Package size={16} class="text-zinc-400" />
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-zinc-900">{stats.inventory}</p>
                        <p class="text-[10px] text-zinc-400 font-medium">Unique gear models</p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-3 gap-6">
                <div class="col-span-2 bg-white border border-zinc-200 rounded-xl p-8 h-[400px] flex flex-col shadow-sm">
                    <h3 class="text-sm font-semibold text-zinc-900 mb-6">Revenue Overview</h3>
                    <div class="flex-grow border-t border-zinc-100 flex items-center justify-center border-dashed border-2 rounded-lg bg-zinc-50/50 mt-2">
                        <p class="text-zinc-400 font-medium text-xs">Analytics Chart Content</p>
                    </div>
                </div>
                
                <div class="bg-white border border-zinc-200 rounded-xl p-8 h-[400px] flex flex-col shadow-sm">
                    <div class="mb-8">
                        <h3 class="text-sm font-semibold text-zinc-900">Recent Activity</h3>
                        <p class="text-xs text-zinc-500 mt-1">Latest updates from your store</p>
                    </div>
                    <div class="space-y-6 overflow-y-auto pr-2">
                        {#each recent_bookings as booking}
                            <div class="flex items-start gap-4 group">
                                <div class="size-2 rounded-full bg-zinc-900 mt-1.5 group-hover:scale-125 transition-transform"></div>
                                <div class="space-y-1">
                                    <p class="text-xs font-semibold text-zinc-900 leading-none">
                                        Booking #{booking.id.toString().padStart(4, '0')}
                                    </p>
                                    <p class="text-[10px] text-zinc-500 font-medium">
                                        {booking.gear?.name || 'Unknown Gear'} • {timeAgo(booking.created_at)}
                                    </p>
                                </div>
                            </div>
                        {:else}
                            <div class="h-40 flex flex-col items-center justify-center text-zinc-300">
                                <Clock size={24} strokeWidth={1} />
                                <p class="text-[10px] font-medium mt-2">No activity yet</p>
                            </div>
                        {/each}
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>
