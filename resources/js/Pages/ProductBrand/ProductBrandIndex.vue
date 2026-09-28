<template>
    <AppSectionHeader :title="__('common.menu.product_brands')" :bread-crumb="breadCrumb">
        <template #right>
            <div class="flex gap-2">
                <AppButton
                    v-if="can('product-brand-recycle-bin-list')"
                    class="btn btn-secondary"
                    @click="$inertia.visit(route('productBrand.recycleBin.index'))"
                >
                    <i class="ri-delete-bin-2-line mr-1"></i> {{ __('common.recycle_bin') }}
                </AppButton>
                <AppButton
                    v-if="can('product-brand-create')"
                    class="btn btn-primary"
                    @click="$inertia.visit(route('productBrand.create'))"
                >
                    {{ __('product::admin.create_brand') }}
                </AppButton>
            </div>
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
                        <div class="flex items-center gap-3">
                            <img
                                v-if="item.image_url"
                                :src="item.image_url"
                                class="h-12 w-20 rounded-sm object-cover"
                            />
                            <span class="font-medium text-skin-neutral-12">{{ item.name }}</span>
                        </div>
                    </AppDataTableData>

                    <AppDataTableData>
                        {{ item.products_count }}
                    </AppDataTableData>

                    <AppDataTableData>
                        <div class="flex gap-2">
                            <span
                                class="rounded-sm px-3 py-1 text-sm"
                                :class="getStatusClass(item.active)"
                            >
                                {{ item.active ? __('common.field.active') : __('common.field.inactive') }}
                            </span>

                            <span
                                v-if="item.featured"
                                class="active rounded-sm px-3 py-1 text-sm"
                            >
                                {{ __('common.field.featured') }}
                            </span>
                        </div>
                    </AppDataTableData>

                    <AppDataTableData>
                        <!-- Edit -->
                        <AppTooltip
                            v-if="can('product-brand-edit')"
                            :text="__('product::admin.edit_brand')"
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
                            :text="__('product::admin.delete_brand')"
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
        {{ __('product::admin.brands_empty') }}
    </AppAlert>

    <AppConfirmDialog ref="confirmDialogRef"></AppConfirmDialog>
</template>

<script setup>
import { ref, computed, inject } from 'vue'
import useAuthCan from '@/Composables/useAuthCan'

const props = defineProps({
    brands: {
        type: Object,
        default: () => {}
    }
})

const translate = inject('translate')

const breadCrumb = [
    { label: translate('common.home'), href: route('dashboard.index') },
    { label: translate('common.menu.product_brands'), last: true }
]

const headers = computed(() => [
    translate('common.header.brand'),
    translate('common.header.products'),
    translate('common.header.status'),
    translate('common.header.actions'),
])

const getStatusClass = (active) => {
    return active ? 'active' : 'inactive'
}

const confirmDialogRef = ref(null)
const confirmDelete = (deleteRoute) => {
    confirmDialogRef.value.openModal(deleteRoute)
}

const { can } = useAuthCan()
</script>
