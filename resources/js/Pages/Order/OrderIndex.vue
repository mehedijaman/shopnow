<template>
    <Head :title="title"></Head>
    <AppSectionHeader :title="title" :bread-crumb="breadCrumb">
        <template #right>
            <AppButton
                class="btn btn-secondary"
                @click="$inertia.visit(route('order.report'))"
            >
                <i class="ri-bar-chart-2-line mr-1"></i> {{ __('common.report') }}
            </AppButton>
        </template>
    </AppSectionHeader>

    <!-- Filter Card -->
    <OrderFilterCard
        :statuses="statuses"
        :payment-methods="paymentMethods"
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
                    <AppDataTableData class="font-mono text-sm font-semibold">
                        {{ item.id }}
                    </AppDataTableData>
                    <AppDataTableData class="text-sm">
                        {{ item.created_at }}
                    </AppDataTableData>
                    <AppDataTableData>
                        {{ item.name }}
                    </AppDataTableData>

                    <AppDataTableData class="text-sm">
                        {{ item.phone }}
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
                                    {{ orderStatusText(s) }}
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
                                {{ orderStatusText(item.status) }}
                            </span>
                            <button
                                type="button"
                                class="hidden h-5 w-5 items-center justify-center rounded text-skin-neutral-7 hover:bg-skin-neutral-3 hover:text-skin-neutral-11 group-hover:flex"
                                :title="__('order::admin.quick_update_status')"
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
                            {{ paymentStatusText(item.payment_status) }}
                        </span>
                    </AppDataTableData>
                    <AppDataTableData class="text-sm">
                        <span v-if="item.payment_method" class="rounded-full bg-skin-neutral-3 px-2.5 py-0.5 text-xs font-medium text-skin-neutral-11">
                            {{ paymentMethodText(item.payment_method) }}
                        </span>
                        <span v-else class="text-skin-neutral-7">—</span>
                    </AppDataTableData>
                    <AppDataTableData class="text-right font-semibold text-skin-neutral-12">
                        {{ formatMoney(item.total) }}
                    </AppDataTableData>
                    <AppDataTableData class="text-right">
                        <div class="flex justify-end gap-1.5">
                            <AppButton
                                class="btn btn-icon btn-neutral"
                                :title="__('order::admin.edit_order')"
                                @click="$inertia.visit(route('order.edit', item.id))"
                            >
                                <i class="ri-pencil-line"></i>
                            </AppButton>
                            <!-- <AppTooltip text="View Details"> -->
                                <AppButton
                                    class="btn btn-icon btn-primary"
                                    @click="$inertia.visit(route('order.show', item.id))"
                                >
                                    <i class="ri-eye-line"></i>
                                </AppButton>
                            <!-- </AppTooltip> -->
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
        {{ __('order::admin.no_orders_found') }}
    </AppAlert>
</template>

<script setup>
import { ref, computed, inject } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import useTitle from '@/Composables/useTitle'
import useAuthCan from '@/Composables/useAuthCan'
import { formatMoney } from '@/Utils/formatMoney'
import OrderFilterCard from './Components/OrderFilterCard.vue'

const translate = inject('translate')

const { title } = useTitle(translate('order::admin.orders'))
const { can } = useAuthCan()

const props = defineProps({
    orders: { type: Object, default: () => ({}) },
    statuses: { type: Array, default: () => [] },
    statusCounts: { type: Object, default: () => ({}) },
    paymentMethods: { type: Array, default: () => [] },
    filters: { type: Object, default: () => ({}) },
})

const breadCrumb = [
    { label: translate('common.home'), href: route('dashboard.index') },
    { label: translate('order::admin.orders'), last: true },
]

const headers = computed(() => [
    translate('common.header.order_no'),
    translate('common.header.date'),
    translate('common.header.name'),
    translate('common.header.phone'),
    translate('common.header.status'),
    translate('common.header.payment'),
    translate('common.header.method'),
    translate('common.header.total'),
    translate('common.header.actions'),
])

const STATUS_KEYS = {
    pending: 'order::enums.order_status.pending',
    processing: 'order::enums.order_status.processing',
    shipped: 'order::enums.order_status.shipped',
    delivered: 'order::enums.order_status.delivered',
    completed: 'order::enums.order_status.completed',
    cancelled: 'order::enums.order_status.cancelled',
}
const orderStatusText = (status) => translate(STATUS_KEYS[status] ?? status)

const PAYMENT_STATUS_KEYS = {
    paid: 'order::enums.payment_status.paid',
    unpaid: 'order::enums.payment_status.unpaid',
}
const paymentStatusText = (status) => translate(PAYMENT_STATUS_KEYS[status] ?? status)

const PAYMENT_METHOD_KEYS = {
    cod: 'order::admin.payment_method_cod',
    card: 'order::admin.payment_method_card',
    mobile: 'order::admin.payment_method_mobile',
}
const paymentMethodText = (method) => translate(PAYMENT_METHOD_KEYS[method] ?? method)

const additionalParams = computed(() => {
    const params = {}
    if (props.filters?.status !== undefined && props.filters?.status !== '') {
        params.status = props.filters.status
    }
    if (props.filters?.payment_status !== undefined && props.filters?.payment_status !== '') {
        params.payment_status = props.filters.payment_status
    }
    if (props.filters?.payment_method !== undefined && props.filters?.payment_method !== '') {
        params.payment_method = props.filters.payment_method
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
    if (newFilters.payment_method !== '') { params.payment_method = newFilters.payment_method }
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
