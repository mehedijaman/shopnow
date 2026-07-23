<template>
    <div class="space-y-3">
        <!-- Variation selector (shown only for variable products) -->
        <ProductVariationSelector v-if="hasVariations" ref="variationSelector" :all-attributes="variationAttributes"
            :variations="variations" :parent-product="product" @variation-change="onVariationChange" />

        <div class="flex flex-wrap items-center gap-3">
            <div class="inline-flex items-center">
                <button @click="decreaseQuantity"
                    class="inline-flex items-center rounded-l border border-r border-gray-200 bg-white px-2 py-1 text-gray-600 hover:bg-gray-100 active:bg-gray-200 disabled:opacity-50"
                    :disabled="!canAddToCart">
                    <i class="ri-subtract-line"></i>
                </button>
                <div
                    class="inline-flex select-none items-center border-b border-t border-gray-100 bg-gray-100 px-4 py-1 text-gray-600 hover:bg-gray-100">
                    {{ quantity }}
                </div>
                <button @click="increaseQuantity"
                    class="inline-flex items-center rounded-r border border-r border-gray-200 bg-white px-2 py-1 text-gray-600 hover:bg-gray-100 active:bg-gray-200 disabled:opacity-50"
                    :disabled="!canAddToCart">
                    <i class="ri-add-line"></i>
                </button>
            </div>

            <button @click="addToCart" :disabled="!canAddToCart"
                class="flex items-center justify-center rounded-sm bg-blue-500 px-2 py-1 text-white hover:bg-blue-600 active:bg-blue-700 disabled:opacity-50">
                <i class="ri-shopping-cart-fill mr-1"></i>
                Add to Cart
            </button>
        </div>

        <p v-if="hasVariations && !selectedVariation && product.type === 'variable'" class="text-xs text-amber-600">
            Please select all options.
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
