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
                    <span>{{ __('order::admin.back_to_orders') }}</span>
                </AppButton>
                <AppButton
                    class="btn btn-secondary inline-flex items-center gap-2 rounded-xl px-3.5 py-2 text-sm font-semibold transition duration-150 ease-in-out sm:px-4"
                    @click="$inertia.visit(route('order.edit', order.id))">
                    <i class="ri-pencil-line text-lg"></i>
                    <span>{{ __('order::admin.edit_order_btn') }}</span>
                </AppButton>
                <button type="button"
                    class="btn btn-secondary inline-flex items-center gap-2 rounded-xl px-3.5 py-2 text-sm font-semibold border border-skin-neutral-4 transition duration-150 ease-in-out sm:px-4"
                    @click="printInvoice">
                    <i class="ri-printer-line text-lg"></i>
                    <span>{{ __('order::admin.print_invoice') }}</span>
                </button>
                <!-- <a :href="route('order.downloadInvoice', order.id)"
                    class="btn btn-primary inline-flex items-center gap-2 rounded-xl px-3.5 py-2 text-sm font-semibold shadow-xs transition duration-150 ease-in-out sm:px-4">
                    <i class="ri-download-cloud-line text-lg"></i>
                    <span>Download PDF</span>
                </a> -->
            </div>
        </template>
    </AppSectionHeader>

    <AppConfirmDialog ref="confirmDialogRef"></AppConfirmDialog>

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
                                    {{ __('order::admin.order_no') }}{{ order.id }}
                                </h1>
                                <button type="button"
                                    class="inline-flex items-center gap-1 rounded-lg bg-skin-neutral-3 px-2 py-0.5 text-xs font-semibold text-skin-neutral-11 hover:bg-skin-neutral-4 transition-colors shrink-0"
                                    @click="copyToClipboard(order.id, 'order_id')" :title="__('order::admin.copy_order_id')">
                                    <i
                                        :class="copiedKey === 'order_id' ? 'ri-check-line text-emerald-600' : 'ri-file-copy-line'"></i>
                                    <span>{{ copiedKey === 'order_id' ? __('order::admin.copied') : __('order::admin.copy') }}</span>
                                </button>
                            </div>
                            <p
                                class="text-xs font-medium text-skin-neutral-9 mt-0.5 flex flex-wrap items-center gap-1.5">
                                <span><i class="ri-calendar-line mr-1"></i>{{ order.created_at }}</span>
                                <span class="text-skin-neutral-6">•</span>
                                <span :class="order.requires_shipping ? 'text-blue-600' : 'text-purple-600'"
                                    class="font-semibold">
                                    {{ order.requires_shipping ? __('order::admin.physical_shipping') : __('order::admin.digital_virtual') }}
                                </span>
                            </p>
                        </div>
                    </div>

                    <!-- Status Badges -->
                    <div class="flex flex-wrap items-center gap-2">
                        <div class="flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-bold ring-1 ring-inset"
                            :class="statusClass(order.status)">
                            <span class="h-2 w-2 rounded-full" :class="statusDotClass(order.status)"></span>
                            <span class="capitalize">{{ __('order::admin.order_colon') }} {{ orderStatusText(order.status) }}</span>
                        </div>
                        <div class="flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-bold ring-1 ring-inset"
                            :class="paymentStatusClass(order.payment_status)">
                            <i :class="order.payment_status === 'paid' ? 'ri-checkbox-circle-fill' : 'ri-time-line'"
                                class="text-sm"></i>
                            <span class="capitalize">{{ __('order::admin.payment_colon') }} {{ paymentStatusText(order.payment_status) }}</span>
                        </div>
                        <div v-if="order.coupon_code"
                            class="flex items-center gap-1.5 rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-700 ring-1 ring-emerald-200 ring-inset">
                            <i class="ri-price-tag-3-line"></i>
                            <span>{{ order.coupon_code }}<template v-if="Number(order.discount) > 0">
                                    (-{{ formatMoney(order.discount) }} {{ __('order::admin.tk') }})</template></span>
                        </div>
                        <div v-if="order.fraud_risk === 'high'"
                            class="flex items-center gap-1.5 rounded-full bg-rose-50 px-3 py-1 text-xs font-bold text-rose-700 ring-1 ring-rose-200 ring-inset"
                            :title="fraudSummary">
                            <i class="ri-shield-star-line"></i>
                            <span>{{ __('order::admin.high_fraud_risk') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Status Timeline Stepper (Vertical Timeline) -->
                <div v-if="order.status !== 'cancelled'" class="py-1">
                    <h3 class="text-xs font-bold  tracking-wider text-skin-neutral-9 mb-4">{{ __('order::admin.status_timeline') }}</h3>
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
                                <p class="text-xs font-bold  tracking-wider"
                                    :class="getStepState(step.key) === 'upcoming' ? 'text-skin-neutral-9' : 'text-skin-neutral-12'">
                                    {{ step.label }}
                                </p>
                                <p class="text-[11px] font-medium  capitalize mt-0.5">
                                    {{ getStepState(step.key) === 'completed'
                                        ? __('order::admin.step_state_completed')
                                        : (getStepState(step.key) === 'active'
                                            ? __('order::admin.step_state_current')
                                            : __('order::admin.step_state_pending')) }}
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
                        <h4 class="text-xs font-bold">{{ __('order::admin.cancelled_title') }}</h4>
                        <p class="text-[11px] text-rose-700 mt-0.5">{{ __('order::admin.cancelled_desc') }}</p>
                    </div>
                </div>
            </div>

            <!-- Customer Notes Card -->
            <OrderSectionCard v-if="order.notes" :title="__('order::admin.customer_notes')"
                :description="__('order::admin.customer_notes_desc')" icon="ri-sticky-note-line"
                icon-class="bg-amber-50 text-amber-600 ring-1 ring-amber-500/10">
                <div
                    class="rounded-xl bg-amber-50/50 border border-amber-200/70 p-4 text-xs font-medium leading-relaxed text-amber-900 flex items-start gap-2.5">
                    <i class="ri-double-quotes-l text-lg text-amber-500 shrink-0"></i>
                    <p class="italic text-amber-950 font-semibold">{{ order.notes }}</p>
                </div>
            </OrderSectionCard>

            <!-- Digital Download Access Card -->
            <OrderSectionCard v-if="order.downloadPermissions?.length" flush :title="__('order::admin.digital_download_access')"
                :description="__('order::admin.digital_download_access_desc')" icon="ri-download-cloud-line"
                icon-class="bg-amber-50 text-amber-600 ring-1 ring-amber-500/10">
                <div class="overflow-x-auto">
                    <table class="w-full text-xs text-left">
                        <thead>
                            <tr
                                class="border-b border-skin-neutral-4/80 bg-skin-neutral-2/60 font-bold  tracking-wider text-skin-neutral-9">
                                <th class="px-3 py-2.5">{{ __('order::admin.item') }}</th>
                                <th class="px-2 py-2.5 text-center">{{ __('order::admin.downloads') }}</th>
                                <th class="px-3 py-2.5 text-center">{{ __('common.header.action') }}</th>
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
                                        {{ dp.active ? __('order::admin.revoke') : __('order::admin.activate') }}
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </OrderSectionCard>
        </div>

        <!-- ── Center Column: Invoice & Payment Log ── -->
        <div class="space-y-6 col-span-1 lg:col-span-2">

            <!-- Printable Invoice Document Card with Corporate Letterhead -->
            <div
                class="printable-invoice overflow-hidden rounded-sm bg-white border border-skin-neutral-4/80 shadow-xs p-3.5 sm:p-5 space-y-4 text-black">

                <!-- Invoice Corporate Letterhead Header -->
                <div
                    class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 border-b-2 border-black pb-3">
                    <!-- Company Logo & Details -->
                    <div class="flex items-center gap-3">
                        <div v-if="companyInfo.logo"
                            class="flex h-11 w-11 shrink-0 items-center justify-center shadow-2xs overflow-hidden">
                            <img :src="companyInfo.logo" :alt="companyInfo.name || 'Company Logo'"
                                class="h-8 w-auto max-w-full object-contain"
                                @error="(e) => { e.target.style.display = 'none'; e.target.nextElementSibling.style.display = 'block'; }" />
                            <i class="ri-store-2-fill text-2xl text-black" style="display:none"></i>
                        </div>
                        <div class="min-w-0">
                            <h1 v-if="companyInfo.name"
                                class="text-lg sm:text-xl font-black text-black tracking-tight leading-none break-words">
                                {{
                                    companyInfo.name }}</h1>
                            <div v-if="companyInfo.address"
                                class="mt-1 flex flex-wrap items-center gap-x-3 gap-y-0.5 text-xs font-semibold text-black">
                                <span class="flex items-center gap-1">
                                    <i class="ri-map-pin-line text-black shrink-0"></i>
                                    <span class="break-words">{{ companyInfo.address }}</span>
                                </span>
                            </div>
                            <div v-if="companyInfo.phone || companyInfo.email"
                                class="mt-0.5 flex flex-wrap items-center gap-x-3 gap-y-0.5 text-xs font-semibold text-black">
                                <span v-if="companyInfo.phone" class="flex items-center gap-1">
                                    <i class="ri-phone-line text-black shrink-0"></i>
                                    <span>{{ companyInfo.phone }}</span>
                                </span>
                                <span v-if="companyInfo.email" class="flex items-center gap-1">
                                    <i class="ri-mail-line text-black shrink-0"></i>
                                    <span class="break-all">{{ companyInfo.email }}</span>
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Invoice Ref Badge -->
                    <div class="flex flex-col items-start sm:items-end gap-1 w-full sm:w-auto">
                        <div class="text-left sm:text-right">
                            <span
                                class="inline-block py-0.5 text-base sm:text-lg font-black tracking-widest text-black">
                                {{ __('order::admin.invoice') }}
                            </span>
                            <p class="font-mono font-extrabold text-black mt-0.5 text-xs sm:text-sm">#{{ order.id }}</p>
                        </div>
                    </div>
                </div>

                <!-- Customer Details & Order Metadata Box (Name, Phone, Address Only) -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-black printable-customer-box">
                    <div class="space-y-1 text-xs">
                        <div class="flex items-start gap-2">
                            <span class="w-16 shrink-0 font-bold tracking-wider text-black">{{ __('order::admin.name_colon') }}</span>
                            <span class="font-extrabold text-black break-words">{{ order.name || __('order::admin.n_a') }}</span>
                        </div>
                        <div class="flex items-start gap-2">
                            <span class="w-16 shrink-0 font-bold tracking-wider text-black">{{ __('order::admin.phone_colon') }}</span>
                            <a v-if="order.phone" :href="`tel:${order.phone}`"
                                class="font-bold text-black hover:underline break-all">{{ order.phone }}</a>
                            <span v-else>{{ __('order::admin.n_a') }}</span>
                        </div>
                        <div class="flex items-start gap-2">
                            <span class="w-16 shrink-0 font-bold  tracking-wider text-black">{{ __('order::admin.address_colon') }}</span>
                            <span class="font-semibold text-black leading-tight break-words">{{ order.address || __('order::admin.n_a')
                            }}</span>
                        </div>
                    </div>
                    <div class="space-y-1 sm:border-l sm:border-black sm:pl-3 text-xs">
                        <div class="flex items-center justify-between gap-2 text-xs">
                            <span class="font-bold tracking-wider text-black shrink-0">{{ __('order::admin.invoice_date_colon') }}</span>
                            <span class="font-semibold text-black text-right">{{ order.created_at }}</span>
                        </div>
                        <div class="flex items-center justify-between gap-2 text-xs">
                            <span class="font-bold tracking-wider text-black shrink-0">{{ __('order::admin.payment_method_colon') }}</span>
                            <span class="font-bold text-black text-right">{{ formatPaymentMethod(order.payment_method)
                            }}</span>
                        </div>

                        <div class="flex items-center justify-between gap-2 text-xs">
                            <span class="font-bold tracking-wider text-black shrink-0">{{ __('order::admin.payment_status_colon') }}</span>
                            <span
                                class="rounded-full px-2 py-0.5 text-[11px] font-bold capitalize ring-1 ring-black border border-black text-black bg-neutral-100 shrink-0">
                                {{ paymentStatusText(order.payment_status) }}
                            </span>
                        </div>

                        <div v-if="invoiceShipment?.tracking_number" class="flex items-center justify-between gap-2 text-xs">
                            <span class="font-bold tracking-wider text-black shrink-0">{{ __('order::admin.tracking_no_colon') }}</span>
                            <a v-if="invoiceShipment.tracking_url" :href="invoiceShipment.tracking_url" target="_blank"
                                rel="noopener noreferrer"
                                class="font-mono font-bold text-black text-right hover:underline break-all">
                                {{ invoiceShipment.tracking_number }}
                            </a>
                            <span v-else class="font-mono font-bold text-black text-right break-all">{{ invoiceShipment.tracking_number }}</span>
                        </div>
                        <div v-if="invoiceShipment?.consignment_id" class="flex items-center justify-between gap-2 text-xs">
                            <span class="font-bold tracking-wider text-black shrink-0">{{ __('order::admin.consignment_id_colon') }}</span>
                            <span class="font-mono font-bold text-black text-right break-all">{{ invoiceShipment.consignment_id }}</span>
                        </div>
                    </div>
                </div>

                <!-- Invoice Itemized Products Table -->
                <div class="overflow-x-auto -mx-1 sm:mx-0">
                    <table class="w-full text-xs text-left border-collapse text-black min-w-[440px] sm:min-w-full">
                        <thead>
                            <tr
                                class="border-y border-black bg-neutral-100 text-[11px] font-bold uppercase tracking-wider text-black">
                                <th class="px-2.5 py-2 w-8 text-center">#</th>
                                <th class="px-3 py-2">{{ __('order::admin.item_description') }}</th>
                                <th class="px-3 py-2 text-center">{{ __('common.header.qty') }}</th>
                                <th class="px-3 py-2 text-right">{{ __('order::admin.unit_price') }}</th>
                                <th class="px-3 py-2 text-right">{{ __('order::admin.total_amount') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-black/30 text-black">
                            <template v-for="(item, index) in order.orderProducts" :key="item.id">
                                <tr class="transition-colors">
                                    <td class="px-2.5 py-2 text-center font-bold text-black">{{ index + 1 }}
                                    </td>
                                    <td class="px-3 py-2">
                                        <p class="font-bold text-black text-xs break-words">{{ item.product_name }}</p>
                                        <p v-if="item.variation_label"
                                            class="mt-0.5 inline-flex items-center gap-1 rounded-md bg-neutral-100 px-1.5 py-0.5 text-[10px] font-semibold text-black ring-1 ring-inset ring-black/20">
                                            {{ item.variation_label }}
                                        </p>
                                    </td>
                                    <td class="px-3 py-2 text-center font-black text-black">{{ item.quantity
                                    }}</td>
                                    <td class="px-3 py-2 text-right font-semibold text-black whitespace-nowrap">{{
                                        formatMoney(item.unit_price) }} {{ __('order::admin.tk') }}</td>
                                    <td
                                        class="px-3 py-2 text-right font-extrabold text-black text-xs whitespace-nowrap">
                                        {{
                                            formatMoney(item.total_price) }} {{ __('order::admin.tk') }}</td>
                                </tr>

                                <!-- Bundle child items snapshot -->
                                <tr v-for="bi in item.bundle_items ?? []" :key="bi.id"
                                    class="bg-neutral-50 text-[11px] text-black">
                                    <td class="px-2.5 py-1 text-center text-black">↳</td>
                                    <td class="px-3 py-1 pl-5">
                                        <span class="font-bold text-black break-words">{{ bi.name }}</span>
                                        <span v-if="bi.sku"
                                            class="ml-2 font-mono text-[9px] bg-neutral-200 px-1 py-0.5 rounded text-black">{{ __('order::admin.sku_colon') }} {{ bi.sku }}</span>
                                    </td>
                                    <td class="px-3 py-1 text-center font-bold text-black">{{ bi.quantity }}
                                    </td>
                                    <td class="px-3 py-1 text-right font-semibold text-black whitespace-nowrap">{{
                                        formatMoney(bi.unit_price) }} {{ __('order::admin.tk') }}</td>
                                    <td class="px-3 py-1 text-right font-bold text-black whitespace-nowrap">{{
                                        formatMoney(bi.total_price) }} {{ __('order::admin.tk') }}</td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                <!-- Totals Financial Ledger & Paid Stamp Banner -->
                <div
                    class="relative flex flex-col sm:flex-row justify-between items-center pt-3 border-t border-black gap-4 text-black printable-totals-banner">
                    <!-- Paid / Unpaid Rubber Stamp (Centered on Left Side) -->
                    <div class="flex items-center justify-center flex-1 my-auto py-2 w-full sm:w-auto">
                        <div v-if="order.payment_status === 'paid'"
                            class="inline-block transform -rotate-6 border-2 border-dashed border-black px-4 py-1.5 text-base font-black uppercase text-black tracking-widest bg-neutral-100 shadow-2xs">
                            {{ __('order::admin.paid_stamp') }}
                        </div>
                        <div v-else-if="Number(order.due) > 0"
                            class="inline-block transform -rotate-6 border-2 border-dashed border-black px-4 py-1.5 text-base font-black uppercase text-black tracking-widest bg-neutral-100 shadow-2xs">
                            {{ __('order::admin.due_stamp') }}
                        </div>
                    </div>

                    <!-- Invoice Calculation Ledger -->
                    <div class="w-full sm:w-72 totals-ledger shrink-0 space-y-1 text-xs text-black">
                        <div class="flex justify-between">
                            <span class="font-semibold text-black">{{ __('order::admin.subtotal_colon') }}</span>
                            <span class="font-bold text-black">{{ formatMoney(order.subtotal) }} {{ __('order::admin.tk') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="font-semibold text-black">{{ __('order::admin.shipping_colon') }}</span>
                            <span v-if="Number(order.shipping) == 0" class="font-bold text-black">{{ __('order::admin.free') }}</span>
                            <span v-else class="font-bold text-black">{{ formatMoney(order.shipping) }}
                                {{ __('order::admin.tk') }}</span>
                        </div>
                        <div v-if="Number(order.tax) > 0" class="flex justify-between">
                            <span class="font-semibold text-black">{{ __('order::admin.tax_colon') }}</span>
                            <span class="font-bold text-black">{{ formatMoney(order.tax) }} {{ __('order::admin.tk') }}</span>
                        </div>
                        <div v-if="Number(order.discount) > 0" class="flex justify-between">
                            <span class="font-semibold text-black">{{ __('order::admin.discount') }}<span v-if="order.coupon_code"
                                    class="font-normal"> ({{ order.coupon_code }})</span>:</span>
                            <span class="font-bold text-black">-{{ formatMoney(order.discount) }} {{ __('order::admin.tk') }}</span>
                        </div>
                        <div class="flex justify-between text-xs font-extrabold border-t border-black pt-1.5">
                            <span>{{ __('order::admin.net_grand_total_colon') }}</span>
                            <span class="text-black text-sm font-black">{{ formatMoney(order.total) }}
                                {{ __('order::admin.tk') }}</span>
                        </div>
                        <div class="flex justify-between text-[11px] font-bold pt-0.5">
                            <span class="text-black">{{ __('order::admin.paid_amount_colon') }}</span>
                            <span class="text-black font-extrabold">{{ formatMoney(order.paid) }}
                                {{ __('order::admin.tk') }}</span>
                        </div>
                        <div class="flex justify-between text-[11px] font-bold">
                            <span class="text-black">{{ __('order::admin.balance_due_colon') }}</span>
                            <span class="font-extrabold text-black">
                                {{ formatMoney(order.due) }} {{ __('order::admin.tk') }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Printable Footer -->
                <div class="border-t pt-2 text-center text-[11px] text-black no-print-footer">
                    {{ __('order::admin.thank_you_footer') }}<span v-if="companyInfo.email"> {{ __('order::admin.support_inquiries_footer') }} {{ companyInfo.email }}.</span>
                </div>
            </div>

            <!-- Fraud Check Card -->
            <OrderSectionCard v-if="order.requires_shipping" :title="__('order::admin.fraud_check')"
                :description="__('order::admin.fraud_check_desc')" icon="ri-shield-check-line"
                icon-class="bg-rose-50 text-rose-600 ring-1 ring-rose-500/10" class="no-print">
                <template #badge>
                    <span v-if="order.fraud_risk === 'high'"
                        class="inline-flex items-center gap-1.5 rounded-full bg-rose-50 px-3 py-1 text-xs font-bold text-rose-700 ring-1 ring-rose-200 ring-inset">
                        <i class="ri-shield-star-line"></i> {{ __('order::admin.high_risk') }}
                    </span>
                    <span v-else-if="order.fraud_risk"
                        class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-700 ring-1 ring-emerald-200 ring-inset">
                        <i class="ri-shield-check-line"></i> {{ __('order::admin.low_risk') }}
                    </span>
                    <span v-else
                        class="inline-flex items-center gap-1.5 rounded-full bg-skin-neutral-3 px-3 py-1 text-xs font-bold text-skin-neutral-11 ring-1 ring-skin-neutral-4 ring-inset">
                        <i class="ri-shield-line"></i> {{ __('order::admin.not_checked') }}
                    </span>
                </template>

                <div class="space-y-4">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <p v-if="order.fraud_checked_at" class="text-xs text-skin-neutral-9">
                            <i class="ri-time-line mr-1"></i>{{ __('order::admin.last_checked') }} {{ order.fraud_checked_at }}
                        </p>
                        <p v-else class="text-xs text-skin-neutral-9">
                            {{ __('order::admin.fraud_not_run') }}
                        </p>
                        <button type="button"
                            class="inline-flex items-center gap-1.5 rounded-lg bg-skin-neutral-3 px-3 py-1.5 text-xs font-semibold text-skin-neutral-11 transition-colors hover:bg-skin-neutral-4 disabled:opacity-60"
                            :disabled="fraudChecking" @click="runFraudCheck">
                            <i :class="fraudChecking ? 'ri-loader-4-line animate-spin' : 'ri-search-eye-line'"></i>
                            {{ fraudChecking ? __('order::admin.checking') : (order.fraud_checked_at ? __('order::admin.rerun_check') : __('order::admin.run_check')) }}
                        </button>
                    </div>

                    <div v-if="!order.fraud_checked_at"
                        class="rounded-xl border border-dashed border-skin-neutral-4 bg-skin-neutral-2/40 p-4 text-xs text-skin-neutral-9">
                        {{ __('order::admin.fraud_check_hint') }}
                    </div>

                    <template v-else>
                        <div class="grid grid-cols-3 gap-3">
                            <div class="rounded-xl border border-skin-neutral-4 bg-skin-neutral-2/40 p-3 text-center">
                                <p class="text-lg font-extrabold text-skin-neutral-12">{{ fraudAggregate.total_deliveries ?? 0 }}</p>
                                <p class="text-[11px] font-semibold uppercase tracking-wide text-skin-neutral-9">{{ __('order::admin.deliveries') }}</p>
                            </div>
                            <div class="rounded-xl border border-skin-neutral-4 bg-skin-neutral-2/40 p-3 text-center">
                                <p class="text-lg font-extrabold"
                                    :class="fraudCancelRatio >= 40 ? 'text-rose-600' : 'text-skin-neutral-12'">
                                    {{ fraudCancelRatio.toFixed(1) }}%
                                </p>
                                <p class="text-[11px] font-semibold uppercase tracking-wide text-skin-neutral-9">{{ __('order::admin.cancel_ratio') }}</p>
                            </div>
                            <div class="rounded-xl border border-skin-neutral-4 bg-skin-neutral-2/40 p-3 text-center">
                                <p class="text-lg font-extrabold text-skin-neutral-12">{{ Number(fraudAggregate.success_ratio ?? 0).toFixed(1) }}%</p>
                                <p class="text-[11px] font-semibold uppercase tracking-wide text-skin-neutral-9">{{ __('order::admin.success_ratio') }}</p>
                            </div>
                        </div>

                        <div class="space-y-3">
                            <p class="text-xs font-semibold uppercase tracking-wide text-skin-neutral-9">
                                {{ __('order::admin.sources_checked') }}
                            </p>

                            <div v-for="source in fraudSources" :key="source.key"
                                class="rounded-xl border border-skin-neutral-4 bg-skin-neutral-1 p-3">
                                <div class="flex flex-wrap items-center justify-between gap-2">
                                    <p class="flex items-center gap-2 text-sm font-semibold text-skin-neutral-12">
                                        <i :class="source.icon" class="text-skin-neutral-9"></i>
                                        {{ source.label }}
                                    </p>
                                    <span v-if="source.answered"
                                        class="rounded-full bg-skin-success-light px-2 py-0.5 text-[11px] font-semibold text-skin-success">
                                        {{ __('order::admin.answered') }}
                                    </span>
                                    <span v-else
                                        class="rounded-full bg-skin-neutral-3 px-2 py-0.5 text-[11px] font-semibold text-skin-neutral-11">
                                        {{ source.error || __('order::admin.no_data') }}
                                    </span>
                                </div>

                                <div v-if="source.answered" class="mt-3 grid grid-cols-2 gap-2 sm:grid-cols-4">
                                    <div class="rounded-lg bg-skin-neutral-2/60 p-2 text-center">
                                        <p class="text-sm font-bold text-skin-neutral-12">{{ source.stats.total ?? 0 }}</p>
                                        <p class="text-[10px] font-semibold uppercase tracking-wide text-skin-neutral-9">{{ __('order::admin.parcels') }}</p>
                                    </div>
                                    <div class="rounded-lg bg-skin-neutral-2/60 p-2 text-center">
                                        <p class="text-sm font-bold"
                                            :class="sourceCancelRatio(source.stats) >= 40 ? 'text-rose-600' : 'text-skin-neutral-12'">
                                            {{ sourceCancelRatio(source.stats).toFixed(1) }}%
                                        </p>
                                        <p class="text-[10px] font-semibold uppercase tracking-wide text-skin-neutral-9">{{ __('order::admin.cancelled') }}</p>
                                    </div>
                                    <div v-if="source.stats.volume_band" class="rounded-lg bg-skin-neutral-2/60 p-2 text-center">
                                        <p class="text-sm font-bold capitalize text-skin-neutral-12">{{ String(source.stats.volume_band).replace('_', ' ') }}</p>
                                        <p class="text-[10px] font-semibold uppercase tracking-wide text-skin-neutral-9">{{ __('order::admin.volume_band') }}</p>
                                    </div>
                                    <div class="rounded-lg bg-skin-neutral-2/60 p-2 text-center">
                                        <p class="text-sm font-bold"
                                            :class="sourceReportCount(source.stats) > 0 ? 'text-rose-600' : 'text-skin-neutral-12'">
                                            {{ sourceReportCount(source.stats) }}
                                        </p>
                                        <p class="text-[10px] font-semibold uppercase tracking-wide text-skin-neutral-9">{{ __('order::admin.fraud_reports') }}</p>
                                    </div>
                                </div>

                                <div v-if="source.answered && source.key === 'bdcourier' && Object.keys(source.stats.couriers || {}).length"
                                    class="mt-3 overflow-x-auto rounded-lg border border-skin-neutral-4">
                                    <table class="w-full text-xs text-left">
                                        <thead>
                                            <tr
                                                class="border-b border-skin-neutral-4/80 bg-skin-neutral-2/60 font-bold uppercase tracking-wide text-skin-neutral-9">
                                                <th class="px-3 py-2">{{ __('order::admin.courier') }}</th>
                                                <th class="px-3 py-2 text-right">{{ __('order::admin.parcels') }}</th>
                                                <th class="px-3 py-2 text-right">{{ __('order::admin.delivered') }}</th>
                                                <th class="px-3 py-2 text-right">{{ __('order::admin.cancelled') }}</th>
                                                <th class="px-3 py-2 text-right">{{ __('order::admin.success') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="(courier, courierKey) in source.stats.couriers" :key="courierKey"
                                                class="border-b border-skin-neutral-4/60 last:border-0 text-skin-neutral-11">
                                                <td class="px-3 py-2 font-semibold text-skin-neutral-12">{{ courier.name || courierKey }}</td>
                                                <td class="px-3 py-2 text-right">{{ courier.total_parcel }}</td>
                                                <td class="px-3 py-2 text-right">{{ courier.success_parcel }}</td>
                                                <td class="px-3 py-2 text-right"
                                                    :class="courier.cancelled_parcel > 0 ? 'font-semibold text-rose-600' : ''">
                                                    {{ courier.cancelled_parcel }}
                                                </td>
                                                <td class="px-3 py-2 text-right">{{ Number(courier.success_ratio).toFixed(1) }}%</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div v-if="fraudReports.length" class="space-y-2">
                            <p class="text-xs font-semibold uppercase tracking-wide text-skin-neutral-9">
                                {{ __('order::admin.reported_fraud_entries') }}
                            </p>
                            <div v-for="report in fraudReports" :key="report.id"
                                class="flex items-start gap-3 rounded-xl border border-rose-200 bg-rose-50/50 p-3">
                                <img v-if="report.courierLogo" :src="report.courierLogo" :alt="report.courierName || ''"
                                    class="h-7 w-7 shrink-0 rounded-md object-contain" loading="lazy">
                                <div class="min-w-0">
                                    <p class="text-xs font-bold text-rose-800">
                                        {{ report.name || __('order::admin.unknown') }}
                                        <span v-if="report.courierName" class="font-semibold text-rose-600">· {{ report.courierName }}</span>
                                    </p>
                                    <p class="mt-0.5 text-xs text-rose-700">{{ report.details }}</p>
                                    <p v-if="report.created_at" class="mt-0.5 text-[11px] text-rose-500">{{ report.created_at }}</p>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </OrderSectionCard>

            <!-- Payment Transactions Log Card -->
            <OrderSectionCard v-if="order.orderPayments?.length" flush :title="__('order::admin.payment_transactions_log')"
                :description="__('order::admin.payment_transactions_desc')" icon="ri-bank-card-line"
                icon-class="bg-emerald-50 text-emerald-600 ring-1 ring-emerald-500/10" class="no-print">
                <div class="overflow-x-auto">
                    <table class="w-full text-xs text-left">
                        <thead>
                            <tr
                                class="border-b border-skin-neutral-4/80 bg-skin-neutral-2/60 font-bold  tracking-wider text-skin-neutral-9">
                                <th class="px-4 py-3">{{ __('common.header.date') }}</th>
                                <th class="px-3 py-3">{{ __('common.header.method') }}</th>
                                <th class="px-3 py-3">{{ __('common.header.status') }}</th>
                                <th class="px-3 py-3 text-right">{{ __('order::admin.amount') }}</th>
                                <th class="px-4 py-3">{{ __('order::admin.tx_ref') }}</th>
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
                                    formatMoney(payment.amount_paid) }} {{ __('order::admin.tk') }}</td>
                                <td class="px-4 py-3 font-mono text-[11px] text-skin-neutral-10">
                                    <div class="flex items-center gap-1">
                                        <span>{{ payment.transaction_id ?? '—' }}</span>
                                        <button v-if="payment.transaction_id" type="button"
                                            class="text-skin-neutral-7 hover:text-skin-neutral-12"
                                            @click="copyToClipboard(payment.transaction_id, `tx_${payment.id}`)"
                                            :title="__('order::admin.copy_transaction_id')">
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
            <OrderSectionCard :title="__('order::admin.order_status_controller')" :description="__('order::admin.order_status_controller_desc')"
                icon="ri-sound-module-line" icon-class="bg-blue-50 text-blue-600 ring-1 ring-blue-500/10">
                <form @submit.prevent="submitStatus" class="space-y-4">
                    <div>

                        <label for="order-status"
                            class="mb-1.5 block text-xs font-bold  tracking-wider text-skin-neutral-9">
                            {{ __('order::admin.order_status_pipeline') }}

                        </label>
                        <div class="relative">
                            <select id="order-status" v-model="statusForm.status"
                                class="block w-full rounded-xl border border-skin-neutral-6 bg-skin-neutral-2 px-3.5 py-2.5 text-sm font-bold text-skin-neutral-12 focus:border-skin-primary-9 focus:outline-hidden focus:ring-1 focus:ring-skin-primary-9 transition duration-150">
                                <option v-for="s in statuses" :key="s" :value="s" class="capitalize font-semibold">
                                    {{ orderStatusText(s) }}
                                </option>
                            </select>
                        </div>
                    </div>

                    <div>

                        <label for="order-payment-status"
                            class="mb-1.5 block text-xs font-bold  tracking-wider text-skin-neutral-9">
                            {{ __('order::admin.payment_settlement') }}

                        </label>
                        <select id="order-payment-status" v-model="statusForm.payment_status"
                            class="block w-full rounded-xl border border-skin-neutral-6 bg-skin-neutral-2 px-3.5 py-2.5 text-sm font-bold text-skin-neutral-12 focus:border-skin-primary-9 focus:outline-hidden focus:ring-1 focus:ring-skin-primary-9 transition duration-150">
                            <option value="unpaid" class="font-semibold">{{ __('order::enums.payment_status.unpaid') }}</option>
                            <option value="paid" class="font-semibold">{{ __('order::enums.payment_status.paid') }}</option>
                        </select>
                    </div>

                    <AppButton type="submit"
                        class="btn btn-primary w-full inline-flex items-center justify-center gap-2 rounded-xl py-2.5 text-sm font-bold shadow-xs transition duration-150 ease-in-out"
                        :loading="statusForm.processing">
                        <i class="ri-save-line text-base"></i>
                        <span>{{ __('order::admin.save_status_changes') }}</span>
                    </AppButton>
                </form>
            </OrderSectionCard>

            <!-- 3. Logistics & Shipment Details Card -->
            <OrderSectionCard v-if="order.requires_shipping && order.orderShipments?.length"
                :title="__('order::admin.logistics_shipment')" :description="__('order::admin.logistics_shipment_desc')"
                icon="ri-truck-line" icon-class="bg-purple-50 text-purple-600 ring-1 ring-purple-500/10">
                <div class="divide-y divide-skin-neutral-3/70">
                    <div v-for="shipment in order.orderShipments" :key="shipment.id"
                        class="space-y-3 py-3 first:pt-0 last:pb-0">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold  tracking-wider text-skin-neutral-9">{{ __('order::admin.status_colon') }}</span>
                            <span
                                class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-bold capitalize ring-1 ring-inset"
                                :class="shipmentStatusClass(shipment.shopment_status)">
                                {{ shipmentStatusText(shipment.shopment_status) }}
                            </span>
                        </div>
                        <div v-if="shipment.carrier" class="flex items-center justify-between text-xs">
                            <span class="font-bold text-skin-neutral-9 ">{{ __('order::admin.carrier_colon') }}</span>
                            <span class="font-bold text-skin-neutral-12">{{ courierLabel(shipment.carrier) }}</span>
                        </div>
                        <div v-if="shipment.courier_status" class="flex items-center justify-between text-xs">
                            <span class="font-bold text-skin-neutral-9 ">{{ __('order::admin.courier_status_colon') }}</span>
                            <span class="font-semibold capitalize text-skin-neutral-12">
                                {{ shipment.courier_status.replaceAll('_', ' ') }}
                            </span>
                        </div>
                        <div v-if="shipment.consignment_id" class="flex items-center justify-between text-xs">
                            <span class="font-bold text-skin-neutral-9 ">{{ __('order::admin.consignment_id_colon') }}</span>
                            <span class="font-mono font-bold text-skin-neutral-12">{{ shipment.consignment_id }}</span>
                        </div>
                        <div v-if="shipment.tracking_number" class="flex items-center justify-between text-xs">
                            <span class="font-bold text-skin-neutral-9 ">{{ __('order::admin.tracking_colon') }}</span>
                            <div class="flex items-center gap-1.5 font-mono font-bold">
                                <a v-if="shipment.tracking_url" :href="shipment.tracking_url" target="_blank"
                                    rel="noopener noreferrer" class="text-blue-600 hover:underline">
                                    {{ shipment.tracking_number }}
                                </a>
                                <span v-else class="text-skin-neutral-12">{{ shipment.tracking_number }}</span>
                                <button type="button" class="text-skin-neutral-7 hover:text-skin-neutral-12"
                                    @click="copyToClipboard(shipment.tracking_number, `track_${shipment.id}`)"
                                    :title="__('order::admin.copy_tracking_number')">
                                    <i
                                        :class="copiedKey === `track_${shipment.id}` ? 'ri-check-line text-emerald-600' : 'ri-file-copy-line'"></i>
                                </button>
                            </div>
                        </div>
                        <div v-if="shipment.shipment_date" class="flex items-center justify-between text-xs">
                            <span class="font-bold text-skin-neutral-9 ">{{ __('order::admin.dispatch_date_colon') }}</span>
                            <span class="font-semibold text-skin-neutral-12">{{ shipment.shipment_date }}</span>
                        </div>
                        <div v-if="shipment.estimated_delivery" class="flex items-center justify-between text-xs">
                            <span class="font-bold text-skin-neutral-9 ">{{ __('order::admin.est_delivery_colon') }}</span>
                            <span class="font-semibold text-skin-neutral-12">{{ shipment.estimated_delivery }}</span>
                        </div>
                        <div v-if="shipment.actual_delivery" class="flex items-center justify-between text-xs">
                            <span class="font-bold text-skin-neutral-9 ">{{ __('order::admin.actual_delivery_colon') }}</span>
                            <span class="font-bold text-emerald-600">{{ shipment.actual_delivery }}</span>
                        </div>
                        <div v-if="shipment.booked_at" class="flex items-center justify-between text-xs">
                            <span class="font-bold text-skin-neutral-9 ">{{ __('order::admin.booked_at_colon') }}</span>
                            <span class="font-semibold text-skin-neutral-12">{{ shipment.booked_at }}</span>
                        </div>
                        <div v-if="shipment.last_synced_at" class="flex items-center justify-between text-xs">
                            <span class="font-bold text-skin-neutral-9 ">{{ __('order::admin.last_synced_colon') }}</span>
                            <span class="font-semibold text-skin-neutral-12">{{ shipment.last_synced_at }}</span>
                        </div>
                        <div v-if="shipment.booking_error"
                            class="rounded-xl bg-rose-50 px-3 py-2 text-xs font-semibold text-rose-700 ring-1 ring-rose-200 ring-inset">
                            <div class="flex items-start gap-2">
                                <i class="ri-error-warning-line mt-0.5 shrink-0"></i>
                                <span>{{ shipment.booking_error }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-3 flex flex-wrap gap-2 border-t border-skin-neutral-3/70 pt-3">
                    <AppButton v-if="!hasTracking" type="button"
                        class="btn btn-primary inline-flex items-center gap-2 rounded-xl px-4 py-2 text-sm font-bold shadow-xs transition duration-150 ease-in-out"
                        :loading="booking" @click="requestBooking">
                        <i class="ri-truck-line text-base"></i>
                        <span>{{ __('order::admin.book_with', { courier: courierLabel(defaultCourier) }) }}</span>
                    </AppButton>
                    <AppButton v-if="hasTracking" type="button"
                        class="btn btn-neutral inline-flex items-center gap-2 rounded-xl px-4 py-2 text-sm font-bold shadow-xs transition duration-150 ease-in-out"
                        :loading="refreshing" @click="refreshStatus">
                        <i class="ri-refresh-line text-base"></i>
                        <span>{{ __('order::admin.refresh_status') }}</span>
                    </AppButton>
                </div>
            </OrderSectionCard>

        </div>
    </div>
</template>

<script setup>
import { computed, inject, ref } from 'vue'
import { formatMoney } from '@/Utils/formatMoney'
import { Head, router, useForm, usePage } from '@inertiajs/vue3'
import useTitle from '@/Composables/useTitle'
import OrderSectionCard from './Components/OrderSectionCard.vue'

const translate = inject('translate')

const { title } = useTitle(translate('order::admin.order_details'))

const props = defineProps({
    order: {
        type: Object,
        default: () => ({}),
    },
    statuses: {
        type: Array,
        default: () => [],
    },
    defaultCourier: {
        type: String,
        default: 'steadfast',
    },
})

const page = usePage()

const companyInfo = computed(() => {
    const branding = page.props.branding || {}
    const contact = page.props.contact || {}

    const getFirstOrValue = (val) => {
        if (!val) return null
        if (Array.isArray(val)) {
            const first = val.find((item) => item && String(item).trim() !== '')
            return first ? String(first).trim() : null
        }
        const str = String(val).trim()
        return str !== '' ? str : null
    }

    return {
        name: branding.site_name || null,
        logo: branding.logo_url || null,
        phone: getFirstOrValue(contact.phone),
        email: getFirstOrValue(contact.email),
        address: getFirstOrValue(contact.address),
    }
})

const breadCrumb = [
    { label: translate('common.home'), href: route('dashboard.index') },
    { label: translate('order::admin.orders'), href: route('order.index') },
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

const courierLabels = {
    pathao: 'Pathao',
    steadfast: 'Steadfast',
    redx: 'RedX',
    ecourier: 'ECourier',
    paperfly: 'Paperfly',
}

const courierLabel = (provider) => courierLabels[provider] || provider || 'courier'

const STATUS_KEYS = {
    pending: 'order::enums.order_status.pending',
    processing: 'order::enums.order_status.processing',
    shipped: 'order::enums.order_status.shipped',
    delivered: 'order::enums.order_status.delivered',
    completed: 'order::enums.order_status.completed',
    cancelled: 'order::enums.order_status.cancelled',
}
const orderStatusText = (status) => translate(STATUS_KEYS[status] ?? status)

const PAYMENT_STATUS_KEYS = {
    paid: 'order::enums.payment_status.paid',
    unpaid: 'order::enums.payment_status.unpaid',
}
const paymentStatusText = (status) => translate(PAYMENT_STATUS_KEYS[status] ?? status)

const SHIPMENT_STATUS_KEYS = {
    pending: 'order::enums.shipment_status.pending',
    processing: 'order::enums.shipment_status.processing',
    shipped: 'order::enums.shipment_status.shipped',
    delivered: 'order::enums.shipment_status.delivered',
    cancelled: 'order::enums.shipment_status.cancelled',
}
const shipmentStatusText = (status) => translate(SHIPMENT_STATUS_KEYS[status] ?? status)

const hasTracking = computed(() =>
    (props.order.orderShipments || []).some((shipment) => shipment.tracking_number))

const invoiceShipment = computed(() =>
    (props.order.orderShipments || []).find((shipment) => shipment.tracking_number || shipment.consignment_id) || null)

const fraudSummary = computed(() => {
    const aggregate = props.order.fraud_details?.aggregate
    if (!aggregate) {
        return translate('order::admin.fraud_flag_high')
    }
    const ratio = aggregate.cancel_ratio != null ? Number(aggregate.cancel_ratio).toFixed(1) : '?'
    return translate('order::admin.fraud_summary_ratio', { ratio, count: aggregate.total_deliveries ?? 0 })
})

const fraudAggregate = computed(() => props.order.fraud_details?.aggregate || {})

const fraudCancelRatio = computed(() => Number(fraudAggregate.value.cancel_ratio ?? 0))

const fraudSourceLabels = {
    steadfast: { label: 'SteadFast API', icon: 'ri-flashlight-line' },
    bdcourier: { label: 'BD Courier API', icon: 'ri-search-eye-line' },
    pathao: { label: 'Pathao Portal', icon: 'ri-e-bike-line' },
    redx: { label: 'RedX Portal', icon: 'ri-flight-takeoff-line' },
    paperfly: { label: 'Paperfly Portal', icon: 'ri-send-plane-2-line' },
    carrybee: { label: 'Carrybee Portal', icon: 'ri-box-3-line' },
}

const fraudSources = computed(() =>
    Object.entries(props.order.fraud_details || {})
        .filter(([key, value]) => key !== 'aggregate' && value != null)
        .map(([key, value]) => ({
            key,
            label: fraudSourceLabels[key]?.label || key,
            icon: fraudSourceLabels[key]?.icon || 'ri-shield-line',
            answered: Boolean(value) && !value.error,
            error: value?.error || '',
            stats: value || {},
        })))

const fraudReports = computed(() => props.order.fraud_details?.bdcourier?.reports || [])

const sourceCancelRatio = (stats) => {
    if (stats.cancellation_ratio != null) return Number(stats.cancellation_ratio)
    const total = Number(stats.total || 0)
    if (!total) return 0
    return (Number(stats.cancel || 0) / total) * 100
}

const sourceReportCount = (stats) =>
    Number(stats.total_reports || 0) + (stats.reports?.length || 0)

const fraudChecking = ref(false)

const runFraudCheck = () => {
    fraudChecking.value = true
    router.post(route('order.fraudCheck', props.order.id), {}, {
        preserveScroll: true,
        onFinish: () => {
            fraudChecking.value = false
        },
    })
}

const confirmDialogRef = ref(null)
const booking = ref(false)
const refreshing = ref(false)

const bookShipment = (force = false) => {
    booking.value = true
    router.visit(route('order.bookShipment', props.order.id), {
        method: 'post',
        data: { force },
        preserveScroll: true,
        onFinish: () => {
            booking.value = false
        },
    })
}

const requestBooking = () => {
    if (props.order.fraud_risk === 'high') {
        confirmDialogRef.value?.openCustomModal({
            title: translate('order::admin.confirm_book_title'),
            message: translate('order::admin.confirm_book_message'),
            buttonText: translate('order::admin.book_anyway'),
            buttonClass: 'btn btn-primary',
            method: 'post',
            modalType: 'danger',
            helpText: fraudSummary.value,
            onConfirm: () => bookShipment(true),
        })

        return
    }

    bookShipment(false)
}

const refreshStatus = () => {
    refreshing.value = true
    router.visit(route('order.refreshShipment', props.order.id), {
        method: 'post',
        preserveScroll: true,
        onFinish: () => {
            refreshing.value = false
        },
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

const PAYMENT_METHOD_KEYS = {
    cod: 'order::admin.payment_method_cod',
    card: 'order::admin.payment_method_card',
    mobile: 'order::admin.payment_method_mobile',
}
const RAW_METHOD_LABELS = {
    sslcommerz: 'SSLCommerz',
}
const formatPaymentMethod = (method) => {
    if (PAYMENT_METHOD_KEYS[method]) return translate(PAYMENT_METHOD_KEYS[method])
    return RAW_METHOD_LABELS[method] ?? method ?? '—'
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
    { key: 'pending', label: translate('order::admin.step_pending'), icon: 'ri-shopping-cart-2-line' },
    { key: 'processing', label: translate('order::admin.step_processing'), icon: 'ri-loader-3-line' },
    { key: 'shipped', label: translate('order::admin.step_shipped'), icon: 'ri-truck-line' },
    { key: 'completed', label: translate('order::admin.step_completed'), icon: 'ri-checkbox-circle-line' },
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

    /* Force 2-column side-by-side row layouts for A4 portrait print */
    .printable-customer-box {
        display: grid !important;
        grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
        gap: 0.75rem !important;
    }

    .printable-customer-box>div:nth-child(2) {
        border-left: 1px solid #000000 !important;
        padding-left: 0.75rem !important;
    }

    .printable-totals-banner {
        display: flex !important;
        flex-direction: row !important;
        justify-content: space-between !important;
        align-items: center !important;
    }

    .printable-totals-banner .totals-ledger {
        width: 18rem !important;
    }
}
</style>
