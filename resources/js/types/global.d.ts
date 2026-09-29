/// <reference types="vite/client" />

// Vite's import.meta.glob type
interface ImportMeta {
    glob(pattern: string, options?: { eager?: boolean }): Record<string, any>;
}

// Vue SFC type (for .vue files when using Vue kit)
declare module '*.vue' {
    import { DefineComponent } from 'vue';
    const component: DefineComponent<{}, {}, any>;
    export default component;
}
