<template>
    <Head :title="title"></Head>
    <AppSectionHeader :title="title" :bread-crumb="breadCrumb">
        <template #right>
            <div class="flex flex-wrap items-center gap-2">
                <input
                    v-model="filterForm.from"
                    type="date"
                    class="rounded-md border border-skin-neutral-4 bg-skin-neutral-2 px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                />
                <span class="text-sm text-gray-500">to</span>
                <input
                    v-model="filterForm.to"
                    type="date"
                    class="rounded-md border border-skin-neutral-4 bg-skin-neutral-2 px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                />
                <AppButton class="btn btn-primary" @click="applyFilter">
                    <i class="ri-filter-3-line mr-1"></i>Apply
                </AppButton>
            </div>
        </template>
    </AppSectionHeader>

    <!-- Summary Cards -->
    <div class="mt-6 grid grid-cols-2 gap-4 md:grid-cols-3 xl:grid-cols-6">
        <div class="flex flex-col rounded-xl border border-skin-neutral-4 bg-skin-neutral-2 p-4 shadow-sm">
            <div class="mb-2 flex h-9 w-9 items-center justify-center rounded-full bg-blue-100"><i class="ri-shopping-bag-3-line text-blue-600"></i></div>
            <p class="text-xs text-gray-500">Total Orders</p>
            <p class="mt-1 text-xl font-bold text-gray-800">{{ props.summary?.totalOrders }}</p>
        </div>
        <div class="flex flex-col rounded-xl border border-skin-neutral-4 bg-skin-neutral-2 p-4 shadow-sm">
            <div class="mb-2 flex h-9 w-9 items-center justify-center rounded-full bg-green-100"><i class="ri-money-dollar-circle-line text-green-600"></i></div>
            <p class="text-xs text-gray-500">Revenue</p>
            <p class="mt-1 text-xl font-bold text-gray-800">৳{{ formatNumber(props.summary?.totalRevenue) }}</p>
        </div>
        <div class="flex flex-col rounded-xl border border-skin-neutral-4 bg-skin-neutral-2 p-4 shadow-sm">
            <div class="mb-2 flex h-9 w-9 items-center justify-center rounded-full bg-purple-100"><i class="ri-scales-3-line text-purple-600"></i></div>
            <p class="text-xs text-gray-500">Avg Order</p>
            <p class="mt-1 text-xl font-bold text-gray-800">৳{{ formatNumber(props.summary?.avgOrderValue) }}</p>
        </div>
        <div class="flex flex-col rounded-xl border border-skin-neutral-4 bg-skin-neutral-2 p-4 shadow-sm">
            <div class="mb-2 flex h-9 w-9 items-center justify-center rounded-full bg-yellow-100"><i class="ri-time-line text-yellow-600"></i></div>
            <p class="text-xs text-gray-500">Pending</p>
            <p class="mt-1 text-xl font-bold text-gray-800">{{ props.summary?.pendingCount }}</p>
        </div>
        <div class="flex flex-col rounded-xl border border-skin-neutral-4 bg-skin-neutral-2 p-4 shadow-sm">
            <div class="mb-2 flex h-9 w-9 items-center justify-center rounded-full bg-emerald-100"><i class="ri-checkbox-circle-line text-emerald-600"></i></div>
            <p class="text-xs text-gray-500">Completed</p>
            <p class="mt-1 text-xl font-bold text-gray-800">{{ props.summary?.completedCount }}</p>
        </div>
        <div class="flex flex-col rounded-xl border border-skin-neutral-4 bg-skin-neutral-2 p-4 shadow-sm">
            <div class="mb-2 flex h-9 w-9 items-center justify-center rounded-full bg-red-100"><i class="ri-close-circle-line text-red-600"></i></div>
            <p class="text-xs text-gray-500">Cancelled</p>
            <p class="mt-1 text-xl font-bold text-gray-800">{{ props.summary?.cancelledCount }}</p>
        </div>
    </div>

    <!-- Charts row -->
    <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="col-span-2 rounded-xl border border-skin-neutral-4 bg-skin-neutral-2 p-5 shadow-sm">
            <h3 class="mb-4 font-semibold text-gray-700">Daily Orders &amp; Revenue</h3>
            <canvas ref="dailyChartRef" height="100"></canvas>
        </div>
        <div class="rounded-xl border border-skin-neutral-4 bg-skin-neutral-2 p-5 shadow-sm">
            <h3 class="mb-4 font-semibold text-gray-700">Orders by Status</h3>
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-skin-neutral-4 text-xs text-gray-500">
                        <th class="py-2 text-left font-medium">Status</th>
                        <th class="py-2 text-right font-medium">Count</th>
                        <th class="py-2 text-right font-medium">Revenue</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(val, status) in props.byStatus" :key="status" class="border-b border-skin-neutral-4">
                        <td class="py-2"><span class="rounded-full px-2 py-0.5 text-xs font-medium" :class="statusBadgeClass(status)">{{ status }}</span></td>
                        <td class="py-2 text-right text-gray-700">{{ val.count }}</td>
                        <td class="py-2 text-right font-semibold text-gray-800">৳{{ formatNumber(val.revenue) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Paginated orders list -->
    <div class="mt-6 rounded-xl border border-skin-neutral-4 bg-skin-neutral-2 shadow-sm">
        <div class="border-b border-skin-neutral-4 px-5 py-4">
            <h3 class="font-semibold text-gray-700">Orders in Selected Period</h3>
        </div>
        <AppDataTable v-if="props.orders?.data?.length" :headers="['#', 'Customer', 'Phone', 'Status', 'Payment', 'Total', 'Date']">
            <template #TableBody>
                <tbody>
                    <AppDataTableRow v-for="item in props.orders.data" :key="item.id">
                        <AppDataTableData>{{ item.id }}</AppDataTableData>
                        <AppDataTableData>{{ item.name }}</AppDataTableData>
                        <AppDataTableData>{{ item.phone }}</AppDataTableData>
                        <AppDataTableData>
                            <span class="rounded-full px-2 py-0.5 text-xs font-medium" :class="statusBadgeClass(item.status)">{{ item.status }}</span>
                        </AppDataTableData>
                        <AppDataTableData>{{ item.payment_status }}</AppDataTableData>
                        <AppDataTableData class="font-semibold">৳{{ item.total }}</AppDataTableData>
                        <AppDataTableData>{{ item.created_at }}</AppDataTableData>
                    </AppDataTableRow>
                </tbody>
            </template>
        </AppDataTable>
        <AppPaginator
            :links="props.orders?.links"
            :from="props.orders?.from"
            :to="props.orders?.to"
            :total="props.orders?.total"
            class="mt-4 justify-center"
        ></AppPaginator>
        <AppAlert v-if="!props.orders?.data?.length" class="m-4">No orders found for selected period.</AppAlert>
    </div>
</template>

<script setup>
import { ref, reactive, onMounted, onUnmounted } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import { Chart, registerables } from 'chart.js'
import useTitle from '@/Composables/useTitle'
import useAuthCan from '@/Composables/useAuthCan'

Chart.register(...registerables)

const { title } = useTitle('Order Report')
const { can } = useAuthCan()

const props = defineProps({
    summary: { type: Object, default: () => ({}) },
    byStatus: { type: Object, default: () => ({}) },
    daily: { type: Array, default: () => [] },
    orders: { type: Object, default: () => ({}) },
    filters: { type: Object, default: () => ({}) },
})

const breadCrumb = [
    { label: 'Home', href: route('dashboard.index') },
    { label: 'Orders', href: route('order.index') },
    { label: 'Order Report', last: true },
]

const filterForm = reactive({
    from: props.filters?.from ?? '',
    to: props.filters?.to ?? '',
})

function applyFilter() {
    router.get(route('order.report'), { from: filterForm.from, to: filterForm.to }, { preserveState: true })
}

function formatNumber(n) {
    if (!n) { return '0' }
    return Number(n).toLocaleString('en-IN')
}

function statusBadgeClass(status) {
    const map = {
        pending: 'bg-yellow-100 text-yellow-700',
        processing: 'bg-blue-100 text-blue-700',
        shipped: 'bg-purple-100 text-purple-700',
        delivered: 'bg-green-100 text-green-700',
        completed: 'bg-emerald-100 text-emerald-700',
        cancelled: 'bg-red-100 text-red-700',
    }
    return map[status] ?? 'bg-gray-100 text-gray-700'
}

const dailyChartRef = ref(null)
let dailyChart = null

onMounted(() => {
    if (dailyChartRef.value && props.daily?.length) {
        dailyChart = new Chart(dailyChartRef.value, {
            type: 'bar',
            data: {
                labels: props.daily.map((d) => d.date),
                datasets: [
                    {
                        label: 'Orders',
                        data: props.daily.map((d) => d.count),
                        backgroundColor: 'rgba(59,130,246,0.7)',
                        borderRadius: 4,
                        yAxisID: 'y',
                    },
                    {
                        label: 'Revenue (৳)',
                        data: props.daily.map((d) => d.revenue),
                        type: 'line',
                        borderColor: '#10B981',
                        backgroundColor: 'rgba(16,185,129,0.1)',
                        borderWidth: 2,
                        pointRadius: 3,
                        fill: false,
                        tension: 0.4,
                        yAxisID: 'y1',
                    },
                ],
            },
            options: {
                responsive: true,
                plugins: { legend: { position: 'top', labels: { font: { size: 11 } } } },
                scales: {
                    y: { beginAtZero: true, ticks: { font: { size: 11 } }, grid: { color: 'rgba(0,0,0,0.05)' } },
                    y1: {
                        beginAtZero: true,
                        position: 'right',
                        ticks: { callback: (v) => '৳' + v.toLocaleString('en-IN'), font: { size: 11 } },
                        grid: { display: false },
                    },
                    x: { ticks: { font: { size: 10 }, maxRotation: 45 }, grid: { display: false } },
                },
            },
        })
    }
})

onUnmounted(() => {
    dailyChart?.destroy()
})
</script>
