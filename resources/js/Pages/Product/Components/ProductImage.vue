<template>
    <div class="mt-5">
        <AppInputFile
            v-model="productStore.product.image"
            :image-preview-url="getImagePreviewURL()"
            @remove-file="productStore.product.remove_previous_image = true"
        ></AppInputFile>
    </div>
</template>

<script setup>
import useFormErrors from '@/Composables/useFormErrors'
import useFormContext from '@/Composables/useFormContext'
import { useProductStore } from '../ProductStore'
const productStore = useProductStore()
const { errorsFields } = useFormErrors()

const { isCreate } = useFormContext()
const getImagePreviewURL = () => {
    if (!isCreate.value && productStore.product.image_url) {
        return productStore.product.image_url
    }

    return null
}
</script>
