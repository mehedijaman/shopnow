<template>
    <Head title="Products"></Head>
    <AppSectionHeader title="Products" :bread-crumb="breadCrumb">
        <template #right>
            <div class="flex gap-2">
                <a :href="route('product.report')" class="btn btn-secondary inline-flex items-center gap-1 rounded-md px-3 py-1.5 text-sm font-medium">
                    <i class="ri-bar-chart-2-line"></i> Report
                </a>
                <AppButton
                    v-if="can('product-create')"
                    class="btn btn-primary"
                    @click="$inertia.visit(route('product.create'))"
                >
                    <i class="ri-add-fill mr-1"></i> New Product
                </AppButton>
            </div>
        </template>
    </AppSectionHeader>

    <!-- Filter row -->
    <div class="mt-5 flex flex-wrap items-center gap-3">
        <div class="flex flex-1 items-center gap-2 rounded-md border border-skin-neutral-4 bg-skin-neutral-2 px-3 py-1.5 text-sm">
            <i class="ri-search-line text-skin-neutral-8"></i>
            <input
                v-model="searchInput"
                type="text"
                placeholder="Search products..."
                class="flex-1 bg-transparent text-sm focus:outline-none"
                @keyup.enter="applyFilters"
            />
            <button v-if="searchInput" class="text-skin-neutral-7 hover:text-skin-neutral-11" @click="clearSearch">
                <i class="ri-close-line"></i>
            </button>
        </div>
        <select v-model="activeFilter" class="rounded-md border border-skin-neutral-4 bg-skin-neutral-2 px-3 py-1.5 text-sm focus:outline-none" @change="applyFilters">
            <option value="">All Status</option>
            <option value="1">Active</option>
            <option value="0">Inactive</option>
        </select>
        <select v-model="stockFilter" class="rounded-md border border-skin-neutral-4 bg-skin-neutral-2 px-3 py-1.5 text-sm focus:outline-none" @change="applyFilters">
            <option value="">All Stock</option>
            <option value="low">Low Stock (&lt;10)</option>
            <option value="out">Out of Stock</option>
        </select>
        <select v-model="featuredFilter" class="rounded-md border border-skin-neutral-4 bg-skin-neutral-2 px-3 py-1.5 text-sm focus:outline-none" @change="applyFilters">
            <option value="">All Products</option>
            <option value="1">Featured Only</option>
        </select>
        <select v-model="brandFilter" class="rounded-md border border-skin-neutral-4 bg-skin-neutral-2 px-3 py-1.5 text-sm focus:outline-none" @change="applyFilters">
            <option value="">All Brands</option>
            <option v-for="brand in brands" :key="brand.value" :value="brand.value">{{ brand.label }}</option>
        </select>
    </div>

    <!-- Table -->
    <div v-if="products.data.length" class="mt-4 overflow-x-auto rounded-xl border border-skin-neutral-4 bg-skin-neutral-2 shadow-sm">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-skin-neutral-4 text-left text-xs text-skin-neutral-9">
                    <th class="px-4 py-3 font-medium">Product</th>
                    <th class="px-4 py-3 font-medium">Category</th>
                    <th class="px-4 py-3 font-medium">Brand</th>
                    <th class="px-4 py-3 text-right font-medium">Price</th>
                    <th class="px-4 py-3 text-right font-medium">Sale Price</th>
                    <th class="px-4 py-3 text-center font-medium">Stock</th>
                    <th class="px-4 py-3 font-medium">Status</th>
                    <th class="px-4 py-3 font-medium">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-skin-neutral-3">
                <tr v-for="item in products.data" :key="item.id" class="hover:bg-skin-neutral-1">
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-3">
                            <img v-if="item.image_url" :src="item.image_url" class="h-10 w-10 rounded-md object-cover" />
                            <AppImageNotAvailable v-else class="h-10 w-10 rounded-md" />
                            <div>
                                <p class="font-medium text-skin-neutral-12">{{ item.name }}</p>
                                <p class="text-xs text-skin-neutral-7">{{ item.unit }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-3 text-xs text-skin-neutral-9">{{ item.category ?? '—' }}</td>
                    <td class="px-4 py-3 text-xs text-skin-neutral-9">{{ item.brand ?? '—' }}</td>
                    <td class="px-4 py-3 text-right text-skin-neutral-11">৳{{ item.price }}</td>
                    <td class="px-4 py-3 text-right">
                        <span v-if="item.sale_price" class="font-semibold text-green-700">৳{{ item.sale_price }}</span>
                        <span v-else class="text-skin-neutral-7">—</span>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <span
                            class="rounded-full px-2.5 py-0.5 text-xs font-bold"
                            :class="item.quantity <= 0 ? 'bg-red-100 text-red-700' : item.quantity < 10 ? 'bg-yellow-100 text-yellow-700' : 'bg-green-100 text-green-700'"
                        >
                            {{ item.quantity }}
                        </span>
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex flex-wrap gap-1">
                            <span class="rounded-full px-2.5 py-0.5 text-xs font-medium" :class="item.active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-600'">
                                {{ item.active ? 'Active' : 'Inactive' }}
                            </span>
                            <span v-if="item.featured" class="rounded-full bg-yellow-100 px-2.5 py-0.5 text-xs font-medium text-yellow-700">
                                Featured
                            </span>
                        </div>
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex gap-1.5">
                            <AppTooltip v-if="can('product-edit')" text="Edit">
                                <AppButton class="btn btn-icon btn-primary" @click="$inertia.visit(route('product.edit', item.id))">
                                    <i class="ri-edit-line"></i>
                                </AppButton>
                            </AppTooltip>
                            <AppTooltip v-if="can('product-delete')" text="Delete">
                                <AppButton class="btn btn-icon btn-destructive" @click="confirmDelete(route('product.destroy', item.id))">
                                    <i class="ri-delete-bin-line"></i>
                                </AppButton>
                            </AppTooltip>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

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
import { Head, router } from '@inertiajs/vue3'
import useAuthCan from '@/Composables/useAuthCan'
import AppImageNotAvailable from '@/Components/Modules/Blog/AppImageNotAvailable.vue'

const { can } = useAuthCan()

const props = defineProps({
    products: { type: Object, default: () => ({}) },
    brands: { type: Array, default: () => [] },
    filters: { type: Object, default: () => ({}) },
})

const breadCrumb = [
    { label: 'Home', href: route('dashboard.index') },
    { label: 'Products', last: true },
]

const searchInput = ref(props.filters?.searchTerm ?? '')
const activeFilter = ref(props.filters?.active ?? '')
const featuredFilter = ref(props.filters?.featured ?? '')
const stockFilter = ref(props.filters?.stock ?? '')
const brandFilter = ref(props.filters?.brand ?? '')

function applyFilters() {
    const params = {}
    if (searchInput.value) { params.searchTerm = searchInput.value }
    if (activeFilter.value !== '') { params.active = activeFilter.value }
    if (featuredFilter.value !== '') { params.featured = featuredFilter.value }
    if (stockFilter.value !== '') { params.stock = stockFilter.value }
    if (brandFilter.value !== '') { params.brand = brandFilter.value }
    router.get(route('product.index'), params, { preserveState: true, replace: true })
}

function clearSearch() {
    searchInput.value = ''
    applyFilters()
}

const confirmDialogRef = ref(null)
const confirmDelete = (deleteRoute) => {
    confirmDialogRef.value.openModal(deleteRoute)
}
</script>

