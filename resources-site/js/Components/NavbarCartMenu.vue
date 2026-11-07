<template>
    <a
        href="/cart"
        class="text-site-text hover:text-site-link flex min-w-12 flex-col items-center justify-center gap-0.5 rounded-lg px-1.5 py-1 transition"
        :aria-label="__('site.nav.cart')"
    >
        <span class="relative">
            <i
                :class="
                    cartStore.totalQuantity > 0
                        ? 'ri-shopping-cart-fill'
                        : 'ri-shopping-cart-line'
                "
                class="block text-2xl leading-none"
                aria-hidden="true"
            ></i>
            <span
                v-if="cartStore.totalQuantity > 0"
                class="bg-site-accent text-site-on-accent absolute -right-2 -top-1 rounded-full px-1 text-[10px] font-bold leading-4"
                >{{ cartStore.totalQuantity }}</span
            >
        </span>
        <span class="text-[10px] font-medium">{{ __('site.nav.cart') }}</span>
    </a>
</template>

<script setup>
import { useCartStore } from '../Stores/CartStore'

const cartStore = useCartStore()
</script>
