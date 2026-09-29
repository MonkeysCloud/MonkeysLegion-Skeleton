import { defineConfig } from 'vite';
import react from '@vitejs/plugin-react';
import { resolve, dirname } from 'path';
import { fileURLToPath } from 'url';

const __dirname = dirname(fileURLToPath(import.meta.url));

export default defineConfig({
    plugins: [react()],
    resolve: {
        alias: {
            '@': resolve(__dirname, 'resources/js'),
        },
    },
    server: {
        host: '0.0.0.0',
        port: 5173,
        strictPort: true,
        hmr: {
            host: 'localhost',
            port: 5173,
        },
        // In Docker, proxy non-asset requests to the PHP app
        proxy: {
            '/build': {
                target: 'http://localhost:8000',
                changeOrigin: true,
            },
        },
    },
    publicDir: false,
    build: {
        // Must match config/vite.mlc: build_path = "public/build"
        outDir: 'public/build',
        manifest: 'manifest.json',
        emptyOutDir: true,
        rollupOptions: {
            input: 'resources/js/app.tsx',
        },
    },
});
