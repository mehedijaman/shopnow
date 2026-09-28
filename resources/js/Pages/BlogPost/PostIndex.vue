<template>
    <AppSectionHeader :title="__('blog::admin.posts')" :bread-crumb="breadCrumb">
        <template #right>
            <div class="flex gap-2">
                <AppButton
                    v-if="can('Blog: Post - Recycle Bin List')"
                    class="btn btn-secondary"
                    @click="$inertia.visit(route('blogPost.recycleBin.index'))"
                >
                    <i class="ri-delete-bin-line mr-1"></i> {{ __('common.recycle_bin') }}
                </AppButton>
                <AppButton
                    v-if="can('Blog: Post - Create')"
                    class="btn btn-primary"
                    @click="$inertia.visit(route('blogPost.create'))"
                >
                    {{ __('blog::admin.create_post') }}
                </AppButton>
            </div>
        </template>
    </AppSectionHeader>

    <AppDataSearch
        v-if="posts.data.length || route().params.searchTerm"
        :url="route('blogPost.index')"
        fields-to-search="title"
    ></AppDataSearch>

    <AppDataTable v-if="posts.data.length" :headers="headers">
        <template #TableBody>
            <tbody>
                <AppDataTableRow v-for="item in posts.data" :key="item.id">
                    <AppDataTableData>
                        <img
                            v-if="item.image_url"
                            :src="item.image_url"
                            class="h-10 w-10 rounded-sm"
                        />

                        <AppImageNotAvailable v-else />
                    </AppDataTableData>

                    <AppDataTableData>
                        {{ item.title }}
                    </AppDataTableData>

                    <AppDataTableData>
                        <span
                            class="rounded-sm px-3 py-1 text-sm"
                            :class="getPostStatusClass(item.status)"
                        >
                            {{ postStatusText(item.status) }}
                        </span>
                    </AppDataTableData>

                    <AppDataTableData>
                        <!-- edit post -->
                        <AppTooltip
                            v-if="can('Blog: Post - Edit')"
                            :text="__('blog::admin.edit_post')"
                            class="mr-3"
                        >
                            <AppButton
                                class="btn btn-icon btn-primary"
                                @click="
                                    $inertia.visit(
                                        route('blogPost.edit', item.id)
                                    )
                                "
                            >
                                <i class="ri-edit-line"></i>
                            </AppButton>
                        </AppTooltip>

                        <!-- delete post -->
                        <AppTooltip
                            v-if="can('Blog: Post - Delete')"
                            :text="__('blog::admin.delete_post')"
                        >
                            <AppButton
                                class="btn btn-icon btn-destructive"
                                @click="
                                    confirmDelete(
                                        route('blogPost.destroy', item.id)
                                    )
                                "
                            >
                                <i class="ri-delete-bin-line"></i>
                            </AppButton>
                        </AppTooltip>
                    </AppDataTableData>
                </AppDataTableRow>
            </tbody>
        </template>
    </AppDataTable>

    <AppPaginator
        :links="posts.links"
        :from="posts.from || 0"
        :to="posts.to || 0"
        :total="posts.total || 0"
        class="mt-4 justify-center"
    ></AppPaginator>

    <AppAlert v-if="!posts.data.length" class="mt-4">
        {{ __('blog::admin.no_posts_found') }}
    </AppAlert>

    <AppConfirmDialog ref="confirmDialogRef"></AppConfirmDialog>
</template>

<script setup>
import { inject, ref } from 'vue'
import useAuthCan from '@/Composables/useAuthCan'
import AppImageNotAvailable from '@/Components/Modules/Blog/AppImageNotAvailable.vue'

const props = defineProps({
    posts: {
        type: Object,
        default: () => {}
    }
})

const translate = inject('translate')

const breadCrumb = [
    { label: translate('common.home'), href: route('dashboard.index') },
    { label: translate('blog::admin.posts'), last: true }
]

const headers = [
    translate('blog::admin.image'),
    translate('blog::admin.title'),
    translate('common.field.status'),
    translate('common.header.actions'),
]

const postStatusText = (status) => {
    if (status === 'Published') return translate('blog::admin.published')
    if (status === 'Draft') return translate('blog::admin.draft')
    return status
}

const getPostStatusClass = (status) => {
    return status === 'Published' ? 'published' : 'draft'
}

const confirmDialogRef = ref(null)
const confirmDelete = (deleteRoute) => {
    confirmDialogRef.value.openModal(deleteRoute)
}

const { can } = useAuthCan()
</script>

<style scoped>
@reference "../../../css/app.css";

.published {
    @apply bg-skin-success-light  text-skin-success;
}

.draft {
    @apply bg-skin-warning-light  text-skin-warning;
}
</style>
