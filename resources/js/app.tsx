import { createInertiaApp } from '@inertiajs/react';
import type { ComponentType } from 'react';
import { createRoot } from 'react-dom/client';
import { resolvePageComponent } from './utils/resolvePageComponent';
import '../css/app.css';

createInertiaApp({
    resolve: (name: string) => {
        const pages = import.meta.glob('./Pages/**/*.tsx', { eager: true });
        return resolvePageComponent(pages, `./Pages/${name}.tsx`);
    },
    setup({ el, App, props }: { el: HTMLElement; App: ComponentType<any>; props: any }) {
        createRoot(el).render(<App {...props} />);
    },
    progress: {
        color: '#4f46e5',
        showSpinner: true,
    },
});
