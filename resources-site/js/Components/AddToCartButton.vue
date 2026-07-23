<template>
    <div class="space-y-4">
        <!-- Variation selector (shown only for variable products) -->
        <ProductVariationSelector
            v-if="hasVariations"
            ref="variationSelector"
            :all-attributes="variationAttributes"
            :variations="variations"
            :parent-product="product"
            @variation-change="onVariationChange"
        />

        <div class="flex flex-wrap items-center gap-4">
            {{-- Quantity counter --}}
            <div class="inline-flex items-center rounded-xl border border-slate-200 bg-slate-50 p-1 dark:border-slate-800 dark:bg-slate-900">
                <button
                    type="button"
                    @click="decreaseQuantity"
                    class="flex h-9 w-9 items-center justify-center rounded-lg bg-white text-slate-700 shadow-sm transition hover:bg-slate-100 hover:text-slate-900 active:scale-95 disabled:opacity-40 dark:bg-slate-800 dark:text-slate-200"
                    :disabled="!canAddToCart || quantity <= 1"
                    aria-label="Decrease quantity"
                >
                    <i class="ri-subtract-line text-base"></i>
                </button>
                <div class="w-10 select-none text-center text-sm font-bold text-slate-900 dark:text-white">
                    {{ quantity }}
                </div>
                <button
                    type="button"
                    @click="increaseQuantity"
                    class="flex h-9 w-9 items-center justify-center rounded-lg bg-white text-slate-700 shadow-sm transition hover:bg-slate-100 hover:text-slate-900 active:scale-95 disabled:opacity-40 dark:bg-slate-800 dark:text-slate-200"
                    :disabled="!canAddToCart"
                    aria-label="Increase quantity"
                >
                    <i class="ri-add-line text-base"></i>
                </button>
            </div>

            {{-- Add to Cart button --}}
            <button
                type="button"
                @click="addToCart"
                :disabled="!canAddToCart"
                class="inline-flex min-h-[48px] items-center justify-center gap-2 rounded-xl bg-primary-600 px-6 py-3 text-sm font-bold text-white shadow-md transition-all duration-200 hover:bg-primary-700 hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 active:scale-[0.99] disabled:cursor-not-allowed disabled:opacity-50 dark:bg-primary-600 dark:hover:bg-primary-700"
            >
                <i class="ri-shopping-cart-fill text-lg"></i>
                <span>Add to Cart</span>
            </button>
        </div>

        <p v-if="hasVariations && !selectedVariation && product.type === 'variable'" class="flex items-center gap-1.5 text-xs font-semibold text-amber-700 dark:text-amber-400">
            <i class="ri-information-line text-sm"></i>
            <span>Please select all product options.</span>
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

const hasVariations = computed(() => props.product.type === 'variable' && props.variations?.length > 0)
const hasBundle = computed(() => props.product.type === 'bundle')

const canAddToCart = computed(() => {
    if (hasBundle.value && props.product.quantity <= 0) return false
    if (props.product.quantity <= 0 && !hasVariations.value && !hasBundle.value) return false
    if (hasVariations.value && !selectedVariation.value) return false
    if (hasVariations.value && selectedVariation.value && selectedVariation.value.quantity <= 0) return false
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
    const item = {
        id: props.product.id,
        name: props.product.name,
        price: effectivePrice.value,
        image_url: props.product.image_url,
        product_variation_id: selectedVariation.value?.id || null,
        variation_label: selectedVariation.value ? generateVariationLabel() : null,
    }
    cartStore.addItem(item, quantity.value)

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
