<template>
    <AppSectionHeader title="Product Categories" :bread-crumb="breadCrumb">
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

    <div v-if="categories.data.length">
        <div class="mb-2 flex items-center gap-2 text-sm text-skin-neutral-9">
            <i class="ri-drag-move-line"></i>
            <span>Drag rows to reorder. Order is saved automatically.</span>
            <span v-if="saving" class="ml-2 text-skin-primary-9">Saving…</span>
            <span v-if="saved" class="ml-2 text-green-600">Saved!</span>
        </div>

        <AppDataTable :headers="headers">
            <template #TableBody>
                <Draggable
                    v-model="sortableCategories"
                    tag="tbody"
                    item-key="id"
                    handle=".drag-handle"
                    ghost-class="opacity-40"
                    @end="onReorder"
                >
                    <template #item="{ element: item }">
                        <AppDataTableRow :key="item.id">
                            <AppDataTableData style="width:40px;">
                                <i class="drag-handle ri-drag-move-line cursor-grab text-skin-neutral-9 active:cursor-grabbing"></i>
                            </AppDataTableData>

                            <AppDataTableData>
                                <img
                                    v-if="item.image_url"
                                    :src="item.image_url"
                                    class="h-12 w-20 rounded-sm"
                                />
                                <AppImageNotAvailable v-else class="h-12! w-20!" />
                            </AppDataTableData>

                            <AppDataTableData>
                                {{ item.name }}
                            </AppDataTableData>

                            <AppDataTableData>
                                <div class="flex gap-2">
                                    <span
                                        class="rounded-sm px-3 py-1 text-sm"
                                        :class="getStatusClass(item.active)"
                                    >
                                        {{ item.active ? 'Active' : 'Inactive' }}
                                    </span>
                                    <span
                                        v-if="item.featured"
                                        class="active rounded-sm px-3 py-1 text-sm"
                                    >
                                        Featured
                                    </span>
                                </div>
                            </AppDataTableData>

                            <AppDataTableData>
                                <AppTooltip
                                    v-if="can('product-category-edit')"
                                    text="Edit Category"
                                    class="mr-3"
                                >
                                    <AppButton
                                        class="btn btn-icon btn-primary"
                                        @click="$inertia.visit(route('productCategory.edit', item.id))"
                                    >
                                        <i class="ri-edit-line"></i>
                                    </AppButton>
                                </AppTooltip>

                                <AppTooltip
                                    v-if="can('product-category-delete')"
                                    text="Delete Category"
                                >
                                    <AppButton
                                        class="btn btn-icon btn-destructive"
                                        @click="confirmDelete(route('productCategory.destroy', item.id))"
                                    >
                                        <i class="ri-delete-bin-line"></i>
                                    </AppButton>
                                </AppTooltip>
                            </AppDataTableData>
                        </AppDataTableRow>
                    </template>
                </Draggable>
            </template>
        </AppDataTable>
    </div>

    <AppAlert v-if="!categories.data.length" class="mt-4">
        No categories found.
    </AppAlert>

    <AppConfirmDialog ref="confirmDialogRef"></AppConfirmDialog>
</template>

<script setup>
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'
import Draggable from 'vuedraggable'
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

const headers = ['', 'Image', 'Name', 'Status', 'Actions']

const getStatusClass = (active) => {
    return active ? 'active' : 'inactive'
}

const sortableCategories = ref([...props.categories.data])

const saving = ref(false)
const saved = ref(false)
let savedTimer = null

const onReorder = () => {
    saving.value = true
    saved.value = false

    const items = sortableCategories.value.map((cat, index) => ({
        id: cat.id,
        sort_order: index,
    }))

    router.post(route('productCategory.reorder'), { items }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            saving.value = false
            saved.value = true
            clearTimeout(savedTimer)
            savedTimer = setTimeout(() => { saved.value = false }, 2000)
        },
        onError: () => {
            saving.value = false
        },
    })
}

const confirmDialogRef = ref(null)
const confirmDelete = (deleteRoute) => {
    confirmDialogRef.value.openModal(deleteRoute)
}

const { can } = useAuthCan()
</script>
