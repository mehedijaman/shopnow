import { createVueApp } from './create-vue-app.js'
import BlogToolbar from './Components/Blog/BlogToolbar.vue'
import NavbarCartMenu from './Components/NavbarCartMenu.vue'

createVueApp({
    BlogToolbar,
    NavbarCartMenu
}).mount('#app')
