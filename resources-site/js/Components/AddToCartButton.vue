<template>
    <div class="mt-2 inline-flex items-center">
        <button
            @click="decreaseQuantity"
            class="inline-flex items-center rounded-l border border-r border-gray-200 bg-white px-2 py-1 text-gray-600 hover:bg-gray-100 active:bg-gray-200 disabled:opacity-50"
        >
            <i class="ri-subtract-line"></i>
        </button>
        <div
            class="inline-flex select-none items-center border-b border-t border-gray-100 bg-gray-100 px-4 py-1 text-gray-600 hover:bg-gray-100"
        >
            {{ quantity }}
        </div>
        <button
            @click="increaseQuantity"
            class="inline-flex items-center rounded-r border border-r border-gray-200 bg-white px-2 py-1 text-gray-600 hover:bg-gray-100 active:bg-gray-200 disabled:opacity-50"
        >
            <i class="ri-add-line"></i>
        </button>
    </div>

    <button
        @click="addToCart"
        class="mt-4 flex w-full items-center justify-center rounded bg-blue-500 px-4 py-2 text-white hover:bg-blue-600 active:bg-blue-700 disabled:opacity-50"
    >
        <i class="ri-shopping-cart-fill mr-2 text-xl"></i>
        Add to Cart
    </button>
</template>

<script setup>
import { ref } from 'vue'
import { useCartStore } from '../Stores/CartStore'

const cartStore = useCartStore()
const props = defineProps({
    product: {
        type: Object,
        required: true
    }
})

const item = {
    id: props.product.id,
    name: props.product.name,
    price: props.product.sale_price
        ? props.product.sale_price
        : props.product.price,
    image_url: props.product.image_url
}

const quantity = ref(1)

const increaseQuantity = () => {
    quantity.value++
}
const decreaseQuantity = () => {
    if (quantity.value > 1) {
        quantity.value--
    }
}

function addToCart() {
    cartStore.addItem(item, quantity.value)
}
</script>
