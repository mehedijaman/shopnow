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

        <!-- Left Column: Customer + Items -->
        <div class="space-y-6 lg:col-span-2">

            <!-- Customer Details -->
            <div class="rounded-lg bg-skin-neutral-1 p-6 shadow-xs ring-1 ring-skin-neutral-4">
                <h3 class="mb-4 flex items-center gap-2 text-sm font-semibold uppercase tracking-wider text-skin-neutral-9">
                    <i class="ri-user-line"></i> Customer Details
                </h3>
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                    <div>
                        <p class="text-xs text-skin-neutral-9">Name</p>
                        <p class="font-medium text-skin-neutral-12">{{ order.name }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-skin-neutral-9">Phone</p>
                        <p class="font-medium text-skin-neutral-12">{{ order.phone }}</p>
                    </div>
                    <div v-if="order.email">
                        <p class="text-xs text-skin-neutral-9">Email</p>
                        <p class="font-medium text-skin-neutral-12">{{ order.email }}</p>
                    </div>
                    <div v-if="order.address">
                        <p class="text-xs text-skin-neutral-9">Address</p>
                        <p class="font-medium text-skin-neutral-12">{{ order.address }}</p>
                    </div>
                    <div v-if="order.country">
                        <p class="text-xs text-skin-neutral-9">Country</p>
                        <p class="font-medium text-skin-neutral-12">{{ order.country }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-skin-neutral-9">Order Date</p>
                        <p class="font-medium text-skin-neutral-12">{{ order.created_at }}</p>
                    </div>
                </div>
            </div>

            <!-- Ordered Items -->
            <div class="rounded-lg bg-skin-neutral-1 p-6 shadow-xs ring-1 ring-skin-neutral-4">
                <h3 class="mb-4 flex items-center gap-2 text-sm font-semibold uppercase tracking-wider text-skin-neutral-9">
                    <i class="ri-shopping-bag-line"></i> Ordered Items
                </h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-skin-neutral-4 text-left text-xs uppercase text-skin-neutral-9">
                                <th class="pb-3 pr-4">Product</th>
                                <th class="pb-3 pr-4 text-center">Qty</th>
                                <th class="pb-3 pr-4 text-right">Unit Price</th>
                                <th class="pb-3 pr-4 text-right">Discount</th>
                                <th class="pb-3 text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-skin-neutral-3">
                            <tr v-for="item in order.orderProducts" :key="item.id">
                                <td class="py-3 pr-4 font-medium text-skin-neutral-12">{{ item.product_name }}</td>
                                <td class="py-3 pr-4 text-center text-skin-neutral-10">{{ item.quantity }}</td>
                                <td class="py-3 pr-4 text-right text-skin-neutral-10">{{ Number(item.unit_price).toFixed(2) }} Tk</td>
                                <td class="py-3 pr-4 text-right text-skin-neutral-10">{{ Number(item.discount).toFixed(2) }} Tk</td>
                                <td class="py-3 text-right font-medium text-skin-neutral-12">{{ Number(item.total_price).toFixed(2) }} Tk</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Totals -->
                <div class="mt-4 border-t border-skin-neutral-4 pt-4">
                    <div class="ml-auto max-w-xs space-y-1.5 text-sm">
                        <div class="flex justify-between">
                            <span class="text-skin-neutral-9">Subtotal</span>
                            <span class="text-skin-neutral-12">{{ Number(order.subtotal).toFixed(2) }} Tk</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-skin-neutral-9">Shipping</span>
                            <span class="text-skin-neutral-12">
                                <span v-if="order.shipping == 0" class="text-green-600">Free</span>
                                <span v-else>{{ Number(order.shipping).toFixed(2) }} Tk</span>
                            </span>
                        </div>
                        <div v-if="order.tax > 0" class="flex justify-between">
                            <span class="text-skin-neutral-9">Tax</span>
                            <span class="text-skin-neutral-12">{{ Number(order.tax).toFixed(2) }} Tk</span>
                        </div>
                        <div class="flex justify-between border-t border-skin-neutral-4 pt-2 font-semibold">
                            <span class="text-skin-neutral-12">Total</span>
                            <span class="text-skin-neutral-12">{{ Number(order.total).toFixed(2) }} Tk</span>
                        </div>
                        <div class="flex justify-between text-xs">
                            <span class="text-skin-neutral-9">Paid</span>
                            <span class="text-green-600">{{ Number(order.paid).toFixed(2) }} Tk</span>
                        </div>
                        <div class="flex justify-between text-xs">
                            <span class="text-skin-neutral-9">Due</span>
                            <span :class="order.due > 0 ? 'text-red-500' : 'text-green-600'">{{ Number(order.due).toFixed(2) }} Tk</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Notes -->
            <div v-if="order.notes" class="rounded-lg bg-skin-neutral-1 p-6 shadow-xs ring-1 ring-skin-neutral-4">
                <h3 class="mb-2 text-sm font-semibold uppercase tracking-wider text-skin-neutral-9">
                    <i class="ri-sticky-note-line mr-1"></i> Notes
                </h3>
                <p class="text-sm text-skin-neutral-11">{{ order.notes }}</p>
            </div>

        </div>

        <!-- Right Column: Order Summary + Status -->
        <div class="space-y-6">

            <!-- Order Summary Card -->
            <div class="rounded-lg bg-skin-neutral-1 p-6 shadow-xs ring-1 ring-skin-neutral-4">
                <h3 class="mb-4 text-sm font-semibold uppercase tracking-wider text-skin-neutral-9">Order Summary</h3>

                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-skin-neutral-9">Order #</span>
                        <span class="font-mono font-semibold text-skin-neutral-12">{{ order.id }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-skin-neutral-9">Payment</span>
                        <span class="text-sm text-skin-neutral-12">
                            {{ order.payment_method === 'cod' ? 'Cash on Delivery' : (order.payment_method ?? '—') }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-skin-neutral-9">Payment Status</span>
                        <span :class="paymentStatusClass(order.payment_status)" class="rounded-full px-2.5 py-0.5 text-xs font-medium">
                            {{ order.payment_status }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-skin-neutral-9">Order Status</span>
                        <span :class="statusClass(order.status)" class="rounded-full px-2.5 py-0.5 text-xs font-medium capitalize">
                            {{ order.status }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Update Status -->
            <div class="rounded-lg bg-skin-neutral-1 p-6 shadow-xs ring-1 ring-skin-neutral-4">
                <h3 class="mb-4 text-sm font-semibold uppercase tracking-wider text-skin-neutral-9">Update Status</h3>

                <form @submit.prevent="submitStatus">
                    <div class="space-y-4">
                        <div>
                            <label class="mb-1 block text-xs font-medium text-skin-neutral-10">Order Status</label>
                            <select
                                v-model="statusForm.status"
                                class="block w-full rounded-md border border-skin-neutral-6 bg-skin-neutral-2 px-3 py-2 text-sm text-skin-neutral-12 focus:border-skin-primary-7 focus:outline-hidden focus:ring-1 focus:ring-skin-primary-7"
                            >
                                <option v-for="s in statuses" :key="s" :value="s" class="capitalize">
                                    {{ s.charAt(0).toUpperCase() + s.slice(1) }}
                                </option>
                            </select>
                        </div>

                        <div>
                            <label class="mb-1 block text-xs font-medium text-skin-neutral-10">Payment Status</label>
                            <select
                                v-model="statusForm.payment_status"
                                class="block w-full rounded-md border border-skin-neutral-6 bg-skin-neutral-2 px-3 py-2 text-sm text-skin-neutral-12 focus:border-skin-primary-7 focus:outline-hidden focus:ring-1 focus:ring-skin-primary-7"
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
                    </div>
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
