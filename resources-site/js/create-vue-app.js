import { createApp } from 'vue/dist/vue.esm-bundler.js'
import NavBar from './Components/CartIcon.vue'
import CartIcon from './Components/CartIcon.vue'
import { createPinia } from 'pinia'

export const createVueApp = (additionalComponents = {}) => {
    const app = createApp({
        components: {
            NavBar,
            CartIcon,
            ...additionalComponents
        }
    })

    app.use(createPinia())

    import.meta.glob(['../images/**'])

    return app
}
