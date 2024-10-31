<template>
    <p class="mb-1 mt-5">Category</p>
    <AppCombobox
        v-model="productStore.product.category_id"
        :options="categories"
        combo-label="Select a Category"
        class="w-64 xl:w-full"
    />
</template>

<script setup>
import { onMounted } from 'vue'
import { useProductStore } from '../ProductStore'
const productStore = useProductStore()

const props = defineProps({
    categories: {
        type: Object,
        default: () => {}
    }
})

onMounted(() => {
    if (productStore.product.category_id) {
        productStore.product.category_id = props.categories.find(
            (category) => category.value === productStore.product.category_id
        )
    }
})
</script>
