import '../css/app.css'
import 'remixicon/fonts/remixicon.css'

import { createApp, h } from 'vue'
import { createInertiaApp } from '@inertiajs/vue3'
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers'
import { ZiggyVue } from '../../vendor/tightenco/ziggy/dist/index.js'
import { createPinia } from 'pinia'

const appName = import.meta.env.VITE_APP_NAME || 'ShopNow'

// global components
import { Link } from '@inertiajs/vue3'
import Layout from './Layouts/AuthenticatedLayout.vue'

import Translations, { loadTranslations } from '@/Plugins/Translations'

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => {
        const page = resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob('./Pages/**/*.vue')
        )

        page.then((module) => {
            module.default.layout = module.default.layout || Layout
        })

        return page
    },
    setup: async ({ el, App, props, plugin }) => {
        await loadTranslations()

        return createApp({ render: () => h(App, props) })
            .use(createPinia())
            .use(plugin)
            .use(ZiggyVue, Ziggy)
            .use(Translations)
            .component('Link', Link)
            .mount(el)
    },
    progress: {
        color: '#3e63dd'
    }
})
