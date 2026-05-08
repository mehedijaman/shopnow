<template>
    <div class="mt-5">
        <div class="mb-2 flex items-center justify-between">
            <AppLabel class="mb-0">Gallery Images</AppLabel>
            <span class="text-xs text-skin-neutral-9">{{ totalCount }} image{{ totalCount !== 1 ? 's' : '' }}</span>
        </div>

        <!-- All images grid -->
        <div v-if="existingImages.length || newPreviews.length" class="grid grid-cols-3 gap-2">
            <!-- Existing saved images -->
            <div
                v-for="img in existingImages"
                :key="'e-' + img.id"
                class="group relative aspect-square overflow-hidden rounded-lg border border-skin-neutral-5 bg-skin-neutral-3"
            >
                <img :src="img.url" :alt="img.name" class="h-full w-full object-cover" />
                <div class="absolute inset-0 flex items-center justify-center bg-black/40 opacity-0 transition-opacity group-hover:opacity-100">
                    <button
                        type="button"
                        class="flex h-7 w-7 items-center justify-center rounded-full bg-red-600 text-white shadow-md hover:bg-red-700"
                        title="Delete image"
                        @click="removeExisting(img.id)"
                    >
                        <i class="ri-delete-bin-line text-sm"></i>
                    </button>
                </div>
                <span class="absolute bottom-0 left-0 right-0 bg-black/50 px-1 py-0.5 text-center text-[10px] text-white">Saved</span>
            </div>

            <!-- New pending images -->
            <div
                v-for="(preview, index) in newPreviews"
                :key="'n-' + index"
                class="group relative aspect-square overflow-hidden rounded-lg border border-blue-300 bg-skin-neutral-3"
            >
                <img :src="preview" alt="New image" class="h-full w-full object-cover" />
                <div class="absolute inset-0 flex items-center justify-center bg-black/40 opacity-0 transition-opacity group-hover:opacity-100">
                    <button
                        type="button"
                        class="flex h-7 w-7 items-center justify-center rounded-full bg-red-600 text-white shadow-md hover:bg-red-700"
                        title="Remove"
                        @click="removeNew(index)"
                    >
                        <i class="ri-delete-bin-line text-sm"></i>
                    </button>
                </div>
                <span class="absolute bottom-0 left-0 right-0 bg-blue-600/70 px-1 py-0.5 text-center text-[10px] text-white">Pending</span>
            </div>
        </div>

        <p v-else class="mt-1 rounded-md border border-dashed border-skin-neutral-5 py-4 text-center text-xs text-skin-neutral-9">
            No gallery images yet
        </p>

        <!-- Upload trigger -->
        <div class="mt-3">
            <input ref="fileInput" type="file" accept="image/*" multiple hidden @change="handleFiles" />
            <button
                type="button"
                class="flex w-full items-center justify-center gap-1.5 rounded-md border border-dashed border-skin-neutral-6 py-2 text-sm text-skin-neutral-10 transition hover:border-skin-primary-9 hover:text-skin-primary-10"
                @click="fileInput.click()"
            >
                <i class="ri-image-add-line text-base"></i>
                Add Images
            </button>
            <p class="mt-1 text-center text-xs text-skin-neutral-9">JPG, PNG, WEBP — max 2 MB each</p>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { router } from '@inertiajs/vue3'
import { useProductStore } from '../ProductStore'

const props = defineProps({
    productId: {
        type: Number,
        default: null
    },
    gallery: {
        type: Array,
        default: () => []
    }
})

const productStore = useProductStore()
const fileInput = ref(null)
const newPreviews = ref([])
const existingImages = ref([...props.gallery])

const totalCount = computed(() => existingImages.value.length + newPreviews.value.length)

const handleFiles = (event) => {
    const files = Array.from(event.target.files)

    if (!Array.isArray(productStore.product.gallery_images)) {
        productStore.product.gallery_images = []
    }

    files.forEach((file) => {
        productStore.product.gallery_images.push(file)
        const reader = new FileReader()
        reader.onload = (e) => newPreviews.value.push(e.target.result)
        reader.readAsDataURL(file)
    })

    event.target.value = null
}

const removeNew = (index) => {
    productStore.product.gallery_images.splice(index, 1)
    newPreviews.value.splice(index, 1)
}

const removeExisting = (mediaId) => {
    if (!props.productId) return

    existingImages.value = existingImages.value.filter((img) => img.id !== mediaId)

    router.delete(route('product.gallery.destroy', { id: props.productId, mediaId }), {
        preserveScroll: true,
        preserveState: false,
    })
}
</script>
