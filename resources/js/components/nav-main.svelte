<script>
    import * as Collapsible from '@/components/ui/collapsible/index.js';
    import * as Sidebar from '@/components/ui/sidebar/index.js';
    import { Link } from '@inertiajs/svelte';
    import ChevronRightIcon from '@lucide/svelte/icons/chevron-right';

    let { items } = $props();
</script>

<Sidebar.Group>
    <Sidebar.GroupLabel>Platform</Sidebar.GroupLabel>
    <Sidebar.Menu>
        {#each items as mainItem (mainItem.title)}
            <Collapsible.Root open={mainItem.isActive}>
                {#snippet child({ props })}
                    <Sidebar.MenuItem {...props}>
                        <Sidebar.MenuButton tooltipContent={mainItem.title}>
                            {#snippet child({ props })}
                                <Link href={mainItem.url} {...props}>
                                    <mainItem.icon />
                                    <span>{mainItem.title}</span>
                                </Link>
                            {/snippet}
                        </Sidebar.MenuButton>
                        {#if mainItem.items?.length}
                            <Collapsible.Trigger class="w-full">
                                {#snippet child({ props })}
                                    <Sidebar.MenuAction
                                        {...props}
                                        class="data-[state=open]:rotate-90"
                                    >
                                        <ChevronRightIcon />
                                        <span class="sr-only">Toggle</span>
                                    </Sidebar.MenuAction>
                                {/snippet}
                            </Collapsible.Trigger>
                            <Collapsible.Content>
                                <Sidebar.MenuSub>
                                    {#each mainItem.items as subItem (subItem.title)}
                                        <Sidebar.MenuSubItem>
                                            <Link href={subItem.url}>
                                                <span>{subItem.title}</span>
                                            </Link>
                                        </Sidebar.MenuSubItem>
                                    {/each}
                                </Sidebar.MenuSub>
                            </Collapsible.Content>
                        {/if}
                    </Sidebar.MenuItem>
                {/snippet}
            </Collapsible.Root>
        {/each}
    </Sidebar.Menu>
</Sidebar.Group>
