import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import type { DefineComponent } from 'vue';
import { createApp, h } from 'vue';
import '../css/app.css';

const appName = (window as any).appName || import.meta.env.VITE_APP_NAME || 'Laravel';

import { wTrans } from './Core/i18n';

createInertiaApp({
    title: (title) => (title ? `${title} - ${appName}` : appName),
    resolve: (name) => resolvePageComponent(`./pages/${name}.vue`, import.meta.glob<DefineComponent>('./pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) });

        app.config.globalProperties.__ = wTrans;
        app.config.globalProperties.trans = wTrans;

        app.use(plugin)
            .mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});
