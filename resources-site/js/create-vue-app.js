import { createApp } from 'vue/dist/vue.esm-bundler.js'
import { createPinia } from 'pinia'
import { useCartStore } from './Stores/CartStore'
import Translations, { loadTranslations } from './Plugins/Translations'

export const createVueApp = async (additionalComponents = {}) => {
    await loadTranslations()

    const app = createApp({
        components: {
            ...additionalComponents
        },
        created() {
            const cartStore = useCartStore()
            cartStore.fetchCart()
            window.toggleSearchModal = () => {}
        }
    })

    app.use(createPinia())
    app.use(Translations)

    import.meta.glob(['../images/**'])

    return app
}
