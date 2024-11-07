<template>
    <div class="w-80 rounded border border-gray-300 bg-white shadow">
        <div
            class="flex h-48 w-full flex-col justify-between bg-gray-200 bg-cover bg-center p-4"
            :style="{ backgroundImage: `url(${product.image_url})` }"
        >
            <div class="flex justify-between">
                <span></span>
                <button class="text-white hover:text-blue-500">
                    <i class="ri-heart-line text-2xl"></i>
                </button>
            </div>
            <div class="flex justify-between">
                <span
                    class="select-none rounded border border-green-500 bg-green-50 p-0.5 text-xs font-medium uppercase text-green-700"
                >
                    <span v-if="product.active"> Available </span>
                    <span v-else>Not Available </span>
                </span>

                <span
                    v-if="product.featured"
                    class="select-none rounded border border-green-500 bg-green-50 p-0.5 text-xs font-medium uppercase text-green-700"
                >
                    Featured
                </span>
            </div>
        </div>
        <div class="flex flex-col items-center p-4">
            <p class="text-center text-xs font-light text-gray-400">
                {{ product.category?.name }}
            </p>
            <h1
                class="mt-1 text-center text-gray-800 hover:text-blue-500 hover:underline"
            >
                <a :href="`/products/${product.id}`">
                    {{ product.name }}
                </a>
            </h1>

            <div class="mt-1 text-center text-gray-800">
                <span class="mr-2 text-lg">
                    <span v-if="product.sale_price"
                        >{{ product.sale_price }}
                    </span>
                    <span v-else>{{ product.price }} </span>
                    BDT
                </span>
                <span
                    v-if="product.sale_price"
                    class="text-sm text-gray-500 line-through"
                >
                    {{ product.price }} BDT
                </span>
            </div>
            <!-- Quantity Counter -->

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
                Add to Cart
                <i class="ri-shopping-cart-fill ml-2 text-xl"></i>
            </button>
        </div>
    </div>
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
