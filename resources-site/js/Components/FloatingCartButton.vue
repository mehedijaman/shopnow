<template>
    <a
        href="/cart"
        class="fixed right-6 top-1/2 z-50 flex -translate-y-1/2 items-center gap-2 rounded-full border-2 border-primary-9 bg-white px-4 py-3 shadow-lg transition-transform hover:scale-105 hover:shadow-xl focus:outline-none focus:ring-4 focus:ring-primary-8/30"
        aria-label="Shopping cart"
        title="View cart"
    >
        <i class="ri-shopping-cart-fill text-xl text-primary-9"></i>
        <span class="text-sm font-semibold text-primary-9">গোটা কার্ট</span>
        <span
            v-if="totalItems > 0"
            class="rounded-full bg-primary-9 px-2 py-0.5 text-xs font-bold text-white"
        >
            {{ totalItems }}
        </span>
    </a>
</template>

<script setup>
import { computed } from 'vue'
import { useCartStore } from '../Stores/CartStore'

const cartStore = useCartStore()

const totalItems = computed(() => cartStore.totalItems)
</script>
