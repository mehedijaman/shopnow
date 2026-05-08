<template>
    <Head title="Dashboard"></Head>

    <!-- Welcome bar -->
    <div class="shadow-2xs mt-6 flex flex-col justify-between rounded-md border border-skin-neutral-4 bg-skin-neutral-2 px-4 py-2 md:flex-row">
        <div>
            <i class="ri-megaphone-line"></i>
            Welcome
            <span class="font-bold">{{ $page.props.auth.user.name }}</span>!
        </div>
        <div>
            <i class="ri-calendar-2-line mr-2"></i>
            {{ $page.props.datetime.now }}
        </div>
    </div>

    <!-- KPI Cards -->
    <div class="mt-6 grid grid-cols-2 gap-4 md:grid-cols-3 xl:grid-cols-6">
        <div class="flex cursor-pointer flex-col rounded-xl border border-skin-neutral-4 bg-skin-neutral-2 p-4 shadow-sm hover:bg-skin-neutral-1" @click="$inertia.visit(route('order.index'))">
            <div class="mb-3 flex h-10 w-10 items-center justify-center rounded-full bg-green-100"><i class="ri-money-dollar-circle-line text-xl text-green-600"></i></div>
            <p class="text-xs font-medium text-gray-500">Total Revenue</p>
            <p class="mt-1 text-xl font-bold text-gray-800">৳{{ formatNumber(props.kpi?.totalRevenue) }}</p>
            <p class="mt-1 text-xs text-gray-400">All time (excl. cancelled)</p>
        </div>
        <div class="flex cursor-pointer flex-col rounded-xl border border-skin-neutral-4 bg-skin-neutral-2 p-4 shadow-sm hover:bg-skin-neutral-1" @click="$inertia.visit(route('order.index'))">
            <div class="mb-3 flex h-10 w-10 items-center justify-center rounded-full bg-blue-100"><i class="ri-calendar-check-line text-xl text-blue-600"></i></div>
            <p class="text-xs font-medium text-gray-500">Month Revenue</p>
            <p class="mt-1 text-xl font-bold text-gray-800">৳{{ formatNumber(props.kpi?.monthRevenue) }}</p>
            <p class="mt-1 text-xs text-gray-400">This month</p>
        </div>
        <div class="flex cursor-pointer flex-col rounded-xl border border-skin-neutral-4 bg-skin-neutral-2 p-4 shadow-sm hover:bg-skin-neutral-1" @click="$inertia.visit(route('order.index'))">
            <div class="mb-3 flex h-10 w-10 items-center justify-center rounded-full bg-purple-100"><i class="ri-shopping-bag-3-line text-xl text-purple-600"></i></div>
            <p class="text-xs font-medium text-gray-500">Total Orders</p>
            <p class="mt-1 text-xl font-bold text-gray-800">{{ props.kpi?.totalOrders }}</p>
            <p class="mt-1 text-xs text-gray-400">{{ props.kpi?.monthOrders }} this month</p>
        </div>
        <div class="flex cursor-pointer flex-col rounded-xl border border-skin-neutral-4 bg-skin-neutral-2 p-4 shadow-sm hover:bg-skin-neutral-1" @click="$inertia.visit(route('customer.index'))">
            <div class="mb-3 flex h-10 w-10 items-center justify-center rounded-full bg-orange-100"><i class="ri-group-line text-xl text-orange-600"></i></div>
            <p class="text-xs font-medium text-gray-500">Customers</p>
            <p class="mt-1 text-xl font-bold text-gray-800">{{ props.kpi?.totalCustomers }}</p>
            <p class="mt-1 text-xs text-gray-400">Registered</p>
        </div>
        <div class="flex cursor-pointer flex-col rounded-xl border border-skin-neutral-4 bg-skin-neutral-2 p-4 shadow-sm hover:bg-skin-neutral-1" @click="$inertia.visit(route('product.index'))">
            <div class="mb-3 flex h-10 w-10 items-center justify-center rounded-full bg-teal-100"><i class="ri-stack-line text-xl text-teal-600"></i></div>
            <p class="text-xs font-medium text-gray-500">Products</p>
            <p class="mt-1 text-xl font-bold text-gray-800">{{ props.kpi?.totalProducts }}</p>
            <p class="mt-1 text-xs text-gray-400">{{ props.count?.activeProducts }} active</p>
        </div>
        <div class="flex cursor-pointer flex-col rounded-xl border border-skin-neutral-4 bg-skin-neutral-2 p-4 shadow-sm hover:bg-skin-neutral-1" @click="$inertia.visit(route('productCategory.index'))">
            <div class="mb-3 flex h-10 w-10 items-center justify-center rounded-full bg-pink-100"><i class="ri-folders-line text-xl text-pink-600"></i></div>
            <p class="text-xs font-medium text-gray-500">Categories</p>
            <p class="mt-1 text-xl font-bold text-gray-800">{{ props.count?.totalProductCategories }}</p>
            <p class="mt-1 text-xs text-gray-400">{{ props.count?.activeProductCategories }} active</p>
        </div>
    </div>

    <!-- Charts row -->
    <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="col-span-2 rounded-xl border border-skin-neutral-4 bg-skin-neutral-2 p-5 shadow-sm">
            <div class="mb-4 flex items-center justify-between">
                <h3 class="font-semibold text-gray-700">Revenue — Last 12 Months</h3>
                <span class="text-xs text-gray-400">Excl. cancelled orders</span>
            </div>
            <canvas ref="revenueChartRef" height="100"></canvas>
        </div>
        <div class="rounded-xl border border-skin-neutral-4 bg-skin-neutral-2 p-5 shadow-sm">
            <h3 class="mb-4 font-semibold text-gray-700">Orders by Status</h3>
            <canvas ref="statusChartRef" height="200"></canvas>
            <div class="mt-4 flex flex-wrap gap-2">
                <span v-for="(label, index) in statusLabels" :key="label" class="flex items-center gap-1 rounded-full px-2 py-1 text-xs font-medium" :style="{ backgroundColor: statusColors[index] + '22', color: statusColors[index] }">
                    {{ label }}: {{ props.ordersByStatus?.[label] ?? 0 }}
                </span>
            </div>
        </div>
    </div>

    <!-- Recent Orders + Top Products -->
    <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-2">
        <div class="rounded-xl border border-skin-neutral-4 bg-skin-neutral-2 shadow-sm">
            <div class="flex items-center justify-between border-b border-skin-neutral-4 px-5 py-4">
                <h3 class="font-semibold text-gray-700">Recent Orders</h3>
                <a :href="route('order.index')" class="text-xs text-blue-600 hover:underline">View all</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-skin-neutral-4 text-xs text-gray-500">
                            <th class="px-5 py-3 text-left font-medium">#</th>
                            <th class="px-5 py-3 text-left font-medium">Customer</th>
                            <th class="px-5 py-3 text-left font-medium">Status</th>
                            <th class="px-5 py-3 text-right font-medium">Total</th>
                            <th class="px-5 py-3 text-left font-medium">Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="order in props.recentOrders" :key="order.id" class="cursor-pointer border-b border-skin-neutral-4 hover:bg-skin-neutral-1" @click="$inertia.visit(route('order.show', order.id))">
                            <td class="px-5 py-3 text-gray-500">{{ order.id }}</td>
                            <td class="px-5 py-3 font-medium text-gray-800">{{ order.name }}</td>
                            <td class="px-5 py-3"><span class="rounded-full px-2 py-0.5 text-xs font-medium" :class="statusBadgeClass(order.status)">{{ order.status }}</span></td>
                            <td class="px-5 py-3 text-right font-semibold text-gray-800">৳{{ order.total }}</td>
                            <td class="px-5 py-3 text-xs text-gray-500">{{ order.created_at }}</td>
                        </tr>
                        <tr v-if="!props.recentOrders?.length"><td colspan="5" class="px-5 py-6 text-center text-gray-400">No orders yet.</td></tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="rounded-xl border border-skin-neutral-4 bg-skin-neutral-2 shadow-sm">
            <div class="flex items-center justify-between border-b border-skin-neutral-4 px-5 py-4">
                <h3 class="font-semibold text-gray-700">Top Selling Products</h3>
                <a :href="route('product.index')" class="text-xs text-blue-600 hover:underline">View all</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-skin-neutral-4 text-xs text-gray-500">
                            <th class="px-5 py-3 text-left font-medium">Product</th>
                            <th class="px-5 py-3 text-right font-medium">Qty Sold</th>
                            <th class="px-5 py-3 text-right font-medium">Revenue</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(product, i) in props.topProducts" :key="i" class="border-b border-skin-neutral-4">
                            <td class="px-5 py-3 font-medium text-gray-800">{{ product.product_name }}</td>
                            <td class="px-5 py-3 text-right text-gray-700">{{ product.sold_qty }}</td>
                            <td class="px-5 py-3 text-right font-semibold text-gray-800">৳{{ product.revenue }}</td>
                        </tr>
                        <tr v-if="!props.topProducts?.length"><td colspan="3" class="px-5 py-6 text-center text-gray-400">No sales data yet.</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import { Head } from '@inertiajs/vue3'
