<script>
    import Sidebar from '@/components/layout/Sidebar.svelte';
    import { LayoutDashboard, CalendarClock, ShieldCheck, PackageCheck, RotateCcw } from 'lucide-svelte';
    import { Link } from '@inertiajs/svelte';

    let { stats = { pending: 5, handover: 3, returns: 2 } } = $props();
</script>

<div class="flex bg-zinc-50 min-h-screen font-sans selection:bg-primary selection:text-white">
    <Sidebar role="staff" />

    <main class="flex-grow p-10">
        <div class="max-w-6xl mx-auto space-y-10">
            <div class="space-y-1">
                <h1 class="text-2xl font-bold tracking-tight text-zinc-900">Operations</h1>
                <p class="text-sm text-zinc-500">Welcome back. Manage bookings and equipment handovers.</p>
            </div>

            <div class="grid grid-cols-3 gap-6">
                <!-- Pending Validations -->
                <div class="bg-white border border-zinc-200 p-6 rounded-xl shadow-sm space-y-4">
                    <div class="flex items-center justify-between">
                        <div class="size-10 bg-blue-50 text-blue-600 rounded-lg flex items-center justify-center">
                            <ShieldCheck size={20} />
                        </div>
                        <span class="px-2.5 py-0.5 bg-blue-50 text-blue-600 rounded-full text-[10px] font-bold uppercase tracking-wider">Action Required</span>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-zinc-500">Pending Validations</p>
                        <p class="text-3xl font-bold text-zinc-900 mt-1">{stats.pending}</p>
                    </div>
                    <Link href="/staff/bookings" class="flex items-center justify-center w-full py-2 bg-zinc-50 hover:bg-zinc-100 text-zinc-900 border border-zinc-200 rounded-lg text-xs font-semibold transition-all">
                        Review All
                    </Link>
                </div>

                <!-- Handovers -->
                <div class="bg-white border border-zinc-200 p-6 rounded-xl shadow-sm space-y-4">
                    <div class="flex items-center justify-between">
                        <div class="size-10 bg-zinc-900 text-white rounded-lg flex items-center justify-center">
                            <PackageCheck size={20} />
                        </div>
                        <span class="px-2.5 py-0.5 bg-zinc-50 text-zinc-500 border border-zinc-100 rounded-full text-[10px] font-bold uppercase tracking-wider">Today</span>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-zinc-500">Handovers Scheduled</p>
                        <p class="text-3xl font-bold text-zinc-900 mt-1">{stats.handover}</p>
                    </div>
                    <Link href="/staff/bookings" class="flex items-center justify-center w-full py-2 bg-zinc-900 hover:bg-zinc-800 text-white rounded-lg text-xs font-semibold transition-all">
                        View Schedule
                    </Link>
                </div>

                <!-- Returns -->
                <div class="bg-white border border-zinc-200 p-6 rounded-xl shadow-sm space-y-4">
                    <div class="flex items-center justify-between">
                        <div class="size-10 bg-primary/10 text-primary rounded-lg flex items-center justify-center">
                            <RotateCcw size={20} />
                        </div>
                        <span class="px-2.5 py-0.5 bg-primary/10 text-primary rounded-full text-[10px] font-bold uppercase tracking-wider">Incoming</span>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-zinc-500">Items to Return</p>
                        <p class="text-3xl font-bold text-zinc-900 mt-1">{stats.returns}</p>
                    </div>
                    <Link href="/staff/bookings" class="flex items-center justify-center w-full py-2 bg-primary hover:bg-primary/90 text-white rounded-lg text-xs font-semibold transition-all">
                        Process Returns
                    </Link>
                </div>
            </div>

            <!-- Guidelines -->
            <div class="bg-white border border-zinc-200 rounded-xl p-8 shadow-sm space-y-6">
                <h3 class="text-xs font-bold uppercase tracking-widest text-zinc-400">Operational Reminders</h3>
                <div class="grid grid-cols-2 gap-6">
                    <div class="flex items-start gap-4 p-5 bg-zinc-50/50 rounded-xl border border-zinc-100">
                        <div class="size-10 bg-white border border-zinc-100 rounded-lg flex items-center justify-center text-amber-500 shadow-sm">
                            <ShieldCheck size={20} />
                        </div>
                        <div class="space-y-1">
                            <p class="text-sm font-bold text-zinc-900">Always Verify KTP</p>
                            <p class="text-xs text-zinc-500 leading-relaxed">Ensure the uploaded identity card matches the person picking up the gear.</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-4 p-5 bg-zinc-50/50 rounded-xl border border-zinc-100">
                        <div class="size-10 bg-white border border-zinc-100 rounded-lg flex items-center justify-center text-primary shadow-sm">
                            <PackageCheck size={20} />
                        </div>
                        <div class="space-y-1">
                            <p class="text-sm font-bold text-zinc-900">Quality Inspection</p>
                            <p class="text-xs text-zinc-500 leading-relaxed">Perform a quick condition check on all items before handover.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>
