import { createVueApp } from './create-vue-app.js'
import 'flowbite'
import NavBar from './Components/NavBar.vue'
import ProductCard from './Components/ProductCard.vue'

createVueApp({
    NavBar,
    ProductCard
}).mount('#app')
