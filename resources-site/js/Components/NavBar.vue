<template>
    <div class="mx-auto max-w-7xl px-6 py-2 lg:px-6">
        <div
            class="mx-auto my-6 flex h-10 rounded-full border border-transparent bg-gray-100 px-6 focus-within:border-blue-500 focus-within:bg-transparent lg:w-2/4"
        >
            <i class="ri-search-2-line mr-2 mt-1 text-xl"></i>
            <input
                type="text"
                placeholder="Search..."
                class="w-full bg-transparent text-[15px] font-semibold text-gray-600 outline-none"
            />
        </div>

        <div class="relative mt-6 flex flex-wrap justify-between">
            <div
                id="collapseMenu"
                class="max-lg:hidden max-lg:before:fixed max-lg:before:inset-0 max-lg:before:z-50 max-lg:before:bg-black max-lg:before:opacity-40 lg:!block"
            >
                <button
                    id="toggleClose"
                    class="fixed right-4 top-2 z-[100] rounded-full bg-white p-3 lg:hidden"
                >
                    <i class="ri-close-line text-2xl"></i>
                </button>

                <ul
                    class="z-50 max-lg:fixed max-lg:left-0 max-lg:top-0 max-lg:h-full max-lg:w-2/3 max-lg:min-w-[300px] max-lg:space-y-3 max-lg:overflow-auto max-lg:bg-white max-lg:p-4 max-lg:shadow-md lg:flex lg:gap-x-10"
                >
                    <li class="px-3 max-lg:border-b max-lg:pb-4 lg:hidden">
                        <a href="{{ route('site.index') }}">
                            <span
                                class="rounded-md bg-blue-500 px-4 py-1 text-3xl font-extrabold text-skin-neutral-3 hover:text-skin-neutral-6 dark:text-skin-neutral-1 dark:hover:text-skin-primary-9"
                            >
                                ShopNow
                            </span>
                        </a>
                    </li>
                    <li class="max-lg:border-b max-lg:px-3 max-lg:py-3">
                        <a
                            href="/"
                            class="block text-[15px] font-semibold hover:text-[#007bff]"
                        >
                            <i class="ri-home-4-line"></i>
                            Home
                        </a>
                    </li>

                    <li class="max-lg:border-b max-lg:px-3 max-lg:py-3">
                        <a
                            href="/shop"
                            class="block text-[15px] font-semibold hover:text-[#007bff]"
                        >
                            <i class="ri-shopping-bag-line"></i>
                            Shop
                        </a>
                    </li>
                    <li
                        class="group relative max-lg:border-b max-lg:px-3 max-lg:py-3"
                    >
                        <a
                            href="javascript:void(0)"
                            class="block text-[15px] font-semibold text-gray-600 hover:fill-[#007bff] hover:text-[#007bff]"
                        >
                            <i class="ri-bookmark-line"></i>
                            Categories
                            <i class="ri-arrow-down-s-line"></i>
                        </a>
                        <ul
                            class="absolute left-0 top-5 z-50 block max-h-0 min-w-[350px] space-y-2 overflow-hidden bg-white px-6 shadow-lg transition-all duration-500 group-hover:max-h-[700px] group-hover:pb-4 group-hover:pt-6 group-hover:opacity-100 max-lg:top-8"
                        >
                            <li
                                v-for="(category, index) in categories"
                                :key="index"
                                class="border-b py-3"
                            >
                                <a
                                    :href="`/categories/${category.id}`"
                                    class="flex gap-2 text-[15px] font-semibold text-gray-600 hover:fill-[#007bff] hover:text-[#007bff]"
                                >
                                    <i class="ri-bookmark-line"></i>
                                    {{ category.name }}
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="max-lg:border-b max-lg:px-3 max-lg:py-3">
                        <a
                            href="/blog"
                            class="block text-[15px] font-semibold text-gray-600 hover:text-[#007bff]"
                        >
                            <i class="ri-newspaper-line"></i>
                            Blog
                        </a>
                    </li>

                    <li class="max-lg:border-b max-lg:px-3 max-lg:py-3">
                        <a
                            href="/about"
                            class="block text-[15px] font-semibold text-gray-600 hover:text-[#007bff]"
                        >
                            <i class="ri-question-line"></i>
                            About
                        </a>
                    </li>

                    <li class="max-lg:border-b max-lg:px-3 max-lg:py-3">
                        <a
                            href="/contact"
                            class="block text-[15px] font-semibold text-gray-600 hover:text-[#007bff]"
                        >
                            <i class="ri-mail-send-line"></i>
                            Contact
                        </a>
                    </li>
                </ul>
            </div>

            <div id="toggleOpen" class="flex lg:hidden">
                <button @click="toggleOpen">
                    <i class="ri-menu-line"></i>
                </button>
            </div>

            <div
                class="ml-auto flex items-center space-x-6 lg:absolute lg:right-0"
            >
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
                <button class="inline-block cursor-pointer border-gray-300">
                    <i
                        class="ri-user-line text-xl hover:text-skin-primary-9"
                    ></i>
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
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
