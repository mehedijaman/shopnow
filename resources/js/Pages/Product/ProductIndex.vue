<template>

    <Head title="Products"></Head>
    <AppSectionHeader title="Products" :bread-crumb="breadCrumb">
        <template #right>
            <div class="flex gap-2">
                <a :href="route('product.report')"
                    class="btn btn-secondary inline-flex items-center gap-1 rounded-md px-3 py-1.5 text-sm font-medium">
                    <i class="ri-bar-chart-2-line"></i> Report
                </a>
                <AppButton v-if="can('product-create')" class="btn btn-primary"
                    @click="$inertia.visit(route('product.create'))">
                    <i class="ri-add-fill mr-1"></i> New Product
                </AppButton>
            </div>
        </template>
    </AppSectionHeader>



    <!-- Filter row -->
    <ProductFilterCard :brands="brands" :categories="categories" :tags="tags" :attributes="attributes"
        :initial-filters="filters" @apply="applyFilters" @clear="clearFilters" />

    <!-- Search Bar -->
    <AppDataSearch v-if="products.data.length || route().params.searchTerm" :url="route('product.index')"
        fields-to-search="name" :additional-params="additionalParams" class="mt-5"></AppDataSearch>
    <!-- Table -->
    <AppDataTable v-if="products.data.length" :headers="headers" class="shadow-sm">
        <template #TableBody>
            <tbody>
                <AppDataTableRow v-for="item in products.data" :key="item.id">
                    <!-- Product Name + Type Badge -->
                    <AppDataTableData>
                        <div class="flex items-center gap-3">
                            <img v-if="item.image_url" :src="item.image_url"
                                class="h-10 w-10 rounded-md object-cover" />
                            <AppImageNotAvailable v-else class="h-10 w-10 rounded-md" />
                            <div>
                                <p class="font-medium text-skin-neutral-12">{{ item.name }}</p>
                                <div class="flex items-center gap-1.5">
                                    <span class="text-xs text-skin-neutral-7">{{ item.unit || '—' }}</span>
                                    <span class="rounded px-1.5 py-0.5 text-[10px] font-semibold uppercase"
                                        :class="typeBadgeClass(item.type)">
                                        {{ item.type }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </AppDataTableData>

                    <!-- Category -->
                    <AppDataTableData class="text-xs text-skin-neutral-9">
                        {{ item.category ?? '—' }}
                    </AppDataTableData>

                    <!-- Brand -->
                    <AppDataTableData class="text-xs text-skin-neutral-9">
                        {{ item.brand ?? '—' }}
                    </AppDataTableData>

                    <!-- Price (handles variable/bundle range) -->
                    <AppDataTableData class="text-right">
                        <template v-if="item.type === 'variable'">
                            <span v-if="item.price_min != null" class="text-sm font-semibold text-skin-neutral-12">
                                ৳{{ Number(item.price_min).toFixed(2) }}
                                <template v-if="item.price_max != null && item.price_max > item.price_min">
                                    – ৳{{ Number(item.price_max).toFixed(2) }}
                                </template>
                            </span>
                            <span v-else class="text-xs text-skin-neutral-9 italic">No variations</span>
                        </template>
                        <template v-else>
                            <span v-if="item.sale_price" class="text-sm font-semibold text-green-700">৳{{
                                Number(item.sale_price).toFixed(2) }}</span>
                            <span v-else-if="item.price" class="text-sm text-skin-neutral-11">৳{{
                                Number(item.price).toFixed(2) }}</span>
                            <span v-else class="text-xs text-skin-neutral-9">—</span>
                        </template>
                    </AppDataTableData>

                    <!-- Stock -->
                    <AppDataTableData class="text-center">
                        <template v-if="item.type === 'variable'">
                            <span class="rounded-full px-2.5 py-0.5 text-xs font-bold"
                                :class="item.stock_variations <= 0 ? 'bg-red-100 text-red-700' : item.stock_variations < 10 ? 'bg-yellow-100 text-yellow-700' : 'bg-green-100 text-green-700'">
                                {{ item.stock_variations }} variants
                            </span>
                        </template>
                        <template v-else-if="item.type === 'bundle'">
                            <span
                                class="rounded-full bg-skin-neutral-3 px-2.5 py-0.5 text-xs font-bold text-skin-neutral-10">
                                Bundle
                            </span>
                        </template>
                        <template v-else>
                            <span class="rounded-full px-2.5 py-0.5 text-xs font-bold"
                                :class="item.quantity <= 0 ? 'bg-red-100 text-red-700' : item.quantity < 10 ? 'bg-yellow-100 text-yellow-700' : 'bg-green-100 text-green-700'">
                                {{ item.quantity }}
                            </span>
                        </template>
                    </AppDataTableData>

                    <!-- Status -->
                    <AppDataTableData>
                        <div class="flex flex-wrap gap-1">
                            <span class="rounded-full px-2.5 py-0.5 text-xs font-medium"
                                :class="item.active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-600'">
                                {{ item.active ? 'Active' : 'Inactive' }}
                            </span>
                            <span v-if="item.featured"
                                class="rounded-full bg-yellow-100 px-2.5 py-0.5 text-xs font-medium text-yellow-700">
                                Featured
                            </span>
                        </div>
                    </AppDataTableData>

                    <!-- Actions -->
                    <AppDataTableData class="text-right">
                        <div class="flex justify-end gap-1.5">
                            <AppTooltip text="View Details">
                                <AppButton class="btn btn-icon btn-secondary"
                                    @click="$inertia.visit(route('product.show', item.id))">
                                    <i class="ri-eye-line"></i>
                                </AppButton>
                            </AppTooltip>
                            <AppTooltip v-if="can('product-edit')" text="Edit">
                                <AppButton class="btn btn-icon btn-primary"
                                    @click="$inertia.visit(route('product.edit', item.id))">
                                    <i class="ri-edit-line"></i>
                                </AppButton>
                            </AppTooltip>
                            <AppTooltip v-if="can('product-delete')" text="Delete">
                                <AppButton class="btn btn-icon btn-destructive"
                                    @click="confirmDelete(route('product.destroy', item.id))">
                                    <i class="ri-delete-bin-line"></i>
                                </AppButton>
                            </AppTooltip>
                        </div>
                    </AppDataTableData>
                </AppDataTableRow>
            </tbody>
        </template>
    </AppDataTable>

    <AppPaginator v-if="products.data.length" :links="products.links" :from="products.from || 0" :to="products.to || 0"
        :total="products.total || 0" class="mt-4 justify-center"></AppPaginator>

    <AppAlert v-if="!products.data.length" class="mt-4">
        No products found.
    </AppAlert>

    <AppConfirmDialog ref="confirmDialogRef"></AppConfirmDialog>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import useAuthCan from '@/Composables/useAuthCan'
import AppImageNotAvailable from '@/Components/Modules/Blog/AppImageNotAvailable.vue'
import ProductFilterCard from './Components/ProductFilterCard.vue'

const { can } = useAuthCan()

const props = defineProps({
    products: { type: Object, default: () => ({}) },
    brands: { type: Array, default: () => [] },
    categories: { type: Array, default: () => [] },
    tags: { type: Array, default: () => [] },
    attributes: { type: Array, default: () => [] },
    filters: { type: Object, default: () => ({}) },
})

const breadCrumb = [
    { label: 'Home', href: route('dashboard.index') },
    { label: 'Products', last: true },
]

const headers = ['Product', 'Category', 'Brand', 'Price', 'Stock', 'Status', 'Actions']

const additionalParams = computed(() => {
    const params = {}
    if (props.filters?.active !== undefined && props.filters?.active !== '') { params.active = props.filters.active }
    if (props.filters?.featured !== undefined && props.filters?.featured !== '') { params.featured = props.filters.featured }
    if (props.filters?.stock !== undefined && props.filters?.stock !== '') { params.stock = props.filters.stock }
    if (props.filters?.brand !== undefined && props.filters?.brand !== '') { params.brand = props.filters.brand }
    if (props.filters?.category !== undefined && props.filters?.category !== '') { params.category = props.filters.category }
    if (props.filters?.tag !== undefined && props.filters?.tag !== '') { params.tag = props.filters.tag }
    if (props.filters?.attribute !== undefined && props.filters?.attribute !== '') { params.attribute = props.filters.attribute }
    if (props.filters?.attribute_value !== undefined && props.filters?.attribute_value !== '') { params.attribute_value = props.filters.attribute_value }
    return params
})

function applyFilters(newFilters) {
    const params = {}
    const urlParams = new URLSearchParams(window.location.search)
    const searchTerm = urlParams.get('searchTerm')
    if (searchTerm) { params.searchTerm = searchTerm }

    if (newFilters.active !== '') { params.active = newFilters.active }
    if (newFilters.featured !== '') { params.featured = newFilters.featured }
    if (newFilters.stock !== '') { params.stock = newFilters.stock }
    if (newFilters.brand !== '') { params.brand = newFilters.brand }
    if (newFilters.category !== '') { params.category = newFilters.category }
    if (newFilters.tag !== '') { params.tag = newFilters.tag }
    if (newFilters.attribute !== '') { params.attribute = newFilters.attribute }
    if (newFilters.attribute_value !== '') { params.attribute_value = newFilters.attribute_value }
    router.get(route('product.index'), params, { preserveState: true, replace: true })
}

function clearFilters() {
    const params = {}
    const urlParams = new URLSearchParams(window.location.search)
    const searchTerm = urlParams.get('searchTerm')
    if (searchTerm) { params.searchTerm = searchTerm }
    router.get(route('product.index'), params, { preserveState: true, replace: true })
}

function typeBadgeClass(type) {
    const classes = {
        simple: 'bg-skin-neutral-3 text-skin-neutral-10',
        variable: 'bg-purple-100 text-purple-700',
        bundle: 'bg-blue-100 text-blue-700',
    }
    return classes[type] || classes.simple
}

const confirmDialogRef = ref(null)
const confirmDelete = (deleteRoute) => {
    confirmDialogRef.value.openModal(deleteRoute)
}
</script>
