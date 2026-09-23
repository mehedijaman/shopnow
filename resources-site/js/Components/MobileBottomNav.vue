<template>
    <nav
        class="fixed bottom-0 left-0 right-0 z-50 border-t border-skin-neutral-4 bg-white"
        aria-label="Mobile navigation"
    >
        <div class="mx-auto flex w-full max-w-7xl justify-around">
            <a href="/" class="flex flex-col items-center justify-center py-2 text-center">
                <i class="ri-home-5-line text-xl text-skin-neutral-9"></i>
                <span class="mt-0.5 text-[10px] font-medium text-skin-neutral-9">হোম</span>
            </a>
            <a href="/shop" class="flex flex-col items-center justify-center py-2 text-center">
                <i class="ri-shopping-bag-line text-xl text-skin-neutral-9"></i>
                <span class="mt-0.5 text-[10px] font-medium text-skin-neutral-9">Shop</span>
            </a>
            <a href="/cart" class="flex flex-col items-center justify-center py-2 text-center relative">
                <i :class="cartStore.totalQuantity > 0 ? 'ri-shopping-cart-fill' : 'ri-shopping-cart-line'" class="cursor-pointer text-xl text-skin-neutral-9"></i>
                <span v-show="cartStore.totalQuantity > 0" class="absolute -top-1 right-0 rounded-full bg-red-500 px-1 py-0 text-xs text-white">{{ cartStore.totalQuantity }}</span>
                <span class="mt-0.5 text-[10px] font-medium text-skin-neutral-9">কার্ট</span>
            </a>
            <a href="/shop/search" class="flex flex-col items-center justify-center py-2 text-center">
                <i class="ri-search-2-line text-xl text-skin-neutral-9"></i>
                <span class="mt-0.5 text-[10px] font-medium text-skin-neutral-9">অনুসন্ধান</span>
            </a>
            <a :href="props.isLoggedIn ? '/account/profile' : '/login'" class="flex flex-col items-center justify-center py-2 text-center">
                <i class="ri-user-line text-xl text-skin-neutral-9"></i>
                <span class="mt-0.5 text-[10px] font-medium text-skin-neutral-9">অ্যাকাউন্ট</span>
            </a>
        </div>
    </nav>
</template>

<script setup>
import { useCartStore } from '../Stores/CartStore'
import { defineProps } from 'vue'

const props = defineProps({
    isLoggedIn: {
        type: Boolean,
        default: false,
    },
})

const cartStore = useCartStore()
</script>
