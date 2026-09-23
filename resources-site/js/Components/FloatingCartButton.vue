<template>
    <a href="/cart" class="fixed bottom-24 right-6 z-50 flex items-center gap-2 rounded-full border-2 border-skin-primary-9 bg-white px-4 py-3 text-skin-primary-9 shadow-lg transition-colors hover:bg-primary-50">
        <i
            :class="
                cartStore.totalQuantity > 0
                    ? 'ri-shopping-cart-fill'
                    : 'ri-shopping-cart-line'
            "
            class="text-2xl"
        ></i>
        <span class="text-sm font-semibold">কার্টে যান</span>
        <span
            v-show="cartStore.totalQuantity > 0"
            class="absolute -top-1 -right-1 flex h-6 w-6 items-center justify-center rounded-full bg-red-500 px-1 py-0 text-xs text-white"
        >
            {{ cartStore.totalQuantity }}
        </span>
    </a>
</template>

<script setup>
import { useCartStore } from '../Stores/CartStore'

const cartStore = useCartStore()
</script>