<script>
	import { Slider as SliderPrimitive } from "bits-ui";
	import { cn } from "@/utils.js";

	let {
		ref = $bindable(null),
		value = $bindable(),
		orientation = "horizontal",
		class: className,
		...restProps
	} = $props();
</script>

<!--
Discriminated Unions + Destructing (required for bindable) do not
get along, so we shut typescript up by casting `value` to `never`.
-->
<SliderPrimitive.Root
	bind:ref
	bind:value={value}
	data-slot="slider"
	{orientation}
	class={cn(
		"data-vertical:min-h-40 relative flex w-full touch-none items-center select-none data-disabled:opacity-50 data-vertical:h-full data-vertical:w-auto data-vertical:flex-col",
		className
	)}
	{...restProps}
>
	{#snippet children({ thumbItems })}
		<span
			data-slot="slider-track"
			data-orientation={orientation}
			class={cn(
				"bg-zinc-200 dark:bg-zinc-800 rounded-full relative grow overflow-hidden",
				orientation === "horizontal" ? "h-2 w-full" : "h-full w-2"
			)}
		>
			<SliderPrimitive.Range
				data-slot="slider-range"
				class={cn(
					"bg-primary absolute select-none h-full w-full"
				)}
			/>
		</span>
		{#each thumbItems as thumb (thumb)}
			<SliderPrimitive.Thumb
				data-slot="slider-thumb"
				index={thumb.index}
				class="border-white dark:border-zinc-950 shadow-lg ring-primary/20 relative size-5 rounded-full border-2 bg-primary transition-all hover:scale-110 hover:ring-4 focus-visible:ring-4 focus-visible:outline-hidden active:scale-95 block shrink-0 select-none disabled:pointer-events-none disabled:opacity-50 cursor-grab active:cursor-grabbing"
			/>
		{/each}
	{/snippet}
</SliderPrimitive.Root>