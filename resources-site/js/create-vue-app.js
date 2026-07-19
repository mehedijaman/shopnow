import { createApp } from 'vue/dist/vue.esm-bundler.js'
import { createPinia } from 'pinia'
import { useCartStore } from './Stores/CartStore'

export const createVueApp = (additionalComponents = {}) => {
    const app = createApp({
        components: {
            ...additionalComponents
        },
        created() {
            const cartStore = useCartStore()
            cartStore.fetchCart()
        }
    })

    app.use(createPinia())

    import.meta.glob(['../images/**'])

    return app
}
