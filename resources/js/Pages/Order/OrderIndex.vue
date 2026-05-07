<template>
    <Head :title="title"></Head>
    <AppSectionHeader :title="title" :bread-crumb="breadCrumb">
        <template #right>
            <a :href="route('order.report')" class="btn btn-secondary inline-flex items-center gap-1 rounded-md px-3 py-1.5 text-sm font-medium">
                <i class="ri-bar-chart-2-line"></i> Report
            </a>
        </template>
    </AppSectionHeader>

    <!-- Status filter tabs -->
    <div class="mt-5 flex flex-wrap gap-2 border-b border-skin-neutral-4 pb-3">
        <button
            class="rounded-full px-3 py-1 text-xs font-medium transition-colors"
            :class="!activeStatus ? 'bg-skin-primary-9 text-white' : 'bg-skin-neutral-3 text-skin-neutral-10 hover:bg-skin-neutral-4'"
            @click="setStatus(null)"
        >
            All <span class="ml-1 opacity-70">{{ orders.total }}</span>
        </button>
        <button
            v-for="s in props.statuses"
            :key="s"
            class="rounded-full px-3 py-1 text-xs font-medium capitalize transition-colors"
            :class="activeStatus === s ? statusActiveBg(s) : 'bg-skin-neutral-3 text-skin-neutral-10 hover:bg-skin-neutral-4'"
            @click="setStatus(s)"
        >
            {{ s }} <span class="ml-1 opacity-70">{{ props.statusCounts?.[s] ?? 0 }}</span>
        </button>
    </div>

    <!-- Search + payment filter row -->
    <div class="mt-3 flex flex-wrap items-center gap-3">
        <div class="flex flex-1 items-center gap-2 rounded-md border border-skin-neutral-4 bg-skin-neutral-2 px-3 py-1.5 text-sm">
            <i class="ri-search-line text-skin-neutral-8"></i>
            <input
                v-model="searchInput"
                type="text"
                placeholder="Search by order # or name..."
                class="flex-1 bg-transparent text-sm focus:outline-none"
                @keyup.enter="applySearch"
            />
            <button v-if="searchInput" class="text-skin-neutral-7 hover:text-skin-neutral-11" @click="clearSearch">
                <i class="ri-close-line"></i>
            </button>
        </div>
        <select
            v-model="paymentFilter"
            class="rounded-md border border-skin-neutral-4 bg-skin-neutral-2 px-3 py-1.5 text-sm focus:outline-none"
            @change="applyFilters"
        >
            <option value="">All Payments</option>
            <option value="paid">Paid</option>
            <option value="unpaid">Unpaid</option>
        </select>
    </div>

    <!-- Table -->
    <div v-if="orders.data.length" class="mt-4 overflow-x-auto rounded-xl border border-skin-neutral-4 bg-skin-neutral-2 shadow-sm">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-skin-neutral-4 text-left text-xs text-skin-neutral-9">
                    <th class="px-4 py-3 font-medium">#</th>
                    <th class="px-4 py-3 font-medium">Date</th>
                    <th class="px-4 py-3 font-medium">Customer</th>
                    <th class="px-4 py-3 font-medium">Status</th>
                    <th class="px-4 py-3 font-medium">Payment</th>
                    <th class="px-4 py-3 text-right font-medium">Total</th>
                    <th class="px-4 py-3 font-medium">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-skin-neutral-3">
                <tr
                    v-for="item in orders.data"
                    :key="item.id"
                    class="group hover:bg-skin-neutral-1"
                >
                    <td class="px-4 py-3 font-mono text-xs font-semibold text-skin-neutral-9">#{{ item.id }}</td>
                    <td class="px-4 py-3 text-xs text-skin-neutral-9">{{ item.created_at }}</td>
                    <td class="px-4 py-3">
                        <p class="font-medium text-skin-neutral-12">{{ item.name }}</p>
                        <p class="text-xs text-skin-neutral-8">{{ item.phone }}</p>
                    </td>
                    <td class="px-4 py-3">
                        <!-- Quick status update -->
                        <div v-if="quickUpdateId === item.id" class="flex items-center gap-1.5">
                            <select
                                v-model="quickStatus"
                                class="rounded border border-skin-neutral-5 bg-skin-neutral-1 px-2 py-1 text-xs focus:outline-none"
                                @keyup.esc="closeQuickUpdate"
                            >
                                <option v-for="s in props.statuses" :key="s" :value="s" class="capitalize">
                                    {{ s.charAt(0).toUpperCase() + s.slice(1) }}
                                </option>
                            </select>
                            <button
                                class="flex h-6 w-6 items-center justify-center rounded bg-green-500 text-white hover:bg-green-600"
                                :disabled="savingId === item.id"
                                @click="saveQuickStatus(item)"
                            >
                                <i v-if="savingId === item.id" class="ri-loader-4-line animate-spin text-xs"></i>
                                <i v-else class="ri-check-line text-xs"></i>
                            </button>
                            <button
                                class="flex h-6 w-6 items-center justify-center rounded bg-skin-neutral-3 text-skin-neutral-9 hover:bg-skin-neutral-4"
                                @click="closeQuickUpdate"
                            >
                                <i class="ri-close-line text-xs"></i>
                            </button>
                        </div>
                        <div v-else class="flex items-center gap-1.5">
                            <span class="rounded-full px-2.5 py-0.5 text-xs font-medium capitalize" :class="statusBadgeClass(item.status)">
                                {{ item.status }}
                            </span>
                            <button
                                class="hidden h-5 w-5 items-center justify-center rounded text-skin-neutral-7 hover:bg-skin-neutral-3 hover:text-skin-neutral-11 group-hover:flex"
                                title="Quick update status"
                                @click="openQuickUpdate(item)"
                            >
                                <i class="ri-pencil-line text-xs"></i>
                            </button>
                        </div>
                    </td>
                    <td class="px-4 py-3">
                        <span
                            class="rounded-full px-2.5 py-0.5 text-xs font-medium"
                            :class="item.payment_status === 'paid' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700'"
                        >
                            {{ item.payment_status === 'paid' ? 'Paid' : 'Unpaid' }}
                        </span>
                        <p v-if="item.payment_method" class="mt-0.5 text-xs text-skin-neutral-7">
                            {{ item.payment_method === 'cod' ? 'COD' : item.payment_method }}
                        </p>
                    </td>
                    <td class="px-4 py-3 text-right font-semibold text-skin-neutral-12">৳{{ item.total }}</td>
                    <td class="px-4 py-3">
                        <AppButton
                            class="btn btn-icon btn-primary"
                            @click="$inertia.visit(route('order.show', item.id))"
                        >
                            <i class="ri-eye-line"></i>
                        </AppButton>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <AppPaginator
        :links="orders.links"
        :from="orders.from ?? 0"
        :to="orders.to ?? 0"
        :total="orders.total ?? 0"
        class="mt-4 justify-center"
    ></AppPaginator>

    <AppAlert v-if="!orders.data.length" class="mt-4">
        No orders found.
    </AppAlert>
