<template>
    <Head :title="title"></Head>
    <AppSectionHeader :title="title" :bread-crumb="breadCrumb"></AppSectionHeader>

    <!-- Summary Cards -->
    <div class="mt-6 grid grid-cols-2 gap-4 md:grid-cols-4">
        <div class="flex flex-col rounded-xl border border-skin-neutral-4 bg-skin-neutral-2 p-4 shadow-sm">
            <div class="mb-2 flex h-9 w-9 items-center justify-center rounded-full bg-blue-100"><i class="ri-group-line text-blue-600"></i></div>
            <p class="text-xs text-gray-500">Total Customers</p>
            <p class="mt-1 text-xl font-bold text-gray-800">{{ props.summary?.totalCustomers }}</p>
        </div>
        <div class="flex flex-col rounded-xl border border-skin-neutral-4 bg-skin-neutral-2 p-4 shadow-sm">
            <div class="mb-2 flex h-9 w-9 items-center justify-center rounded-full bg-green-100"><i class="ri-user-follow-line text-green-600"></i></div>
            <p class="text-xs text-gray-500">Active</p>
            <p class="mt-1 text-xl font-bold text-gray-800">{{ props.summary?.activeCustomers }}</p>
        </div>
        <div class="flex flex-col rounded-xl border border-skin-neutral-4 bg-skin-neutral-2 p-4 shadow-sm">
            <div class="mb-2 flex h-9 w-9 items-center justify-center rounded-full bg-red-100"><i class="ri-user-unfollow-line text-red-600"></i></div>
            <p class="text-xs text-gray-500">Inactive</p>
            <p class="mt-1 text-xl font-bold text-gray-800">{{ props.summary?.inactiveCustomers }}</p>
        </div>
        <div class="flex flex-col rounded-xl border border-skin-neutral-4 bg-skin-neutral-2 p-4 shadow-sm">
            <div class="mb-2 flex h-9 w-9 items-center justify-center rounded-full bg-green-100"><i class="ri-money-dollar-circle-line text-green-600"></i></div>
            <p class="text-xs text-gray-500">Total Spent</p>
            <p class="mt-1 text-xl font-bold text-gray-800">৳{{ formatNumber(props.summary?.totalSpent) }}</p>
        </div>
    </div>

    <!-- Charts + Top Spenders -->
    <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="col-span-2 rounded-xl border border-skin-neutral-4 bg-skin-neutral-2 p-5 shadow-sm">
            <h3 class="mb-4 font-semibold text-gray-700">New Customers — Last 12 Months</h3>
            <canvas ref="monthlyChartRef" height="100"></canvas>
        </div>

        <div class="rounded-xl border border-skin-neutral-4 bg-skin-neutral-2 shadow-sm">
            <div class="border-b border-skin-neutral-4 px-5 py-4">
                <h3 class="font-semibold text-gray-700">Top Spenders</h3>
            </div>
            <div class="divide-y divide-skin-neutral-4">
                <div v-for="(c, i) in props.topSpenders" :key="c.id" class="flex items-center justify-between px-5 py-3 hover:bg-skin-neutral-1">
                    <div class="flex items-center gap-3">
                        <span class="flex h-7 w-7 items-center justify-center rounded-full bg-blue-100 text-xs font-bold text-blue-700">{{ i + 1 }}</span>
                        <div>
                            <p class="text-sm font-medium text-gray-800">{{ c.name }}</p>
                            <p class="text-xs text-gray-400">{{ c.phone }}</p>
                        </div>
                    </div>
                    <span class="text-sm font-bold text-gray-800">৳{{ formatNumber(c.total_spent) }}</span>
                </div>
                <div v-if="!props.topSpenders?.length" class="px-5 py-6 text-center text-sm text-gray-400">No customers yet.</div>
            </div>
        </div>
    </div>

    <!-- Paginated customer list -->
    <div class="mt-6 rounded-xl border border-skin-neutral-4 bg-skin-neutral-2 shadow-sm">
        <div class="border-b border-skin-neutral-4 px-5 py-4">
            <h3 class="font-semibold text-gray-700">All Customers by Spend</h3>
        </div>
        <AppDataTable v-if="props.customers?.data?.length" :headers="['Name', 'Email', 'Phone', 'Total Spent', 'Status', 'Joined']">
            <template #TableBody>
                <tbody>
                    <AppDataTableRow v-for="item in props.customers.data" :key="item.id">
                        <AppDataTableData class="font-medium">{{ item.name }}</AppDataTableData>
                        <AppDataTableData>{{ item.email }}</AppDataTableData>
                        <AppDataTableData>{{ item.phone }}</AppDataTableData>
                        <AppDataTableData class="font-semibold">৳{{ formatNumber(item.total_spent) }}</AppDataTableData>
                        <AppDataTableData>
                            <span class="rounded-full px-2 py-0.5 text-xs font-medium" :class="item.active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'">
                                {{ item.active ? 'Active' : 'Inactive' }}
                            </span>
                        </AppDataTableData>
                        <AppDataTableData>{{ item.joined }}</AppDataTableData>
                    </AppDataTableRow>
                </tbody>
            </template>
        </AppDataTable>
        <AppPaginator
            :links="props.customers?.links"
            :from="props.customers?.from ?? 0"
            :to="props.customers?.to ?? 0"
            :total="props.customers?.total ?? 0"
            class="mt-4 justify-center"
        ></AppPaginator>
        <AppAlert v-if="!props.customers?.data?.length" class="m-4">No customers found.</AppAlert>
    </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import { Head } from '@inertiajs/vue3'
import { Chart, registerables } from 'chart.js'
import useTitle from '@/Composables/useTitle'
import useAuthCan from '@/Composables/useAuthCan'

Chart.register(...registerables)

const { title } = useTitle('Customer Report')
const { can } = useAuthCan()

const props = defineProps({
    summary: { type: Object, default: () => ({}) },
    topSpenders: { type: Array, default: () => [] },
    monthlyNew: { type: Object, default: () => ({ labels: [], data: [] }) },
    customers: { type: Object, default: () => ({}) },
})

const breadCrumb = [
    { label: 'Home', href: route('dashboard.index') },
    { label: 'Customers', href: route('customer.index') },
    { label: 'Customer Report', last: true },
]

function formatNumber(n) {
    if (!n) { return '0' }
    return Number(n).toLocaleString('en-IN')
}

const monthlyChartRef = ref(null)
let monthlyChart = null

onMounted(() => {
    if (monthlyChartRef.value) {
        monthlyChart = new Chart(monthlyChartRef.value, {
            type: 'line',
            data: {
                labels: props.monthlyNew.labels,
                datasets: [{
                    label: 'New Customers',
                    data: props.monthlyNew.data,
                    borderColor: '#8B5CF6',
                    backgroundColor: 'rgba(139,92,246,0.08)',
                    borderWidth: 2.5,
                    pointBackgroundColor: '#8B5CF6',
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
                        ticks: { stepSize: 1, font: { size: 11 } },
                        grid: { color: 'rgba(0,0,0,0.05)' },
                    },
                    x: { ticks: { font: { size: 11 }, maxRotation: 45 }, grid: { display: false } },
                },
            },
        })
    }
})

onUnmounted(() => {
    monthlyChart?.destroy()
})
</script>
