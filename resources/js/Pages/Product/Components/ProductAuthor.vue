<template>
    <p class="mb-1 mt-5">Author</p>
    <AppCombobox
        v-model="productStore.product.blog_author_id"
        :options="authors"
        combo-label="Select an Author"
        class="w-64 xl:w-full"
    />
</template>

<script setup>
import { onMounted } from 'vue'
import { useProductStore } from '../ProductStore'
const productStore = useProductStore()

const props = defineProps({
    authors: {
        type: Object,
        default: () => {}
    }
})

onMounted(() => {
    if (productStore.product.blog_author_id) {
        productStore.product.blog_author_id = props.authors.find(
            (author) => author.value === productStore.product.blog_author_id
        )
    }
})
</script>
