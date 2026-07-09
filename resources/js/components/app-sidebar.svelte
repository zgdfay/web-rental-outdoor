<script module>
    import BookOpenIcon from '@lucide/svelte/icons/book-open';
    import BotIcon from '@lucide/svelte/icons/bot';
    import LifeBuoyIcon from '@lucide/svelte/icons/life-buoy';
    import SendIcon from '@lucide/svelte/icons/send';
    import Settings2Icon from '@lucide/svelte/icons/settings-2';
    import SquareTerminalIcon from '@lucide/svelte/icons/square-terminal';

    const data = {
        user: {
            name: 'shadcn',
            email: 'm@example.com',
            avatar: '/avatars/shadcn.jpg',
        },
        navMain: [
            {
                title: 'Dashboard',
                url: route('dashboard'),
                icon: SquareTerminalIcon,
                isActive: true,
            },
            {
                title: 'Models',
                url: '#',
                icon: BotIcon,
                items: [
                    {
                        title: 'Genesis',
                        url: '#',
                    },
                    {
                        title: 'Explorer',
                        url: '#',
                    },
                    {
                        title: 'Quantum',
                        url: '#',
                    },
                ],
            },
            {
                title: 'Documentation',
                url: '#',
                icon: BookOpenIcon,
                items: [
                    {
                        title: 'Introduction',
                        url: '#',
                    },
                    {
                        title: 'Get Started',
                        url: '#',
                    },
                    {
                        title: 'Tutorials',
                        url: '#',
                    },
                    {
                        title: 'Changelog',
                        url: '#',
                    },
                ],
            },
            {
                title: 'Settings',
                url: route('profile.edit'),
                icon: Settings2Icon,
            },
        ],
        navSecondary: [
            {
                title: 'Support',
                url: '#',
                icon: LifeBuoyIcon,
            },
            {
                title: 'Feedback',
                url: '#',
                icon: SendIcon,
            },
        ],
    };
</script>

<script>
    import NavMain from './nav-main.svelte';
    import NavSecondary from './nav-secondary.svelte';
    import NavUser from './nav-user.svelte';
    import * as Sidebar from '@/components/ui/sidebar/index.js';
    import CommandIcon from '@lucide/svelte/icons/command';
    import { Link, page } from '@inertiajs/svelte';
    let { ref = $bindable(null), ...restProps } = $props();

    let user = $page.props.auth.user;
    data.user.name = user.name;
    data.user.email = user.email;
</script>

<Sidebar.Root bind:ref variant="inset" {...restProps}>
    <Sidebar.Header>
        <Sidebar.Menu>
            <Sidebar.MenuItem>
                <Sidebar.MenuButton size="lg">
                    {#snippet child({ props })}
                        <Link href="##" {...props}>
                            <div
                                class="bg-sidebar-primary text-sidebar-primary-foreground flex aspect-square size-8 items-center justify-center rounded-lg"
                            >
                                <CommandIcon class="size-4" />
                            </div>
                            <div class="grid flex-1 text-left text-sm leading-tight">
                                <span class="truncate font-medium">Acme Inc</span>
                                <span class="truncate text-xs">Enterprise</span>
                            </div>
                        </Link>
                    {/snippet}
                </Sidebar.MenuButton>
            </Sidebar.MenuItem>
        </Sidebar.Menu>
    </Sidebar.Header>
    <Sidebar.Content>
        <NavMain items={data.navMain} />
        <NavSecondary items={data.navSecondary} class="mt-auto" />
    </Sidebar.Content>
    <Sidebar.Footer>
        <NavUser user={data.user} />
    </Sidebar.Footer>
</Sidebar.Root>