</template>

<script setup>
import { ref } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import useTitle from '@/Composables/useTitle'
import useAuthCan from '@/Composables/useAuthCan'
import axios from 'axios'

const { title } = useTitle('Orders')
const { can } = useAuthCan()

const props = defineProps({
    orders: { type: Object, default: () => ({}) },
    statuses: { type: Array, default: () => [] },
    statusCounts: { type: Object, default: () => ({}) },
    filters: { type: Object, default: () => ({}) },
})

const breadCrumb = [
    { label: 'Home', href: route('dashboard.index') },
    { label: 'Orders', last: true },
]

// Filters
const activeStatus = ref(props.filters?.status ?? null)
const paymentFilter = ref(props.filters?.payment_status ?? '')
const searchInput = ref(props.filters?.searchTerm ?? '')

function applyFilters(overrides = {}) {
    const params = {}
    if (activeStatus.value) { params.status = activeStatus.value }
    if (paymentFilter.value) { params.payment_status = paymentFilter.value }
    if (searchInput.value) { params.searchTerm = searchInput.value }
    router.get(route('order.index'), { ...params, ...overrides }, { preserveState: true, replace: true })
}

function setStatus(s) {
    activeStatus.value = s
    applyFilters()
}

function applySearch() {
    applyFilters()
}

function clearSearch() {
    searchInput.value = ''
    applyFilters()
}

// Quick status update
const quickUpdateId = ref(null)
const quickStatus = ref('')
const savingId = ref(null)

function openQuickUpdate(item) {
    quickUpdateId.value = item.id
    quickStatus.value = item.status
}

function closeQuickUpdate() {
    quickUpdateId.value = null
    quickStatus.value = ''
}

async function saveQuickStatus(item) {
    if (quickStatus.value === item.status) {
        closeQuickUpdate()
        return
    }
    savingId.value = item.id
    try {
        await axios.patch(route('order.updateStatus', item.id), {
            status: quickStatus.value,
            payment_status: item.payment_status,
        })
        router.reload({ preserveState: true, preserveScroll: true })
    } finally {
        savingId.value = null
        closeQuickUpdate()
    }
}

// Styling helpers
function statusBadgeClass(status) {
    const map = {
        pending: 'bg-yellow-100 text-yellow-700',
        processing: 'bg-blue-100 text-blue-700',
        shipped: 'bg-purple-100 text-purple-700',
        delivered: 'bg-indigo-100 text-indigo-700',
        completed: 'bg-green-100 text-green-700',
        cancelled: 'bg-red-100 text-red-700',
    }
    return map[status] ?? 'bg-gray-100 text-gray-700'
}

function statusActiveBg(status) {
    const map = {
        pending: 'bg-yellow-500 text-white',
        processing: 'bg-blue-500 text-white',
        shipped: 'bg-purple-500 text-white',
        delivered: 'bg-indigo-500 text-white',
        completed: 'bg-green-500 text-white',
        cancelled: 'bg-red-500 text-white',
    }
    return map[status] ?? 'bg-gray-500 text-white'
}
</script>

