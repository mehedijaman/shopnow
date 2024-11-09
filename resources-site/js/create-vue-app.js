import { createApp } from 'vue/dist/vue.esm-bundler.js'
import { createPinia } from 'pinia'

export const createVueApp = (additionalComponents = {}) => {
    const app = createApp({
        components: {
            ...additionalComponents
        }
    })

    app.use(createPinia())

    import.meta.glob(['../images/**'])

    return app
}
