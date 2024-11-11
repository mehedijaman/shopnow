import { createVueApp } from './create-vue-app.js'
import ShoppingCart from './Components/ShoppingCart.vue'
import NavbarCartMenu from './Components/NavbarCartMenu.vue'
import AddToCartButton from './Components/AddToCartButton.vue'
import ShopSearch from './Components/ShopSearch.vue'
import CheckoutForm from './Components/CheckoutForm.vue'

createVueApp({
    ShopSearch,
    ShoppingCart,
    NavbarCartMenu,
    AddToCartButton,
    CheckoutForm
}).mount('#app')
