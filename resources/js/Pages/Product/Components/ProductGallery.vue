<template>
    <div class="mt-5">
        <AppLabel>Gallery Images</AppLabel>

        <!-- Existing gallery images -->
        <div v-if="existingImages.length" class="mt-2 grid grid-cols-3 gap-2">
            <div
                v-for="img in existingImages"
                :key="img.id"
                class="group relative overflow-hidden rounded-md border border-skin-neutral-5"
            >
                <img
                    :src="img.url"
                    :alt="img.name"
                    class="h-20 w-full object-cover"
                />
                <button
                    type="button"
                    class="absolute right-1 top-1 flex h-5 w-5 items-center justify-center rounded-full bg-red-600 text-xs text-white opacity-0 transition-opacity group-hover:opacity-100"
                    title="Remove image"
                    @click="removeExisting(img.id)"
                >
                    <i class="ri-close-line"></i>
                </button>
            </div>
        </div>

        <!-- New image previews -->
        <div v-if="newPreviews.length" class="mt-2 grid grid-cols-3 gap-2">
            <div
                v-for="(preview, index) in newPreviews"
                :key="index"
                class="group relative overflow-hidden rounded-md border border-skin-neutral-5"
            >
                <img
                    :src="preview"
                    alt="New gallery image"
                    class="h-20 w-full object-cover"
                />
                <button
                    type="button"
                    class="absolute right-1 top-1 flex h-5 w-5 items-center justify-center rounded-full bg-red-600 text-xs text-white opacity-0 transition-opacity group-hover:opacity-100"
                    title="Remove"
                    @click="removeNew(index)"
                >
                    <i class="ri-close-line"></i>
                </button>
            </div>
        </div>

        <!-- Upload button -->
        <div class="mt-3">
            <input
                ref="fileInput"
                type="file"
                accept="image/*"
                multiple
                hidden
                @change="handleFiles"
            />
            <AppButton type="button" class="btn btn-secondary text-sm" @click="fileInput.click()">
                <i class="ri-image-add-line mr-1"></i> Add Images
            </AppButton>
            <p class="mt-1 text-xs text-skin-neutral-9">JPG, PNG, WEBP — max 2 MB each</p>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { router } from '@inertiajs/vue3'
import useFormContext from '@/Composables/useFormContext'
import { useProductStore } from '../ProductStore'

const props = defineProps({
    gallery: {
        type: Array,
        default: () => []
    }
})

const productStore = useProductStore()
const { isCreate } = useFormContext()

const fileInput = ref(null)
const newPreviews = ref([])

// Keep a local copy of existing images so we can hide deleted ones immediately
const existingImages = ref([...props.gallery])

const handleFiles = (event) => {
    const files = Array.from(event.target.files)

    files.forEach((file) => {
        productStore.product.gallery_images.push(file)
        const reader = new FileReader()
        reader.onload = (e) => newPreviews.value.push(e.target.result)
        reader.readAsDataURL(file)
    })

    // Clear input so same file can be re-added if removed
    event.target.value = null
}

const removeNew = (index) => {
    productStore.product.gallery_images.splice(index, 1)
    newPreviews.value.splice(index, 1)
}

const removeExisting = (mediaId) => {
    const productId = history.state?.props?.product?.id
    if (!productId) return

    existingImages.value = existingImages.value.filter((img) => img.id !== mediaId)

    router.delete(route('product.gallery.destroy', { id: productId, mediaId }), {
        preserveScroll: true,
        preserveState: false,
    })
}
</script>
