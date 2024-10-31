<template>
    <AppSectionHeader title="Categories" :bread-crumb="breadCrumb">
        <template #right>
            <AppButton
                v-if="can('Blog: Category - Create')"
                class="btn btn-primary"
                @click="$inertia.visit(route('productCategory.create'))"
            >
                Create Category
            </AppButton>
        </template>
    </AppSectionHeader>

    <AppDataSearch
        v-if="categories.data.length || route().params.searchTerm"
        :url="route('productCategory.index')"
        fields-to-search="name"
    ></AppDataSearch>

    <AppDataTable v-if="categories.data.length" :headers="headers">
        <template #TableBody>
            <tbody>
                <AppDataTableRow v-for="item in categories.data" :key="item.id">
                    <AppDataTableData>
                        <img
                            v-if="item.image_url"
                            :src="item.image_url"
                            class="h-6 w-24 rounded"
                        />

                        <AppImageNotAvailable v-else class="!h-6 !w-24" />
                    </AppDataTableData>

                    <AppDataTableData>
                        {{ item.name }}
                    </AppDataTableData>

                    <AppDataTableData>
                        <span
                            class="rounded px-3 py-1 text-sm"
                            :class="getCategoryStatusClass(item.is_visible)"
                        >
                            {{ item.is_visible ? 'Visible' : 'Invisible' }}
                        </span>
                    </AppDataTableData>

                    <AppDataTableData>
                        <!-- edit category -->
                        <AppTooltip
                            v-if="can('product-category-edit')"
                            text="Edit Category"
                            class="mr-3"
                        >
                            <AppButton
                                class="btn btn-icon btn-primary"
                                @click="
                                    $inertia.visit(
                                        route('productCategory.edit', item.id)
                                    )
                                "
                            >
                                <i class="ri-edit-line"></i>
                            </AppButton>
                        </AppTooltip>

                        <!-- delete category -->
                        <AppTooltip
                            v-if="can('product-category-delete')"
                            text="Delete Category"
                        >
                            <AppButton
                                class="btn btn-icon btn-destructive"
                                @click="
                                    confirmDelete(
                                        route(
                                            'productCategory.destroy',
                                            item.id
                                        )
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
        :links="categories.links"
        :from="categories.from || 0"
        :to="categories.to || 0"
        :total="categories.total || 0"
        class="mt-4 justify-center"
    ></AppPaginator>

    <AppAlert v-if="!categories.data.length" class="mt-4">
        No categories found.
    </AppAlert>

    <AppConfirmDialog ref="confirmDialogRef"></AppConfirmDialog>
</template>

<script setup>
import { ref } from 'vue'
import useAuthCan from '@/Composables/useAuthCan'
import AppImageNotAvailable from '@/Components/Modules/Blog/AppImageNotAvailable.vue'

const props = defineProps({
    categories: {
        type: Object,
        default: () => {}
    }
})

const breadCrumb = [
    { label: 'Home', href: route('dashboard.index') },
    { label: 'Categories', last: true }
]

const headers = ['Image', 'Name', 'Status', 'Actions']

const getCategoryStatusClass = (isVisible) => {
    return isVisible ? 'category-visible' : 'category-invisible'
}

const confirmDialogRef = ref(null)
const confirmDelete = (deleteRoute) => {
    confirmDialogRef.value.openModal(deleteRoute)
}

const { can } = useAuthCan()
</script>

<style scoped>
.category-visible {
    @apply bg-skin-success-light text-skin-success;
}

.category-invisible {
    @apply bg-skin-warning-light text-skin-warning;
}
</style>
