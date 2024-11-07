import { createVueApp } from './create-vue-app.js'
import AppAuthLogo from '@/Components/Auth/AppAuthLogo.vue'
import ProductCard from './Components/ProductCard.vue'
import NavBar from './Components/NavBar.vue'
import { createPinia } from 'pinia'
import ShoppingCart from './Components/ShoppingCart.vue'

createVueApp({
    AppAuthLogo,
    NavBar,
    ProductCard,
    ShoppingCart
})
    .use(createPinia())
    .mount('#app')
