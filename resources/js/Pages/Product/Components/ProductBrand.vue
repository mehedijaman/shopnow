<template>
    <p class="mb-1 mt-5">Brand</p>
    <AppCombobox
        v-model="productStore.product.brand_id"
        :options="brands"
        combo-label="Select a Brand"
        class="w-64 xl:w-full"
    />
</template>

<script setup>
import { onMounted } from 'vue'
import { useProductStore } from '../ProductStore'
const productStore = useProductStore()

const props = defineProps({
    brands: {
        type: Object,
        default: () => {}
    }
})

onMounted(() => {
    if (productStore.product.brand_id) {
        productStore.product.brand_id = props.brands.find(
            (brand) => brand.value === productStore.product.brand_id
        )
    }
})
</script>
