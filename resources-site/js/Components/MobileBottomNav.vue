<template>
    <nav
        class="bg-site-surface dark:bg-site-surface-muted fixed bottom-0 left-0 right-0 z-40 block border-t border-skin-neutral-4 lg:hidden"
        :aria-label="__('site.nav.mobile_navigation')"
    >
        <div class="mx-auto flex w-full max-w-7xl justify-around">
            <a
                href="/"
                class="flex flex-col items-center justify-center py-2 text-center"
            >
                <i class="ri-home-5-line text-xl text-skin-neutral-9"></i>
                <span
                    class="mt-0.5 text-[10px] font-medium text-skin-neutral-9"
                    >{{ __('site.nav.home') }}</span
                >
            </a>
            <button
                type="button"
                data-mobile-search-toggle
                aria-controls="mobile-header-search"
                aria-expanded="false"
                :aria-label="__('site.aria.search_products')"
                :data-open-label="__('site.aria.search_products')"
                :data-close-label="__('site.aria.close_search')"
                class="flex flex-col items-center justify-center py-2 text-center"
            >
                <i
                    class="ri-search-2-line text-xl text-skin-neutral-9"
                    data-mobile-search-icon
                    aria-hidden="true"
                ></i>
                <span
                    class="mt-0.5 text-[10px] font-medium text-skin-neutral-9"
                    >{{ __('site.nav.search') }}</span
                >
            </button>
            <button
                type="button"
                data-mobile-categories-toggle
                aria-controls="collapseMenu"
                aria-expanded="false"
                :aria-label="__('product::site.shop.categories')"
                class="text-site-text hover:text-site-link flex flex-col items-center justify-center py-2 text-center"
            >
                <i class="ri-layout-grid-line text-xl" aria-hidden="true"></i>
                <span class="mt-0.5 text-[10px] font-medium">{{
                    __('product::site.shop.categories')
                }}</span>
            </button>
            <a
                href="/cart"
                class="relative flex flex-col items-center justify-center py-2 text-center"
            >
                <i
                    :class="
                        cartStore.totalQuantity > 0
                            ? 'ri-shopping-cart-fill'
                            : 'ri-shopping-cart-line'
                    "
                    class="cursor-pointer text-xl text-skin-neutral-9"
                ></i>
                <span
                    v-show="cartStore.totalQuantity > 0"
                    class="absolute -top-1 right-0 rounded-full bg-red-500 px-1 py-0 text-xs text-white"
                    >{{ cartStore.totalQuantity }}</span
                >
                <span
                    class="mt-0.5 text-[10px] font-medium text-skin-neutral-9"
                    >{{ __('site.nav.cart') }}</span
                >
            </a>
            <a
                :href="props.isLoggedIn ? '/account/profile' : '/login'"
                class="flex flex-col items-center justify-center py-2 text-center"
            >
                <i class="ri-user-line text-xl text-skin-neutral-9"></i>
                <span
                    class="mt-0.5 text-[10px] font-medium text-skin-neutral-9"
                    >{{ __('site.nav.account') }}</span
                >
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
        default: false
    }
})

const cartStore = useCartStore()
</script>
