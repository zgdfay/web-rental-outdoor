import '../css/app.css';
import './bootstrap';

import { createInertiaApp } from '@inertiajs/svelte';
import { mount } from 'svelte';
import Base from './layouts/base.svelte';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => {
        const pages = import.meta.glob('./pages/**/*.svelte', { eager: true })
        let page = pages[`./pages/${name}.svelte`]


        if (!page?.default) {
            // TODO: handle not found
            return { default: page?.default, layout: page?.layout ? [Base, page.layout] : Base }
        }

        return { default: page.default, layout: page.layout ? [Base, page.layout] : Base }
    },
    setup({ el, App, props }) {
        mount(App, { target: el, props })
    },
    progress: {
        color: '#4B5563',
    },
});
