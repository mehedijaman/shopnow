<template>
    <!-- Top bar -->
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <button
                type="button"
                class="inline-flex items-center gap-1.5 text-sm text-skin-neutral-9 hover:text-skin-neutral-12"
                @click="$inertia.visit(route('blogPost.index'))"
            >
                <i class="ri-arrow-left-s-line"></i>
                All Posts
            </button>
            <span class="h-4 w-px bg-skin-neutral-5"></span>
            <span class="text-sm font-medium text-skin-neutral-9">{{ title }}</span>
        </div>

        <AppButton
            v-if="can(isCreate ? 'Blog: Post - Create' : 'Blog: Post - Edit')"
            class="btn btn-primary"
            @click="submitForm"
        >
            <i class="ri-save-line mr-1.5"></i>
            {{ isCreate ? 'Publish' : 'Update' }}
        </AppButton>
    </div>

    <AppFormErrors class="mb-4" />

    <!-- WordPress-style layout -->
    <div class="grid grid-cols-1 gap-5 xl:grid-cols-[1fr_300px]">

        <!-- Main column -->
        <div class="space-y-5">

            <!-- Title + Rich text editor -->
            <div class="overflow-hidden rounded-xl bg-skin-neutral-1 shadow-xs ring-1 ring-skin-neutral-4">
                <PostBody />
            </div>

            <!-- SEO -->
            <div class="overflow-hidden rounded-xl bg-skin-neutral-1 shadow-xs ring-1 ring-skin-neutral-4">
                <PostSeo />
            </div>

        </div>

        <!-- Sidebar -->
        <div class="space-y-4">

            <!-- Publish -->
            <div class="overflow-hidden rounded-xl bg-skin-neutral-1 shadow-xs ring-1 ring-skin-neutral-4">
                <div class="flex items-center gap-2 border-b border-skin-neutral-4 bg-skin-neutral-2 px-4 py-2.5">
                    <i class="ri-send-plane-line text-xs text-skin-neutral-8"></i>
                    <h3 class="text-xs font-semibold uppercase tracking-wider text-skin-neutral-8">Publish</h3>
                </div>
                <div class="p-4">
                    <PostPublishDate />
                    <AppButton
                        v-if="can(isCreate ? 'Blog: Post - Create' : 'Blog: Post - Edit')"
                        class="btn btn-primary mt-4 w-full"
                        @click="submitForm"
                    >
                        <i class="ri-save-line mr-1.5"></i>
                        {{ isCreate ? 'Publish Post' : 'Update Post' }}
                    </AppButton>
                </div>
            </div>

            <!-- Featured Image -->
            <div class="overflow-hidden rounded-xl bg-skin-neutral-1 shadow-xs ring-1 ring-skin-neutral-4">
                <div class="flex items-center gap-2 border-b border-skin-neutral-4 bg-skin-neutral-2 px-4 py-2.5">
                    <i class="ri-image-line text-xs text-skin-neutral-8"></i>
                    <h3 class="text-xs font-semibold uppercase tracking-wider text-skin-neutral-8">Featured Image</h3>
                </div>
                <div class="p-4">
                    <PostImage />
                </div>
            </div>

            <!-- Category -->
            <div class="rounded-xl bg-skin-neutral-1 shadow-xs ring-1 ring-skin-neutral-4">
                <div class="flex items-center gap-2 rounded-t-xl border-b border-skin-neutral-4 bg-skin-neutral-2 px-4 py-2.5">
                    <i class="ri-folder-line text-xs text-skin-neutral-8"></i>
                    <h3 class="text-xs font-semibold uppercase tracking-wider text-skin-neutral-8">Category</h3>
                </div>
                <div class="p-4">
                    <PostCategory :categories="categories" />
                </div>
            </div>

            <!-- Tags -->
            <div class="rounded-xl bg-skin-neutral-1 shadow-xs ring-1 ring-skin-neutral-4">
                <div class="flex items-center gap-2 rounded-t-xl border-b border-skin-neutral-4 bg-skin-neutral-2 px-4 py-2.5">
                    <i class="ri-price-tag-3-line text-xs text-skin-neutral-8"></i>
                    <h3 class="text-xs font-semibold uppercase tracking-wider text-skin-neutral-8">Tags</h3>
                </div>
                <div class="p-4">
                    <PostTags :tags="tags" />
                </div>
            </div>

            <!-- Author -->
            <div class="rounded-xl bg-skin-neutral-1 shadow-xs ring-1 ring-skin-neutral-4">
                <div class="flex items-center gap-2 rounded-t-xl border-b border-skin-neutral-4 bg-skin-neutral-2 px-4 py-2.5">
                    <i class="ri-user-line text-xs text-skin-neutral-8"></i>
                    <h3 class="text-xs font-semibold uppercase tracking-wider text-skin-neutral-8">Author</h3>
                </div>
                <div class="p-4">
                    <PostAuthor :authors="authors" />
                </div>
            </div>

        </div>
    </div>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3'
import useAuthCan from '@/Composables/useAuthCan'

import { onUnmounted } from 'vue'

import useTitle from '@/Composables/useTitle'
import useFormContext from '@/Composables/useFormContext'
import PostBody from './Components/PostBody.vue'
import PostImage from './Components/PostImage.vue'
import PostSeo from './Components/PostSeo.vue'
import PostPublishDate from './Components/PostPublishDate.vue'
import PostCategory from './Components/PostCategory.vue'
import PostTags from './Components/PostTags.vue'
import PostAuthor from './Components/PostAuthor.vue'
import { usePostStore } from './PostStore'

const postStore = usePostStore()
const { can } = useAuthCan()

const props = defineProps({
    post: {
        type: Object,
        default: null
    },

    categories: {
        type: Object,
        default: () => {}
    },

    tags: {
        type: Object,
        default: () => {}
    },

    authors: {
        type: Object,
        default: () => {}
    }
})

if (props.post) {
    postStore.setPost(props.post)
}

onUnmounted(() => {
    postStore.$reset()
})

const breadCrumb = [
    { label: 'Home', href: route('dashboard.index') },
    { label: 'Posts', href: route('blogPost.index') },
    { label: 'Post', last: true }
]

const { title } = useTitle('Blog Post')
const { isCreate } = useFormContext()

const getValueFromKey = (data, key) => {
    return data[key] ? data[key].value : null
}

const submitForm = () => {
    const form = useForm(postStore.post)

    const postData = (data) => {
        const commonData = {
            ...data,
            blog_category_id: getValueFromKey(data, 'blog_category_id'),
            blog_author_id: getValueFromKey(data, 'blog_author_id')
        }

        return isCreate.value ? commonData : { ...commonData, _method: 'PUT' }
    }

    if (isCreate.value) {
        form.transform(postData).post(route('blogPost.store'))
    } else {
        form.transform(postData).post(route('blogPost.update', props.post.id))
    }
}
</script>
