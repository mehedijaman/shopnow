import { createVueApp } from './create-vue-app.js'
import ShoppingCart from './Components/ShoppingCart.vue'
import NavbarCartMenu from './Components/NavbarCartMenu.vue'
import AddToCartButton from './Components/AddToCartButton.vue'

createVueApp({
    ShoppingCart,
    NavbarCartMenu,
    AddToCartButton
}).mount('#app')
