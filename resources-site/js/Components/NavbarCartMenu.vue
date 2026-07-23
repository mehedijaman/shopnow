<template>
    <span class="relative">
        <a href="/cart">
            <i
                :class="
                    cartStore.totalQuantity > 0
                        ? 'ri-shopping-cart-fill'
                        : 'ri-shopping-cart-line'
                "
                class="cursor-pointer text-2xl hover:text-skin-primary-9"
            ></i>
            <span
                v-show="cartStore.totalQuantity > 0"
                class="absolute -right-2 -top-1.5 flex h-4 min-w-4 items-center justify-center rounded-full bg-primary-600 px-1 text-[11px] font-bold leading-none text-white shadow-sm"
            >
                {{ cartStore.totalQuantity }}
            </span>
        </a>
    </span>
</template>

<script setup>
import { useCartStore } from '../Stores/CartStore'

const cartStore = useCartStore()
</script>
