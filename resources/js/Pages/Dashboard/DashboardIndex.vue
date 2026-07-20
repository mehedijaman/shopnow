<template>
    <Head title="Dashboard"></Head>

    <!-- Welcome bar -->
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 p-6 text-white shadow-md">
        <!-- Decorative background elements -->
        <div class="absolute -right-10 -top-10 h-40 w-40 rounded-full bg-indigo-500/10 blur-3xl"></div>
        <div class="absolute -bottom-10 -left-10 h-40 w-40 rounded-full bg-violet-500/10 blur-3xl"></div>

        <div class="relative flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
            <div>
                <span class="inline-flex items-center gap-1.5 rounded-full bg-indigo-500/20 px-3 py-1 text-xs font-semibold text-indigo-300 ring-1 ring-indigo-400/20">
                    <span class="relative flex h-2 w-2">
                        <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-indigo-400 opacity-75"></span>
                        <span class="relative inline-flex h-2 w-2 rounded-full bg-indigo-500"></span>
                    </span>
                    Live Overview
                </span>
                <h1 class="mt-3 text-2xl font-bold tracking-tight sm:text-3xl">
                    Welcome back, <span class="bg-gradient-to-r from-indigo-200 via-violet-200 to-indigo-200 bg-clip-text text-transparent">{{ $page.props.auth.user.name }}</span>!
                </h1>
                <p class="mt-1 text-sm text-slate-300">Here's a summary of what's happening with your store today.</p>
            </div>
            <div class="flex items-center gap-2 self-start rounded-xl bg-white/5 p-3 text-sm font-medium text-slate-200 backdrop-blur-xs ring-1 ring-white/10 sm:self-center">
                <i class="ri-calendar-2-line text-lg text-indigo-400"></i>
                <span>{{ $page.props.datetime.now }}</span>
            </div>
        </div>
    </div>

    <!-- KPI Cards -->
    <div class="mt-8 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6">
        <!-- Total Revenue -->
        <div class="group relative flex cursor-pointer flex-col justify-between overflow-hidden rounded-2xl border border-slate-100 bg-white p-5 shadow-xs transition-all duration-300 hover:-translate-y-1 hover:shadow-md dark:border-slate-800 dark:bg-slate-900" @click="$inertia.visit(route('order.index'))">
            <div class="absolute -right-2 -top-2 h-16 w-16 rounded-full bg-emerald-500/5 transition-all duration-500 group-hover:scale-150"></div>
            <div>
                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600 dark:bg-emerald-950/30 dark:text-emerald-400">
                    <i class="ri-money-dollar-circle-line text-2xl"></i>
                </div>
                <p class="mt-4 text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500">Total Revenue</p>
                <p class="mt-1 text-2xl font-bold tracking-tight text-slate-800 dark:text-slate-100">৳{{ formatNumber(props.kpi?.totalRevenue) }}</p>
            </div>
            <p class="mt-3 text-xs text-slate-400 dark:text-slate-500">All time (excl. cancelled)</p>
        </div>

        <!-- Month Revenue -->
        <div class="group relative flex cursor-pointer flex-col justify-between overflow-hidden rounded-2xl border border-slate-100 bg-white p-5 shadow-xs transition-all duration-300 hover:-translate-y-1 hover:shadow-md dark:border-slate-800 dark:bg-slate-900" @click="$inertia.visit(route('order.index'))">
            <div class="absolute -right-2 -top-2 h-16 w-16 rounded-full bg-blue-500/5 transition-all duration-500 group-hover:scale-150"></div>
            <div>
                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-blue-50 text-blue-600 dark:bg-blue-950/30 dark:text-blue-400">
                    <i class="ri-calendar-check-line text-2xl"></i>
                </div>
                <p class="mt-4 text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500">Month Revenue</p>
                <p class="mt-1 text-2xl font-bold tracking-tight text-slate-800 dark:text-slate-100">৳{{ formatNumber(props.kpi?.monthRevenue) }}</p>
            </div>
            <p class="mt-3 text-xs text-slate-400 dark:text-slate-500">This month</p>
        </div>

        <!-- Total Orders -->
        <div class="group relative flex cursor-pointer flex-col justify-between overflow-hidden rounded-2xl border border-slate-100 bg-white p-5 shadow-xs transition-all duration-300 hover:-translate-y-1 hover:shadow-md dark:border-slate-800 dark:bg-slate-900" @click="$inertia.visit(route('order.index'))">
            <div class="absolute -right-2 -top-2 h-16 w-16 rounded-full bg-violet-500/5 transition-all duration-500 group-hover:scale-150"></div>
            <div>
                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-violet-50 text-violet-600 dark:bg-violet-950/30 dark:text-violet-400">
                    <i class="ri-shopping-bag-3-line text-2xl"></i>
                </div>
                <p class="mt-4 text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500">Total Orders</p>
                <p class="mt-1 text-2xl font-bold tracking-tight text-slate-800 dark:text-slate-100">{{ props.kpi?.totalOrders }}</p>
            </div>
            <p class="mt-3 text-xs text-slate-400 dark:text-slate-500">{{ props.kpi?.monthOrders }} this month</p>
        </div>

        <!-- Customers -->
        <div class="group relative flex cursor-pointer flex-col justify-between overflow-hidden rounded-2xl border border-slate-100 bg-white p-5 shadow-xs transition-all duration-300 hover:-translate-y-1 hover:shadow-md dark:border-slate-800 dark:bg-slate-900" @click="$inertia.visit(route('customer.index'))">
            <div class="absolute -right-2 -top-2 h-16 w-16 rounded-full bg-amber-500/5 transition-all duration-500 group-hover:scale-150"></div>
            <div>
                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-amber-50 text-amber-600 dark:bg-amber-950/30 dark:text-amber-400">
                    <i class="ri-group-line text-2xl"></i>
                </div>
                <p class="mt-4 text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500">Customers</p>
                <p class="mt-1 text-2xl font-bold tracking-tight text-slate-800 dark:text-slate-100">{{ props.kpi?.totalCustomers }}</p>
            </div>
            <p class="mt-3 text-xs text-slate-400 dark:text-slate-500">Registered</p>
        </div>

        <!-- Products -->
        <div class="group relative flex cursor-pointer flex-col justify-between overflow-hidden rounded-2xl border border-slate-100 bg-white p-5 shadow-xs transition-all duration-300 hover:-translate-y-1 hover:shadow-md dark:border-slate-800 dark:bg-slate-900" @click="$inertia.visit(route('product.index'))">
            <div class="absolute -right-2 -top-2 h-16 w-16 rounded-full bg-cyan-500/5 transition-all duration-500 group-hover:scale-150"></div>
            <div>
                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-cyan-50 text-cyan-600 dark:bg-cyan-950/30 dark:text-cyan-400">
                    <i class="ri-stack-line text-2xl"></i>
                </div>
                <p class="mt-4 text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500">Products</p>
                <p class="mt-1 text-2xl font-bold tracking-tight text-slate-800 dark:text-slate-100">{{ props.kpi?.totalProducts }}</p>
            </div>
            <p class="mt-3 text-xs text-slate-400 dark:text-slate-500">{{ props.count?.activeProducts }} active</p>
        </div>

        <!-- Categories -->
        <div class="group relative flex cursor-pointer flex-col justify-between overflow-hidden rounded-2xl border border-slate-100 bg-white p-5 shadow-xs transition-all duration-300 hover:-translate-y-1 hover:shadow-md dark:border-slate-800 dark:bg-slate-900" @click="$inertia.visit(route('productCategory.index'))">
            <div class="absolute -right-2 -top-2 h-16 w-16 rounded-full bg-rose-500/5 transition-all duration-500 group-hover:scale-150"></div>
            <div>
                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-rose-50 text-rose-600 dark:bg-rose-950/30 dark:text-rose-400">
                    <i class="ri-folders-line text-2xl"></i>
                </div>
                <p class="mt-4 text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500">Categories</p>
                <p class="mt-1 text-2xl font-bold tracking-tight text-slate-800 dark:text-slate-100">{{ props.count?.totalProductCategories }}</p>
            </div>
            <p class="mt-3 text-xs text-slate-400 dark:text-slate-500">{{ props.count?.activeProductCategories }} active</p>
        </div>
    </div>

    <!-- Charts row -->
    <div class="mt-8 grid grid-cols-1 gap-6 lg:grid-cols-3">
        <!-- Revenue Chart -->
        <div class="col-span-2 rounded-2xl border border-slate-100 bg-white p-6 shadow-xs dark:border-slate-800 dark:bg-slate-900">
            <div class="mb-6 flex items-center justify-between">
                <div>
                    <h3 class="text-base font-bold text-slate-800 dark:text-slate-100">Revenue Evolution</h3>
                    <p class="text-xs text-slate-400 dark:text-slate-500">Excluding cancelled orders</p>
                </div>
                <span class="inline-flex items-center gap-1 rounded-full bg-blue-50 px-2.5 py-1 text-xs font-semibold text-blue-600 dark:bg-blue-950/30 dark:text-blue-400">
                    Last 12 Months
                </span>
            </div>
            <div class="relative h-[300px] w-full">
                <canvas ref="revenueChartRef"></canvas>
            </div>
        </div>

        <!-- Orders by Status Chart -->
        <div class="rounded-2xl border border-slate-100 bg-white p-6 shadow-xs dark:border-slate-800 dark:bg-slate-900">
            <div class="mb-6">
                <h3 class="text-base font-bold text-slate-800 dark:text-slate-100">Orders Status</h3>
                <p class="text-xs text-slate-400 dark:text-slate-500">Distribution by status</p>
            </div>
            <div class="relative flex justify-center py-2 h-[220px]">
                <canvas ref="statusChartRef"></canvas>
            </div>
            <div class="mt-6 grid grid-cols-2 gap-2">
                <div v-for="(label, index) in statusLabels" :key="label" class="flex items-center gap-2 rounded-xl bg-slate-50 p-2 text-xs font-medium dark:bg-slate-800/50">
                    <span class="h-2.5 w-2.5 rounded-full" :style="{ backgroundColor: statusColors[index] }"></span>
                    <span class="flex-1 text-slate-600 dark:text-slate-400 capitalize">{{ label }}</span>
                    <span class="font-bold text-slate-800 dark:text-slate-200">{{ props.ordersByStatus?.[label] ?? 0 }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Orders + Top Products -->
    <div class="mt-8 grid grid-cols-1 gap-6 lg:grid-cols-2">
        <!-- Recent Orders -->
        <div class="overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-xs dark:border-slate-800 dark:bg-slate-900">
            <div class="flex items-center justify-between border-b border-slate-100 px-6 py-5 dark:border-slate-800">
                <div>
                    <h3 class="text-base font-bold text-slate-800 dark:text-slate-100">Recent Orders</h3>
                    <p class="text-xs text-slate-400 dark:text-slate-500">Latest transactions on your store</p>
                </div>
                <a :href="route('order.index')" class="inline-flex items-center gap-1 text-xs font-semibold text-indigo-600 hover:text-indigo-700 dark:text-indigo-400">
                    View all orders
                    <i class="ri-arrow-right-line"></i>
                </a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600 dark:text-slate-400">
                    <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wider text-slate-400 dark:bg-slate-800/40 dark:text-slate-500">
                        <tr>
                            <th class="px-6 py-3">Order ID</th>
                            <th class="px-6 py-3">Customer</th>
                            <th class="px-6 py-3">Status</th>
                            <th class="px-6 py-3 text-right">Total</th>
                            <th class="px-6 py-3">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800/50">
                        <tr 
                            v-for="order in props.recentOrders" 
                            :key="order.id" 
                            class="group cursor-pointer hover:bg-slate-50/55 dark:hover:bg-slate-800/20" 
                            @click="$inertia.visit(route('order.show', order.id))"
                        >
                            <td class="px-6 py-4 font-semibold text-slate-700 dark:text-slate-300">#{{ order.id }}</td>
                            <td class="px-6 py-4">
                                <span class="font-semibold text-slate-800 group-hover:text-indigo-600 dark:text-slate-200 dark:group-hover:text-indigo-400">{{ order.name }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-semibold" :class="statusBadgeClass(order.status)">
                                    <span class="h-1.5 w-1.5 rounded-full" :class="statusDotClass(order.status)"></span>
                                    {{ order.status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right font-bold text-slate-800 dark:text-slate-200">৳{{ order.total }}</td>
                            <td class="px-6 py-4 text-xs text-slate-400 dark:text-slate-500">{{ order.created_at }}</td>
                        </tr>
                        <tr v-if="!props.recentOrders?.length">
                            <td colspan="5" class="px-6 py-10 text-center text-slate-400 dark:text-slate-500">No orders yet.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Top Selling Products -->
        <div class="overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-xs dark:border-slate-800 dark:bg-slate-900">
            <div class="flex items-center justify-between border-b border-slate-100 px-6 py-5 dark:border-slate-800">
                <div>
                    <h3 class="text-base font-bold text-slate-800 dark:text-slate-100">Top Selling Products</h3>
                    <p class="text-xs text-slate-400 dark:text-slate-500">Most popular products in terms of sales</p>
                </div>
                <a :href="route('product.index')" class="inline-flex items-center gap-1 text-xs font-semibold text-indigo-600 hover:text-indigo-700 dark:text-indigo-400">
                    View all products
                    <i class="ri-arrow-right-line"></i>
                </a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600 dark:text-slate-400">
                    <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wider text-slate-400 dark:bg-slate-800/40 dark:text-slate-500">
                        <tr>
                            <th class="px-6 py-3">Product Name</th>
                            <th class="px-6 py-3 text-right">Qty Sold</th>
                            <th class="px-6 py-3 text-right">Revenue</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800/50">
                        <tr v-for="(product, i) in props.topProducts" :key="i" class="hover:bg-slate-50/55 dark:hover:bg-slate-800/20">
                            <td class="px-6 py-4 font-semibold text-slate-800 dark:text-slate-200">{{ product.product_name }}</td>
                            <td class="px-6 py-4 text-right font-medium text-slate-700 dark:text-slate-300">{{ product.sold_qty }}</td>
                            <td class="px-6 py-4 text-right font-bold text-emerald-600 dark:text-emerald-400">৳{{ product.revenue }}</td>
                        </tr>
                        <tr v-if="!props.topProducts?.length">
                            <td colspan="3" class="px-6 py-10 text-center text-slate-400 dark:text-slate-500">No sales data yet.</td>
                        </tr>
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
        pending: 'bg-amber-50 text-amber-700 dark:bg-amber-950/20 dark:text-amber-400',
        processing: 'bg-blue-50 text-blue-700 dark:bg-blue-950/20 dark:text-blue-400',
        shipped: 'bg-violet-50 text-violet-700 dark:bg-violet-950/20 dark:text-violet-400',
        delivered: 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/20 dark:text-emerald-400',
        completed: 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/20 dark:text-emerald-400',
        cancelled: 'bg-rose-50 text-rose-700 dark:bg-rose-950/20 dark:text-rose-400',
    }
    return map[status] ?? 'bg-slate-50 text-slate-700 dark:bg-slate-850 dark:text-slate-400'
}

function statusDotClass(status) {
    const map = {
        pending: 'bg-amber-500',
        processing: 'bg-blue-500',
        shipped: 'bg-violet-500',
        delivered: 'bg-emerald-500',
        completed: 'bg-emerald-500',
        cancelled: 'bg-rose-500',
    }
    return map[status] ?? 'bg-slate-400'
}

onMounted(() => {
    if (revenueChartRef.value) {
        const ctx = revenueChartRef.value.getContext('2d')
        const gradient = ctx.createLinearGradient(0, 0, 0, 300)
        gradient.addColorStop(0, 'rgba(99, 102, 241, 0.25)')
        gradient.addColorStop(1, 'rgba(99, 102, 241, 0)')

        revenueChart = new Chart(revenueChartRef.value, {
            type: 'line',
            data: {
                labels: props.monthlyRevenue.labels,
                datasets: [{
                    label: 'Revenue (৳)',
                    data: props.monthlyRevenue.data,
                    borderColor: '#6366f1',
                    backgroundColor: gradient,
                    borderWidth: 3,
                    pointBackgroundColor: '#6366f1',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    fill: true,
                    tension: 0.35,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        padding: 12,
                        backgroundColor: '#1e293b',
                        titleColor: '#f8fafc',
                        bodyColor: '#cbd5e1',
                        bodyFont: { size: 12 },
                        callbacks: { label: (ctx) => ` ৳${ctx.raw.toLocaleString('en-IN')}` }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { callback: (v) => '৳' + v.toLocaleString('en-IN'), font: { size: 10, weight: '500' }, color: '#94a3b8' },
                        grid: { color: 'rgba(148, 163, 184, 0.08)' },
                        border: { dash: [5, 5] }
                    },
                    x: { ticks: { font: { size: 10, weight: '500' }, color: '#94a3b8' }, grid: { display: false } },
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
                    backgroundColor: statusColors,
                    borderWidth: 4,
                    borderColor: '#ffffff',
                    hoverOffset: 4
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        padding: 12,
                        backgroundColor: '#1e293b',
                        callbacks: { label: (ctx) => ` ${ctx.label}: ${ctx.raw} orders` }
                    },
                },
                cutout: '75%',
            },
        })
    }
})

onUnmounted(() => {
    revenueChart?.destroy()
    statusChart?.destroy()
})
</script>
