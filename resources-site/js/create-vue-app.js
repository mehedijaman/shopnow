import { createApp } from 'vue/dist/vue.esm-bundler.js'
import NavBar from './Components/NavBar.vue'
import { createPinia } from 'pinia'

export const createVueApp = (additionalComponents = {}) => {
    const app = createApp({
        components: {
            NavBar,
            ...additionalComponents
        }
    })

    app.use(createPinia())

    import.meta.glob(['../images/**'])

    return app
}
