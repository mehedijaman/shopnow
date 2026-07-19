<template>
    <Head :title="title"></Head>
    <AppSectionHeader :title="title" :bread-crumb="breadCrumb">
        <template #right>
            <div class="flex gap-2">
                <a :href="route('customer.report')" class="btn btn-secondary inline-flex items-center gap-1 rounded-md px-3 py-1.5 text-sm font-medium">
                    <i class="ri-bar-chart-2-line"></i> Report
                </a>
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
                    <i class="ri-recycle-line"></i>
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
                placeholder="Search by name, phone or email..."
                class="flex-1 bg-transparent text-sm focus:outline-none"
                @keyup.enter="applyFilters"
            />
            <button v-if="searchInput" class="text-skin-neutral-7 hover:text-skin-neutral-11" @click="clearSearch">
                <i class="ri-close-line"></i>
            </button>
        </div>
        <select
            v-model="activeFilter"
            class="rounded-md border border-skin-neutral-4 bg-skin-neutral-2 px-3 py-1.5 text-sm focus:outline-none"
            @change="applyFilters"
        >
            <option value="">All Customers</option>
            <option value="1">Active</option>
            <option value="0">Inactive</option>
        </select>
    </div>

    <!-- Table -->
    <div v-if="customers.data.length" class="mt-4 overflow-x-auto rounded-xl border border-skin-neutral-4 bg-skin-neutral-2 shadow-sm">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-skin-neutral-4 text-left text-xs text-skin-neutral-9">
                    <th class="px-4 py-3 font-medium">#</th>
                    <th class="px-4 py-3 font-medium">Customer</th>
                    <th class="px-4 py-3 font-medium">Contact</th>
                    <th class="px-4 py-3 font-medium">Verified</th>
                    <th class="px-4 py-3 font-medium">Status</th>
                    <th class="px-4 py-3 font-medium">Gender</th>
                    <th class="px-4 py-3 font-medium">Joined</th>
                    <th class="px-4 py-3 font-medium">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-skin-neutral-3">
                <tr
                    v-for="(item, index) in customers.data"
                    :key="item.id"
                    class="hover:bg-skin-neutral-1"
                >
                    <td class="px-4 py-3 font-mono text-xs text-skin-neutral-9">{{ (customers.current_page - 1) * customers.per_page + (index + 1) }}</td>
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-2.5">
                            <div class="flex h-8 w-8 items-center justify-center rounded-full bg-blue-100 text-xs font-bold text-blue-700">
                                {{ item.name?.charAt(0)?.toUpperCase() ?? '?' }}
                            </div>
                            <div>
                                <p class="font-medium text-skin-neutral-12">{{ item.name }}</p>
                                <p class="text-xs text-skin-neutral-8">ID: {{ item.id }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-3">
                        <p class="text-skin-neutral-11">{{ item.phone }}</p>
                        <p class="text-xs text-skin-neutral-8">{{ item.email }}</p>
                    </td>
                    <td class="px-4 py-3">
                        <span
                            class="rounded-full px-2.5 py-0.5 text-xs font-medium"
                            :class="item.email_verified_at ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500'"
                        >
                            {{ item.email_verified_at ? 'Verified' : 'Unverified' }}
                        </span>
                    </td>
                    <td class="px-4 py-3">
                        <span
                            class="rounded-full px-2.5 py-0.5 text-xs font-medium"
                            :class="item.active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-600'"
                        >
                            {{ item.active ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-xs capitalize text-skin-neutral-8">{{ item.gender ?? '—' }}</td>
                    <td class="px-4 py-3 text-xs text-skin-neutral-8">{{ item.created_at }}</td>
                    <td class="px-4 py-3">
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
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <AppPaginator
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
import { ref } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import useTitle from '@/Composables/useTitle'
import useAuthCan from '@/Composables/useAuthCan'

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

const searchInput = ref(props.filters?.searchTerm ?? '')
const activeFilter = ref(props.filters?.active ?? '')

function applyFilters() {
    const params = {}
    if (searchInput.value) { params.searchTerm = searchInput.value }
    if (activeFilter.value !== '') { params.active = activeFilter.value }
    router.get(route('customer.index'), params, { preserveState: true, replace: true })
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

