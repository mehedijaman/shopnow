<template>
    <Head :title="title"></Head>
    <AppSectionHeader :title="title" :bread-crumb="breadCrumb">
        <template #right>
            <AppButton
                class="btn btn-secondary"
                @click="$inertia.visit(route('order.report'))"
            >
                <i class="ri-bar-chart-2-line mr-1"></i> Report
            </AppButton>
        </template>
    </AppSectionHeader>

    <!-- Filter Card -->
    <OrderFilterCard
        :statuses="statuses"
        :initial-filters="filters"
        @apply="applyFilters"
        @clear="clearFilters"
    />

    <!-- Search Bar -->
    <AppDataSearch
        v-if="orders.data.length || route().params.searchTerm"
        :url="route('order.index')"
        fields-to-search="id,name,phone"
        :additional-params="additionalParams"
        class="mt-5"
    />

    <!-- Table -->
    <AppDataTable v-if="orders.data.length" :headers="headers" class="shadow-sm mt-4">
        <template #TableBody>
            <tbody>
                <AppDataTableRow
                    v-for="item in orders.data"
                    :key="item.id"
                    class="group hover:bg-skin-neutral-1"
                >
                    <AppDataTableData class="font-mono text-xs font-semibold text-skin-neutral-9">
                        #{{ item.id }}
                    </AppDataTableData>
                    <AppDataTableData class="text-xs text-skin-neutral-9">
                        {{ item.created_at }}
                    </AppDataTableData>
                    <AppDataTableData>
                        <p class="font-medium text-skin-neutral-12">{{ item.name }}</p>
                        <p class="text-xs text-skin-neutral-8">{{ item.phone }}</p>
                    </AppDataTableData>
                    <AppDataTableData>
                        <!-- Quick status update -->
                        <div v-if="quickUpdateId === item.id" class="flex items-center gap-1.5" @click.stop>
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
                                type="button"
                                class="flex h-6 w-6 items-center justify-center rounded bg-green-500 text-white hover:bg-green-600"
                                :disabled="savingId === item.id"
                                @click="saveQuickStatus(item)"
                            >
                                <i v-if="savingId === item.id" class="ri-loader-4-line animate-spin text-xs"></i>
                                <i v-else class="ri-check-line text-xs"></i>
                            </button>
                            <button
                                type="button"
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
                                type="button"
                                class="hidden h-5 w-5 items-center justify-center rounded text-skin-neutral-7 hover:bg-skin-neutral-3 hover:text-skin-neutral-11 group-hover:flex"
                                title="Quick update status"
                                @click="openQuickUpdate(item)"
                            >
                                <i class="ri-pencil-line text-xs"></i>
                            </button>
                        </div>
                    </AppDataTableData>
                    <AppDataTableData>
                        <span
                            class="rounded-full px-2.5 py-0.5 text-xs font-medium"
                            :class="item.payment_status === 'paid' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700'"
                        >
                            {{ item.payment_status === 'paid' ? 'Paid' : 'Unpaid' }}
                        </span>
                        <p v-if="item.payment_method" class="mt-0.5 text-xs text-skin-neutral-7">
                            {{ item.payment_method === 'cod' ? 'COD' : item.payment_method }}
                        </p>
                    </AppDataTableData>
                    <AppDataTableData class="text-right font-semibold text-skin-neutral-12">
                        ৳{{ Number(item.total).toFixed(2) }}
                    </AppDataTableData>
                    <AppDataTableData class="text-right">
                        <div class="flex justify-end gap-1.5">
                            <AppTooltip text="View Details">
                                <AppButton
                                    class="btn btn-icon btn-primary"
                                    @click="$inertia.visit(route('order.show', item.id))"
                                >
                                    <i class="ri-eye-line"></i>
                                </AppButton>
                            </AppTooltip>
                        </div>
                    </AppDataTableData>
                </AppDataTableRow>
            </tbody>
        </template>
    </AppDataTable>

    <AppPaginator
        v-if="orders.data.length"
        :links="orders.links"
        :from="orders.from ?? 0"
        :to="orders.to ?? 0"
        :total="orders.total ?? 0"
        class="mt-4 justify-center"
    />

    <AppAlert v-if="!orders.data.length" class="mt-4">
        No orders found.
    </AppAlert>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import useTitle from '@/Composables/useTitle'
import useAuthCan from '@/Composables/useAuthCan'
import OrderFilterCard from './Components/OrderFilterCard.vue'

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

const headers = ['#', 'Date', 'Customer', 'Status', 'Payment', 'Total', 'Actions']

const additionalParams = computed(() => {
    const params = {}
    if (props.filters?.status !== undefined && props.filters?.status !== '') {
        params.status = props.filters.status
    }
    if (props.filters?.payment_status !== undefined && props.filters?.payment_status !== '') {
        params.payment_status = props.filters.payment_status
    }
    return params
})

function applyFilters(newFilters) {
    const params = {}
    const urlParams = new URLSearchParams(window.location.search)
    const searchTerm = urlParams.get('searchTerm')
    if (searchTerm) { params.searchTerm = searchTerm }

    if (newFilters.status !== '') { params.status = newFilters.status }
    if (newFilters.payment_status !== '') { params.payment_status = newFilters.payment_status }
    router.get(route('order.index'), params, { preserveState: true, replace: true })
}

function clearFilters() {
    const params = {}
    const urlParams = new URLSearchParams(window.location.search)
    const searchTerm = urlParams.get('searchTerm')
    if (searchTerm) { params.searchTerm = searchTerm }
    router.get(route('order.index'), params, { preserveState: true, replace: true })
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

function saveQuickStatus(item) {
    if (quickStatus.value === item.status) {
        closeQuickUpdate()
        return
    }
    savingId.value = item.id
    router.patch(
        route('order.updateStatus', item.id),
        { status: quickStatus.value, payment_status: item.payment_status },
        {
            preserveState: true,
            preserveScroll: true,
            onFinish: () => {
                savingId.value = null
                closeQuickUpdate()
            },
        },
    )
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
</script>
