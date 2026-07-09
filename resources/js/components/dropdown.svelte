<script>
    import { onMount, onDestroy } from "svelte";

    export let align = "right";
    export let width = "48";
    export let contentClasses = "py-1 bg-white dark:bg-gray-700";

    let open = false;

    // Width Class
    const widthClass = width === "48" ? "w-48" : "";

    // Alignment Class
    const alignmentClasses =
        align === "left"
            ? "ltr:origin-top-left rtl:origin-top-right start-0"
            : align === "right"
              ? "ltr:origin-top-right rtl:origin-top-left end-0"
              : "origin-top";

    // Close on Escape Key
    const closeOnEscape = (e) => {
        if (open && e.key === "Escape") {
            open = false;
        }
    };

    onMount(() => {
        document.addEventListener("keydown", closeOnEscape);
    });

    onDestroy(() => {
        document.removeEventListener("keydown", closeOnEscape);
    });
</script>

<div class="relative">
    <!-- Trigger Button -->
    <button onclick={() => (open = !open)}>
        <slot name="trigger" />
    </button>

    <!-- Full Screen Dropdown Overlay -->
    {#if open}
        <button
            class="fixed inset-0 z-40"
            onclick={() => (open = false)}
            title=""
        ></button>
    {/if}

    <!-- Dropdown Content -->
    {#if open}
        <div
            class={`absolute z-50 mt-2 rounded-md shadow-lg ${widthClass} ${alignmentClasses}`}
        >
            <div
                class="rounded-md ring-1 ring-black ring-opacity-5 {contentClasses}"
            >
                <slot name="content" />
            </div>
        </div>
    {/if}
</div>

<style>
    /* Add any additional styles if needed */
</style>