import { Chart, registerables } from 'chart.js'
import useAuthCan from '@/Composables/useAuthCan'

Chart.register(...registerables)

const { can } = useAuthCan()

const props = defineProps({
    count: { type: Object, default: () => ({}) },
    kpi: { type: Object, default: () => ({}) },
    ordersByStatus: { type: Object, default: () => ({}) },
    monthlyRevenue: { type: Object, default: () => ({ labels: [], data: [] }) },
    recentOrders: { type: Array, default: () => [] },
    topProducts: { type: Array, default: () => [] },
})

const revenueChartRef = ref(null)
const statusChartRef = ref(null)
let revenueChart = null
let statusChart = null

const statusLabels = ['pending', 'processing', 'shipped', 'delivered', 'completed', 'cancelled']
const statusColors = ['#F59E0B', '#3B82F6', '#8B5CF6', '#10B981', '#059669', '#EF4444']

function formatNumber(n) {
    if (!n) return '0'
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

onMounted(() => {
    if (revenueChartRef.value) {
        revenueChart = new Chart(revenueChartRef.value, {
            type: 'line',
            data: {
                labels: props.monthlyRevenue.labels,
                datasets: [{
                    label: 'Revenue (৳)',
                    data: props.monthlyRevenue.data,
                    borderColor: '#3B82F6',
                    backgroundColor: 'rgba(59,130,246,0.08)',
                    borderWidth: 2.5,
                    pointBackgroundColor: '#3B82F6',
                    pointRadius: 3,
                    fill: true,
                    tension: 0.4,
                }],
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { callback: (v) => '৳' + v.toLocaleString('en-IN'), font: { size: 11 } },
                        grid: { color: 'rgba(0,0,0,0.05)' },
                    },
                    x: { ticks: { font: { size: 11 } }, grid: { display: false } },
                },
            },
        })
    }

    if (statusChartRef.value) {
        statusChart = new Chart(statusChartRef.value, {
            type: 'doughnut',
            data: {
                labels: statusLabels,
                datasets: [{
                    data: statusLabels.map((s) => props.ordersByStatus?.[s] ?? 0),
                    backgroundColor: statusColors.map((c) => c + 'cc'),
                    borderWidth: 2,
                    borderColor: '#fff',
                }],
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false },
                    tooltip: { callbacks: { label: (ctx) => ` ${ctx.label}: ${ctx.raw} orders` } },
                },
                cutout: '68%',
            },
        })
    }
})

onUnmounted(() => {
    revenueChart?.destroy()
    statusChart?.destroy()
})
</script>
