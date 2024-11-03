<template>
    <div class="mt-5">
        <AppInputFile
            v-model="brandStore.brand.image"
            :image-preview-url="getImagePreviewURL()"
            @remove-file="brandStore.brand.remove_previous_image = true"
        ></AppInputFile>
    </div>
</template>

<script setup>
import useFormErrors from '@/Composables/useFormErrors'
import useFormContext from '@/Composables/useFormContext'
import { useProductBrandStore } from '../ProductBrandStore'

const brandStore = useProductBrandStore()
const { errorsFields } = useFormErrors()

const { isCreate } = useFormContext()
const getImagePreviewURL = () => {
    if (!isCreate.value && brandStore.brand.image_url) {
        return brandStore.brand.image_url
    }

    return null
}
</script>
