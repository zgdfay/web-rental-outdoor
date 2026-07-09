<script>
    import { Toaster } from '@/components/ui/sonner/index.js';
    import { page } from '@inertiajs/svelte';
    import { toast } from 'svelte-sonner';

    let { children } = $props();

    page.subscribe((data) => {
        if (data.props.flash.message) {
            toast(data.props.flash.message);
        }
    });
</script>

<Toaster position="top-right" />
{@render children?.()}
