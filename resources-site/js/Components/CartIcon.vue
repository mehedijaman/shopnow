<template>
    <span class="relative">
        <a href="/cart">
            <i
                :class="
                    cartStore.totalQuantity > 0
                        ? 'ri-shopping-cart-fill'
                        : 'ri-shopping-cart-line'
                "
                class="cursor-pointer text-xl hover:text-skin-primary-9"
            ></i>
            <span
                v-show="cartStore.totalQuantity > 0"
                class="absolute left-auto top-0 -ml-1 rounded-full bg-red-500 px-1 py-0 text-xs text-white"
            >
                {{ cartStore.totalQuantity }}
            </span>
        </a>
    </span>
</template>

<script setup>
import { ref } from 'vue'
import { useCartStore } from '../Stores/CartStore'

const cartStore = useCartStore()

const props = defineProps({
    categories: {
        type: Array,
        required: true
    }
})

const toggleOpen = () => {
    const open = document.getElementById('open')
    const close = document.getElementById('close')
    const toggleOpen = document.getElementById('toggleOpen')
    open.classList.toggle('hidden')
    close.classList.toggle('hidden')
    toggleOpen.classList.toggle('hidden')
}
</script>
