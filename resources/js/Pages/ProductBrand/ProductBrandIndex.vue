<template>
    <AppSectionHeader title="Product Brands" :bread-crumb="breadCrumb">
        <template #right>
            <AppButton
                v-if="can('product-brand-create')"
                class="btn btn-primary"
                @click="$inertia.visit(route('productBrand.create'))"
            >
                Create Brand
            </AppButton>
        </template>
    </AppSectionHeader>

    <AppDataSearch
        v-if="brands.data.length || route().params.searchTerm"
        :url="route('productBrand.index')"
        fields-to-search="name"
    ></AppDataSearch>

    <AppDataTable v-if="brands.data.length" :headers="headers">
        <template #TableBody>
            <tbody>
                <AppDataTableRow v-for="item in brands.data" :key="item.id">
                    <AppDataTableData>
                        <img
                            v-if="item.image_url"
                            :src="item.image_url"
                            class="h-12 w-20 rounded"
                        />

                        <AppImageNotAvailable v-else class="!h-12 !w-20" />
                    </AppDataTableData>

                    <AppDataTableData>
                        {{ item.name }}
                    </AppDataTableData>

                    <AppDataTableData>
                        <div class="flex gap-2">
                            <span
                                class="rounded px-3 py-1 text-sm"
                                :class="getStatusClass(item.active)"
                            >
                                {{ item.active ? 'Active' : 'Inactive' }}
                            </span>

                            <span
                                v-if="item.featured"
                                class="active rounded px-3 py-1 text-sm"
                            >
                                Featured
                            </span>
                        </div>
                    </AppDataTableData>

                    <AppDataTableData>
                        <!-- Edit -->
                        <AppTooltip
                            v-if="can('product-brand-edit')"
                            text="Edit Brand"
                            class="mr-3"
                        >
                            <AppButton
                                class="btn btn-icon btn-primary"
                                @click="
                                    $inertia.visit(
                                        route('productBrand.edit', item.id)
                                    )
                                "
                            >
                                <i class="ri-edit-line"></i>
                            </AppButton>
                        </AppTooltip>

                        <!-- Delete -->
                        <AppTooltip
                            v-if="can('product-brand-delete')"
                            text="Delete Brand"
                        >
                            <AppButton
                                class="btn btn-icon btn-destructive"
                                @click="
                                    confirmDelete(
                                        route('productBrand.destroy', item.id)
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
        :links="brands.links"
        :from="brands.from || 0"
        :to="brands.to || 0"
        :total="brands.total || 0"
        class="mt-4 justify-center"
    ></AppPaginator>

    <AppAlert v-if="!brands.data.length" class="mt-4">
        No brands found.
    </AppAlert>

    <AppConfirmDialog ref="confirmDialogRef"></AppConfirmDialog>
</template>

<script setup>
import { ref } from 'vue'
import useAuthCan from '@/Composables/useAuthCan'
import AppImageNotAvailable from '@/Components/Modules/Blog/AppImageNotAvailable.vue'

const props = defineProps({
    brands: {
        type: Object,
        default: () => {}
    }
})

const breadCrumb = [
    { label: 'Home', href: route('dashboard.index') },
    { label: 'brands', last: true }
]

const headers = ['Image', 'Name', 'Status', 'Actions']

const getStatusClass = (active) => {
    return active ? 'active' : 'inactive'
}

const confirmDialogRef = ref(null)
const confirmDelete = (deleteRoute) => {
    confirmDialogRef.value.openModal(deleteRoute)
}

const { can } = useAuthCan()
</script>
