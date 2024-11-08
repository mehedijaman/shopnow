import { createVueApp } from './create-vue-app.js'
import ProductCard from './Components/ProductCard.vue'
import ShoppingCart from './Components/ShoppingCart.vue'
import ProductDetails from './Components/ProductDetails.vue'

createVueApp({
    ProductCard,
    ProductDetails,
    ShoppingCart
}).mount('#app')
