<template>
    <!-- WP-style large title input -->
    <div class="border-b border-skin-neutral-4 px-5 pt-5 pb-4">
        <input
            id="title"
            v-model="postStore.post.title"
            type="text"
            placeholder="Add title"
            class="w-full border-0 bg-transparent text-2xl font-bold text-skin-neutral-12 placeholder-skin-neutral-6 outline-none focus:ring-0"
            :class="{ 'placeholder-red-400': errorsFields.includes('title') }"
        />
    </div>

    <!-- Rich text editor -->
    <div class="p-5">
        <AppTipTapEditor
            v-model="postStore.post.content"
            editor-id="notes"
            :class="{
                'app-tip-tap-error': errorsFields.includes('content')
            }"
            :file-upload-url="route('blogPost.uploadEditorImage')"
        />
    </div>
</template>

<script setup>
import useFormErrors from '@/Composables/useFormErrors'
import { usePostStore } from '../PostStore'
const postStore = usePostStore()
const { errorsFields } = useFormErrors()
</script>
