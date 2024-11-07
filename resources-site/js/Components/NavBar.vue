<template>
    <div class="relative flex flex-wrap justify-center px-10 py-3">
        <div
            id="collapseMenu"
            class="max-lg:hidden max-lg:before:fixed max-lg:before:inset-0 max-lg:before:z-50 max-lg:before:bg-black max-lg:before:opacity-40 lg:!block"
        >
            <button
                id="toggleClose"
                class="fixed right-4 top-2 z-[100] rounded-full bg-white p-3 lg:hidden"
            >
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="w-4 fill-black"
                    viewBox="0 0 320.591 320.591"
                >
                    <path
                        d="M30.391 318.583a30.37 30.37 0 0 1-21.56-7.288c-11.774-11.844-11.774-30.973 0-42.817L266.643 10.665c12.246-11.459 31.462-10.822 42.921 1.424 10.362 11.074 10.966 28.095 1.414 39.875L51.647 311.295a30.366 30.366 0 0 1-21.256 7.288z"
                        data-original="#000000"
                    ></path>
                    <path
                        d="M287.9 318.583a30.37 30.37 0 0 1-21.257-8.806L8.83 51.963C-2.078 39.225-.595 20.055 12.143 9.146c11.369-9.736 28.136-9.736 39.504 0l259.331 257.813c12.243 11.462 12.876 30.679 1.414 42.922-.456.487-.927.958-1.414 1.414a30.368 30.368 0 0 1-23.078 7.288z"
                        data-original="#000000"
                    ></path>
                </svg>
            </button>

            <ul
                class="z-50 max-lg:fixed max-lg:left-0 max-lg:top-0 max-lg:h-full max-lg:w-2/3 max-lg:min-w-[300px] max-lg:space-y-3 max-lg:overflow-auto max-lg:bg-white max-lg:p-4 max-lg:shadow-md lg:flex lg:gap-x-10"
            >
                <li class="px-3 max-lg:border-b max-lg:pb-4 lg:hidden">
                    <a href="{{ route('site.index') }}">
                        <!-- <img
                            src="https://readymadeui.com/readymadeui.svg"
                            alt="logo"
                            class="w-36"
                        /> -->
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
                        href="/products"
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
            <button>
                <i class="ri-menu-line"></i>
            </button>
        </div>

        <div
            class="ml-auto flex items-center space-x-8 lg:absolute lg:right-10"
        >
            <span class="relative">
                <i
                    class="ri-heart-line cursor-pointer text-xl hover:text-skin-primary-9"
                ></i>
                <span
                    class="absolute left-auto top-0 -ml-1 rounded-full bg-red-500 px-1 py-0 text-xs text-white"
                >
                    1
                </span>
            </span>
            <span class="relative">
                <a href="/cart">
                    <i
                        class="ri-shopping-cart-line cursor-pointer text-xl hover:text-skin-primary-9"
                    ></i>
                    <span
                        class="absolute left-auto top-0 -ml-1 rounded-full bg-red-500 px-1 py-0 text-xs text-white"
                    >
                        {{ cartStore.totalQuantity }}
                    </span>
                </a>
            </span>
            <div class="inline-block cursor-pointer border-gray-300">
                <i class="ri-user-fill text-xl hover:text-skin-primary-9"></i>
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

// const toggleOpen = () => {
//     const open = document.getElementById('open'),
//     const close = document.getElementById('close'),
//     const toggleOpen = document.getElementById('toggleOpen'),
//     open.classList.toggle('hidden'),
//     close.classList.toggle('hidden'),
//     toggleOpen.classList.toggle('hidden'),
// }
</script>
