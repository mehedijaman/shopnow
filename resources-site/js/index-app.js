import { createVueApp } from './create-vue-app.js'
import ShoppingCart from './Components/ShoppingCart.vue'
import NavbarCartMenu from './Components/NavbarCartMenu.vue'
import AddToCartButton from './Components/AddToCartButton.vue'
import ShopSearch from './Components/ShopSearch.vue'

createVueApp({
    ShopSearch,
    ShoppingCart,
    NavbarCartMenu,
    AddToCartButton
}).mount('#app')
