<template>
    <Head title="Posts Recycle Bin"></Head>
    <AppSectionHeader title="Posts — Recycle Bin" :bread-crumb="breadCrumb">
        <template #right>
            <div class="flex gap-2">
                <AppButton class="btn btn-secondary" @click="$inertia.visit(route('blogPost.index'))">
                    <i class="ri-arrow-left-line mr-1"></i> Back
                </AppButton>
                <AppButton v-if="can('Blog: Post - Recycle Bin Restore') && posts.data.length"
                    class="btn btn-secondary" @click="restoreAll">
                    <i class="ri-arrow-go-back-line mr-1"></i> Restore All
                </AppButton>
                <AppButton v-if="can('Blog: Post - Recycle Bin Delete') && posts.data.length"
                    class="btn btn-destructive" @click="emptyBin">
                    <i class="ri-delete-bin-line mr-1"></i> Empty Bin
                </AppButton>
            </div>
        </template>
    </AppSectionHeader>

    <AppDataTable v-if="posts.data.length" :headers="headers">
        <template #TableBody>
            <tbody>
                <AppDataTableRow v-for="(item, index) in posts.data" :key="item.id">
                    <AppDataTableData class="w-16">
                        {{ posts.from + index }}
                    </AppDataTableData>
                    <AppDataTableData class="w-20">
                        <img
                            v-if="item.image_url"
                            :src="item.image_url"
                            class="h-10 w-10 rounded-sm object-cover"
                        />
                        <AppImageNotAvailable v-else />
                    </AppDataTableData>
                    <AppDataTableData class="font-medium text-skin-neutral-12">
                        {{ item.title }}
                    </AppDataTableData>
                    <AppDataTableData>
                        <span
                            class="rounded-sm px-3 py-1 text-sm"
                            :class="getPostStatusClass(item.status)"
                        >
                            {{ item.status }}
                        </span>
                    </AppDataTableData>
                    <AppDataTableData class="w-40 text-right">
                        <AppTooltip v-if="can('Blog: Post - Recycle Bin Restore')" text="Restore" class="mr-3">
                            <AppButton class="btn btn-icon btn-primary"
                                @click="router.get(route('blogPost.recycleBin.restore', item.id))">
                                <i class="ri-arrow-go-back-line"></i>
                            </AppButton>
                        </AppTooltip>
                        <AppTooltip v-if="can('Blog: Post - Recycle Bin Delete')" text="Delete Permanently">
                            <AppButton class="btn btn-icon btn-destructive"
                                @click="confirmDelete(route('blogPost.recycleBin.destroyForce', item.id))">
                                <i class="ri-delete-bin-line"></i>
                            </AppButton>
                        </AppTooltip>
                    </AppDataTableData>
                </AppDataTableRow>
            </tbody>
        </template>
    </AppDataTable>

    <AppPaginator v-if="posts.data.length" :links="posts.links" :from="posts.from || 0"
        :to="posts.to || 0" :total="posts.total || 0" class="mt-4 justify-center" />

    <AppAlert v-if="!posts.data.length" class="mt-4">
        The recycle bin is empty.
    </AppAlert>

    <AppConfirmDialog ref="confirmDialogRef" />
</template>

<script setup>
import { ref } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import useAuthCan from '@/Composables/useAuthCan'
import AppImageNotAvailable from '@/Components/Modules/Blog/AppImageNotAvailable.vue'

const props = defineProps({
    posts: { type: Object, default: () => ({}) },
})

const breadCrumb = [
    { label: 'Home', href: route('dashboard.index') },
    { label: 'Posts', href: route('blogPost.index') },
    { label: 'Recycle Bin', last: true },
]

const headers = ['SL', 'Image', 'Title', 'Status', 'Actions']

const confirmDialogRef = ref(null)
const confirmDelete = (deleteRoute) => {
    confirmDialogRef.value.openModal(deleteRoute)
}

const restoreAll = () => router.get(route('blogPost.recycleBin.restoreAll'))
const emptyBin = () => confirmDialogRef.value.openModal(route('blogPost.recycleBin.empty'))

const getPostStatusClass = (status) => {
    return status === 'Published' ? 'published' : 'draft'
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
