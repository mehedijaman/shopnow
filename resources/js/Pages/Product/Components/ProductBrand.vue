<template>
    <p class="mb-1 mt-5">Brand</p>
    <AppCombobox
        v-model="productStore.product.brand_id"
        :options="brands"
        combo-label="Select a Brand"
        class="w-64 xl:w-full"
    ></AppCombobox>
</template>

<script setup>
import { watch } from 'vue'
import { useProductStore } from '../ProductStore'
const productStore = useProductStore()

const props = defineProps({
    brands: {
        type: Object,
        default: () => {}
    }
})

const syncBrand = () => {
    const val = productStore.product.brand_id
    if (val && typeof val === 'object' && val.value != null && val.label) return
    const rawId = val?.value ?? val
    if (rawId != null) {
        const match = props.brands.find((b) => b.value === Number(rawId))
        if (match) {
            productStore.product.brand_id = match
        }
    }
}

syncBrand()
watch(() => props.brands, syncBrand)
</script>
