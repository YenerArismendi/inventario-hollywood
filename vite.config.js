import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
            // Forzar URLs relativas en producción
            publicDirectory: 'public',
        }),
        tailwindcss(),
    ],
    build: {
        outDir: 'public/build',
        manifest: true,
        rollupOptions: {
            output: {
                // Usa paths relativos para evitar mixed content
                assetFileNames: 'assets/[name]-[hash][extname]',
                chunkFileNames: 'assets/[name]-[hash].js',
                entryFileNames: 'assets/[name]-[hash].js',
            },
        },
    },
    server: {
        hmr: {
            protocol: 'wss', // útil si haces dev con HTTPS
        },
    },
});
