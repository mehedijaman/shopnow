<template>
    <div class="max-w-8xl mx-auto grid grid-cols-1 gap-4 md:grid-cols-3">
        <div class="rounded-md p-4 md:col-span-2">
            <h2 class="text-2xl font-bold text-gray-800">Cart</h2>
            <hr class="mb-8 mt-4 border-gray-300" />

            <div class="space-y-4">
                <div
                    v-for="item in cartStore.items"
                    :key="item.id"
                    class="grid grid-cols-3 items-center gap-4"
                >
                    <div class="col-span-2 flex items-center gap-4">
                        <div class="h-24 w-24 shrink-0 rounded-md bg-white p-2">
                            <img
                                :src="item.item.image_url"
                                class="h-full w-full object-contain"
                            />
                        </div>

                        <div>
                            <h3 class="text-base font-bold text-gray-800">
                                {{ item.item.name }}
                            </h3>
                            <h6
                                @click="cartStore.removeItem(item)"
                                class="mt-0.5 cursor-pointer text-xs text-red-500"
                            >
                                Remove
                            </h6>

                            <div class="mt-4 flex gap-4">
                                <div>
                                    <button
                                        type="button"
                                        class="flex items-center rounded-md border border-gray-300 bg-transparent px-2.5 py-1.5 text-xs text-gray-800 outline-none"
                                    >
                                        <i
                                            @click="
                                                cartStore.decreaseQuantity(item)
                                            "
                                            class="ri-subtract-fill text-lg font-bold"
                                        ></i>

                                        <span class="mx-2.5">
                                            {{ item.quantity }}
                                        </span>
                                        <i
                                            @click="
                                                cartStore.increaseQuantity(item)
                                            "
                                            class="ri-add-line text-lg font-bold"
                                        ></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="ml-auto">
                        <h4 class="text-base font-bold text-gray-800">
                            {{ item.item.price * item.quantity }}
                        </h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="rounded-md bg-gray-100 p-4 md:sticky">
            <div class="flex overflow-hidden rounded-md border border-blue-600">
                <input
                    type="email"
                    placeholder="Promo code"
                    class="w-full bg-white px-4 py-2.5 text-sm text-gray-600 outline-none"
                />
                <button
                    type="button"
                    class="flex items-center justify-center bg-blue-600 px-4 text-sm font-semibold tracking-wide text-white hover:bg-blue-700"
                >
                    Apply
                </button>
            </div>

            <ul class="mt-8 space-y-4 text-gray-800">
                <li class="flex flex-wrap gap-4 text-base">
                    Discount
                    <span class="ml-auto font-bold">$0.00</span>
                </li>
                <li class="flex flex-wrap gap-4 text-base">
                    Shipping
                    <span class="ml-auto font-bold">$2.00</span>
                </li>
                <li class="flex flex-wrap gap-4 text-base">
                    Tax
                    <span class="ml-auto font-bold">$4.00</span>
                </li>
                <li class="flex flex-wrap gap-4 text-base font-bold">
                    Total
                    <span class="ml-auto">$52.00</span>
                </li>
            </ul>

            <div class="mt-8 space-y-2">
                <button
                    type="button"
                    class="w-full rounded-md bg-blue-600 px-4 py-2.5 text-sm font-semibold tracking-wide text-white hover:bg-blue-700"
                >
                    Checkout
                </button>
                <button
                    type="button"
                    class="w-full rounded-md border border-gray-300 bg-transparent px-4 py-2.5 text-sm font-semibold tracking-wide text-gray-800"
                >
                    Continue Shopping
                </button>
            </div>
        </div>
    </div>
</template>
<script setup>
import { useCartStore } from '../Stores/CartStore'

const cartStore = useCartStore()
</script>
