import { createVueApp } from './create-vue-app.js'
import 'flowbite'
import NavBar from './Components/NavBar.vue'
import ProductCard from './Components/ProductCard.vue'
import ProductDetailsCard from './Components/ProductDetailsCard.vue'

createVueApp({
    NavBar,
    ProductCard,
    ProductDetailsCard
}).mount('#app')
