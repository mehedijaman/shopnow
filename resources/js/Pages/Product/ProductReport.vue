<template>
    <Head :title="title"></Head>
    <AppSectionHeader :title="title" :bread-crumb="breadCrumb"></AppSectionHeader>

    <!-- Summary Cards -->
    <div class="mt-6 grid grid-cols-2 gap-4 md:grid-cols-4">
        <div class="flex flex-col rounded-xl border border-skin-neutral-4 bg-skin-neutral-2 p-4 shadow-sm">
            <div class="mb-2 flex h-9 w-9 items-center justify-center rounded-full bg-blue-100"><i class="ri-stack-line text-blue-600"></i></div>
            <p class="text-xs text-gray-500">Total Products</p>
            <p class="mt-1 text-xl font-bold text-gray-800">{{ props.summary?.totalProducts }}</p>
        </div>
        <div class="flex flex-col rounded-xl border border-skin-neutral-4 bg-skin-neutral-2 p-4 shadow-sm">
            <div class="mb-2 flex h-9 w-9 items-center justify-center rounded-full bg-green-100"><i class="ri-checkbox-circle-line text-green-600"></i></div>
            <p class="text-xs text-gray-500">Active</p>
            <p class="mt-1 text-xl font-bold text-gray-800">{{ props.summary?.activeProducts }}</p>
        </div>
        <div class="flex flex-col rounded-xl border border-skin-neutral-4 bg-skin-neutral-2 p-4 shadow-sm">
            <div class="mb-2 flex h-9 w-9 items-center justify-center rounded-full bg-red-100"><i class="ri-close-circle-line text-red-600"></i></div>
            <p class="text-xs text-gray-500">Inactive</p>
            <p class="mt-1 text-xl font-bold text-gray-800">{{ props.summary?.inactiveProducts }}</p>
        </div>
        <div class="flex flex-col rounded-xl border border-skin-neutral-4 bg-skin-neutral-2 p-4 shadow-sm">
            <div class="mb-2 flex h-9 w-9 items-center justify-center rounded-full bg-yellow-100"><i class="ri-star-line text-yellow-600"></i></div>
            <p class="text-xs text-gray-500">Featured</p>
            <p class="mt-1 text-xl font-bold text-gray-800">{{ props.summary?.featuredProducts }}</p>
        </div>
    </div>

    <!-- Charts + Top Sellers -->
    <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="rounded-xl border border-skin-neutral-4 bg-skin-neutral-2 p-5 shadow-sm">
            <h3 class="mb-4 font-semibold text-gray-700">Products by Category</h3>
            <canvas ref="categoryChartRef" height="250"></canvas>
        </div>

        <div class="col-span-2 rounded-xl border border-skin-neutral-4 bg-skin-neutral-2 shadow-sm">
            <div class="border-b border-skin-neutral-4 px-5 py-4">
                <h3 class="font-semibold text-gray-700">Top Selling Products</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-skin-neutral-4 text-xs text-gray-500">
                            <th class="px-5 py-3 text-left font-medium">#</th>
                            <th class="px-5 py-3 text-left font-medium">Product</th>
                            <th class="px-5 py-3 text-left font-medium">Category</th>
                            <th class="px-5 py-3 text-right font-medium">Qty Sold</th>
                            <th class="px-5 py-3 text-right font-medium">Revenue</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(p, i) in props.topSelling" :key="i" class="border-b border-skin-neutral-4">
                            <td class="px-5 py-3 text-gray-400">{{ i + 1 }}</td>
                            <td class="px-5 py-3 font-medium text-gray-800">{{ p.product_name }}</td>
                            <td class="px-5 py-3 text-gray-600">{{ p.category }}</td>
                            <td class="px-5 py-3 text-right text-gray-700">{{ p.sold_qty }}</td>
                            <td class="px-5 py-3 text-right font-semibold text-gray-800">৳{{ formatNumber(p.revenue) }}</td>
                        </tr>
                        <tr v-if="!props.topSelling?.length"><td colspan="5" class="px-5 py-6 text-center text-gray-400">No sales data yet.</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Low Stock -->
    <div class="mt-6 rounded-xl border border-skin-neutral-4 bg-skin-neutral-2 shadow-sm">
        <div class="border-b border-skin-neutral-4 px-5 py-4">
            <h3 class="font-semibold text-gray-700">
                Low Stock Alert
                <span class="ml-2 rounded-full bg-red-100 px-2 py-0.5 text-xs text-red-600">qty &lt; 10</span>
            </h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-skin-neutral-4 text-xs text-gray-500">
                        <th class="px-5 py-3 text-left font-medium">Product</th>
                        <th class="px-5 py-3 text-left font-medium">Category</th>
                        <th class="px-5 py-3 text-right font-medium">Stock</th>
                        <th class="px-5 py-3 text-left font-medium">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="p in props.lowStock" :key="p.id" class="border-b border-skin-neutral-4">
                        <td class="px-5 py-3 font-medium text-gray-800">{{ p.name }}</td>
                        <td class="px-5 py-3 text-gray-600">{{ p.category }}</td>
                        <td class="px-5 py-3 text-right">
                            <span class="rounded-full px-2 py-0.5 text-xs font-bold" :class="p.quantity < 5 ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700'">{{ p.quantity }}</span>
                        </td>
                        <td class="px-5 py-3">
                            <AppButton class="btn btn-icon btn-primary" @click="$inertia.visit(route('product.edit', p.id))">
                                <i class="ri-edit-line"></i>
                            </AppButton>
                        </td>
                    </tr>
                    <tr v-if="!props.lowStock?.length"><td colspan="4" class="px-5 py-6 text-center text-gray-400">All products are well-stocked.</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import { Head } from '@inertiajs/vue3'
import { Chart, registerables } from 'chart.js'
import useTitle from '@/Composables/useTitle'
import useAuthCan from '@/Composables/useAuthCan'

Chart.register(...registerables)

const { title } = useTitle('Product Report')
const { can } = useAuthCan()

const props = defineProps({
    summary: { type: Object, default: () => ({}) },
    topSelling: { type: Array, default: () => [] },
    lowStock: { type: Array, default: () => [] },
    byCategory: { type: Array, default: () => [] },
})

const breadCrumb = [
    { label: 'Home', href: route('dashboard.index') },
    { label: 'Products', href: route('product.index') },
    { label: 'Product Report', last: true },
]

function formatNumber(n) {
    if (!n) { return '0' }
    return Number(n).toLocaleString('en-IN')
}

const categoryChartRef = ref(null)
let categoryChart = null

onMounted(() => {
    if (categoryChartRef.value && props.byCategory?.length) {
        const colors = [
            '#3B82F6', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6',
            '#EC4899', '#06B6D4', '#84CC16', '#F97316', '#6366F1',
        ]
        categoryChart = new Chart(categoryChartRef.value, {
            type: 'bar',
            data: {
                labels: props.byCategory.map((c) => c.name),
                datasets: [{
                    label: 'Products',
                    data: props.byCategory.map((c) => c.count),
                    backgroundColor: props.byCategory.map((_, i) => colors[i % colors.length] + 'cc'),
                    borderRadius: 4,
                }],
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                plugins: { legend: { display: false } },
                scales: {
                    x: { beginAtZero: true, ticks: { font: { size: 11 } } },
                    y: { ticks: { font: { size: 11 } } },
                },
            },
        })
    }
})

onUnmounted(() => {
    categoryChart?.destroy()
})
</script>
