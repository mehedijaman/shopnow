<template>
    <div class="w-full space-y-2 sm:space-y-2.5">
        <!-- Variation selector (shown only for variable products) -->
        <ProductVariationSelector v-if="hasVariations" ref="variationSelector" :all-attributes="variationAttributes"
            :variations="variations" :parent-product="product" @variation-change="onVariationChange" />

        <div class="flex items-center gap-1.5 sm:gap-2">
            <!-- Quantity control -->
            <div class="inline-flex items-center rounded-lg border border-gray-200 bg-gray-50/80 p-0.5 shadow-inner dark:border-gray-700 dark:bg-gray-800 shrink-0">
                <button @click="decreaseQuantity" type="button"
                    class="flex h-7 w-7 sm:h-8 sm:w-8 items-center justify-center rounded-md text-gray-600 transition-colors hover:bg-white hover:text-gray-900 active:scale-95 disabled:opacity-30 dark:text-gray-300 dark:hover:bg-gray-700 dark:hover:text-white"
                    :disabled="!canAddToCart || quantity <= 1" aria-label="Decrease quantity">
                    <i class="ri-subtract-line text-xs sm:text-sm"></i>
                </button>
                <span class="w-5 sm:w-6 text-center text-xs font-extrabold text-gray-900 select-none dark:text-white">
                    {{ quantity }}
                </span>
                <button @click="increaseQuantity" type="button"
                    class="flex h-7 w-7 sm:h-8 sm:w-8 items-center justify-center rounded-md text-gray-600 transition-colors hover:bg-white hover:text-gray-900 active:scale-95 disabled:opacity-30 dark:text-gray-300 dark:hover:bg-gray-700 dark:hover:text-white"
                    :disabled="!canAddToCart" aria-label="Increase quantity">
                    <i class="ri-add-line text-xs sm:text-sm"></i>
                </button>
            </div>

            <!-- Add to Cart CTA Button -->
            <button @click="addToCart" :disabled="!canAddToCart" type="button"
                :class="[
                    'flex flex-1 items-center justify-center gap-1.5 rounded-lg sm:rounded-xl px-2.5 py-1.5 sm:px-4 sm:py-2 text-[10px] sm:text-xs font-bold uppercase tracking-wider transition-all duration-300 shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-1 min-w-0',
                    isJustAdded
                        ? 'bg-emerald-600 text-white ring-2 ring-emerald-500/50 scale-[1.02] shadow-md'
                        : canAddToCart
                            ? 'bg-gray-900 text-white hover:bg-primary-600 hover:shadow-md active:scale-[0.98] dark:bg-white dark:text-gray-900 dark:hover:bg-primary-500 dark:hover:text-white focus:ring-primary-500'
                            : 'bg-gray-200 text-gray-400 cursor-not-allowed dark:bg-gray-800 dark:text-gray-600'
                ]">
                <i :class="isJustAdded ? 'ri-checkbox-circle-fill text-xs sm:text-sm animate-bounce' : 'ri-shopping-bag-3-line text-xs sm:text-sm'"></i>
                <span class="truncate">{{ isJustAdded ? 'Added!' : (hasVariations && !selectedVariation ? 'Select Options' : 'Add to Cart') }}</span>
            </button>
        </div>

        <p v-if="hasVariations && !selectedVariation" class="text-[10px] sm:text-[11px] font-medium text-amber-600 dark:text-amber-400">
            Select options above to add to cart.
        </p>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useCartStore } from '../Stores/CartStore'
import ProductVariationSelector from './ProductVariationSelector.vue'

const cartStore = useCartStore()
const props = defineProps({
    product: { type: Object, required: true },
    variations: { type: Array, default: () => [] },
    variationAttributes: { type: Object, default: () => ({}) },
    bundleItems: { type: Array, default: () => [] },
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
            mainImgEl.src = props.product.image_url || 'https://placehold.co/800x800/f3f4f6/9ca3af?text=No+Image'
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

    const item = {
        id: props.product.id,
        name: props.product.name,
        price: effectivePrice.value,
        image_url: props.product.image_url,
        product_variation_id: selectedVariation.value?.id || null,
        variation_label: selectedVariation.value ? generateVariationLabel() : null,
    }
    cartStore.addItem(item, quantity.value)

    isJustAdded.value = true
    if (addedTimeout) clearTimeout(addedTimeout)
    addedTimeout = setTimeout(() => {
        isJustAdded.value = false
    }, 1800)

    if (window.ShopNowTracking) {
        window.ShopNowTracking.track('AddToCart', {
            content_ids: [String(item.id)],
            content_type: 'product',
            content_name: item.name,
            value: Number(item.price || 0),
            currency: 'BDT',
            quantity: Number(quantity.value || 1),
        })
        window.ShopNowTracking.trackGa('add_to_cart', {
            currency: 'BDT',
            value: Number(item.price || 0) * Number(quantity.value || 1),
            items: [{
                item_id: String(item.id),
                item_name: item.name,
                price: Number(item.price || 0),
                item_variant: item.variation_label || undefined,
                quantity: Number(quantity.value || 1),
            }]
        })
    }

    window.dataLayer = window.dataLayer || []
    window.dataLayer.push({
        event: 'add_to_cart',
        ecommerce: {
            currency: 'BDT',
            value: Number(item.price || 0) * Number(quantity.value || 1),
            items: [{
                item_id: String(item.id),
                item_name: item.name,
                price: Number(item.price || 0),
                item_variant: item.variation_label || undefined,
                quantity: Number(quantity.value || 1),
            }]
        }
    })
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
