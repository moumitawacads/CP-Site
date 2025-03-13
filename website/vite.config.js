import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
            ],
            refresh: true,
            base: process.env.ASSET_URL || '/',
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
    ],
    resolve: {
        alias: {
            vue: 'vue/dist/vue.esm-bundler.js',
            'path': 'path-browserify',
        },
    },
    optimizeDeps: {
        include: [
        '@ckeditor/ckeditor5-build-classic',
        '@ckeditor/ckeditor5-vue',
        ],
    },
    build: {
        outDir: 'public/build', // Ensure build output goes to public/build
        emptyOutDir: true, // Cleans the output directory before building
        manifest: true, 
    },
});
