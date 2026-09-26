<template>
    <div class="w-full space-y-2 sm:space-y-2.5">
        <!-- Variation selector (shown only for variable products) -->
        <ProductVariationSelector v-if="hasVariations" ref="variationSelector" :all-attributes="variationAttributes"
            :variations="variations" :parent-product="product" @variation-change="onVariationChange" />

        <!-- Quantity + Wishlist -->
        <div class="flex w-full items-center gap-2">
            <div
                class="flex min-w-0 flex-1 items-center rounded-lg border border-gray-200 bg-gray-50/80 p-0.5 shadow-inner dark:border-gray-700 dark:bg-gray-800">
                <button @click="decreaseQuantity" type="button"
                    class="flex h-8 flex-1 items-center justify-center rounded-lg text-gray-600 transition-colors hover:bg-white hover:text-gray-900 active:scale-95 disabled:opacity-30 dark:text-gray-300 dark:hover:bg-gray-700 dark:hover:text-white"
                    :disabled="!canAddToCart || quantity <= 1" aria-label="Decrease quantity">
                    <i class="ri-subtract-line text-xs sm:text-sm"></i>
                </button>
                <span class="flex-1 text-center text-xs font-extrabold text-gray-900 select-none dark:text-white">
                    {{ quantity }}
                </span>
                <button @click="increaseQuantity" type="button"
                    class="flex h-8 flex-1 items-center justify-center rounded-lg text-gray-600 transition-colors hover:bg-white hover:text-gray-900 active:scale-95 disabled:opacity-30 dark:text-gray-300 dark:hover:bg-gray-700 dark:hover:text-white"
                    :disabled="!canAddToCart" aria-label="Increase quantity">
                    <i class="ri-add-line text-xs sm:text-sm"></i>
                </button>
            </div>

            <button v-if="showWishlist" type="button"
                class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg border border-gray-200 bg-white text-gray-400 shadow-sm transition-all duration-300 hover:border-red-200 hover:text-red-500 focus:outline-none focus:ring-4 focus:ring-red-500/10 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:border-red-500/30 dark:hover:text-red-400"
                aria-label="Add to wishlist">
                <i class="ri-heart-line text-base leading-none"></i>
            </button>
        </div>

        <div class="flex w-full flex gap-2 sm:gap-2.5">
            <!-- Add to Cart CTA Button (full width) -->
            <button @click="addToCart" :disabled="!canAddToCart" type="button" :class="[
                'flex w-full items-center justify-center gap-1.5 rounded-lg sm:rounded-lg px-2.5 py-2 sm:px-4 sm:py-2.5 text-[10px] sm:text-xs font-bold  transition-all duration-300 shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-1',
                isJustAdded
                    ? 'bg-emerald-600 text-white ring-2 ring-emerald-500/50 scale-[1.02] shadow-md'
                    : canAddToCart
                        ? 'bg-gray-900 text-white hover:bg-primary-600 hover:shadow-md active:scale-[0.98] dark:bg-white dark:text-gray-900 dark:hover:bg-primary-500 dark:hover:text-white focus:ring-primary-500'
                        : 'bg-gray-200 text-gray-400 cursor-not-allowed dark:bg-gray-800 dark:text-gray-600'
            ]">
                <i
                    :class="isJustAdded ? 'ri-checkbox-circle-fill text-xs sm:text-sm animate-bounce' : 'ri-shopping-bag-3-line text-xs sm:text-sm'"></i>
                <span class="truncate">{{ isJustAdded ? 'Added!' : (hasVariations && !selectedVariation ?
                    'SelectOptions' : 'Add to Cart') }}</span>
            </button>

            <!-- Order Now Button (full width) -->
            <button @click="orderNow" :disabled="!canAddToCart" type="button" :class="[
                'flex w-full items-center justify-center gap-1.5 rounded-lg sm:rounded-lg px-2.5 py-2 sm:px-4 sm:py-2.5 text-[10px] sm:text-xs font-bold  transition-all duration-300 shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-1',
                canAddToCart
                    ? 'bg-primary-600 text-white hover:bg-primary-700 hover:shadow-md active:scale-[0.98] focus:ring-primary-500'
                    : 'bg-gray-200 text-gray-400 cursor-not-allowed dark:bg-gray-800 dark:text-gray-600'
            ]">
                <i class="ri-flashlight-line text-xs sm:text-sm"></i>
                <span class="truncate">Buy Now</span>
            </button>
        </div>

        <p v-if="hasVariations && !selectedVariation"
            class="text-[10px] sm:text-[11px] font-medium text-amber-600 dark:text-amber-400">
            Select options above to add to cart.
        </p>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useCartStore } from '../Stores/CartStore'
