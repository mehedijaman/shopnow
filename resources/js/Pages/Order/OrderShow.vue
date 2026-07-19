<template>
    <Head :title="title"></Head>
    <AppSectionHeader :title="title" :bread-crumb="breadCrumb">
        <template #right>
            <div class="flex flex-wrap items-center gap-3">
                <a
                    :href="route('order.downloadInvoice', order.id)"
                    class="btn btn-primary inline-flex items-center gap-2 rounded-xl px-4 py-2 text-sm font-semibold transition duration-150 ease-in-out"
                >
                    <i class="ri-download-cloud-line text-lg"></i>
                    <span>Download Invoice</span>
                </a>
                <AppButton
                    class="btn btn-secondary inline-flex items-center gap-2 rounded-xl px-4 py-2 text-sm font-semibold transition duration-150 ease-in-out"
                    @click="$inertia.visit(route('order.index'))"
                >
                    <i class="ri-arrow-left-line text-lg"></i>
                    <span>Back to Orders</span>
                </AppButton>
            </div>
        </template>
    </AppSectionHeader>

    <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-3">

        <!-- ── Left Column ── -->
        <div class="space-y-6 lg:col-span-2">

            <!-- Customer Details Card -->
            <div class="overflow-hidden rounded-2xl bg-skin-neutral-1 shadow-md ring-1 ring-skin-neutral-4 transition-all duration-200 hover:shadow-lg">
                <div class="flex items-center gap-3 border-b border-skin-neutral-4 bg-skin-neutral-2/50 px-6 py-4">
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-50 text-blue-600">
                        <i class="ri-user-3-line text-lg"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold uppercase tracking-wider text-skin-neutral-12">Customer Details</h3>
                        <p class="text-xs text-skin-neutral-8">Billing and shipping recipient information</p>
                    </div>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                        <div class="flex items-start gap-2.5">
                            <i class="ri-user-line mt-0.5 text-skin-neutral-7"></i>
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wider text-skin-neutral-8">Name</p>
                                <p class="font-bold text-skin-neutral-12 mt-0.5">{{ order.name }}</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-2.5">
                            <i class="ri-phone-line mt-0.5 text-skin-neutral-7"></i>
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wider text-skin-neutral-8">Phone</p>
                                <p class="font-bold text-skin-neutral-12 mt-0.5">{{ order.phone }}</p>
                            </div>
                        </div>
                        <div v-if="order.email" class="flex items-start gap-2.5">
                            <i class="ri-mail-line mt-0.5 text-skin-neutral-7"></i>
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wider text-skin-neutral-8">Email</p>
                                <p class="font-bold text-skin-neutral-12 mt-0.5">{{ order.email }}</p>
                            </div>
                        </div>
                        <div v-if="order.district" class="flex items-start gap-2.5">
                            <i class="ri-map-2-line mt-0.5 text-skin-neutral-7"></i>
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wider text-skin-neutral-8">District</p>
                                <p class="font-bold text-skin-neutral-12 mt-0.5">{{ order.district }}</p>
                            </div>
                        </div>
                        <div v-if="order.upazila" class="flex items-start gap-2.5">
                            <i class="ri-map-pin-2-line mt-0.5 text-skin-neutral-7"></i>
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wider text-skin-neutral-8">Upazila</p>
                                <p class="font-bold text-skin-neutral-12 mt-0.5">{{ order.upazila }}</p>
                            </div>
                        </div>
                        <div v-if="order.division" class="flex items-start gap-2.5">
                            <i class="ri-government-line mt-0.5 text-skin-neutral-7"></i>
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wider text-skin-neutral-8">Division</p>
                                <p class="font-bold text-skin-neutral-12 mt-0.5">{{ order.division }}</p>
                            </div>
                        </div>
                        <div v-if="order.union" class="flex items-start gap-2.5">
                            <i class="ri-community-line mt-0.5 text-skin-neutral-7"></i>
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wider text-skin-neutral-8">Union / Area</p>
                                <p class="font-bold text-skin-neutral-12 mt-0.5">{{ order.union }}</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-2.5">
                            <i class="ri-calendar-event-line mt-0.5 text-skin-neutral-7"></i>
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wider text-skin-neutral-8">Order Date</p>
                                <p class="font-bold text-skin-neutral-12 mt-0.5">{{ order.created_at }}</p>
                            </div>
                        </div>
                        <div v-if="order.address" class="sm:col-span-2 flex items-start gap-2.5">
                            <i class="ri-map-pin-line mt-0.5 text-skin-neutral-7"></i>
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wider text-skin-neutral-8">Address</p>
                                <p class="font-bold text-skin-neutral-12 mt-0.5 leading-relaxed">{{ order.address }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Ordered Items Card -->
            <div class="overflow-hidden rounded-2xl bg-skin-neutral-1 shadow-md ring-1 ring-skin-neutral-4 transition-all duration-200 hover:shadow-lg">
                <div class="flex items-center gap-3 border-b border-skin-neutral-4 bg-skin-neutral-2/50 px-6 py-4">
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-primary-50 text-primary-600">
                        <i class="ri-shopping-bag-3-line text-lg"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold uppercase tracking-wider text-skin-neutral-12">Ordered Items</h3>
                        <p class="text-xs text-skin-neutral-8">Items details and pricing calculation</p>
                    </div>
                    <span class="ml-auto rounded-full bg-skin-neutral-4 px-3 py-1 text-xs font-bold text-skin-neutral-10 shadow-xs">
                        {{ order.orderProducts.length }} Item{{ order.orderProducts.length !== 1 ? 's' : '' }}
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-skin-neutral-4 bg-skin-neutral-2 text-left text-xs font-bold uppercase tracking-wider text-skin-neutral-8">
                                <th class="px-6 py-3.5">Product</th>
                                <th class="px-4 py-3.5 text-center">Qty</th>
                                <th class="px-4 py-3.5 text-right">Unit Price</th>
                                <th class="px-4 py-3.5 text-right">Discount</th>
                                <th class="px-6 py-3.5 text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-skin-neutral-3">
                            <template v-for="item in order.orderProducts" :key="item.id">
                                <tr class="transition-colors hover:bg-skin-neutral-2/30">
                                    <td class="px-6 py-4">
                                        <div>
                                            <p class="font-bold text-skin-neutral-12 text-sm">{{ item.product_name }}</p>
                                            <p v-if="item.variation_label" class="mt-1 inline-flex items-center rounded-md bg-blue-50 px-2 py-0.5 text-xs font-semibold text-blue-700 ring-1 ring-inset ring-blue-700/10">
                                                {{ item.variation_label }}
                                            </p>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4 text-center">
                                        <span class="inline-flex h-8 w-8 items-center justify-center rounded-xl bg-skin-neutral-3 text-xs font-extrabold text-skin-neutral-11 shadow-xs ring-1 ring-skin-neutral-4">
                                            {{ item.quantity }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-4 text-right font-medium text-skin-neutral-10">{{ Number(item.unit_price).toFixed(2) }} Tk</td>
                                    <td class="px-4 py-4 text-right">
                                        <span v-if="Number(item.discount) > 0" class="inline-flex items-center rounded-md bg-green-50 px-2 py-1 text-xs font-bold text-green-700 ring-1 ring-inset ring-green-600/10">
                                            -{{ Number(item.discount).toFixed(2) }} Tk
                                        </span>
                                        <span v-else class="text-skin-neutral-6">—</span>
                                    </td>
                                    <td class="px-6 py-4 text-right font-bold text-skin-neutral-12 text-base">{{ Number(item.total_price).toFixed(2) }} Tk</td>
                                </tr>
                                <!-- Bundle child items snapshot -->
                                <tr v-if="item.bundle_items?.length" v-for="bi in item.bundle_items" :key="bi.id" class="bg-skin-neutral-2/20">
                                    <td class="px-6 py-2.5 pl-12">
                                        <div class="flex items-center gap-2 text-xs text-skin-neutral-9">
                                            <i class="ri-corner-down-right-line text-skin-neutral-6"></i>
                                            <span class="font-semibold text-skin-neutral-11">{{ bi.name }}</span>
                                            <span v-if="bi.sku" class="rounded-sm bg-skin-neutral-3 px-1.5 py-0.5 text-[10px] font-mono text-skin-neutral-8">({{ bi.sku }})</span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-2.5 text-center text-xs font-semibold text-skin-neutral-8">{{ bi.quantity }}</td>
                                    <td class="px-4 py-2.5 text-right text-xs font-semibold text-skin-neutral-8">{{ Number(bi.unit_price).toFixed(2) }} Tk</td>
                                    <td class="px-4 py-2.5 text-right text-xs text-skin-neutral-6">—</td>
                                    <td class="px-6 py-2.5 text-right text-xs font-bold text-skin-neutral-9">{{ Number(bi.total_price).toFixed(2) }} Tk</td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                <!-- Totals -->
                <div class="border-t border-skin-neutral-4 bg-skin-neutral-2/40 px-6 py-5">
                    <div class="ml-auto max-w-sm space-y-3 text-sm">
                        <div class="flex justify-between">
                            <span class="font-semibold text-skin-neutral-9">Subtotal</span>
                            <span class="font-bold text-skin-neutral-12">{{ Number(order.subtotal).toFixed(2) }} Tk</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="font-semibold text-skin-neutral-9">Shipping</span>
                            <span v-if="order.shipping == 0" class="inline-flex items-center rounded-md bg-green-50 px-2 py-0.5 text-xs font-bold text-green-700 ring-1 ring-inset ring-green-600/20">Free</span>
                            <span v-else class="font-bold text-skin-neutral-12">{{ Number(order.shipping).toFixed(2) }} Tk</span>
                        </div>
                        <div v-if="order.tax > 0" class="flex justify-between">
                            <span class="font-semibold text-skin-neutral-9">Tax</span>
                            <span class="font-bold text-skin-neutral-12">{{ Number(order.tax).toFixed(2) }} Tk</span>
                        </div>
                        <div class="flex justify-between border-t border-skin-neutral-4 pt-3 text-base font-extrabold">
                            <span class="text-skin-neutral-12">Total</span>
                            <span class="text-skin-primary-7 text-lg">{{ Number(order.total).toFixed(2) }} Tk</span>
                        </div>
                        <div class="flex justify-between border-t border-skin-neutral-3 pt-2 text-xs font-bold">
                            <span class="text-skin-neutral-8">Paid</span>
                            <span class="text-green-600 font-extrabold text-sm">{{ Number(order.paid).toFixed(2) }} Tk</span>
                        </div>
                        <div class="flex justify-between text-xs font-bold">
                            <span class="text-skin-neutral-8">Due</span>
                            <span class="font-extrabold text-sm" :class="order.due > 0 ? 'text-red-500' : 'text-green-600'">{{ Number(order.due).toFixed(2) }} Tk</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment History Card -->
            <div v-if="order.orderPayments?.length" class="overflow-hidden rounded-2xl bg-skin-neutral-1 shadow-md ring-1 ring-skin-neutral-4 transition-all duration-200 hover:shadow-lg">
                <div class="flex items-center gap-3 border-b border-skin-neutral-4 bg-skin-neutral-2/50 px-6 py-4">
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-green-50 text-green-600">
                        <i class="ri-bank-card-line text-lg"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold uppercase tracking-wider text-skin-neutral-12">Payment History</h3>
                        <p class="text-xs text-skin-neutral-8">Transactions logged for this order</p>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-skin-neutral-4 bg-skin-neutral-2 text-left text-xs font-bold uppercase tracking-wider text-skin-neutral-8">
                                <th class="px-6 py-3.5">Date</th>
                                <th class="px-4 py-3.5">Method</th>
                                <th class="px-4 py-3.5">Status</th>
                                <th class="px-4 py-3.5 text-right">Amount</th>
                                <th class="px-6 py-3.5">Transaction ID</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-skin-neutral-3">
                            <tr v-for="payment in order.orderPayments" :key="payment.id" class="transition-colors hover:bg-skin-neutral-2/30">
                                <td class="px-6 py-3.5 text-skin-neutral-10 font-semibold">{{ payment.payment_date ?? '—' }}</td>
                                <td class="px-4 py-3.5 text-skin-neutral-12 font-bold">{{ formatPaymentMethod(payment.payment_method) }}</td>
                                <td class="px-4 py-3.5">
                                    <span :class="payment.payment_status === 'success' ? 'bg-green-100 text-green-800 ring-green-600/20' : 'bg-yellow-100 text-yellow-800 ring-yellow-600/20'" class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-bold capitalize ring-1 ring-inset">
                                        {{ payment.payment_status }}
                                    </span>
                                </td>
                                <td class="px-4 py-3.5 text-right font-extrabold text-skin-neutral-12 text-sm">{{ Number(payment.amount_paid).toFixed(2) }} Tk</td>
                                <td class="px-6 py-3.5 font-mono text-xs font-semibold text-skin-neutral-9">{{ payment.transaction_id ?? '—' }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Shipment Tracking Card -->
            <div v-if="order.requires_shipping && order.orderShipments?.length" class="overflow-hidden rounded-2xl bg-skin-neutral-1 shadow-md ring-1 ring-skin-neutral-4 transition-all duration-200 hover:shadow-lg">
                <div class="flex items-center gap-3 border-b border-skin-neutral-4 bg-skin-neutral-2/50 px-6 py-4">
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-purple-50 text-purple-600">
                        <i class="ri-truck-line text-lg"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold uppercase tracking-wider text-skin-neutral-12">Shipment Tracking</h3>
                        <p class="text-xs text-skin-neutral-8">Logistics, carrier, and transit details</p>
                    </div>
                </div>
                <div class="p-6">
                    <div v-for="shipment in order.orderShipments" :key="shipment.id" class="space-y-4">
                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                            <div>
                                <p class="mb-1 text-xs font-semibold uppercase tracking-wider text-skin-neutral-8">Status</p>
                                <span class="inline-flex items-center rounded-full px-3 py-0.5 text-xs font-bold capitalize ring-1 ring-inset"
                                    :class="shipmentStatusClass(shipment.shopment_status)">
                                    {{ shipment.shopment_status }}
                                </span>
                            </div>
                            <div v-if="shipment.carrier">
                                <p class="mb-1 text-xs font-semibold uppercase tracking-wider text-skin-neutral-8">Carrier</p>
                                <p class="font-bold text-skin-neutral-12 text-sm">{{ shipment.carrier }}</p>
                            </div>
                            <div v-if="shipment.tracking_number">
                                <p class="mb-1 text-xs font-semibold uppercase tracking-wider text-skin-neutral-8">Tracking Number</p>
                                <p class="font-mono text-sm font-bold text-skin-neutral-12">
                                    <template v-if="shipment.tracking_url">
                                        <a :href="shipment.tracking_url" target="_blank" rel="noopener noreferrer" class="text-skin-primary-7 hover:underline flex items-center gap-1">
                                            <span>{{ shipment.tracking_number }}</span>
                                            <i class="ri-external-link-line text-xs"></i>
                                        </a>
                                    </template>
                                    <template v-else>
                                        {{ shipment.tracking_number }}
                                    </template>
                                </p>
                            </div>
                            <div v-if="shipment.shipment_date">
                                <p class="mb-1 text-xs font-semibold uppercase tracking-wider text-skin-neutral-8">Shipment Date</p>
                                <p class="font-bold text-skin-neutral-12 text-sm">{{ shipment.shipment_date }}</p>
                            </div>
                            <div v-if="shipment.estimated_delivery">
                                <p class="mb-1 text-xs font-semibold uppercase tracking-wider text-skin-neutral-8">Estimated Delivery</p>
                                <p class="font-bold text-skin-neutral-12 text-sm">{{ shipment.estimated_delivery }}</p>
                            </div>
                            <div v-if="shipment.actual_delivery">
                                <p class="mb-1 text-xs font-semibold uppercase tracking-wider text-skin-neutral-8">Actual Delivery</p>
                                <p class="font-bold text-skin-neutral-12 text-sm text-green-600">{{ shipment.actual_delivery }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Download Permissions Card -->
            <div v-if="order.downloadPermissions?.length" class="overflow-hidden rounded-2xl bg-skin-neutral-1 shadow-md ring-1 ring-skin-neutral-4 transition-all duration-200 hover:shadow-lg">
                <div class="flex items-center gap-3 border-b border-skin-neutral-4 bg-skin-neutral-2/50 px-6 py-4">
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-amber-50 text-amber-600">
                        <i class="ri-download-cloud-line text-lg"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold uppercase tracking-wider text-skin-neutral-12">Download Permissions</h3>
                        <p class="text-xs text-skin-neutral-8">Digital product file download tokens and limits</p>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-skin-neutral-4 bg-skin-neutral-2 text-left text-xs font-bold uppercase tracking-wider text-skin-neutral-8">
                                <th class="px-6 py-3.5">Product</th>
                                <th class="px-4 py-3.5">File</th>
                                <th class="px-4 py-3.5 text-center">Downloads</th>
                                <th class="px-4 py-3.5">Expires</th>
                                <th class="px-4 py-3.5 text-center">Status</th>
                                <th class="px-6 py-3.5 text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-skin-neutral-3">
                            <tr v-for="dp in order.downloadPermissions" :key="dp.id" class="transition-colors hover:bg-skin-neutral-2/30">
                                <td class="px-6 py-3.5 font-bold text-skin-neutral-12 text-sm">{{ dp.product_name ?? '—' }}</td>
                                <td class="px-4 py-3.5 text-skin-neutral-10 font-semibold">{{ dp.product_file_name }}</td>
                                <td class="px-4 py-3.5 text-center text-skin-neutral-12 font-extrabold">
                                    {{ dp.download_count }}{{ dp.download_limit ? ' / ' + dp.download_limit : '' }}
                                </td>
                                <td class="px-4 py-3.5 text-skin-neutral-10 font-semibold">{{ dp.expires_at ?? 'Never' }}</td>
                                <td class="px-4 py-3.5 text-center">
                                    <span :class="dp.active ? 'bg-green-100 text-green-800 ring-green-600/20' : 'bg-red-100 text-red-800 ring-red-600/20'" class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-bold ring-1 ring-inset">
                                        {{ dp.active ? 'Active' : 'Revoked' }}
                                    </span>
                                </td>
                                <td class="px-6 py-3.5 text-center">
                                    <button
                                        type="button"
                                        :class="dp.active ? 'bg-red-50 text-red-600 hover:bg-red-100 hover:text-red-700' : 'bg-green-50 text-green-600 hover:bg-green-100 hover:text-green-700'"
                                        class="inline-flex items-center gap-1 rounded-lg px-2.5 py-1.5 text-xs font-bold transition-colors"
                                        @click="togglePermission(dp.id)"
                                    >
                                        <i :class="dp.active ? 'ri-close-circle-line' : 'ri-checkbox-circle-line'" class="text-sm"></i>
                                        {{ dp.active ? 'Revoke' : 'Activate' }}
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Notes Card -->
            <div v-if="order.notes" class="overflow-hidden rounded-2xl bg-skin-neutral-1 shadow-md ring-1 ring-skin-neutral-4 transition-all duration-200 hover:shadow-lg">
                <div class="flex items-center gap-3 border-b border-skin-neutral-4 bg-skin-neutral-2/50 px-6 py-4">
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-orange-50 text-orange-600">
                        <i class="ri-sticky-note-line text-lg"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold uppercase tracking-wider text-skin-neutral-12">Customer Notes</h3>
                        <p class="text-xs text-skin-neutral-8">Special instructions or requests provided by the customer</p>
                    </div>
                </div>
                <div class="p-6">
                    <p class="rounded-xl bg-orange-50/40 p-4 text-sm font-medium leading-relaxed text-skin-neutral-11 ring-1 ring-orange-100">{{ order.notes }}</p>
                </div>
            </div>

        </div>

        <!-- ── Right Column ── -->
        <div class="space-y-6">

            <!-- Order Info Card -->
            <div class="overflow-hidden rounded-2xl bg-skin-neutral-1 shadow-md ring-1 ring-skin-neutral-4 transition-all duration-200 hover:shadow-lg">
                <div class="border-b border-skin-neutral-4 bg-skin-neutral-2/50 px-6 py-4">
                    <h3 class="text-sm font-bold uppercase tracking-wider text-skin-neutral-12">Order Summary</h3>
                    <p class="text-xs text-skin-neutral-8">Key parameters and transaction state</p>
                </div>
                <div class="divide-y divide-skin-neutral-3 px-6">
                    <div class="flex items-center justify-between py-3.5">
                        <span class="text-xs font-semibold text-skin-neutral-8 uppercase tracking-wider">Order Reference</span>
                        <span class="font-mono text-sm font-bold text-skin-neutral-12">#{{ order.id }}</span>
                    </div>
                    <div class="flex items-center justify-between py-3.5">
                        <span class="text-xs font-semibold text-skin-neutral-8 uppercase tracking-wider">Payment Method</span>
                        <span class="text-sm font-bold text-skin-neutral-12">
                            {{ formatPaymentMethod(order.payment_method) }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between py-3.5">
                        <span class="text-xs font-semibold text-skin-neutral-8 uppercase tracking-wider">Payment Status</span>
                        <span :class="paymentStatusClass(order.payment_status)" class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-bold capitalize ring-1 ring-inset">
                            {{ order.payment_status }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between py-3.5">
                        <span class="text-xs font-semibold text-skin-neutral-8 uppercase tracking-wider">Order Status</span>
                        <span :class="statusClass(order.status)" class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-bold capitalize ring-1 ring-inset">
                            {{ order.status }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between py-3.5">
                        <span class="text-xs font-semibold text-skin-neutral-8 uppercase tracking-wider">Order Type</span>
                        <span :class="order.requires_shipping ? 'text-green-600 bg-green-50 ring-green-600/20' : 'text-blue-600 bg-blue-50 ring-blue-600/20'" class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-bold capitalize ring-1 ring-inset">
                            {{ order.requires_shipping ? 'Physical Order' : 'Virtual / Digital' }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between py-4">
                        <span class="text-xs font-semibold text-skin-neutral-8 uppercase tracking-wider">Grand Total</span>
                        <span class="text-lg font-extrabold text-skin-primary-7">{{ Number(order.total).toFixed(2) }} Tk</span>
                    </div>
                </div>
                <div class="bg-skin-neutral-2/30 px-6 py-4 border-t border-skin-neutral-3">
                    <a
                        :href="route('order.downloadInvoice', order.id)"
                        class="btn btn-secondary flex w-full items-center justify-center gap-2 rounded-xl py-2.5 text-sm font-semibold transition duration-150 ease-in-out"
                    >
                        <i class="ri-printer-line text-base"></i>
                        <span>Print Invoice PDF</span>
                    </a>
                </div>
            </div>

            <!-- Update Status Card -->
            <div class="overflow-hidden rounded-2xl bg-skin-neutral-1 shadow-md ring-1 ring-skin-neutral-4 transition-all duration-200 hover:shadow-lg">
                <div class="border-b border-skin-neutral-4 bg-skin-neutral-2/50 px-6 py-4">
                    <h3 class="text-sm font-bold uppercase tracking-wider text-skin-neutral-12">Update Progress</h3>
                    <p class="text-xs text-skin-neutral-8">Manually override workflow status</p>
                </div>
                <form @submit.prevent="submitStatus" class="space-y-4 p-6">
                    <div>
                        <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-skin-neutral-9">Order Status</label>
                        <select
                            v-model="statusForm.status"
                            class="block w-full rounded-xl border border-skin-neutral-6 bg-skin-neutral-2 px-3.5 py-2.5 text-sm font-medium text-skin-neutral-12 focus:border-skin-primary-7 focus:outline-hidden focus:ring-1 focus:ring-skin-primary-7 transition duration-150 ease-in-out"
                        >
                            <option v-for="s in statuses" :key="s" :value="s" class="capitalize font-semibold">
                                {{ s.charAt(0).toUpperCase() + s.slice(1) }}
                            </option>
                        </select>
                    </div>

                    <div>
                        <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-skin-neutral-9">Payment Status</label>
                        <select
                            v-model="statusForm.payment_status"
                            class="block w-full rounded-xl border border-skin-neutral-6 bg-skin-neutral-2 px-3.5 py-2.5 text-sm font-medium text-skin-neutral-12 focus:border-skin-primary-7 focus:outline-hidden focus:ring-1 focus:ring-skin-primary-7 transition duration-150 ease-in-out"
                        >
                            <option value="unpaid" class="font-semibold">Unpaid</option>
                            <option value="paid" class="font-semibold">Paid</option>
                        </select>
                    </div>

                    <AppButton
                        type="submit"
                        class="btn btn-primary w-full inline-flex items-center justify-center gap-2 rounded-xl py-2.5 text-sm font-bold transition duration-150 ease-in-out"
                        :loading="statusForm.processing"
                    >
                        <i class="ri-save-line text-base"></i>
                        <span>Apply Status Updates</span>
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

const togglePermission = (id) => {
    const form = useForm({})
    form.patch(route('product.downloadPermission.toggle', { id }), {
        preserveScroll: true,
    })
}

const formatPaymentMethod = (method) => {
    const methods = {
        cod: 'Cash on Delivery',
        sslcommerz: 'SSLCommerz',
        card: 'Card',
        mobile: 'Mobile Payment',
    }
    return methods[method] ?? method ?? '—'
}

const statusClass = (status) => {
    const classes = {
        pending: 'bg-yellow-50 text-yellow-800 ring-yellow-600/20',
        processing: 'bg-blue-50 text-blue-800 ring-blue-600/20',
        shipped: 'bg-purple-50 text-purple-800 ring-purple-600/20',
        delivered: 'bg-indigo-50 text-indigo-800 ring-indigo-600/20',
        completed: 'bg-green-50 text-green-800 ring-green-600/20',
        cancelled: 'bg-red-50 text-red-800 ring-red-600/20',
    }
    return classes[status] ?? 'bg-gray-50 text-gray-800 ring-gray-600/20'
}

const paymentStatusClass = (status) => {
    return status === 'paid' ? 'bg-green-50 text-green-800 ring-green-600/20' : 'bg-yellow-50 text-yellow-800 ring-yellow-600/20'
}

const shipmentStatusClass = (status) => {
    const classes = {
        pending: 'bg-yellow-50 text-yellow-800 ring-yellow-600/20',
        processing: 'bg-blue-50 text-blue-800 ring-blue-600/20',
        shipped: 'bg-purple-50 text-purple-800 ring-purple-600/20',
        delivered: 'bg-indigo-50 text-indigo-800 ring-indigo-600/20',
        cancelled: 'bg-red-50 text-red-800 ring-red-600/20',
    }
    return classes[status] ?? 'bg-gray-50 text-gray-800 ring-gray-600/20'
}
</script>
