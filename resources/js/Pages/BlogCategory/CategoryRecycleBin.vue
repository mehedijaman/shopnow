<template>
    <Head title="Categories Recycle Bin"></Head>
    <AppSectionHeader title="Categories — Recycle Bin" :bread-crumb="breadCrumb">
        <template #right>
            <div class="flex gap-2">
                <AppButton class="btn btn-secondary" @click="$inertia.visit(route('blogCategory.index'))">
                    <i class="ri-arrow-left-line mr-1"></i> Back
                </AppButton>
                <AppButton v-if="can('Blog: Category - Recycle Bin Restore') && categories.data.length"
                    class="btn btn-secondary" @click="restoreAll">
                    <i class="ri-arrow-go-back-line mr-1"></i> Restore All
                </AppButton>
                <AppButton v-if="can('Blog: Category - Recycle Bin Delete') && categories.data.length"
                    class="btn btn-destructive" @click="emptyBin">
                    <i class="ri-delete-bin-line mr-1"></i> Empty Bin
                </AppButton>
            </div>
        </template>
    </AppSectionHeader>

    <AppDataTable v-if="categories.data.length" :headers="headers">
        <template #TableBody>
            <tbody>
                <AppDataTableRow v-for="(item, index) in categories.data" :key="item.id">
                    <AppDataTableData class="w-16">
                        {{ categories.from + index }}
                    </AppDataTableData>
                    <AppDataTableData class="w-20">
                        <img
                            v-if="item.image_url"
                            :src="item.image_url"
                            class="h-6 w-24 rounded-sm object-cover"
                        />
                        <AppImageNotAvailable v-else class="h-6! w-24!" />
                    </AppDataTableData>
                    <AppDataTableData class="font-medium text-skin-neutral-12">
                        {{ item.name }}
                    </AppDataTableData>
                    <AppDataTableData>
                        <span
                            class="rounded-sm px-3 py-1 text-sm"
                            :class="getCategoryVisibilityClass(item.is_visible)"
                        >
                            {{ item.is_visible ? 'Visible' : 'Invisible' }}
                        </span>
                    </AppDataTableData>
                    <AppDataTableData class="w-40 text-right">
                        <AppTooltip v-if="can('Blog: Category - Recycle Bin Restore')" text="Restore" class="mr-3">
                            <AppButton class="btn btn-icon btn-primary"
                                @click="router.get(route('blogCategory.recycleBin.restore', item.id))">
                                <i class="ri-arrow-go-back-line"></i>
                            </AppButton>
                        </AppTooltip>
                        <AppTooltip v-if="can('Blog: Category - Recycle Bin Delete')" text="Delete Permanently">
                            <AppButton class="btn btn-icon btn-destructive"
                                @click="confirmDelete(route('blogCategory.recycleBin.destroyForce', item.id))">
                                <i class="ri-delete-bin-line"></i>
                            </AppButton>
                        </AppTooltip>
                    </AppDataTableData>
                </AppDataTableRow>
            </tbody>
        </template>
    </AppDataTable>

    <AppPaginator v-if="categories.data.length" :links="categories.links" :from="categories.from || 0"
        :to="categories.to || 0" :total="categories.total || 0" class="mt-4 justify-center" />

    <AppAlert v-if="!categories.data.length" class="mt-4">
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
    categories: { type: Object, default: () => ({}) },
})

const breadCrumb = [
    { label: 'Home', href: route('dashboard.index') },
    { label: 'Categories', href: route('blogCategory.index') },
    { label: 'Recycle Bin', last: true },
]

const headers = ['SL', 'Image', 'Name', 'Visibility', 'Actions']

const confirmDialogRef = ref(null)
const confirmDelete = (deleteRoute) => {
    confirmDialogRef.value.openModal(deleteRoute)
}

const restoreAll = () => router.get(route('blogCategory.recycleBin.restoreAll'))
const emptyBin = () => confirmDialogRef.value.openModal(route('blogCategory.recycleBin.empty'))

const getCategoryVisibilityClass = (isVisible) => {
    return isVisible ? 'category-visible' : 'category-invisible'
}

const { can } = useAuthCan()
</script>

<style scoped>
@reference "../../../css/app.css";

.category-visible {
    @apply bg-skin-success-light  text-skin-success;
}

.category-invisible {
    @apply bg-skin-warning-light  text-skin-warning;
}
</style>
