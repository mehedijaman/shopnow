<template>
    <p class="mb-1 mt-5">Tag</p>
    <AppCombobox
        v-model="selectedTag"
        :options="tags"
        combo-label="Select a Tag"
        class="w-64 xl:w-full"
    />

    <ul class="mt-2">
        <li
            v-for="tag in productStore.product.tags"
            :key="tag.id"
            class="mb-3 flex items-center justify-between rounded bg-skin-neutral-3 p-3"
        >
            <span>
                {{ tag.name }}
            </span>

            <i
                class="ri-close-line text-skin-neutral-11 hover:cursor-pointer hover:text-skin-neutral-12"
                @click="removeTag(tag)"
            ></i>
        </li>
    </ul>
</template>

<script setup>
import { ref, watch } from 'vue'
import { useProductStore } from '../ProductStore'
const productStore = useProductStore()

const props = defineProps({
    tags: {
        type: Object,
        default: () => {}
    }
})

const selectedTag = ref(null)

const updateTagsStatus = () => {
    if (!productStore.tagsHasChanged) {
        productStore.product.tagsHasChanged = true
    }
}

watch(selectedTag, (value) => {
    if (!value) {
        return
    }

    const productTags = productStore.product.tags

    const tag = {
        id: value.value,
        name: value.label
    }

    if (!productTags.some((tag) => tag.id === value.value)) {
        productTags.push(tag)

        updateTagsStatus()
    }
})

const removeTag = (tag) => {
    const productTags = productStore.product.tags
    const index = productTags.indexOf(tag)

    productTags.splice(index, 1)

    updateTagsStatus()
}
</script>
