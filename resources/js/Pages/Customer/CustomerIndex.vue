<template>
    <Head :title="title"></Head>
    <AppSectionHeader :title="title" :bread-crumb="breadCrumb">
        <template #right>
            <div class="flex gap-2">
                <AppButton
                    class="btn btn-secondary"
                    @click="$inertia.visit(route('customer.report'))"
                >
                    <i class="ri-bar-chart-2-line mr-1"></i> Report
                </AppButton>
                <AppButton
                    v-if="can('customer-create')"
                    class="btn btn-primary"
                    @click="$inertia.visit(route('customer.create'))"
                >
                    <i class="ri-add-fill mr-1"></i> New Customer
                </AppButton>
                <AppButton
                    v-if="can('customer-recycle-bin-list')"
                    class="btn btn-secondary"
                    @click="$inertia.visit(route('customer.recycleBin.index'))"
                >
                    <i class="ri-delete-bin-2-line mr-1"></i> Recycle Bin
                </AppButton>
            </div>
        </template>
    </AppSectionHeader>

    <!-- Filter Card -->
    <CustomerFilterCard
        :initial-filters="filters"
        @apply="applyFilters"
        @clear="clearFilters"
    />

    <!-- Search Bar -->
    <AppDataSearch
        v-if="customers.data.length || route().params.searchTerm"
        :url="route('customer.index')"
        fields-to-search="name,phone,email"
        :additional-params="additionalParams"
        class="mt-5"
    />

    <!-- Table -->
    <AppDataTable v-if="customers.data.length" :headers="headers" class="mt-4 shadow-sm">
        <template #TableBody>
            <tbody>
                <AppDataTableRow
                    v-for="(item, index) in customers.data"
                    :key="item.id"
                >
                    <AppDataTableData class="font-mono text-xs text-skin-neutral-9">{{ (customers.current_page - 1) * customers.per_page + (index + 1) }}</AppDataTableData>
                    <AppDataTableData>
                        <div class="flex items-center gap-2.5">
                            <div class="flex h-8 w-8 items-center justify-center rounded-full bg-blue-100 text-xs font-bold text-blue-700">
                                {{ item.name?.charAt(0)?.toUpperCase() ?? '?' }}
                            </div>
                            <div>
                                <p class="font-medium text-skin-neutral-12">{{ item.name }}</p>
                                <p class="text-xs text-skin-neutral-8">ID: {{ item.id }}</p>
                            </div>
                        </div>
                    </AppDataTableData>
                    <AppDataTableData>
                        <p class="text-skin-neutral-11">{{ item.phone }}</p>
                        <p class="text-xs text-skin-neutral-8">{{ item.email }}</p>
                    </AppDataTableData>
                    <AppDataTableData>
                        <span
                            class="rounded-full px-2.5 py-0.5 text-xs font-medium"
                            :class="item.email_verified_at ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500'"
                        >
                            {{ item.email_verified_at ? 'Verified' : 'Unverified' }}
                        </span>
                    </AppDataTableData>
                    <AppDataTableData>
                        <span
                            class="rounded-full px-2.5 py-0.5 text-xs font-medium"
                            :class="item.active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-600'"
                        >
                            {{ item.active ? 'Active' : 'Inactive' }}
                        </span>
                    </AppDataTableData>
                    <AppDataTableData class="text-xs capitalize text-skin-neutral-8">{{ item.gender ?? '—' }}</AppDataTableData>
                    <AppDataTableData class="text-xs text-skin-neutral-8">{{ item.created_at }}</AppDataTableData>
                    <AppDataTableData>
                        <div class="flex gap-1.5">
                            <AppTooltip v-if="can('customer-edit')" text="Edit">
                                <AppButton class="btn btn-icon btn-primary" @click="$inertia.visit(route('customer.edit', item.id))">
                                    <i class="ri-edit-line"></i>
                                </AppButton>
                            </AppTooltip>
                            <AppTooltip v-if="can('customer-delete')" text="Delete">
                                <AppButton class="btn btn-icon btn-destructive" @click="confirmDelete(route('customer.destroy', item.id))">
                                    <i class="ri-delete-bin-line"></i>
                                </AppButton>
                            </AppTooltip>
                        </div>
                    </AppDataTableData>
                </AppDataTableRow>
            </tbody>
        </template>
    </AppDataTable>

    <AppPaginator
        v-if="customers.data.length"
        :links="customers.links"
        :from="customers.from ?? 0"
        :to="customers.to ?? 0"
        :total="customers.total ?? 0"
        class="mt-4 justify-center"
    ></AppPaginator>

    <AppAlert v-if="!customers.data.length" class="mt-4">
        No customers found.
    </AppAlert>

    <AppConfirmDialog ref="confirmDialogRef"></AppConfirmDialog>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import useTitle from '@/Composables/useTitle'
import useAuthCan from '@/Composables/useAuthCan'
import CustomerFilterCard from './Components/CustomerFilterCard.vue'

const { title } = useTitle('Customers')
const { can } = useAuthCan()

const props = defineProps({
    customers: { type: Object, default: () => ({}) },
    filters: { type: Object, default: () => ({}) },
})

const breadCrumb = [
    { label: 'Home', href: route('dashboard.index') },
    { label: 'Customers', last: true },
]

const headers = ['#', 'Customer', 'Contact', 'Verified', 'Status', 'Gender', 'Joined', 'Actions']

const additionalParams = computed(() => {
    const params = {}
    if (props.filters?.active !== undefined && props.filters?.active !== null && props.filters?.active !== '') {
        params.active = props.filters.active
    }
    if (props.filters?.gender !== undefined && props.filters?.gender !== null && props.filters?.gender !== '') {
        params.gender = props.filters.gender
    }
    return params
})

function applyFilters(newFilters) {
    const params = {}
    const urlParams = new URLSearchParams(window.location.search)
    const searchTerm = urlParams.get('searchTerm')
    if (searchTerm) { params.searchTerm = searchTerm }

    if (newFilters.active !== '') { params.active = newFilters.active }
    if (newFilters.gender !== '') { params.gender = newFilters.gender }
    router.get(route('customer.index'), params, { preserveState: true, replace: true })
}

function clearFilters() {
    const params = {}
    const urlParams = new URLSearchParams(window.location.search)
    const searchTerm = urlParams.get('searchTerm')
    if (searchTerm) { params.searchTerm = searchTerm }
    router.get(route('customer.index'), params, { preserveState: true, replace: true })
}

const confirmDialogRef = ref(null)
const confirmDelete = (deleteRoute) => {
    confirmDialogRef.value.openModal(deleteRoute)
}
</script>
