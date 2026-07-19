<template>
    <p class="mb-1 mt-5">Category</p>
    <AppCombobox
        v-model="productStore.product.category_id"
        :options="categories"
        combo-label="Select a Category"
        class="w-64 xl:w-full"
    ></AppCombobox>
</template>

<script setup>
import { watch } from 'vue'
import { useProductStore } from '../ProductStore'
const productStore = useProductStore()

const props = defineProps({
    categories: {
        type: Object,
        default: () => {}
    }
})

const syncCategory = () => {
    const val = productStore.product.category_id
    // Already a valid combobox object with a label — no sync needed
    if (val && typeof val === 'object' && val.value != null && val.label) return
    // Has a raw ID or an object without label — look up the option
    const rawId = val?.value ?? val
    if (rawId != null) {
        const match = props.categories.find((c) => c.value === Number(rawId))
        if (match) {
            productStore.product.category_id = match
        }
    }
}

syncCategory()
watch(() => props.categories, syncCategory)
</script>
