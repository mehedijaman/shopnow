import { createVueApp } from './create-vue-app.js'
import BlogToolbar from './Components/Blog/BlogToolbar.vue'
import NavbarCartMenu from './Components/NavbarCartMenu.vue'
import ShopSearch from './Components/ShopSearch.vue'
import WhatsappFloatingButton from './Components/WhatsappFloatingButton.vue'
import MobileBottomNav from './Components/MobileBottomNav.vue'

createVueApp({
    BlogToolbar,
    NavbarCartMenu,
    ShopSearch,
    WhatsappFloatingButton,
    MobileBottomNav
}).mount('#app')
