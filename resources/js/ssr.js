import { createInertiaApp } from '@inertiajs/svelte';
import createServer from "@inertiajs/svelte/server";

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createServer((page) =>
    createInertiaApp({
        page,
        title: (title) => `${title} - ${appName}`,
        resolve: (name) => {
            const pages = import.meta.glob('./pages/**/*.svelte', { eager: true })
            let page = pages[`./pages/${name}.svelte`]

            if (!page.default) {
                // TODO: handle not found
                return { default: page?.default, layout: page.layout ? [Base, page.layout] : Base }
            }
            return { default: page.default, layout: page.layout ? [Base, page.layout] : Base }
        },
    }),
);
