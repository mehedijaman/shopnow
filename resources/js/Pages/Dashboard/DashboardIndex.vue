<template>
    <Head title="Dashboard"></Head>

    <!-- Welcome bar with integrated filters -->
    <div class="relative overflow-hidden rounded-xl bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 p-4 text-white shadow-xs">
        <div class="absolute -right-10 -top-10 h-32 w-32 rounded-full bg-indigo-500/10 blur-2xl"></div>
        <div class="absolute -bottom-10 -left-10 h-32 w-32 rounded-full bg-violet-500/10 blur-2xl"></div>

        <div class="relative flex flex-col justify-between gap-4 md:flex-row md:items-center">
            <div class="flex items-center gap-3">
                <div class="hidden h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white/10 backdrop-blur-xs md:flex">
                    <i class="ri-dashboard-3-line text-lg text-indigo-300"></i>
                </div>
                <div>
                    <h1 class="text-lg font-bold tracking-tight">
                        Welcome back, <span class="bg-gradient-to-r from-indigo-200 via-violet-200 to-indigo-200 bg-clip-text text-transparent">{{ $page.props.auth.user.name }}</span>!
                    </h1>
                    <p class="text-xs text-slate-300">Store summary for today: {{ $page.props.datetime.now }}</p>
                </div>
            </div>
            <!-- Timeframe Filter integrated right into the welcome bar -->
            <div class="flex items-center gap-1 rounded-lg bg-white/10 p-0.5 backdrop-blur-xs ring-1 ring-white/10">
                <button 
                    class="rounded-md px-3 py-1 text-xs font-semibold transition-all duration-200"
                    :class="periodFilter === 'all' ? 'bg-white text-slate-800 shadow-xs' : 'text-slate-200 hover:text-white'"
                    @click="periodFilter = 'all'"
                >
                    All Time
                </button>
                <button 
                    class="rounded-md px-3 py-1 text-xs font-semibold transition-all duration-200"
                    :class="periodFilter === 'month' ? 'bg-white text-slate-800 shadow-xs' : 'text-slate-200 hover:text-white'"
                    @click="periodFilter = 'month'"
                >
                    This Month
                </button>
            </div>
        </div>
    </div>

    <!-- Primary KPIs Grid -->
    <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <!-- Revenue Card -->
        <div class="group relative flex flex-col justify-between overflow-hidden rounded-xl border border-slate-100 bg-white p-4 shadow-2xs transition-all duration-300 hover:-translate-y-0.5 hover:shadow-xs dark:border-slate-800 dark:bg-slate-900">
            <div class="absolute -right-2 -top-2 h-14 w-14 rounded-full bg-emerald-500/5 transition-all duration-500 group-hover:scale-150"></div>
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">Gross Revenue</p>
                    <p class="mt-1 text-xl font-extrabold tracking-tight text-slate-800 dark:text-slate-100">৳{{ formatNumber(displayRevenue) }}</p>
                </div>
                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-emerald-50 text-emerald-600 dark:bg-emerald-950/30 dark:text-emerald-400">
                    <i class="ri-money-dollar-circle-line text-lg"></i>
                </div>
            </div>
            <p class="mt-2 text-[10px] text-slate-400 dark:text-slate-500">
                <span class="capitalize">{{ displayPeriodLabel }}</span> (excl. cancelled)
            </p>
        </div>

        <!-- Orders Card -->
        <div class="group relative flex flex-col justify-between overflow-hidden rounded-xl border border-slate-100 bg-white p-4 shadow-2xs transition-all duration-300 hover:-translate-y-0.5 hover:shadow-xs dark:border-slate-800 dark:bg-slate-900">
            <div class="absolute -right-2 -top-2 h-14 w-14 rounded-full bg-indigo-500/5 transition-all duration-500 group-hover:scale-150"></div>
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">Orders Received</p>
                    <p class="mt-1 text-xl font-extrabold tracking-tight text-slate-800 dark:text-slate-100">{{ formatNumber(displayOrders) }}</p>
                </div>
                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-indigo-50 text-indigo-600 dark:bg-indigo-950/30 dark:text-indigo-400">
                    <i class="ri-shopping-bag-3-line text-lg"></i>
                </div>
            </div>
            <p class="mt-2 text-[10px] text-slate-400 dark:text-slate-500">
                Transactions in <span class="lowercase">{{ displayPeriodLabel }}</span>
            </p>
        </div>

        <!-- Average Order Value Card -->
        <div class="group relative flex flex-col justify-between overflow-hidden rounded-xl border border-slate-100 bg-white p-4 shadow-2xs transition-all duration-300 hover:-translate-y-0.5 hover:shadow-xs dark:border-slate-800 dark:bg-slate-900">
            <div class="absolute -right-2 -top-2 h-14 w-14 rounded-full bg-violet-500/5 transition-all duration-500 group-hover:scale-150"></div>
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">Avg Order Value</p>
                    <p class="mt-1 text-xl font-extrabold tracking-tight text-slate-800 dark:text-slate-100">৳{{ formatNumber(displayAOV) }}</p>
                </div>
                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-violet-50 text-violet-600 dark:bg-violet-950/30 dark:text-violet-400">
                    <i class="ri-calculator-line text-lg"></i>
                </div>
            </div>
            <p class="mt-2 text-[10px] text-slate-400 dark:text-slate-500">
                Average per order in <span class="lowercase">{{ displayPeriodLabel }}</span>
            </p>
        </div>

        <!-- Conversion & Growth Card -->
        <div class="group relative flex flex-col justify-between overflow-hidden rounded-xl border border-slate-100 bg-white p-4 shadow-2xs transition-all duration-300 hover:-translate-y-0.5 hover:shadow-xs dark:border-slate-800 dark:bg-slate-900">
            <div class="absolute -right-2 -top-2 h-14 w-14 rounded-full bg-amber-500/5 transition-all duration-500 group-hover:scale-150"></div>
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">Conversion Rate</p>
                    <p class="mt-1 text-xl font-extrabold tracking-tight text-slate-800 dark:text-slate-100">2.84%</p>
                </div>
                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-amber-50 text-amber-600 dark:bg-amber-950/30 dark:text-amber-400">
                    <i class="ri-percent-line text-lg"></i>
                </div>
            </div>
            <div class="mt-2 flex items-center gap-1 text-[10px] text-emerald-600 dark:text-emerald-400">
                <i class="ri-arrow-up-line"></i>
                <span>+0.4% this week</span>
            </div>
        </div>
    </div>

    <!-- Secondary Management Stats Grid -->
    <div class="mt-4 grid grid-cols-2 gap-4 lg:grid-cols-4">
        <!-- Customers summary -->
        <div class="flex items-center gap-3 rounded-xl border border-slate-100 bg-white p-3 shadow-2xs dark:border-slate-800 dark:bg-slate-900">
            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-blue-50 text-blue-600 dark:bg-blue-950/30 dark:text-blue-400">
                <i class="ri-group-line text-lg"></i>
            </div>
            <div class="min-w-0">
                <p class="truncate text-[10px] font-medium text-slate-400 dark:text-slate-500">Customers</p>
                <p class="truncate text-sm font-bold text-slate-800 dark:text-slate-100">{{ formatNumber(props.kpi?.totalCustomers) }}</p>
            </div>
        </div>

        <!-- Products Status -->
        <div class="flex items-center gap-3 rounded-xl border border-slate-100 bg-white p-3 shadow-2xs dark:border-slate-800 dark:bg-slate-900">
            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-cyan-50 text-cyan-600 dark:bg-cyan-950/30 dark:text-cyan-400">
                <i class="ri-stack-line text-lg"></i>
            </div>
            <div class="min-w-0">
                <p class="truncate text-[10px] font-medium text-slate-400 dark:text-slate-500">Products</p>
                <p class="truncate text-sm font-bold text-slate-800 dark:text-slate-100">
                    {{ props.count?.totalProducts }} <span class="text-[10px] font-normal text-slate-450">({{ props.count?.activeProducts }} active)</span>
                </p>
            </div>
        </div>

        <!-- Categories & Brands -->
        <div class="flex items-center gap-3 rounded-xl border border-slate-100 bg-white p-3 shadow-2xs dark:border-slate-800 dark:bg-slate-900">
            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-rose-50 text-rose-600 dark:bg-rose-950/30 dark:text-rose-400">
                <i class="ri-folders-line text-lg"></i>
            </div>
            <div class="min-w-0">
                <p class="truncate text-[10px] font-medium text-slate-400 dark:text-slate-500">Categories & Brands</p>
                <p class="truncate text-sm font-bold text-slate-800 dark:text-slate-100">
                    {{ props.count?.totalProductCategories }} <span class="text-[10px] font-normal text-slate-450">/ {{ props.count?.totalProductBrands }}</span>
                </p>
            </div>
        </div>

        <!-- System & Staff Roles -->
        <div class="flex items-center gap-3 rounded-xl border border-slate-100 bg-white p-3 shadow-2xs dark:border-slate-800 dark:bg-slate-900">
            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-purple-50 text-purple-600 dark:bg-purple-950/30 dark:text-purple-400">
                <i class="ri-shield-keyhole-line text-lg"></i>
            </div>
            <div class="min-w-0">
                <p class="truncate text-[10px] font-medium text-slate-400 dark:text-slate-500">Staff & Roles</p>
                <p class="truncate text-sm font-bold text-slate-800 dark:text-slate-100">
                    {{ props.count?.users }} <span class="text-[10px] font-normal text-slate-450">({{ props.count?.roles }} roles)</span>
                </p>
            </div>
        </div>
    </div>

    <!-- Charts row -->
    <div class="mt-4 grid grid-cols-1 gap-4 lg:grid-cols-3">
        <!-- Revenue Chart -->
        <div class="col-span-2 rounded-xl border border-slate-100 bg-white p-4 shadow-2xs dark:border-slate-800 dark:bg-slate-900">
            <div class="mb-3 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-bold text-slate-800 dark:text-slate-100">Revenue Evolution</h3>
                    <p class="text-[10px] text-slate-400 dark:text-slate-500">Excluding cancelled orders</p>
                </div>
                <span class="inline-flex items-center gap-1 rounded-full bg-blue-50 px-2 py-0.5 text-[10px] font-semibold text-blue-600 dark:bg-blue-950/30 dark:text-blue-400">
                    Last 12 Months
                </span>
            </div>
            <div class="relative h-[200px] w-full">
                <canvas ref="revenueChartRef"></canvas>
            </div>
        </div>

        <!-- Orders by Status Chart -->
        <div class="rounded-xl border border-slate-100 bg-white p-4 shadow-2xs dark:border-slate-800 dark:bg-slate-900">
            <div class="mb-3">
                <h3 class="text-sm font-bold text-slate-800 dark:text-slate-100">Orders Status</h3>
                <p class="text-[10px] text-slate-400 dark:text-slate-500">Distribution by status</p>
            </div>
            <div class="relative flex justify-center h-[120px]">
                <canvas ref="statusChartRef"></canvas>
            </div>
            <div class="mt-3 grid grid-cols-2 gap-1.5">
                <div v-for="(label, index) in statusLabels" :key="label" class="flex items-center gap-1.5 rounded-lg bg-slate-50 p-1.5 text-[10px] font-medium dark:bg-slate-800/50">
                    <span class="h-2 w-2 rounded-full shrink-0" :style="{ backgroundColor: statusColors[index] }"></span>
                    <span class="flex-1 truncate text-slate-600 dark:text-slate-400 capitalize">{{ label }}</span>
                    <span class="font-bold text-slate-800 dark:text-slate-200">{{ props.ordersByStatus?.[label] ?? 0 }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Tables Grid -->
    <div class="mt-4 grid grid-cols-1 gap-4 lg:grid-cols-2">
        <!-- Recent Orders Table Container -->
        <div class="rounded-xl border border-slate-100 bg-white shadow-2xs dark:border-slate-800 dark:bg-slate-900">
            <div class="border-b border-slate-100 p-4 dark:border-slate-800">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h3 class="text-sm font-bold text-slate-800 dark:text-slate-100">Recent Orders</h3>
                        <p class="text-[10px] text-slate-400 dark:text-slate-550">Overview of latest transactions.</p>
                    </div>
                    <a :href="route('order.index')" class="inline-flex items-center gap-1 text-[10px] font-semibold text-indigo-600 hover:text-indigo-700 dark:text-indigo-400">
                        View all orders
                        <i class="ri-arrow-right-line"></i>
                    </a>
                </div>
                <!-- Filters Controls inside header -->
                <div class="mt-3 flex flex-col gap-2 sm:flex-row sm:items-center">
                    <div class="relative flex-1">
                        <i class="ri-search-line absolute left-2.5 top-2 text-slate-450"></i>
                        <input 
                            v-model="orderSearchQuery" 
                            type="text" 
                            placeholder="Search orders..." 
                            class="w-full rounded-lg border border-slate-200 bg-slate-50 py-1.5 pl-8 pr-3 text-xs outline-hidden transition-all duration-200 focus:border-indigo-500 focus:bg-white dark:border-slate-800 dark:bg-slate-900 dark:focus:border-indigo-400"
                        />
                    </div>
                    <div class="relative">
                        <select 
                            v-model="orderStatusFilter" 
                            class="w-full rounded-lg border border-slate-200 bg-slate-50 py-1.5 pl-2.5 pr-7 text-xs text-slate-600 outline-hidden transition-all duration-200 focus:border-indigo-500 focus:bg-white dark:border-slate-800 dark:bg-slate-900 dark:text-slate-300 dark:focus:border-indigo-400"
                        >
                            <option value="all">All Statuses</option>
                            <option v-for="status in statusLabels" :key="status" :value="status" class="capitalize">
                                {{ status }}
                            </option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- AppDataTable implementation -->
            <AppDataTable :headers="['Order ID', 'Customer', 'Status', 'Total', 'Date']">
                <template #TableBody>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800/50">
                        <tr 
                            v-for="order in filteredRecentOrders" 
                            :key="order.id" 
                            class="group cursor-pointer hover:bg-slate-50/55 dark:hover:bg-slate-800/20" 
                            @click="$inertia.visit(route('order.show', order.id))"
                        >
                            <td class="px-4 py-2 text-xs font-semibold text-slate-700 dark:text-slate-300">#{{ order.id }}</td>
                            <td class="px-4 py-2 text-xs">
                                <span class="font-semibold text-slate-800 group-hover:text-indigo-600 dark:text-slate-200 dark:group-hover:text-indigo-400">{{ order.name }}</span>
                            </td>
                            <td class="px-4 py-2 text-xs">
                                <span class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-[10px] font-semibold" :class="statusBadgeClass(order.status)">
                                    <span class="h-1 w-1 rounded-full" :class="statusDotClass(order.status)"></span>
                                    {{ order.status }}
                                </span>
                            </td>
                            <td class="px-4 py-2 text-xs text-right font-bold text-slate-800 dark:text-slate-200">৳{{ order.total }}</td>
                            <td class="px-4 py-2 text-[10px] text-slate-450 dark:text-slate-550">{{ order.created_at }}</td>
                        </tr>
                        <tr v-if="!filteredRecentOrders.length">
                            <td colspan="5" class="px-4 py-6 text-center text-xs text-slate-400 dark:text-slate-500">No matching orders found.</td>
                        </tr>
                    </tbody>
                </template>
            </AppDataTable>
        </div>

        <!-- Top Selling Products Table Container -->
        <div class="rounded-xl border border-slate-100 bg-white shadow-2xs dark:border-slate-800 dark:bg-slate-900">
            <div class="border-b border-slate-100 p-4 dark:border-slate-800">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h3 class="text-sm font-bold text-slate-800 dark:text-slate-100">Top Selling Products</h3>
                        <p class="text-[10px] text-slate-400 dark:text-slate-500">Most popular items by sales qty.</p>
                    </div>
                    <a :href="route('product.index')" class="inline-flex items-center gap-1 text-[10px] font-semibold text-indigo-600 hover:text-indigo-700 dark:text-indigo-400">
                        View all products
                        <i class="ri-arrow-right-line"></i>
                    </a>
                </div>
                <!-- Filters Controls inside header -->
                <div class="mt-3 flex items-center">
                    <div class="relative flex-1">
                        <i class="ri-search-line absolute left-2.5 top-2 text-slate-450"></i>
                        <input 
                            v-model="productSearchQuery" 
                            type="text" 
                            placeholder="Search products..." 
                            class="w-full rounded-lg border border-slate-200 bg-slate-50 py-1.5 pl-8 pr-3 text-xs outline-hidden transition-all duration-200 focus:border-indigo-500 focus:bg-white dark:border-slate-800 dark:bg-slate-900 dark:focus:border-indigo-400"
                        />
                    </div>
                </div>
            </div>

            <!-- AppDataTable implementation -->
            <AppDataTable :headers="['Product Name', 'Qty Sold', 'Revenue']">
                <template #TableBody>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800/50">
                        <tr v-for="(product, i) in filteredTopProducts" :key="i" class="hover:bg-slate-50/55 dark:hover:bg-slate-800/20">
                            <td class="px-4 py-2 text-xs font-semibold text-slate-800 dark:text-slate-200">{{ product.product_name }}</td>
                            <td class="px-4 py-2 text-xs text-right font-medium text-slate-700 dark:text-slate-300">{{ product.sold_qty }}</td>
                            <td class="px-4 py-2 text-xs text-right font-bold text-emerald-600 dark:text-emerald-400">৳{{ product.revenue }}</td>
                        </tr>
                        <tr v-if="!filteredTopProducts.length">
                            <td colspan="3" class="px-4 py-6 text-center text-xs text-slate-400 dark:text-slate-500">No matching products found.</td>
                        </tr>
                    </tbody>
                </template>
            </AppDataTable>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
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

// Interactive dashboard period filter
const periodFilter = ref('all') // 'all', 'month'

const displayRevenue = computed(() => {
    return periodFilter.value === 'month' ? props.kpi?.monthRevenue : props.kpi?.totalRevenue
})

const displayOrders = computed(() => {
    return periodFilter.value === 'month' ? props.kpi?.monthOrders : props.kpi?.totalOrders
})

const displayAOV = computed(() => {
    const rev = displayRevenue.value
    const ord = displayOrders.value
    return ord > 0 ? roundNumber(rev / ord) : 0
})

const displayPeriodLabel = computed(() => {
    return periodFilter.value === 'month' ? 'This Month' : 'All Time'
})

// Search & Status filters for Recent Orders Table
const orderSearchQuery = ref('')
const orderStatusFilter = ref('all')

const filteredRecentOrders = computed(() => {
    return props.recentOrders.filter(order => {
        const matchesSearch = 
            (order.name && order.name.toLowerCase().includes(orderSearchQuery.value.toLowerCase())) || 
            (order.id && order.id.toString().includes(orderSearchQuery.value))
        const matchesStatus = orderStatusFilter.value === 'all' || order.status === orderStatusFilter.value
        return matchesSearch && matchesStatus
    })
})

// Search filters for Top Selling Products Table
const productSearchQuery = ref('')

const filteredTopProducts = computed(() => {
    return props.topProducts.filter(product => {
        return product.product_name && product.product_name.toLowerCase().includes(productSearchQuery.value.toLowerCase())
    })
})

function formatNumber(n) {
    if (!n) return '0'
    return Number(n).toLocaleString('en-IN')
}

function roundNumber(num) {
    return Math.round((num + Number.EPSILON) * 100) / 100
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

// Fixed minor color weight representation mapping
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
        const gradient = ctx.createLinearGradient(0, 0, 0, 200)
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
                    borderWidth: 3.0,
                    pointBackgroundColor: '#6366f1',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                    pointRadius: 3,
                    pointHoverRadius: 5,
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
                    borderWidth: 3,
                    borderColor: '#ffffff',
                    hoverOffset: 3
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
