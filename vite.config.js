import tailwindcss from '@tailwindcss/vite';
import vue from '@vitejs/plugin-vue';
import laravel from 'laravel-vite-plugin';
import path from 'path';
import { defineConfig } from 'vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        tailwindcss(),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
    ],
    server: {
        proxy: {
            '/api': 'http://localhost:8000'
        }
    },
    resolve: {
        alias: {
            '@': path.resolve(__dirname, './resources/js'),
            'vue': 'vue/dist/vue.esm-bundler.js',
        },
    },
    optimizeDeps: {
        include: ['element-plus']
    },
    build: {
        assetsInlineLimit: 0,
        rollupOptions: {
            output: {
                assetFileNames: 'assets/[name][extname]'
            }
        }
    }
});
