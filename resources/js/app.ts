import { createInertiaApp } from '@inertiajs/vue3';
import { createSSRApp, h } from 'vue';
import { resolvePageComponent } from './utils/resolvePageComponent';
import '../css/app.css';

createInertiaApp({
    resolve: (name: string) => {
        const pages = import.meta.glob('./Pages/**/*.vue', { eager: true });
        return resolvePageComponent(pages, `./Pages/${name}.vue`);
    },
    setup({ el, App, props, plugin }: { el: HTMLElement; App: any; props: any; plugin: any }) {
        createSSRApp({ render: () => h(App, props) })
            .use(plugin)
            .mount(el);
    },
    progress: {
        color: '#4f46e5',
        showSpinner: true,
    },
});
