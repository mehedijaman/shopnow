import { createVueApp } from './create-vue-app.js'
import ProductCard from './Components/ProductCard.vue'
import ProductDetailsCard from './Components/ProductDetailsCard.vue'

createVueApp({
    ProductCard,
    ProductDetailsCard
}).mount('#app')
