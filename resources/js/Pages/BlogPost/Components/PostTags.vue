<template>
    <AppCombobox
        v-model="selectedTag"
        :options="tags"
        combo-label="Select a Tag"
        class="w-full"
    />

    <div v-if="postStore.post.tags.length" class="mt-3 flex flex-wrap gap-1.5">
        <span
            v-for="tag in postStore.post.tags"
            :key="tag.id"
            class="inline-flex items-center gap-1 rounded-full bg-skin-primary-2 px-2.5 py-1 text-xs font-medium text-skin-primary-9"
        >
            {{ tag.name }}
            <button
                type="button"
                class="ml-0.5 hover:text-skin-primary-12 focus:outline-none"
                @click="removeTag(tag)"
            >
                <i class="ri-close-line text-xs"></i>
            </button>
        </span>
    </div>
</template>

<script setup>
import { ref, watch } from 'vue'
import { usePostStore } from '../PostStore'
const postStore = usePostStore()

const props = defineProps({
    tags: {
        type: Object,
        default: () => {}
    }
})

const selectedTag = ref(null)

const updateTagsStatus = () => {
    if (!postStore.tagsHasChanged) {
        postStore.post.tagsHasChanged = true
    }
}

watch(selectedTag, (value) => {
    if (!value) {
        return
    }

    const postTags = postStore.post.tags

    const tag = {
        id: value.value,
        name: value.label
    }

    if (!postTags.some((tag) => tag.id === value.value)) {
        postTags.push(tag)

        updateTagsStatus()
    }
})

const removeTag = (tag) => {
    const postTags = postStore.post.tags
    const index = postTags.indexOf(tag)

    postTags.splice(index, 1)

    updateTagsStatus()
}
</script>
