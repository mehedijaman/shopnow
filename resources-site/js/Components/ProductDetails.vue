<template>
    <div class="w-full bg-gray-100">
        <div class="-mx-4 flex flex-wrap">
            <!-- Product Images -->
            <div class="mb-8 w-full px-4 md:w-1/2">
                <img
                    :src="mainImage"
                    alt="Product"
                    class="mb-4 h-fit w-full rounded-lg shadow-md"
                    id="mainImage"
                />
                <div class="flex justify-center gap-4 overflow-x-auto py-4">
                    <img
                        :src="mainImage"
                        alt="Thumbnail 1"
                        class="size-16 cursor-pointer rounded-md object-cover opacity-60 transition duration-300 hover:opacity-100 sm:size-20"
                        onclick="changeImage(this.src)"
                    />
                    <img
                        src="https://images.unsplash.com/photo-1484704849700-f032a568e944?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w0NzEyNjZ8MHwxfHNlYXJjaHw0fHxoZWFkcGhvbmV8ZW58MHwwfHx8MTcyMTMwMzY5MHww&ixlib=rb-4.0.3&q=80&w=1080"
                        alt="Thumbnail 2"
                        class="size-16 cursor-pointer rounded-md object-cover opacity-60 transition duration-300 hover:opacity-100 sm:size-20"
                        onclick="changeImage(this.src)"
                    />
                    <img
                        src="https://images.unsplash.com/photo-1496957961599-e35b69ef5d7c?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w0NzEyNjZ8MHwxfHNlYXJjaHw4fHxoZWFkcGhvbmV8ZW58MHwwfHx8MTcyMTMwMzY5MHww&ixlib=rb-4.0.3&q=80&w=1080"
                        alt="Thumbnail 3"
                        class="size-16 cursor-pointer rounded-md object-cover opacity-60 transition duration-300 hover:opacity-100 sm:size-20"
                        onclick="changeImage(this.src)"
                    />
                    <img
                        src="https://images.unsplash.com/photo-1528148343865-51218c4a13e6?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w0NzEyNjZ8MHwxfHNlYXJjaHwzfHxoZWFkcGhvbmV8ZW58MHwwfHx8MTcyMTMwMzY5MHww&ixlib=rb-4.0.3&q=80&w=1080"
                        alt="Thumbnail 4"
                        class="size-16 cursor-pointer rounded-md object-cover opacity-60 transition duration-300 hover:opacity-100 sm:size-20"
                        onclick="changeImage(this.src)"
                    />
                </div>
            </div>

            <!-- Product Details -->
            <div class="w-full px-4 md:w-1/2">
                <h2 class="mb-2 text-3xl font-bold">
                    {{ product.name }}
                </h2>
                <p class="mb-4 text-gray-600">
                    Category: {{ product.category?.name }}
                </p>
                <div class="mb-4">
                    <span class="mr-2 text-2xl font-bold">
                        {{ product.sale_price }} BDT
                    </span>
                    <span class="text-gray-500 line-through">
                        {{ product.price }} BDT
                    </span>
                </div>

                <div class="mb-4 flex items-center">
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 24 24"
                        fill="currentColor"
                        class="size-6 text-yellow-500"
                    >
                        <path
                            fill-rule="evenodd"
                            d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.006 5.404.434c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.434 2.082-5.005Z"
                            clip-rule="evenodd"
                        />
                    </svg>
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 24 24"
                        fill="currentColor"
                        class="size-6 text-yellow-500"
                    >
                        <path
                            fill-rule="evenodd"
                            d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.006 5.404.434c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.434 2.082-5.005Z"
                            clip-rule="evenodd"
                        />
                    </svg>
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 24 24"
                        fill="currentColor"
                        class="size-6 text-yellow-500"
                    >
                        <path
                            fill-rule="evenodd"
                            d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.006 5.404.434c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.434 2.082-5.005Z"
                            clip-rule="evenodd"
                        />
                    </svg>
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 24 24"
                        fill="currentColor"
                        class="size-6 text-yellow-500"
                    >
                        <path
                            fill-rule="evenodd"
                            d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.006 5.404.434c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.434 2.082-5.005Z"
                            clip-rule="evenodd"
                        />
                    </svg>
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 24 24"
                        fill="currentColor"
                        class="size-6 text-yellow-500"
                    >
                        <path
                            fill-rule="evenodd"
                            d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.006 5.404.434c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.434 2.082-5.005Z"
                            clip-rule="evenodd"
                        />
                    </svg>
                    <span class="ml-2 text-gray-600"> 4.5 (120 reviews) </span>
                </div>

                <p class="mb-6 text-gray-700">
                    {{ product.summary }}
                </p>

                <div class="mb-6">
                    <label
                        for="quantity"
                        class="mb-1 block text-sm font-medium text-gray-700"
                    >
                        Quantity:
                    </label>
                    <div class="mt-2 inline-flex items-center">
                        <button
                            class="inline-flex items-center rounded-l border border-r border-gray-200 bg-white px-2 py-1 text-gray-600 hover:bg-gray-100 active:bg-gray-200 disabled:opacity-50"
                        >
                            <i class="ri-subtract-line"></i>
                        </button>
                        <div
                            class="inline-flex select-none items-center border-b border-t border-gray-100 bg-gray-100 px-4 py-1 text-gray-600 hover:bg-gray-100"
                        >
                            2
                        </div>
                        <button
                            class="inline-flex items-center rounded-r border border-r border-gray-200 bg-white px-2 py-1 text-gray-600 hover:bg-gray-100 active:bg-gray-200 disabled:opacity-50"
                        >
                            <i class="ri-add-line"></i>
                        </button>
                    </div>
                </div>

                <div class="mb-6 flex space-x-4">
                    <button
                        class="flex items-center gap-2 rounded-md bg-indigo-600 px-6 py-2 text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                    >
                        <i class="ri-shopping-cart-line text-2xl"></i>
                        Add to Cart
                    </button>
                    <button
                        class="flex items-center gap-2 rounded-md bg-gray-200 px-6 py-2 text-gray-800 hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2"
                    >
                        <i class="ri-heart-line text-2xl"></i>
                        Wishlist
                    </button>
                </div>

                <div>
                    <h3 class="mb-2 text-lg font-semibold">Key Features:</h3>
                    <ul class="list-inside list-disc text-gray-700">
                        <li>Industry-leading noise cancellation</li>
                        <li>30-hour battery life</li>
                        <li>Touch sensor controls</li>
                        <li>Speak-to-chat technology</li>
                    </ul>
                </div>
            </div>
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

// Define a reactive variable for the main image source
const mainImage = ref('default-image.jpg')

// Function to change the main image source
function changeImage(src) {
    mainImage.value = src
}
</script>
