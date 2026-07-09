import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { svelte } from '@sveltejs/vite-plugin-svelte';
import { sveltePreprocess } from "svelte-preprocess";
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/js/app.js', 'resources/css/app.css'],
            ssr: 'resources/js/ssr.js',
            refresh: true,
        }),
        svelte({ preprocess: [sveltePreprocess({ typescript: false })], }),
        tailwindcss(),
    ],
});
