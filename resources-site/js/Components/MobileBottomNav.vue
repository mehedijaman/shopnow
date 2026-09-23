<template>
    <nav
        class="fixed bottom-0 left-0 right-0 z-50 border-t border-skin-neutral-4 bg-white"
        aria-label="Mobile navigation"
    >
<div class="mx-auto flex w-full max-w-7xl justify-around">
    <a
        v-for="item in items"
        :key="item.label"
        :href="item.href"
        class="flex flex-1 flex-col items-center justify-center py-2 text-center"
    >
                <i :class="item.icon" class="text-xl text-skin-neutral-9"></i>
                <span class="mt-0.5 text-[10px] font-medium text-skin-neutral-9">{{ item.label }}</span>
            </a>
        </div>
    </nav>
</template>

<script setup>
import { computed } from 'vue'
import { useCartStore } from '../Stores/CartStore'

const props = defineProps({
    isLoggedIn: {
        type: Boolean,
        default: false,
    },
})

const cartStore = useCartStore()

const totalItems = computed(() => cartStore.totalItems)

const items = computed(() => [
    { icon: 'ri-home-5-line', label: 'হোম', href: '/' },
    { icon: 'ri-shopping-bag-line', label: 'Shop', href: '/shop' },
    {
        icon: totalItems.value > 0 ? 'ri-shopping-cart-fill' : 'ri-shopping-cart-line',
        label: 'কার্ট',
        href: '/cart',
    },
    { icon: 'ri-search-2-line', label: 'অনুসন্ধান', href: '/shop/search' },
    {
        icon: 'ri-user-line',
        label: 'অ্যাকাউন্ট',
        href: props.isLoggedIn ? '/account/profile' : '/login',
    },
])
</script>
