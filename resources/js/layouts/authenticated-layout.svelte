<script>
    import AppSidebar from '@/components/app-sidebar.svelte';
    import Separator from '@/components/ui/separator/separator.svelte';
    import * as Sidebar from '@/components/ui/sidebar';

    let { breadcrumb, children } = $props();
</script>

<Sidebar.Provider>
    <AppSidebar />
    <Sidebar.Inset>
        <header
            class="bg-background sticky top-0 flex h-16 shrink-0 items-center gap-2 border-b px-4"
        >
            <div class="flex items-center gap-2 px-4">
                <Sidebar.Trigger class="-ml-1" />
                <Separator orientation="vertical" class="mr-2 data-[orientation=vertical]:h-4" />
                {@render breadcrumb?.()}
            </div>
        </header>
        <div class="flex flex-1 flex-col gap-4 p-4 pt-0">
            {@render children?.()}
        </div>
    </Sidebar.Inset>
</Sidebar.Provider>
