<template>

    <Head :title="title"></Head>

    <!-- Top Header -->
    <AppSectionHeader :title="title" :bread-crumb="breadCrumb" class="no-print">
        <template #right>
            <div class="flex flex-wrap items-center gap-2 sm:gap-3 no-print">
                <AppButton
                    class="btn btn-secondary inline-flex items-center gap-2 rounded-xl px-3.5 py-2 text-sm font-semibold transition duration-150 ease-in-out sm:px-4"
                    @click="$inertia.visit(route('order.index'))">
                    <i class="ri-arrow-left-line text-lg"></i>
                    <span>Back to Orders</span>
                </AppButton>
                <button type="button"
                    class="btn btn-secondary inline-flex items-center gap-2 rounded-xl px-3.5 py-2 text-sm font-semibold border border-skin-neutral-4 transition duration-150 ease-in-out sm:px-4"
                    @click="printInvoice">
                    <i class="ri-printer-line text-lg"></i>
                    <span>Print Invoice</span>
                </button>
                <a :href="route('order.downloadInvoice', order.id)"
                    class="btn btn-primary inline-flex items-center gap-2 rounded-xl px-3.5 py-2 text-sm font-semibold shadow-xs transition duration-150 ease-in-out sm:px-4">
                    <i class="ri-download-cloud-line text-lg"></i>
                    <span>Download PDF</span>
                </a>
            </div>
        </template>
    </AppSectionHeader>

    <!-- ── Main Grid Layout (3 Columns) ── -->
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-4">

        <!-- ── Left Column: Order Header Banner & Status Timeline Stepper ── -->
        <div class="space-y-6 no-print">

            <!-- Order Header Banner & Status Timeline Stepper Card -->
            <div
                class="overflow-hidden rounded-2xl bg-skin-neutral-1 border border-skin-neutral-4/80 shadow-xs p-5 sm:p-6 space-y-6">
                <!-- Header Info -->
                <div class="space-y-4 border-b border-skin-neutral-4/80 pb-5">
                    <div class="flex items-center gap-3">
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-2">
                                <h1 class="text-lg font-extrabold tracking-tight text-skin-neutral-12 truncate">
                                    Order #{{ order.id }}
                                </h1>
                                <button type="button"
                                    class="inline-flex items-center gap-1 rounded-lg bg-skin-neutral-3 px-2 py-0.5 text-xs font-semibold text-skin-neutral-11 hover:bg-skin-neutral-4 transition-colors shrink-0"
                                    @click="copyToClipboard(order.id, 'order_id')" title="Copy Order ID">
                                    <i
                                        :class="copiedKey === 'order_id' ? 'ri-check-line text-emerald-600' : 'ri-file-copy-line'"></i>
                                    <span>{{ copiedKey === 'order_id' ? 'Copied' : 'Copy' }}</span>
                                </button>
                            </div>
                            <p
                                class="text-xs font-medium text-skin-neutral-9 mt-0.5 flex flex-wrap items-center gap-1.5">
                                <span><i class="ri-calendar-line mr-1"></i>{{ order.created_at }}</span>
                                <span class="text-skin-neutral-6">•</span>
                                <span :class="order.requires_shipping ? 'text-blue-600' : 'text-purple-600'"
                                    class="font-semibold">
                                    {{ order.requires_shipping ? 'Physical Shipping' : 'Digital / Virtual' }}
                                </span>
                            </p>
                        </div>
                    </div>

                    <!-- Status Badges -->
                    <div class="flex flex-wrap items-center gap-2">
                        <div class="flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-bold ring-1 ring-inset"
                            :class="statusClass(order.status)">
                            <span class="h-2 w-2 rounded-full" :class="statusDotClass(order.status)"></span>
                            <span class="capitalize">Order: {{ order.status }}</span>
                        </div>
                        <div class="flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-bold ring-1 ring-inset"
                            :class="paymentStatusClass(order.payment_status)">
                            <i :class="order.payment_status === 'paid' ? 'ri-checkbox-circle-fill' : 'ri-time-line'"
                                class="text-sm"></i>
                            <span class="capitalize">Payment: {{ order.payment_status }}</span>
                        </div>
                    </div>
                </div>

                <!-- Status Timeline Stepper (Vertical Timeline) -->
                <div v-if="order.status !== 'cancelled'" class="py-1">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-skin-neutral-9 mb-4">Status Timeline</h3>
                    <div class="relative space-y-6 pl-1">
                        <div v-for="(step, idx) in pipelineSteps" :key="step.key"
                            class="relative flex items-start gap-3.5 group">
                            <!-- Step Connector Line -->
                            <div v-if="idx < pipelineSteps.length - 1" class="absolute top-8 left-4 -ml-px h-full w-0.5"
                                :class="getStepState(pipelineSteps[idx + 1].key) !== 'upcoming' ? 'bg-emerald-500' : 'bg-skin-neutral-4'">
                            </div>

                            <!-- Step Icon Badge -->
                            <div class="relative z-10 flex h-8 w-8 shrink-0 items-center justify-center rounded-xl text-xs font-bold transition-all duration-300 shadow-2xs"
                                :class="{
                                    'bg-emerald-500 text-white ring-4 ring-emerald-100': getStepState(step.key) === 'completed',
                                    'bg-blue-600 text-white ring-4 ring-blue-100 animate-pulse': getStepState(step.key) === 'active',
                                    'bg-skin-neutral-3  ring-1 ring-skin-neutral-4': getStepState(step.key) === 'upcoming'
                                }">
                                <i v-if="getStepState(step.key) === 'completed'" class="ri-check-line text-base"></i>
                                <i v-else :class="step.icon" class="text-sm"></i>
                            </div>

                            <!-- Step Labels -->
                            <div class="pt-0.5">
                                <p class="text-xs font-bold uppercase tracking-wider"
                                    :class="getStepState(step.key) === 'upcoming' ? 'text-skin-neutral-9' : 'text-skin-neutral-12'">
                                    {{ step.label }}
                                </p>
                                <p class="text-[11px] font-medium  capitalize mt-0.5">
                                    {{ getStepState(step.key) === 'completed' ? 'Completed' : (getStepState(step.key)
                                        ===
                                        'active' ? 'Current Phase' : 'Pending') }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Cancelled Order Banner -->
                <div v-else
                    class="rounded-xl bg-rose-50 border border-rose-200 p-4 flex items-center gap-3 text-rose-800">
                    <i class="ri-close-circle-fill text-xl text-rose-600 shrink-0"></i>
                    <div>
                        <h4 class="text-xs font-bold">This Order Has Been Cancelled</h4>
                        <p class="text-[11px] text-rose-700 mt-0.5">The order workflow was interrupted or voided.</p>
                    </div>
                </div>
            </div>

            <!-- Customer Notes Card -->
            <OrderSectionCard v-if="order.notes" title="Customer Notes"
                description="Instructions provided during checkout" icon="ri-sticky-note-line"
                icon-class="bg-amber-50 text-amber-600 ring-1 ring-amber-500/10">
                <div
                    class="rounded-xl bg-amber-50/50 border border-amber-200/70 p-4 text-xs font-medium leading-relaxed text-amber-900 flex items-start gap-2.5">
                    <i class="ri-double-quotes-l text-lg text-amber-500 shrink-0"></i>
                    <p class="italic text-amber-950 font-semibold">{{ order.notes }}</p>
                </div>
            </OrderSectionCard>

            <!-- Digital Download Access Card -->
            <OrderSectionCard v-if="order.downloadPermissions?.length" flush title="Digital Download Access"
                description="File download quota & permissions" icon="ri-download-cloud-line"
                icon-class="bg-amber-50 text-amber-600 ring-1 ring-amber-500/10">
                <div class="overflow-x-auto">
                    <table class="w-full text-xs text-left">
                        <thead>
                            <tr
                                class="border-b border-skin-neutral-4/80 bg-skin-neutral-2/60 font-bold uppercase tracking-wider text-skin-neutral-9">
                                <th class="px-3 py-2.5">Item</th>
                                <th class="px-2 py-2.5 text-center">Downloads</th>
                                <th class="px-3 py-2.5 text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-skin-neutral-3/70">
                            <tr v-for="dp in order.downloadPermissions" :key="dp.id"
                                class="transition-colors hover:bg-skin-neutral-2/40">
                                <td class="px-3 py-2.5">
                                    <p class="font-bold text-skin-neutral-12">{{ dp.product_name ?? '—' }}</p>
                                    <p class="text-[10px] font-mono text-skin-neutral-9">{{ dp.product_file_name }}</p>
                                </td>
                                <td class="px-2 py-2.5 text-center font-extrabold text-skin-neutral-12">
                                    {{ dp.download_count }}{{ dp.download_limit ? ' / ' + dp.download_limit : '' }}
                                </td>
                                <td class="px-3 py-2.5 text-center">
                                    <button type="button" :disabled="togglingId === dp.id"
                                        :class="dp.active ? 'bg-rose-50 text-rose-600 hover:bg-rose-100' : 'bg-emerald-50 text-emerald-600 hover:bg-emerald-100'"
                                        class="inline-flex items-center gap-1 rounded-lg px-2 py-1 text-[11px] font-bold transition-colors disabled:opacity-50"
                                        @click="togglePermission(dp.id)">
                                        {{ dp.active ? 'Revoke' : 'Activate' }}
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </OrderSectionCard>
        </div>

        <!-- ── Center Column: Invoice & Payment Log ── -->
        <div class="space-y-6 col-span-2">

            <!-- Printable Invoice Document Card with Corporate Letterhead -->
            <div
                class="printable-invoice overflow-hidden rounded-2xl bg-white border border-black shadow-xs p-4 sm:p-5 space-y-4 text-black">

                <!-- Invoice Corporate Letterhead Header -->
                <div
                    class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 border-b-2 border-black pb-3">
                    <!-- Company Logo & Details -->
                    <div class="flex items-center gap-3">
                        <div
                            class="flex h-11 w-11 shrink-0 items-center justify-center rounded-lg bg-neutral-100 border border-black shadow-2xs overflow-hidden">
                            <img :src="companyInfo.logo" :alt="companyInfo.name"
                                class="h-8 w-auto max-w-full object-contain"
                                @error="(e) => { e.target.style.display = 'none'; e.target.nextElementSibling.style.display = 'block'; }" />
                            <i class="ri-store-2-fill text-2xl text-black" style="display:none"></i>
                        </div>
                        <div>
                            <h1 class="text-xl font-black text-black tracking-tight leading-none">{{
                                companyInfo.name }}</h1>
                            <div
                                class="mt-1 flex flex-wrap items-center gap-x-3 gap-y-0.5 text-xs font-semibold text-black">
                                <span v-if="companyInfo.address" class="flex items-center gap-1">
                                    <i class="ri-map-pin-line text-black"></i>
                                    <span>{{ companyInfo.address }}</span>
                                </span>
                            </div>
                            <div
                                class="mt-0.5 flex flex-wrap items-center gap-x-3 gap-y-0.5 text-xs font-semibold text-black">
                                <span v-if="companyInfo.phone" class="flex items-center gap-1">
                                    <i class="ri-phone-line text-black"></i>
                                    <span>{{ companyInfo.phone }}</span>
                                </span>
                                <span v-if="companyInfo.email" class="flex items-center gap-1">
                                    <i class="ri-mail-line text-black"></i>
                                    <span>{{ companyInfo.email }}</span>
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Invoice Ref Badge -->
                    <div class="flex flex-col items-start sm:items-end gap-1 w-full sm:w-auto">
                        <div class="text-left sm:text-right">
                            <span
                                class="inline-block rounded-md bg-neutral-100 border border-black px-2 py-0.5 text-[10px] font-black uppercase tracking-widest text-black">
                                Invoice
                            </span>
                            <p class="font-mono text-xs font-extrabold text-black mt-0.5">#{{ order.id }}</p>
                        </div>
                    </div>
                </div>

                <!-- Customer Details & Order Metadata Box (Name, Phone, Address Only) -->
                <div
                    class="grid grid-cols-1 md:grid-cols-2 gap-3 border border-black rounded-xl p-3 bg-neutral-50 text-black">
                    <div class="space-y-1 text-xs">
                        <div class="flex items-start gap-2">
                            <span
                                class="w-16 shrink-0 font-bold uppercase tracking-wider text-black">Name:</span>
                            <span class="font-extrabold text-black">{{ order.name || 'N/A' }}</span>
                        </div>
                        <div class="flex items-start gap-2">
                            <span
                                class="w-16 shrink-0 font-bold uppercase tracking-wider text-black">Phone:</span>
                            <a v-if="order.phone" :href="`tel:${order.phone}`"
                                class="font-bold text-black hover:underline">{{ order.phone }}</a>
                            <span v-else>N/A</span>
                        </div>
                        <div class="flex items-start gap-2">
                            <span
                                class="w-16 shrink-0 font-bold uppercase tracking-wider text-black">Address:</span>
                            <span class="font-semibold text-black leading-tight">{{ order.address || 'N/A'
                                }}</span>
                        </div>
                    </div>
                    <div class="space-y-1 md:border-l md:border-black md:pl-3 text-xs">
                        <div class="flex items-center justify-between text-xs">
                            <span class="font-bold uppercase tracking-wider text-black">Invoice Date:</span>
                            <span class="font-semibold text-black">{{ order.created_at }}</span>
                        </div>
                        <div class="flex items-center justify-between text-xs">
                            <span class="font-bold uppercase tracking-wider text-black">Payment Method:</span>
                            <span class="font-bold text-black">{{ formatPaymentMethod(order.payment_method)
                                }}</span>
                        </div>

                        <div class="flex items-center justify-between text-xs">
                            <span class="font-bold uppercase tracking-wider text-black">Payment Status:</span>
                            <span
                                class="rounded-full px-2 py-0.5 text-[11px] font-bold capitalize ring-1 ring-black border border-black text-black bg-neutral-100">
                                {{ order.payment_status }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Invoice Itemized Products Table -->
                <div class="overflow-x-auto">
                    <table class="w-full text-xs text-left border-collapse text-black">
                        <thead>
                            <tr
                                class="border-y border-black bg-neutral-100 text-[11px] font-bold uppercase tracking-wider text-black">
                                <th class="px-2.5 py-2 w-8 text-center">#</th>
                                <th class="px-3 py-2">Item Description</th>
                                <th class="px-3 py-2 text-center">Qty</th>
                                <th class="px-3 py-2 text-right">Unit Price</th>
                                <th class="px-3 py-2 text-right">Total Amount</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-black/30 text-black">
                            <template v-for="(item, index) in order.orderProducts" :key="item.id">
                                <tr class="transition-colors">
                                    <td class="px-2.5 py-2 text-center font-bold text-black">{{ index + 1 }}
                                    </td>
                                    <td class="px-3 py-2">
                                        <p class="font-bold text-black text-xs">{{ item.product_name }}</p>
                                        <p v-if="item.variation_label"
                                            class="mt-0.5 inline-flex items-center gap-1 rounded-md bg-neutral-100 px-1.5 py-0.5 text-[10px] font-semibold text-black ring-1 ring-inset ring-black/20">
                                            {{ item.variation_label }}
                                        </p>
                                    </td>
                                    <td class="px-3 py-2 text-center font-black text-black">{{ item.quantity
                                        }}</td>
                                    <td class="px-3 py-2 text-right font-semibold text-black">{{
                                        formatMoney(item.unit_price) }} Tk</td>
                                    <td class="px-3 py-2 text-right font-extrabold text-black text-xs">{{
                                        formatMoney(item.total_price) }} Tk</td>
                                </tr>

                                <!-- Bundle child items snapshot -->
                                <tr v-for="bi in item.bundle_items ?? []" :key="bi.id"
                                    class="bg-neutral-50 text-[11px] text-black">
                                    <td class="px-2.5 py-1 text-center text-black">↳</td>
                                    <td class="px-3 py-1 pl-5">
                                        <span class="font-bold text-black">{{ bi.name }}</span>
                                        <span v-if="bi.sku"
                                            class="ml-2 font-mono text-[9px] bg-neutral-200 px-1 py-0.5 rounded text-black">SKU:
                                            {{ bi.sku }}</span>
                                    </td>
                                    <td class="px-3 py-1 text-center font-bold text-black">{{ bi.quantity }}
                                    </td>
                                    <td class="px-3 py-1 text-right font-semibold text-black">{{
                                        formatMoney(bi.unit_price) }} Tk</td>
                                    <td class="px-3 py-1 text-right font-bold text-black">{{
                                        formatMoney(bi.total_price) }} Tk</td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                <!-- Totals Financial Ledger & Paid Stamp Banner -->
                <div
                    class="relative flex flex-col md:flex-row justify-between items-center pt-3 border-t border-black gap-4 text-black">
                    <!-- Paid / Unpaid Rubber Stamp (Centered on Left Side) -->
                    <div class="flex items-center justify-center flex-1 w-full md:w-auto my-auto py-2">
                        <div v-if="order.payment_status === 'paid'"
                            class="inline-block transform -rotate-6 border-2 border-dashed border-black px-4 py-1.5 text-base font-black uppercase text-black tracking-widest bg-neutral-100 shadow-2xs">
                            PAID
                        </div>
                        <div v-else-if="Number(order.due) > 0"
                            class="inline-block transform -rotate-6 border-2 border-dashed border-black px-4 py-1.5 text-base font-black uppercase text-black tracking-widest bg-neutral-100 shadow-2xs">
                            DUE
                        </div>
                    </div>

                    <!-- Invoice Calculation Ledger -->
                    <div class="w-full md:w-72 space-y-1 text-xs text-black">
                        <div class="flex justify-between">
                            <span class="font-semibold text-black">Subtotal Amount:</span>
                            <span class="font-bold text-black">{{ formatMoney(order.subtotal) }} Tk</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="font-semibold text-black">Shipping Charge:</span>
                            <span v-if="Number(order.shipping) == 0" class="font-bold text-black">Free</span>
                            <span v-else class="font-bold text-black">{{ formatMoney(order.shipping) }}
                                Tk</span>
                        </div>
                        <div v-if="Number(order.tax) > 0" class="flex justify-between">
                            <span class="font-semibold text-black">Tax / VAT:</span>
                            <span class="font-bold text-black">{{ formatMoney(order.tax) }} Tk</span>
                        </div>
                        <div
                            class="flex justify-between text-xs font-extrabold border-t border-black pt-1.5">
                            <span>Net Grand Total:</span>
                            <span class="text-black text-sm font-black">{{ formatMoney(order.total) }}
                                Tk</span>
                        </div>
                        <div class="flex justify-between text-[11px] font-bold pt-0.5">
                            <span class="text-black">Paid Amount:</span>
                            <span class="text-black font-extrabold">{{ formatMoney(order.paid) }}
                                Tk</span>
                        </div>
                        <div class="flex justify-between text-[11px] font-bold">
                            <span class="text-black">Balance Due:</span>
                            <span class="font-extrabold text-black">
                                {{ formatMoney(order.due) }} Tk
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Printable Footer -->
                <div
                    class="border-t border-black pt-2 text-center text-[11px] text-black no-print-footer">
                    Thank you for your order! For support or inquiries, please contact us at {{ companyInfo.email }}.
                </div>
            </div>

            <!-- Payment Transactions Log Card -->
            <OrderSectionCard v-if="order.orderPayments?.length" flush title="Payment Transactions Log"
                description="Audited payment attempts and transaction records" icon="ri-bank-card-line"
                icon-class="bg-emerald-50 text-emerald-600 ring-1 ring-emerald-500/10" class="no-print">
                <div class="overflow-x-auto">
                    <table class="w-full text-xs text-left">
                        <thead>
                            <tr
                                class="border-b border-skin-neutral-4/80 bg-skin-neutral-2/60 font-bold uppercase tracking-wider text-skin-neutral-9">
                                <th class="px-4 py-3">Date</th>
                                <th class="px-3 py-3">Method</th>
                                <th class="px-3 py-3">Status</th>
                                <th class="px-3 py-3 text-right">Amount</th>
                                <th class="px-4 py-3">Tx Ref</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-skin-neutral-3/70">
                            <tr v-for="payment in order.orderPayments" :key="payment.id"
                                class="transition-colors hover:bg-skin-neutral-2/40">
                                <td class="px-4 py-3  font-medium">{{ payment.payment_date ?? '—' }}
                                </td>
                                <td class="px-3 py-3 font-bold text-skin-neutral-12">
                                    {{ formatPaymentMethod(payment.payment_method) }}
                                </td>
                                <td class="px-3 py-3">
                                    <span
                                        :class="payment.payment_status === 'success' ? 'bg-emerald-50 text-emerald-700 ring-emerald-600/20' : 'bg-amber-50 text-amber-700 ring-amber-600/20'"
                                        class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-[11px] font-bold capitalize ring-1 ring-inset">
                                        {{ payment.payment_status }}
                                    </span>
                                </td>
                                <td class="px-3 py-3 text-right font-black text-skin-neutral-12">{{
                                    formatMoney(payment.amount_paid) }} Tk</td>
                                <td class="px-4 py-3 font-mono text-[11px] text-skin-neutral-10">
                                    <div class="flex items-center gap-1">
                                        <span>{{ payment.transaction_id ?? '—' }}</span>
                                        <button v-if="payment.transaction_id" type="button"
                                            class="text-skin-neutral-7 hover:text-skin-neutral-12"
                                            @click="copyToClipboard(payment.transaction_id, `tx_${payment.id}`)"
                                            title="Copy Transaction ID">
                                            <i
                                                :class="copiedKey === `tx_${payment.id}` ? 'ri-check-line text-emerald-600' : 'ri-file-copy-line'"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </OrderSectionCard>

        </div>

        <!-- ── Right Column: Controller, Order Parameters, Logistics & Shipment ── -->
        <div class="space-y-6 no-print">

            <!-- 1. Order Status Controller Card -->
            <OrderSectionCard title="Order Status Controller" description="Update status pipeline & payment state"
                icon="ri-sound-module-line" icon-class="bg-blue-50 text-blue-600 ring-1 ring-blue-500/10">
                <form @submit.prevent="submitStatus" class="space-y-4">
                    <div>

                        <label for="order-status"
                            class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-skin-neutral-9">
                            Order Status Pipeline

                        </label>
                        <div class="relative">
                            <select id="order-status" v-model="statusForm.status"
                                class="block w-full rounded-xl border border-skin-neutral-6 bg-skin-neutral-2 px-3.5 py-2.5 text-sm font-bold text-skin-neutral-12 focus:border-skin-primary-9 focus:outline-hidden focus:ring-1 focus:ring-skin-primary-9 transition duration-150">
                                <option v-for="s in statuses" :key="s" :value="s" class="capitalize font-semibold">
                                    {{ s.charAt(0).toUpperCase() + s.slice(1) }}
                                </option>
                            </select>
                        </div>
                    </div>

                    <div>

                        <label for="order-payment-status"
                            class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-skin-neutral-9">
                            Payment Settlement

                        </label>
                        <select id="order-payment-status" v-model="statusForm.payment_status"
                            class="block w-full rounded-xl border border-skin-neutral-6 bg-skin-neutral-2 px-3.5 py-2.5 text-sm font-bold text-skin-neutral-12 focus:border-skin-primary-9 focus:outline-hidden focus:ring-1 focus:ring-skin-primary-9 transition duration-150">
                            <option value="unpaid" class="font-semibold">Unpaid</option>
                            <option value="paid" class="font-semibold">Paid</option>
                        </select>
                    </div>

                    <AppButton type="submit"
                        class="btn btn-primary w-full inline-flex items-center justify-center gap-2 rounded-xl py-2.5 text-sm font-bold shadow-xs transition duration-150 ease-in-out"
                        :loading="statusForm.processing">
                        <i class="ri-save-line text-base"></i>
                        <span>Save Status Changes</span>
                    </AppButton>
                </form>
            </OrderSectionCard>

            <!-- 2. Order Parameters Summary Card -->
            <OrderSectionCard title="Order Parameters" description="Core metadata & financial parameters"
                icon="ri-file-list-3-line" icon-class="bg-purple-50 text-purple-600 ring-1 ring-purple-500/10">
                <div class="divide-y divide-skin-neutral-3/70">
                    <div class="flex items-center justify-between py-3">
                        <span class="text-xs font-semibold text-skin-neutral-9 uppercase tracking-wider">Order
                            No.</span>
                        <span class="font-mono text-sm font-extrabold text-skin-neutral-12">#{{ order.id }}</span>
                    </div>
                    <div class="flex items-center justify-between py-3">
                        <span class="text-xs font-semibold text-skin-neutral-9 uppercase tracking-wider">Payment
                            Method</span>
                        <span class="text-sm font-bold text-skin-neutral-12 flex items-center gap-1.5">
                            <i class="ri-bank-card-2-line text-blue-600"></i>
                            {{ formatPaymentMethod(order.payment_method) }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between py-3">
                        <span class="text-xs font-semibold text-skin-neutral-9 uppercase tracking-wider">Payment
                            Status</span>
                        <span :class="paymentStatusClass(order.payment_status)"
                            class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-bold capitalize ring-1 ring-inset">
                            {{ order.payment_status }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between py-3">
                        <span class="text-xs font-semibold text-skin-neutral-9 uppercase tracking-wider">Order
                            Status</span>
                        <span :class="statusClass(order.status)"
                            class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-bold capitalize ring-1 ring-inset">
                            {{ order.status }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between py-3">
                        <span class="text-xs font-semibold text-skin-neutral-9 uppercase tracking-wider">Fulfillment
                            Type</span>
                        <span
                            :class="order.requires_shipping ? 'text-blue-700 bg-blue-50 ring-blue-600/20' : 'text-purple-700 bg-purple-50 ring-purple-600/20'"
                            class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-bold capitalize ring-1 ring-inset">
                            {{ order.requires_shipping ? 'Physical Order' : 'Virtual / Digital' }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between py-3.5">
                        <span class="text-xs font-bold text-skin-neutral-9 uppercase tracking-wider">Grand Total</span>
                        <span class="text-lg font-black text-skin-primary-9">{{ formatMoney(order.total) }} Tk</span>
                    </div>
                </div>
            </OrderSectionCard>

            <!-- 3. Logistics & Shipment Details Card -->
            <OrderSectionCard v-if="order.requires_shipping && order.orderShipments?.length"
                title="Logistics & Shipment Details" description="Carrier assignments & parcel tracking"
                icon="ri-truck-line" icon-class="bg-purple-50 text-purple-600 ring-1 ring-purple-500/10">
                <div class="divide-y divide-skin-neutral-3/70">
                    <div v-for="shipment in order.orderShipments" :key="shipment.id"
                        class="space-y-3 py-3 first:pt-0 last:pb-0">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold uppercase tracking-wider text-skin-neutral-9">Status:</span>
                            <span
                                class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-bold capitalize ring-1 ring-inset"
                                :class="shipmentStatusClass(shipment.shopment_status)">
                                {{ shipment.shopment_status }}
                            </span>
                        </div>
                        <div v-if="shipment.carrier" class="flex items-center justify-between text-xs">
                            <span class="font-bold text-skin-neutral-9 uppercase">Carrier:</span>
                            <span class="font-bold text-skin-neutral-12">{{ shipment.carrier }}</span>
                        </div>
                        <div v-if="shipment.tracking_number" class="flex items-center justify-between text-xs">
                            <span class="font-bold text-skin-neutral-9 uppercase">Tracking:</span>
                            <div class="flex items-center gap-1.5 font-mono font-bold">
                                <a v-if="shipment.tracking_url" :href="shipment.tracking_url" target="_blank"
                                    rel="noopener noreferrer" class="text-blue-600 hover:underline">
                                    {{ shipment.tracking_number }}
                                </a>
                                <span v-else class="text-skin-neutral-12">{{ shipment.tracking_number }}</span>
                                <button type="button" class="text-skin-neutral-7 hover:text-skin-neutral-12"
                                    @click="copyToClipboard(shipment.tracking_number, `track_${shipment.id}`)"
                                    title="Copy Tracking Number">
                                    <i
                                        :class="copiedKey === `track_${shipment.id}` ? 'ri-check-line text-emerald-600' : 'ri-file-copy-line'"></i>
                                </button>
                            </div>
                        </div>
                        <div v-if="shipment.shipment_date" class="flex items-center justify-between text-xs">
                            <span class="font-bold text-skin-neutral-9 uppercase">Dispatch Date:</span>
                            <span class="font-semibold text-skin-neutral-12">{{ shipment.shipment_date }}</span>
                        </div>
                        <div v-if="shipment.estimated_delivery" class="flex items-center justify-between text-xs">
                            <span class="font-bold text-skin-neutral-9 uppercase">Est. Delivery:</span>
                            <span class="font-semibold text-skin-neutral-12">{{ shipment.estimated_delivery }}</span>
                        </div>
                        <div v-if="shipment.actual_delivery" class="flex items-center justify-between text-xs">
                            <span class="font-bold text-skin-neutral-9 uppercase">Actual Delivery:</span>
                            <span class="font-bold text-emerald-600">{{ shipment.actual_delivery }}</span>
                        </div>
                    </div>
                </div>
            </OrderSectionCard>

        </div>
    </div>
</template>

<script setup>
import { computed, ref } from 'vue'
import { formatMoney } from '@/Utils/formatMoney'
import { Head, useForm, usePage } from '@inertiajs/vue3'
import useTitle from '@/Composables/useTitle'
import OrderSectionCard from './Components/OrderSectionCard.vue'

const { title } = useTitle('Order Details')

const props = defineProps({
    order: {
        type: Object,
        default: () => ({}),
    },
    statuses: {
        type: Array,
        default: () => [],
    },
})

const page = usePage()

const companyInfo = computed(() => {
    const branding = page.props.branding || {}
    const contact = page.props.contact || {}
    return {
        name: branding.site_name || 'ShopNow E-Commerce',
        logo: branding.logo_url || '/logo.png',
        phone: Array.isArray(contact.phone) ? contact.phone[0] : (contact.phone || '+880 1700-000000'),
        email: Array.isArray(contact.email) ? contact.email[0] : (contact.email || 'support@shopnow.com'),
        address: Array.isArray(contact.address) ? contact.address[0] : (contact.address || 'Dhaka, Bangladesh'),
    }
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

const togglingId = ref(null)

const togglePermission = (id) => {
    togglingId.value = id
    const form = useForm({})
    form.patch(route('product.downloadPermission.toggle', { id }), {
        preserveScroll: true,
        onFinish: () => {
            togglingId.value = null
        },
    })
}

const copiedKey = ref('')

const copyToClipboard = (text, key) => {
    if (!text) return
    navigator.clipboard.writeText(String(text))
    copiedKey.value = key
    setTimeout(() => {
        if (copiedKey.value === key) {
            copiedKey.value = ''
        }
    }, 2000)
}

const printInvoice = () => {
    window.print()
}

const totalItemCount = computed(() => {
    if (!props.order.orderProducts) return 0
    return props.order.orderProducts.reduce((sum, item) => sum + Number(item.quantity || 1), 0)
})

const formatPaymentMethod = (method) => {
    const methods = {
        cod: 'Cash on Delivery',
        sslcommerz: 'SSLCommerz',
        card: 'Card Payment',
        mobile: 'Mobile Payment',
    }
    return methods[method] ?? method ?? '—'
}

const statusClass = (status) => {
    const classes = {
        pending: 'bg-amber-50 text-amber-700 ring-amber-600/20',
        processing: 'bg-blue-50 text-blue-700 ring-blue-600/20',
        shipped: 'bg-purple-50 text-purple-700 ring-purple-600/20',
        delivered: 'bg-indigo-50 text-indigo-700 ring-indigo-600/20',
        completed: 'bg-emerald-50 text-emerald-700 ring-emerald-600/20',
        cancelled: 'bg-rose-50 text-rose-700 ring-rose-600/20',
    }
    return classes[status] ?? 'bg-gray-50 text-gray-700 ring-gray-600/20'
}

const statusDotClass = (status) => {
    const classes = {
        pending: 'bg-amber-500',
        processing: 'bg-blue-500',
        shipped: 'bg-purple-500',
        delivered: 'bg-indigo-500',
        completed: 'bg-emerald-500',
        cancelled: 'bg-rose-500',
    }
    return classes[status] ?? 'bg-gray-400'
}

const paymentStatusClass = (status) => {
    return status === 'paid' ? 'bg-emerald-50 text-emerald-700 ring-emerald-600/20' : 'bg-amber-50 text-amber-700 ring-amber-600/20'
}

const shipmentStatusClass = (status) => {
    const classes = {
        pending: 'bg-amber-50 text-amber-700 ring-amber-600/20',
        processing: 'bg-blue-50 text-blue-700 ring-blue-600/20',
        shipped: 'bg-purple-50 text-purple-700 ring-purple-600/20',
        delivered: 'bg-indigo-50 text-indigo-700 ring-indigo-600/20',
        cancelled: 'bg-rose-50 text-rose-700 ring-rose-600/20',
    }
    return classes[status] ?? 'bg-gray-50 text-gray-700 ring-gray-600/20'
}

// Pipeline Workflow Stepper
const pipelineSteps = [
    { key: 'pending', label: 'Order Placed', icon: 'ri-shopping-cart-2-line' },
    { key: 'processing', label: 'Processing', icon: 'ri-loader-3-line' },
    { key: 'shipped', label: 'Shipped', icon: 'ri-truck-line' },
    { key: 'completed', label: 'Completed', icon: 'ri-checkbox-circle-line' },
]

const getStepState = (stepKey) => {
    const status = (props.order.status || '').toLowerCase()
    if (status === 'cancelled') return 'cancelled'

    const stepOrder = ['pending', 'processing', 'shipped', 'delivered', 'completed']
    const normalizedStatus = status === 'delivered' ? 'completed' : status
    const currentIndex = stepOrder.indexOf(normalizedStatus)
    const targetIndex = stepOrder.indexOf(stepKey)

    if (currentIndex > targetIndex) return 'completed'
    if (currentIndex === targetIndex) return 'active'
    return 'upcoming'
}
</script>

<style>
.printable-invoice,
.printable-invoice * {
    color: #000000 !important;
}

@media print {

    /* Hide layout sidebar, app header, navigation, and non-print elements */
    body * {
        visibility: hidden !important;
    }

    header,
    nav,
    aside,
    footer,
    .no-print,
    .app-header,
    .app-sidebar,
    .app-navigation {
        display: none !important;
    }

    /* Make ONLY the printable invoice visible and position at top-left */
    .printable-invoice,
    .printable-invoice * {
        visibility: visible !important;
    }

    .printable-invoice {
        position: absolute !important;
        left: 0 !important;
        top: 0 !important;
        width: 100% !important;
        margin: 0 !important;
        padding: 0 !important;
        border: none !important;
        box-shadow: none !important;
        background: #ffffff !important;
        color: #000000 !important;
    }
}
</style>
