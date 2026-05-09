<template>
    <Head :title="title"></Head>
    <AppSectionHeader :title="title" :bread-crumb="breadCrumb">
        <template #right>
            <AppButton
                class="btn btn-secondary"
                @click="$inertia.visit(route('order.index'))"
            >
                <i class="ri-arrow-left-line mr-1"></i>
                Back to Orders
            </AppButton>
        </template>
    </AppSectionHeader>

    <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-3">

        <!-- ── Left Column ── -->
        <div class="space-y-6 lg:col-span-2">

            <!-- Customer Details -->
            <div class="overflow-hidden rounded-xl bg-skin-neutral-1 shadow-xs ring-1 ring-skin-neutral-4">
                <div class="flex items-center gap-2 border-b border-skin-neutral-4 bg-skin-neutral-2 px-6 py-3">
                    <i class="ri-user-3-line text-skin-neutral-9"></i>
                    <h3 class="text-sm font-semibold uppercase tracking-wider text-skin-neutral-9">Customer Details</h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                        <div>
                            <p class="mb-0.5 text-xs font-medium uppercase tracking-wider text-skin-neutral-8">Name</p>
                            <p class="font-semibold text-skin-neutral-12">{{ order.name }}</p>
                        </div>
                        <div>
                            <p class="mb-0.5 text-xs font-medium uppercase tracking-wider text-skin-neutral-8">Phone</p>
                            <p class="font-semibold text-skin-neutral-12">{{ order.phone }}</p>
                        </div>
                        <div v-if="order.email">
                            <p class="mb-0.5 text-xs font-medium uppercase tracking-wider text-skin-neutral-8">Email</p>
                            <p class="font-semibold text-skin-neutral-12">{{ order.email }}</p>
                        </div>
                        <div v-if="order.district">
                            <p class="mb-0.5 text-xs font-medium uppercase tracking-wider text-skin-neutral-8">District</p>
                            <p class="font-semibold text-skin-neutral-12">{{ order.district }}</p>
                        </div>
                        <div v-if="order.upazila">
                            <p class="mb-0.5 text-xs font-medium uppercase tracking-wider text-skin-neutral-8">Upazila</p>
                            <p class="font-semibold text-skin-neutral-12">{{ order.upazila }}</p>
                        </div>
                        <div v-if="order.division">
                            <p class="mb-0.5 text-xs font-medium uppercase tracking-wider text-skin-neutral-8">Division</p>
                            <p class="font-semibold text-skin-neutral-12">{{ order.division }}</p>
                        </div>
                        <div v-if="order.union">
                            <p class="mb-0.5 text-xs font-medium uppercase tracking-wider text-skin-neutral-8">Union / Area</p>
                            <p class="font-semibold text-skin-neutral-12">{{ order.union }}</p>
                        </div>
                        <div v-if="order.address" class="sm:col-span-2">
                            <p class="mb-0.5 text-xs font-medium uppercase tracking-wider text-skin-neutral-8">Address</p>
                            <p class="font-semibold text-skin-neutral-12">{{ order.address }}</p>
                        </div>
                        <div>
                            <p class="mb-0.5 text-xs font-medium uppercase tracking-wider text-skin-neutral-8">Order Date</p>
                            <p class="font-semibold text-skin-neutral-12">{{ order.created_at }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Ordered Items -->
            <div class="overflow-hidden rounded-xl bg-skin-neutral-1 shadow-xs ring-1 ring-skin-neutral-4">
                <div class="flex items-center gap-2 border-b border-skin-neutral-4 bg-skin-neutral-2 px-6 py-3">
                    <i class="ri-shopping-bag-3-line text-skin-neutral-9"></i>
                    <h3 class="text-sm font-semibold uppercase tracking-wider text-skin-neutral-9">Ordered Items</h3>
                    <span class="ml-auto rounded-full bg-skin-neutral-4 px-2 py-0.5 text-xs font-semibold text-skin-neutral-10">
                        {{ order.orderProducts.length }} item{{ order.orderProducts.length !== 1 ? 's' : '' }}
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-skin-neutral-4 bg-skin-neutral-2 text-left text-xs font-semibold uppercase tracking-wider text-skin-neutral-8">
                                <th class="px-6 py-3">Product</th>
                                <th class="px-4 py-3 text-center">Qty</th>
                                <th class="px-4 py-3 text-right">Unit Price</th>
                                <th class="px-4 py-3 text-right">Discount</th>
                                <th class="px-6 py-3 text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-skin-neutral-3">
                            <tr v-for="item in order.orderProducts" :key="item.id" class="transition-colors hover:bg-skin-neutral-2">
                                <td class="px-6 py-4 font-semibold text-skin-neutral-12">{{ item.product_name }}</td>
                                <td class="px-4 py-4 text-center">
                                    <span class="inline-flex h-7 w-7 items-center justify-center rounded-full bg-skin-neutral-3 text-xs font-bold text-skin-neutral-11">
                                        {{ item.quantity }}
                                    </span>
                                </td>
                                <td class="px-4 py-4 text-right text-skin-neutral-10">{{ Number(item.unit_price).toFixed(2) }} Tk</td>
                                <td class="px-4 py-4 text-right">
                                    <span v-if="Number(item.discount) > 0" class="text-green-600">-{{ Number(item.discount).toFixed(2) }} Tk</span>
                                    <span v-else class="text-skin-neutral-8">—</span>
                                </td>
                                <td class="px-6 py-4 text-right font-bold text-skin-neutral-12">{{ Number(item.total_price).toFixed(2) }} Tk</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Totals -->
                <div class="border-t border-skin-neutral-4 bg-skin-neutral-2 px-6 py-4">
                    <div class="ml-auto max-w-xs space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-skin-neutral-9">Subtotal</span>
                            <span class="font-medium text-skin-neutral-12">{{ Number(order.subtotal).toFixed(2) }} Tk</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-skin-neutral-9">Shipping</span>
                            <span v-if="order.shipping == 0" class="font-medium text-green-600">Free</span>
                            <span v-else class="font-medium text-skin-neutral-12">{{ Number(order.shipping).toFixed(2) }} Tk</span>
                        </div>
                        <div v-if="order.tax > 0" class="flex justify-between">
                            <span class="text-skin-neutral-9">Tax</span>
                            <span class="font-medium text-skin-neutral-12">{{ Number(order.tax).toFixed(2) }} Tk</span>
                        </div>
                        <div class="flex justify-between border-t border-skin-neutral-4 pt-2 text-base font-bold">
                            <span class="text-skin-neutral-12">Total</span>
                            <span class="text-skin-neutral-12">{{ Number(order.total).toFixed(2) }} Tk</span>
                        </div>
                        <div class="flex justify-between text-xs">
                            <span class="text-skin-neutral-9">Paid</span>
                            <span class="font-semibold text-green-600">{{ Number(order.paid).toFixed(2) }} Tk</span>
                        </div>
                        <div class="flex justify-between text-xs">
                            <span class="text-skin-neutral-9">Due</span>
                            <span class="font-semibold" :class="order.due > 0 ? 'text-red-500' : 'text-green-600'">{{ Number(order.due).toFixed(2) }} Tk</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Notes -->
            <div v-if="order.notes" class="overflow-hidden rounded-xl bg-skin-neutral-1 shadow-xs ring-1 ring-skin-neutral-4">
                <div class="flex items-center gap-2 border-b border-skin-neutral-4 bg-skin-neutral-2 px-6 py-3">
                    <i class="ri-sticky-note-line text-skin-neutral-9"></i>
                    <h3 class="text-sm font-semibold uppercase tracking-wider text-skin-neutral-9">Notes</h3>
                </div>
                <p class="px-6 py-4 text-sm leading-relaxed text-skin-neutral-11">{{ order.notes }}</p>
            </div>

        </div>

        <!-- ── Right Column ── -->
        <div class="space-y-6">

            <!-- Order Info Card -->
            <div class="overflow-hidden rounded-xl bg-skin-neutral-1 shadow-xs ring-1 ring-skin-neutral-4">
                <div class="border-b border-skin-neutral-4 bg-skin-neutral-2 px-6 py-3">
                    <h3 class="text-sm font-semibold uppercase tracking-wider text-skin-neutral-9">Order Info</h3>
                </div>
                <div class="divide-y divide-skin-neutral-3 px-6">
                    <div class="flex items-center justify-between py-3">
                        <span class="text-xs text-skin-neutral-9">Order #</span>
                        <span class="font-mono text-sm font-bold text-skin-neutral-12">#{{ order.id }}</span>
                    </div>
                    <div class="flex items-center justify-between py-3">
                        <span class="text-xs text-skin-neutral-9">Payment Method</span>
                        <span class="text-sm font-medium text-skin-neutral-12">
                            {{ order.payment_method === 'cod' ? 'Cash on Delivery' : (order.payment_method ?? '—') }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between py-3">
                        <span class="text-xs text-skin-neutral-9">Payment Status</span>
                        <span :class="paymentStatusClass(order.payment_status)" class="rounded-full px-2.5 py-0.5 text-xs font-semibold capitalize">
                            {{ order.payment_status }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between py-3">
                        <span class="text-xs text-skin-neutral-9">Order Status</span>
                        <span :class="statusClass(order.status)" class="rounded-full px-2.5 py-0.5 text-xs font-semibold capitalize">
                            {{ order.status }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between py-3">
                        <span class="text-xs text-skin-neutral-9">Order Total</span>
                        <span class="text-base font-extrabold text-skin-primary-7">{{ Number(order.total).toFixed(2) }} Tk</span>
                    </div>
                </div>
            </div>

            <!-- Update Status -->
            <div class="overflow-hidden rounded-xl bg-skin-neutral-1 shadow-xs ring-1 ring-skin-neutral-4">
                <div class="border-b border-skin-neutral-4 bg-skin-neutral-2 px-6 py-3">
                    <h3 class="text-sm font-semibold uppercase tracking-wider text-skin-neutral-9">Update Status</h3>
                </div>
                <form @submit.prevent="submitStatus" class="space-y-4 p-6">
                    <div>
                        <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-skin-neutral-9">Order Status</label>
                        <select
                            v-model="statusForm.status"
                            class="block w-full rounded-lg border border-skin-neutral-6 bg-skin-neutral-2 px-3 py-2 text-sm text-skin-neutral-12 focus:border-skin-primary-7 focus:outline-hidden focus:ring-1 focus:ring-skin-primary-7"
                        >
                            <option v-for="s in statuses" :key="s" :value="s" class="capitalize">
                                {{ s.charAt(0).toUpperCase() + s.slice(1) }}
                            </option>
                        </select>
                    </div>

                    <div>
                        <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-skin-neutral-9">Payment Status</label>
                        <select
                            v-model="statusForm.payment_status"
                            class="block w-full rounded-lg border border-skin-neutral-6 bg-skin-neutral-2 px-3 py-2 text-sm text-skin-neutral-12 focus:border-skin-primary-7 focus:outline-hidden focus:ring-1 focus:ring-skin-primary-7"
                        >
                            <option value="unpaid">Unpaid</option>
                            <option value="paid">Paid</option>
                        </select>
                    </div>

                    <AppButton
                        type="submit"
                        class="btn btn-primary w-full"
                        :loading="statusForm.processing"
                    >
                        <i class="ri-save-line mr-1"></i>
                        Update Status
                    </AppButton>
                </form>
            </div>

        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue'
import { Head, useForm } from '@inertiajs/vue3'
import useTitle from '@/Composables/useTitle'

const { title } = useTitle('Order Details')

const props = defineProps({
    order: {
        type: Object,
        default: () => {},
    },
    statuses: {
        type: Array,
        default: () => [],
    },
})

const breadCrumb = [
    { label: 'Home', href: route('dashboard.index') },
    { label: 'Orders', href: route('order.index') },
    { label: `#${props.order.id}`, last: true },
]

const statusForm = useForm({
    status: props.order.status,
    payment_status: props.order.payment_status,
})

const submitStatus = () => {
    statusForm.patch(route('order.updateStatus', props.order.id), {
        preserveScroll: true,
    })
}

const statusClass = (status) => {
    const classes = {
        pending: 'bg-yellow-100 text-yellow-800',
        processing: 'bg-blue-100 text-blue-800',
        shipped: 'bg-purple-100 text-purple-800',
        delivered: 'bg-indigo-100 text-indigo-800',
        completed: 'bg-green-100 text-green-800',
        cancelled: 'bg-red-100 text-red-800',
    }
    return classes[status] ?? 'bg-gray-100 text-gray-800'
}

const paymentStatusClass = (status) => {
    return status === 'paid' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'
}
</script>
