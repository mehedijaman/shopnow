<template>
    <nav
        class="fixed inset-x-0 bottom-0 z-40 border-t border-site-divider bg-site-surface/95 pb-[env(safe-area-inset-bottom)] shadow-[0_-4px_16px_-6px_rgba(0,0,0,0.15)] backdrop-blur-md lg:hidden"
        :aria-label="__('site.nav.mobile_navigation')"
    >
        <div class="mx-auto flex w-full max-w-md items-stretch justify-around">
            <a
                href="/"
                :aria-current="isActive('/') ? 'page' : null"
                :class="[itemBase, isActive('/') ? itemActive : itemIdle]"
            >
                <span
                    v-show="isActive('/')"
                    class="absolute inset-x-4 top-0 h-0.5 rounded-full bg-site-link"
                    aria-hidden="true"
                ></span>
                <i
                    :class="isActive('/') ? 'ri-home-5-fill' : 'ri-home-5-line'"
                    class="text-xl leading-none"
                    aria-hidden="true"
                ></i>
                <span class="leading-none">{{ __('site.nav.home') }}</span>
            </a>

            <button
                type="button"
                data-mobile-search-toggle
                aria-controls="mobile-header-search"
                aria-expanded="false"
                :aria-label="__('site.aria.search_products')"
                :data-open-label="__('site.aria.search_products')"
                :data-close-label="__('site.aria.close_search')"
                :class="[itemBase, itemIdle, 'cursor-pointer']"
            >
                <i
                    class="ri-search-2-line text-xl leading-none"
                    data-mobile-search-icon
                    aria-hidden="true"
                ></i>
                <span class="leading-none">{{ __('site.nav.search') }}</span>
            </button>

            <button
                type="button"
                data-mobile-categories-toggle
                aria-controls="collapseMenu"
                aria-expanded="false"
                :aria-label="__('product::site.shop.categories')"
                :class="[itemBase, itemIdle, 'cursor-pointer']"
            >
                <i
                    class="ri-layout-grid-line text-xl leading-none"
                    aria-hidden="true"
                ></i>
                <span class="leading-none">{{
                    __('product::site.shop.categories')
                }}</span>
            </button>

            <a
                href="/cart"
                :aria-current="isActive('/cart') ? 'page' : null"
                :class="[itemBase, isActive('/cart') ? itemActive : itemIdle]"
            >
                <span
                    v-show="isActive('/cart')"
                    class="absolute inset-x-4 top-0 h-0.5 rounded-full bg-site-link"
                    aria-hidden="true"
                ></span>
                <span class="relative flex items-center justify-center">
                    <i
                        :class="
                            cartStore.totalQuantity > 0
                                ? 'ri-shopping-cart-fill'
                                : 'ri-shopping-cart-line'
                        "
                        class="text-xl leading-none"
                        aria-hidden="true"
                    ></i>
                    <span
                        v-show="cartStore.totalQuantity > 0"
                        class="absolute -right-2.5 -top-1.5 min-w-4 rounded-full bg-site-primary px-1 text-center text-[9px] font-bold leading-4 text-site-on-primary ring-2 ring-site-surface"
                        >{{ cartStore.totalQuantity }}</span
                    >
                </span>
                <span class="leading-none">{{ __('site.nav.cart') }}</span>
            </a>

            <a
                :href="props.isLoggedIn ? '/account/profile' : '/login'"
                :aria-current="accountActive ? 'page' : null"
                :class="[itemBase, accountActive ? itemActive : itemIdle]"
            >
                <span
                    v-show="accountActive"
                    class="absolute inset-x-4 top-0 h-0.5 rounded-full bg-site-link"
                    aria-hidden="true"
                ></span>
                <i class="ri-user-line text-xl leading-none" aria-hidden="true"></i>
                <span class="leading-none">{{ __('site.nav.account') }}</span>
            </a>
        </div>
    </nav>
</template>

<script setup>
import { useCartStore } from '../Stores/CartStore'
import { computed, defineProps } from 'vue'

const props = defineProps({
    isLoggedIn: {
        type: Boolean,
        default: false
    }
})

const cartStore = useCartStore()

const currentPath = window.location.pathname

function isActive(path) {
    return path === '/'
        ? currentPath === '/'
        : currentPath === path || currentPath.startsWith(path + '/')
}

const accountActive = computed(() =>
    props.isLoggedIn
        ? currentPath.startsWith('/account')
        : currentPath === '/login'
)

// Shared item styling: appearance tokens follow the admin palette in both themes.
const itemBase =
    'relative flex min-h-14 flex-1 flex-col items-center justify-center gap-1.5 px-1 pt-2.5 pb-1.5 text-[10px] transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-site-focus'
const itemIdle = 'font-medium text-site-muted'
const itemActive = 'font-semibold text-site-link'
</script>