import { pushAddToCart } from '../analytics/datalayer'
import ProductVariationSelector from './ProductVariationSelector.vue'

const cartStore = useCartStore()
const props = defineProps({
    product: { type: Object, required: true },
    variations: { type: Array, default: () => [] },
    variationAttributes: { type: Object, default: () => ({}) },
    bundleItems: { type: Array, default: () => [] },
    showWishlist: { type: Boolean, default: false },
})

const variationSelector = ref(null)
const selectedVariation = ref(null)
const quantity = ref(1)
const isJustAdded = ref(false)
let addedTimeout = null

const hasVariations = computed(() => props.product.type === 'variable' && props.variations?.length > 0)
const hasBundle = computed(() => props.product.type === 'bundle')

const canAddToCart = computed(() => {
    if (hasBundle.value && props.product.quantity <= 0) return false
    if (props.product.quantity <= 0 && !hasVariations.value && !hasBundle.value) return false
    if (hasVariations.value && !selectedVariation.value) return false
    if (hasVariations.value && selectedVariation.value && (!selectedVariation.value.active || selectedVariation.value.quantity <= 0)) return false
    return true
})

const effectivePrice = computed(() => {
    if (selectedVariation.value) {
        return selectedVariation.value.sale_price || selectedVariation.value.price
    }
    return props.product.sale_price || props.product.price
})

const onVariationChange = (variation) => {
    selectedVariation.value = variation

    const mainImgEl = document.getElementById('mainImage')
    if (mainImgEl) {
        if (variation && variation.image_url) {
            mainImgEl.src = variation.image_url
        } else {
            const fallbackLogo = document.querySelector('header img')?.src || '/logo.png'
            mainImgEl.src = props.product.image_url || fallbackLogo
        }
    }
}

const increaseQuantity = () => {
    quantity.value++
}

const decreaseQuantity = () => {
    if (quantity.value > 1) {
        quantity.value--
    }
}

function addToCart() {
    if (!canAddToCart.value) return

    const item = buildCartItem()
    cartStore.addItem(item, quantity.value)

    isJustAdded.value = true
    if (addedTimeout) clearTimeout(addedTimeout)
    addedTimeout = setTimeout(() => {
        isJustAdded.value = false
    }, 1800)

    pushAddToCart(item, quantity.value)
}

function orderNow() {
    if (!canAddToCart.value) return

    const item = buildCartItem()
    cartStore.addItem(item, quantity.value)
    pushAddToCart(item, quantity.value)

    window.location.href = '/cart'
}

function buildCartItem() {
    return {
        id: props.product.id,
        name: props.product.name,
        price: effectivePrice.value,
        image_url: props.product.image_url,
        product_variation_id: selectedVariation.value?.id || null,
        variation_label: selectedVariation.value ? generateVariationLabel() : null,
    }
}

const generateVariationLabel = () => {
    if (!selectedVariation.value?.attribute_value_ids) return ''
    const labels = []
    for (const attr of Object.values(props.variationAttributes)) {
        for (const val of attr.values || []) {
            if (selectedVariation.value.attribute_value_ids.includes(val.id)) {
                labels.push(`${attr.name}: ${val.value}`)
            }
        }
    }
    return labels.join(', ')
}
</script>
