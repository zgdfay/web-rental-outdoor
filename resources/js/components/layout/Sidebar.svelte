<script>
    import { Link, page } from '@inertiajs/svelte';
    import { LayoutDashboard, Package, CalendarClock, LogOut, User, ChevronRight, TrendingUp } from 'lucide-svelte';
    import * as Dialog from "@/components/ui/dialog/index.js";
    import { Button } from "@/components/ui/button/index.js";

    let { role = 'admin' } = $props();
    let isLoggingOut = $state(false);

    const menuItems = {
        admin: [
            { name: 'Dashboard', icon: LayoutDashboard, href: '/dashboard' },
            { name: 'Inventory', icon: Package, href: '/admin/gears' },
            { name: 'Reports', icon: TrendingUp, href: '/admin/reports' },
        ],
        staff: [
            { name: 'Dashboard', icon: LayoutDashboard, href: '/dashboard' },
            { name: 'Bookings', icon: CalendarClock, href: '/staff/bookings' },
        ]
    };

    const user = $derived($page.props.auth?.user);
</script>

<aside class="w-64 bg-white border-r border-zinc-200 h-screen sticky top-0 flex flex-col">
    <!-- Header -->
    <div class="h-16 flex items-center px-6 border-b border-zinc-100 mb-6">
        <Link href="/" class="flex items-center gap-2.5 font-bold tracking-tight text-zinc-900 transition-opacity hover:opacity-80">
            <img src="/assets/logo/logo-outdoor-species.png" alt="Logo" class="h-8 w-auto object-contain">
            <span class="text-[15px]">Outdoor Species</span>
        </Link>
    </div>

    <!-- Navigation -->
    <nav class="flex-grow px-3 space-y-1">
        <p class="px-3 mb-2 text-[10px] font-bold text-zinc-400 uppercase tracking-[0.15em]">{role} menu</p>
        {#each menuItems[role] as item}
            <Link 
                href={item.href} 
                class="flex items-center justify-between px-3 py-2 rounded-md transition-all group {$page.url === item.href ? 'bg-zinc-100 text-zinc-900 font-semibold' : 'text-zinc-500 hover:bg-zinc-50 hover:text-zinc-900'}"
            >
                <div class="flex items-center gap-3">
                    <item.icon size={18} strokeWidth={$page.url === item.href ? 2.5 : 2} />
                    <span class="text-sm tracking-tight">{item.name}</span>
                </div>
                {#if $page.url !== item.href}
                    <ChevronRight size={14} class="opacity-0 -translate-x-1 group-hover:opacity-100 group-hover:translate-x-0 transition-all text-zinc-300" />
                {/if}
            </Link>
        {/each}
    </nav>

    <!-- Footer / User Profile -->
    <div class="p-4 border-t border-zinc-100 space-y-4">
        <div class="flex items-center gap-3 px-2 py-1">
            <div class="size-8 rounded-full bg-zinc-50 border border-zinc-200 flex items-center justify-center text-zinc-400 overflow-hidden ring-2 ring-zinc-100">
                <User size={16} />
            </div>
            <div class="flex-grow min-w-0">
                <p class="text-xs font-bold text-zinc-900 truncate">{user?.name || 'User'}</p>
                <p class="text-[9px] text-zinc-400 font-bold uppercase tracking-wider truncate">{role}</p>
            </div>
        </div>

        <Dialog.Root bind:open={isLoggingOut}>
            <Dialog.Trigger class="w-full flex items-center gap-3 px-3 py-2.5 text-zinc-500 hover:text-red-600 hover:bg-red-50 rounded-md transition-all text-xs font-bold uppercase tracking-widest">
                <LogOut size={16} />
                Sign out
            </Dialog.Trigger>
            <Dialog.Content class="max-w-[340px]">
                <Dialog.Header>
                    <Dialog.Title>Confirm Sign Out</Dialog.Title>
                    <Dialog.Description class="text-xs">
                        Are you sure you want to sign out? You will need to log back in to access your dashboard.
                    </Dialog.Description>
                </Dialog.Header>
                <Dialog.Footer class="mt-6 flex gap-2">
                    <Button variant="outline" class="flex-grow text-xs" onclick={() => isLoggingOut = false}>Cancel</Button>
                    <Link 
                        href={route('logout')} 
                        method="post" 
                        as="button"
                        class="flex-grow px-4 py-2 bg-red-600 text-white rounded-md text-xs font-bold uppercase tracking-wider hover:bg-red-700 transition-all text-center"
                    >
                        Log out
                    </Link>
                </Dialog.Footer>
            </Dialog.Content>
        </Dialog.Root>
    </div>
</aside>
