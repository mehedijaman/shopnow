import { execFileSync } from 'node:child_process'
import { defineConfig } from 'vite'
import laravel from 'laravel-vite-plugin'
import vue from '@vitejs/plugin-vue'

import Components from 'unplugin-vue-components/vite'
import AppComponentsResolver from './resources/js/Resolvers/AppComponentsResolver.js'

/**
 * Regenerates the JS translation bundles whenever a PHP lang file changes, so
 * `npm run dev` always serves the current strings. The bundles themselves are
 * imported modules, so Vite reloads the page on the write.
 */
const localizationExport = () => ({
    name: 'localization-export',
    apply: 'serve',
    configureServer(server) {
        let timer = null

        server.watcher.on('change', (file) => {
            if (!/\/lang\/.+\.php$/.test(file)) {
                return
            }

            clearTimeout(timer)

            timer = setTimeout(() => {
                try {
                    execFileSync(
                        'php',
                        ['artisan', 'localization:export', '--surface=all'],
                        { stdio: 'inherit' }
                    )
                } catch (error) {
                    console.error('localization:export failed', error)
                }
            }, 250)
        })
    }
})

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/js/app.js',
                'resources-site/js/index-app.js',
                'resources-site/js/blog-app.js',
                'resources-site/css/site.css'
            ],
            refresh: [
                'resources/**/*',
                'resources-site/**/*',
                'modules/**/views/**/*.blade.php'
            ]
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false
                }
            }
        }),
        Components({
            resolvers: [AppComponentsResolver]
        }),
        localizationExport()
    ],
    resolve: {
        alias: {
            '@resources': '/resources',
            '@resourcesSite': '/resources-site'
        }
    }
})
