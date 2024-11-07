import { createApp } from 'vue/dist/vue.esm-bundler.js'
export const createVueApp = (additionalComponents = {}) => {
    const app = createApp({
        components: {
            ...additionalComponents
        }
    })

    import.meta.glob(['../images/**'])

    return app
}
