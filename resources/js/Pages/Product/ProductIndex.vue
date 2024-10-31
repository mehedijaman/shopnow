<template>
    <AppSectionHeader title="Products" :bread-crumb="breadCrumb">
        <template #right>
            <AppButton
                v-if="can('product-create')"
                class="btn btn-primary"
                @click="$inertia.visit(route('product.create'))"
            >
                Create Product
            </AppButton>
        </template>
    </AppSectionHeader>

    <AppDataSearch
        v-if="products.data.length || route().params.searchTerm"
        :url="route('product.index')"
        fields-to-search="title"
    ></AppDataSearch>

    <AppDataTable v-if="products.data.length" :headers="headers">
        <template #TableBody>
            <tbody>
                <AppDataTableRow v-for="item in products.data" :key="item.id">
                    <AppDataTableData>
                        <img
                            v-if="item.image_url"
                            :src="item.image_url"
                            class="h-10 w-10 rounded"
                        />

                        <AppImageNotAvailable v-else />
                    </AppDataTableData>

                    <AppDataTableData>
                        {{ item.name }}
                    </AppDataTableData>

                    <AppDataTableData>
                        {{ item.price }}
                    </AppDataTableData>

                    <AppDataTableData>
                        {{ item.sale_price }}
                    </AppDataTableData>

                    <AppDataTableData>
                        {{ item.quantity }}
                    </AppDataTableData>

                    <AppDataTableData>
                        {{ item.unit }}
                    </AppDataTableData>

                    <AppDataTableData>
                        {{ item.min_order }}
                    </AppDataTableData>

                    <AppDataTableData>
                        <span
                            class="rounded px-3 py-1 text-sm"
                            :class="getstatusClass(item.active)"
                        >
                            {{ item.active ? 'Active' : 'Inactive' }}
                        </span>

                        <span
                            v-if="item.featured"
                            class="rounded px-3 py-1 text-sm"
                            :class="getstatusClass(item.featured)"
                        >
                            Featured
                        </span>
                    </AppDataTableData>

                    <AppDataTableData>
                        <!-- Edit -->
                        <AppTooltip
                            v-if="can('product-edit')"
                            text="Edit Post"
                            class="mr-3"
                        >
                            <AppButton
                                class="btn btn-icon btn-primary"
                                @click="
                                    $inertia.visit(
                                        route('product.edit', item.id)
                                    )
                                "
                            >
                                <i class="ri-edit-line"></i>
                            </AppButton>
                        </AppTooltip>

                        <!-- Delete -->
                        <AppTooltip
                            v-if="can('product-delete')"
                            text="Delete Post"
                        >
                            <AppButton
                                class="btn btn-icon btn-destructive"
                                @click="
                                    confirmDelete(
                                        route('product.destroy', item.id)
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
        v-if="products.data.length"
        :links="products.links"
        :from="products.from || 0"
        :to="products.to || 0"
        :total="products.total || 0"
        class="mt-4 justify-center"
    ></AppPaginator>

    <AppAlert v-if="!products.data.length" class="mt-4">
        No products found.
    </AppAlert>

    <AppConfirmDialog ref="confirmDialogRef"></AppConfirmDialog>
</template>

<script setup>
import { ref } from 'vue'
import useAuthCan from '@/Composables/useAuthCan'
import AppImageNotAvailable from '@/Components/Modules/Blog/AppImageNotAvailable.vue'

const props = defineProps({
    products: {
        type: Object,
        default: () => {}
    }
})

const breadCrumb = [
    { label: 'Home', href: route('dashboard.index') },
    { label: 'Products', last: true }
]

const headers = [
    'Image',
    'Name',
    'Price',
    'Sale Price',
    'Quantity',
    'Unit',
    'Min Order',
    'Status',
    'Actions'
]

const getstatusClass = (status) => {
    return status ? 'active' : 'inactive'
}

const confirmDialogRef = ref(null)
const confirmDelete = (deleteRoute) => {
    confirmDialogRef.value.openModal(deleteRoute)
}

const { can } = useAuthCan()
</script>

<style scoped>
.active {
    @apply bg-skin-success-light text-skin-success;
}

.inactive {
    @apply bg-skin-warning-light text-skin-warning;
}
</style>
