<template>
    <Head :title="title"></Head>

    <AppSectionHeader :title="title" :bread-crumb="breadCrumb">
        <template #right>
            <div class="flex flex-wrap items-center gap-2">
                <AppButton class="btn btn-secondary" @click="$inertia.visit(route('order.index'))">
                    <i class="ri-arrow-left-line mr-1"></i> {{ __('common.back') }}
                </AppButton>
            </div>
        </template>
    </AppSectionHeader>

    <AppFormErrors class="mb-4" />

    <!-- ── Invoice Document ── -->
    <div class="mx-auto w-full max-w-6xl overflow-hidden rounded-2xl border border-skin-neutral-4 bg-white shadow-sm dark:bg-skin-neutral-1">
        <!-- Invoice head -->
        <div class="flex flex-col gap-4 border-b border-skin-neutral-4 bg-skin-neutral-2 px-5 py-5 sm:flex-row sm:items-start sm:justify-between sm:px-8">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.2em] text-skin-neutral-9">{{ __('order::admin.invoice') }}</p>
                <h2 class="mt-1 text-2xl font-extrabold tracking-tight text-skin-neutral-12">{{ __('order::admin.new_order') }}</h2>
                <p class="mt-1 text-sm text-skin-neutral-9">{{ __('order::admin.form_intro') }}</p>
            </div>
            <div class="flex flex-col items-start gap-2 sm:items-end">
                <div class="flex flex-wrap gap-2">
                    <span class="rounded-full px-2.5 py-0.5 text-xs font-medium capitalize" :class="statusBadgeClass">
                        {{ orderStatusText(form.status) }}
                    </span>
                    <span class="rounded-full px-2.5 py-0.5 text-xs font-medium capitalize"
                        :class="isPaid ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700'">
                        {{ paymentStatusText(isPaid ? 'paid' : 'unpaid') }}
                    </span>
                </div>
                <p class="text-sm capitalize text-skin-neutral-9">{{ paymentMethodText }}</p>
            </div>
        </div>

        <!-- Bill To & Order Details -->
        <div class="grid grid-cols-1 gap-6 border-b border-skin-neutral-4 px-5 py-6 sm:grid-cols-2 sm:px-8">
            <div class="space-y-4">
                <p class="text-xs font-bold uppercase tracking-[0.2em] text-skin-neutral-9">{{ __('order::admin.bill_to') }}</p>

                <div>
                    <label for="customer_id" class="block text-xs font-semibold uppercase tracking-wider text-skin-neutral-9">{{ __('order::admin.link_existing_customer') }}</label>
                    <AppCombobox
                        id="customer_id"
                        :model-value="customerOption"
                        :options="customerOptions"
                        :combo-label="__('order::admin.walk_in_customer')"
                        :search-placeholder="__('order::admin.search_customers')"
                        @update:model-value="onCustomerSelect"
                    />
                    <p class="mt-1 text-xs text-skin-neutral-7">{{ __('order::admin.customer_prefill_hint') }}</p>
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label for="name" class="block text-xs font-semibold uppercase tracking-wider text-skin-neutral-9">{{ __('order::admin.customer_name') }}</label>
                        <AppInputText id="name" v-model="form.name" type="text"
                            :class="{ 'input-error': errorsFields.includes('name') }" />
                    </div>
                    <div>
                        <label for="phone" class="block text-xs font-semibold uppercase tracking-wider text-skin-neutral-9">{{ __('common.header.phone') }}</label>
                        <AppInputText id="phone" v-model="form.phone" type="text"
                            :class="{ 'input-error': errorsFields.includes('phone') }" />
                    </div>
                </div>

                <div>
                    <label for="address" class="block text-xs font-semibold uppercase tracking-wider text-skin-neutral-9">{{ __('order::admin.address') }}</label>
                    <AppInputText id="address" v-model="form.address" type="text"
                        :placeholder="__('order::admin.address_placeholder')"
                        :class="{ 'input-error': errorsFields.includes('address') }" />
                </div>
            </div>

            <div class="space-y-4">
                <p class="text-xs font-bold uppercase tracking-[0.2em] text-skin-neutral-9">{{ __('order::admin.order_details') }}</p>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label for="status" class="block text-xs font-semibold uppercase tracking-wider text-skin-neutral-9">{{ __('common.field.status') }}</label>
                        <select
                            id="status"
                            v-model="form.status"
                            class="mt-1 block w-full rounded-md border-0 bg-skin-neutral-1 px-3 py-2 text-skin-neutral-12 shadow-xs ring-1 ring-inset ring-skin-neutral-7 focus:ring-2 focus:ring-inset focus:ring-skin-neutral-7 sm:text-sm sm:leading-6"
                        >
                            <option v-for="status in statuses" :key="status" :value="status">
                                {{ orderStatusText(status) }}
                            </option>
                        </select>
                    </div>
                    <div>
                        <label for="payment_method" class="block text-xs font-semibold uppercase tracking-wider text-skin-neutral-9">{{ __('order::admin.payment_method') }}</label>
                        <select
                            id="payment_method"
                            v-model="form.payment_method"
                            class="mt-1 block w-full rounded-md border-0 bg-skin-neutral-1 px-3 py-2 text-skin-neutral-12 shadow-xs ring-1 ring-inset ring-skin-neutral-7 focus:ring-2 focus:ring-inset focus:ring-skin-neutral-7 sm:text-sm sm:leading-6"
                            :class="{ 'input-error': errorsFields.includes('payment_method') }"
                        >
                            <option value="">{{ __('order::admin.select_method') }}</option>
                            <option v-for="method in paymentMethods" :key="method.value" :value="method.value">
                                {{ method.label }}
                            </option>
                        </select>
                    </div>
                </div>

                <div>
                    <label for="paid" class="block text-xs font-semibold uppercase tracking-wider text-skin-neutral-9">{{ __('order::admin.paid_amount') }}</label>
                    <div class="sm:max-w-[240px]">
                        <AppInputText id="paid" v-model="form.paid" type="number" min="0" step="0.01"
                            :class="{ 'input-error': errorsFields.includes('paid') }" />
                    </div>
                    <p class="mt-1 text-xs text-skin-neutral-7">{{ __('order::admin.balance_hint') }}</p>
                </div>
            </div>
        </div>

        <!-- Items table -->
        <div class="px-5 py-6 sm:px-8">
            <div class="flex items-center justify-between">
                <p class="text-xs font-bold uppercase tracking-[0.2em] text-skin-neutral-9">{{ __('order::admin.items') }}</p>
                <AppButton class="btn btn-neutral btn-sm" @click="addRow">
                    <i class="ri-add-line mr-1"></i> {{ __('order::admin.add_item') }}
                </AppButton>
            </div>

            <div class="mt-4 overflow-x-auto">
                <table class="w-full min-w-[760px] border-collapse text-sm">
                    <thead>
                        <tr class="border-y border-skin-neutral-4 bg-skin-neutral-2 text-xs font-semibold uppercase tracking-wider text-skin-neutral-9">
                            <th class="w-10 px-3 py-2.5 text-left">#</th>
                            <th class="px-3 py-2.5 text-left">{{ __('common.field.description') }}</th>
                            <th class="w-24 px-3 py-2.5 text-center">{{ __('common.header.qty') }}</th>
                            <th class="w-36 px-3 py-2.5 text-right">{{ __('order::admin.unit_price') }}</th>
                            <th class="w-36 px-3 py-2.5 text-right">{{ __('order::admin.amount') }}</th>
                            <th class="w-12 px-3 py-2.5"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-skin-neutral-3">
                        <tr v-if="form.items.length === 0">
                            <td colspan="6" class="px-3 py-8 text-center text-sm text-skin-neutral-9">
                                {{ __('order::admin.no_items_yet') }}
                            </td>
                        </tr>
                        <tr v-for="(row, index) in form.items" :key="'new-' + index" class="align-top">
                            <td class="px-3 py-3 text-skin-neutral-9">{{ index + 1 }}</td>
                            <td class="min-w-[280px] px-3 py-3">
                                <AppCombobox
                                    :model-value="productOption(row)"
                                    :options="productOptions"
                                    :combo-label="__('order::admin.select_product')"
                                    :search-placeholder="__('order::admin.search_products')"
                                    :class="{ 'input-error': errorsFields.includes(`items.${index}.product_id`) }"
                                    @update:model-value="onProductSelect(row, $event)"
                                />

                                <AppCombobox
                                    v-if="rowType(row) === 'variable'"
                                    :model-value="variationOption(row)"
                                    :options="variationOptions(row)"
                                    :combo-label="__('order::admin.select_variation')"
                                    :search-placeholder="__('order::admin.search_variations')"
                                    class="mt-2"
                                    @update:model-value="onVariationSelect(row, $event)"
                                />

                                <p v-if="rowVariationLabel(row)" class="mt-1.5 text-xs text-skin-neutral-9">
                                    {{ rowVariationLabel(row) }}
                                </p>
                                <ul v-if="rowBundleChildren(row).length" class="mt-1.5 space-y-0.5 text-xs text-skin-neutral-9">
                                    <li v-for="child in rowBundleChildren(row)" :key="child.name">
                                        {{ child.name }} — {{ __('order::admin.qty_label') }} {{ child.quantity }}
                                    </li>
                                </ul>
                            </td>
                            <td class="px-3 py-3 text-center">
                                <div class="mx-auto w-20">
                                    <AppInputText v-model="row.quantity" type="number" min="1" step="1"
                                        class="text-center"
                                        :class="{ 'input-error': errorsFields.includes(`items.${index}.quantity`) }" />
                                </div>
                            </td>
                            <td class="px-3 py-3 text-right">
                                <div class="ml-auto w-28">
                                    <AppInputText v-model="row.unit_price" type="number" min="0" step="0.01"
                                        class="text-right"
                                        :class="{ 'input-error': errorsFields.includes(`items.${index}.unit_price`) }" />
                                </div>
                            </td>
                            <td class="whitespace-nowrap px-3 py-3 text-right font-semibold text-skin-neutral-12">
                                {{ formatMoney(lineTotal(row)) }}
                            </td>
                            <td class="px-3 py-3 text-center">
                                <button type="button"
                                    class="flex h-7 w-7 items-center justify-center rounded-md text-red-500 hover:bg-red-50 hover:text-red-600 disabled:opacity-40"
                                    :disabled="form.items.length <= 1" :title="__('order::admin.remove_item')"
                                    @click="removeRow(index)">
                                    <i class="ri-delete-bin-line"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Adjustments & Summary -->
        <div class="grid grid-cols-1 border-t border-skin-neutral-4 lg:grid-cols-2">
            <div class="space-y-5 px-5 py-6 sm:px-8">
                <p class="text-xs font-bold uppercase tracking-[0.2em] text-skin-neutral-9">{{ __('order::admin.adjustments_notes') }}</p>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label for="coupon_code" class="block text-xs font-semibold uppercase tracking-wider text-skin-neutral-9">{{ __('order::admin.coupon_code') }}</label>
                        <div class="flex items-start gap-2">
                            <div class="min-w-0 flex-1">
                                <AppInputText id="coupon_code" v-model="form.coupon_code" type="text"
                                    :placeholder="__('order::admin.coupon_none')"
                                    :class="{ 'input-error': errorsFields.includes('coupon_code') }"
                                    @update:model-value="couponError = ''" />
                            </div>
                            <AppButton class="btn btn-neutral btn-sm mt-1"
                                :disabled="applyingCoupon" @click="applyCoupon">
                                {{ applyingCoupon ? __('order::admin.applying') : __('order::admin.apply') }}
                            </AppButton>
                        </div>
                        <p v-if="couponError" class="mt-1 text-xs text-red-600">{{ couponError }}</p>
                        <p v-else-if="form.coupon_code && !isCouponApplied" class="mt-1 text-xs text-amber-600">
                            {{ __('order::admin.coupon_click_apply') }}
                        </p>
                        <p v-else-if="isCouponApplied && waivesShipping" class="mt-1 text-xs text-skin-neutral-7">
                            {{ __('order::admin.shipping_waived') }}
                        </p>
                    </div>
                    <div>
                        <label for="discount" class="block text-xs font-semibold uppercase tracking-wider text-skin-neutral-9">{{ __('order::admin.discount_tk') }}</label>
                        <AppInputText id="discount" v-model="form.discount" type="number" min="0" step="0.01"
                            :disabled="!!form.coupon_code"
                            :class="{ 'input-error': errorsFields.includes('discount') }" />
                        <p v-if="form.coupon_code" class="mt-1 text-xs text-skin-neutral-7">
                            {{ __('order::admin.discount_from_promo') }}
                        </p>
                    </div>
                    <div>
                        <label for="shipping" class="block text-xs font-semibold uppercase tracking-wider text-skin-neutral-9">{{ __('order::admin.shipping_tk') }}</label>
                        <AppInputText id="shipping" v-model="form.shipping" type="number" min="0" step="0.01"
                            :class="{ 'input-error': errorsFields.includes('shipping') }" />
                    </div>
                </div>

                <div>
                    <label for="notes" class="block text-xs font-semibold uppercase tracking-wider text-skin-neutral-9">{{ __('order::admin.notes') }}</label>
                    <AppTextArea id="notes" v-model="form.notes" :auto-resize="false"
                        :placeholder="__('order::admin.notes_placeholder')"
                        :class="{ 'input-error': errorsFields.includes('notes') }"></AppTextArea>
                </div>
            </div>

            <div class="border-t border-skin-neutral-4 bg-skin-neutral-2 px-5 py-6 sm:px-8 lg:border-l lg:border-t-0">
                <p class="text-xs font-bold uppercase tracking-[0.2em] text-skin-neutral-9">{{ __('common.field.summary') }}</p>

                <dl class="mt-4 space-y-2.5 text-sm">
                    <div class="flex justify-between gap-4">
                        <dt class="text-skin-neutral-9">{{ __('order::admin.subtotal') }}</dt>
                        <dd class="font-semibold text-skin-neutral-12">{{ formatMoney(subtotal) }}</dd>
                    </div>
                    <div class="flex justify-between gap-4">
                        <dt class="text-skin-neutral-9">{{ __('order::admin.shipping') }}</dt>
                        <dd class="font-semibold text-skin-neutral-12">{{ formatMoney(previewShipping) }}</dd>
                    </div>
                    <div class="flex justify-between gap-4">
                        <dt class="text-skin-neutral-9">{{ __('order::admin.discount') }}</dt>
                        <dd class="font-semibold text-red-600">−{{ formatMoney(effectiveDiscount) }}</dd>
                    </div>
                </dl>

                <div class="mt-4 flex items-center justify-between gap-4 rounded-lg bg-skin-primary-9 px-4 py-3 text-skin-primary-1">
                    <span class="text-sm font-bold uppercase tracking-wider">{{ __('order::admin.grand_total') }}</span>
                    <span class="text-lg font-extrabold">{{ formatMoney(previewTotal) }}</span>
                </div>

                <dl class="mt-4 space-y-2.5 text-sm">
                    <div class="flex justify-between gap-4">
                        <dt class="text-skin-neutral-9">{{ __('order::admin.paid') }}</dt>
                        <dd class="font-semibold text-green-600">{{ formatMoney(Number(form.paid || 0)) }}</dd>
                    </div>
                    <div class="flex justify-between gap-4">
                        <dt class="font-semibold text-skin-neutral-12">{{ __('order::admin.balance_due') }}</dt>
                        <dd class="font-bold" :class="previewDue > 0 ? 'text-red-600' : 'text-green-600'">
                            {{ formatMoney(previewDue) }}
                        </dd>
                    </div>
                </dl>

                <p class="mt-4 text-xs text-skin-neutral-7">
                    {{ __('order::admin.totals_note') }}
                </p>

                <AppButton class="btn btn-primary mt-5 w-full justify-center" :disabled="form.processing" @click="submitForm">
                    {{ form.processing ? __('common.saving') : __('common.menu.create_order') }}
                </AppButton>
            </div>
        </div>
    </div>
</template>

<script setup>
import axios from 'axios'
import { computed, inject, ref, watch } from 'vue'
import { Head, useForm } from '@inertiajs/vue3'

import useTitle from '@/Composables/useTitle'
import useFormErrors from '@/Composables/useFormErrors'
import { formatMoney } from '@/Utils/formatMoney'

const props = defineProps({
    products: { type: Array, default: () => [] },
    statuses: { type: Array, default: () => [] },
    paymentMethods: { type: Array, default: () => [] },
    customers: { type: Array, default: () => [] },
})

const translate = inject('translate')

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

const breadCrumb = [
    { label: translate('common.home'), href: route('dashboard.index') },
    { label: translate('order::admin.orders'), href: route('order.index') },
    { label: translate('common.create'), last: true },
]

const { title } = useTitle(translate('order::admin.order'))
const { errorsFields } = useFormErrors()

const blankRow = () => ({
    id: null,
    product_id: null,
    product_variation_id: null,
    quantity: 1,
    unit_price: 0,
    discount: 0,
    product_type: null,
    variation_label: null,
    bundle_items: [],
})

const form = useForm({
    customer_id: null,
    name: '',
    phone: '',
    address: '',
    notes: '',
    status: 'pending',
    payment_method: '',
    paid: 0,
    shipping: 0,
    discount: 0,
    coupon_code: '',
    items: [blankRow()],
})

const statusBadgeClass = computed(() => {
    const map = {
        pending: 'bg-yellow-100 text-yellow-700',
        processing: 'bg-blue-100 text-blue-700',
        shipped: 'bg-purple-100 text-purple-700',
        delivered: 'bg-indigo-100 text-indigo-700',
        completed: 'bg-green-100 text-green-700',
        cancelled: 'bg-red-100 text-red-700',
    }
    return map[form.status] ?? 'bg-skin-neutral-3 text-skin-neutral-11'
})

const paymentMethodText = computed(() => {
    if (!form.payment_method) return translate('order::admin.no_payment_method')
    const method = props.paymentMethods.find((m) => m.value === form.payment_method)
    return translate('order::admin.payment_via', { method: method?.label ?? form.payment_method })
})

/* ── Customer picker ── */
const customersById = computed(() =>
    Object.fromEntries(props.customers.map((c) => [c.id, c]))
)

const customerOption = computed(() => {
    if (!form.customer_id) return null
    const customer = customersById.value[form.customer_id]
    if (!customer) {
        return { value: form.customer_id, label: translate('order::admin.customer_ref', { id: form.customer_id }) }
    }
    return { value: customer.id, label: customer.name + ' (' + customer.phone + ')' }
})

const customerOptions = computed(() =>
    props.customers.map((c) => ({ value: c.id, label: c.name + ' (' + c.phone + ')' }))
)

const onCustomerSelect = (option) => {
    form.customer_id = option?.value ?? null
    const customer = option ? customersById.value[option.value] : null
    if (!customer) return
    if (!form.name) form.name = customer.name
    if (!form.phone) form.phone = customer.phone
}

/* ── Item pickers ── */
const productsById = computed(() =>
    Object.fromEntries(props.products.map((p) => [p.id, p]))
)

const TYPE_LABELS = {
    simple: 'product::admin.simple_product',
    variable: 'product::admin.variable_product',
    bundle: 'product::admin.bundle_product',
}
const typeLabel = (type) => translate(TYPE_LABELS[type] ?? type)

const productLabel = (p) => p.name + (p.type !== 'simple' ? ' (' + typeLabel(p.type) + ')' : '')

const productOptions = computed(() =>
    props.products.map((p) => ({ value: p.id, label: productLabel(p) }))
)

const productOption = (row) => {
    if (!row.product_id) return null
    const product = productsById.value[row.product_id]
    if (!product) {
        return { value: row.product_id, label: translate('order::admin.product_ref', { id: row.product_id }) }
    }
    return { value: row.product_id, label: productLabel(product) }
}

const selectedProduct = (row) => productsById.value[row.product_id] ?? null

const rowType = (row) => selectedProduct(row)?.type ?? row.product_type ?? null

const rowVariations = (row) => selectedProduct(row)?.variations ?? []

const rowVariationLabel = (row) => {
    if (!row.product_variation_id) return null
    const variation = rowVariations(row).find((v) => v.id === row.product_variation_id)
    return variation?.label ? translate('order::admin.option_label', { label: variation.label }) : row.variation_label
}

const variationLabel = (v) =>
    (v.label || translate('order::admin.variation_ref', { id: v.id })) +
    ' (' +
    formatMoney(v.sale_price > 0 ? v.sale_price : v.price) +
    ', ' +
    translate('order::admin.stock_label') +
    ' ' +
    v.quantity +
    ')'

const variationOptions = (row) =>
    rowVariations(row).map((v) => ({ value: v.id, label: variationLabel(v) }))

const variationOption = (row) => {
    if (!row.product_variation_id) return null
    const variation = rowVariations(row).find((v) => v.id === row.product_variation_id)
    return {
        value: row.product_variation_id,
        label: variation
            ? variationLabel(variation)
            : row.variation_label || translate('order::admin.variation_ref', { id: row.product_variation_id }),
    }
}

const onVariationSelect = (row, option) => {
    row.product_variation_id = option?.value ?? null
    onVariationChange(row)
}

const rowBundleChildren = (row) => {
    if (rowType(row) !== 'bundle') return []
    const product = selectedProduct(row)
    if (!product) return []
    const bundleQty = Number(row.quantity || 0)
    return (product.bundle_items ?? []).map((bi) => ({
        name: bi.name,
        quantity: bi.quantity * bundleQty,
    }))
}

const defaultPrice = (product, variation) => {
    if (variation) {
        return Number(variation.sale_price) > 0 ? Number(variation.sale_price) : Number(variation.price)
    }
    if (!product) return 0
    return Number(product.sale_price) > 0 ? Number(product.sale_price) : Number(product.price)
}

const onProductSelect = (row, option) => {
    row.product_id = option?.value ?? null
    onProductChange(row)
}

const onProductChange = (row) => {
    row.product_variation_id = null
    row.variation_label = null
    const product = selectedProduct(row)
    row.unit_price = product ? defaultPrice(product, null) : 0
}

const onVariationChange = (row) => {
    const product = selectedProduct(row)
    const variation = rowVariations(row).find((v) => v.id === row.product_variation_id)
    row.unit_price = defaultPrice(product, variation)
    row.variation_label = variation?.label ?? null
}

const addRow = () => form.items.push(blankRow())

const removeRow = (index) => {
    if (form.items.length > 1) form.items.splice(index, 1)
}

const lineTotal = (row) =>
    Number(row.unit_price || 0) * Number(row.quantity || 0) - Number(row.discount || 0)

const subtotal = computed(() =>
    form.items
        .filter((row) => row.product_id)
        .reduce((sum, row) => sum + lineTotal(row), 0)
)

/* ── Coupon apply (order-less preview) ── */
const appliedCoupon = ref(null)
const couponError = ref('')
const applyingCoupon = ref(false)
const waivesShipping = ref(false)

const isCouponApplied = computed(() => {
    if (!form.coupon_code || !appliedCoupon.value) return false
    return appliedCoupon.value.code.toUpperCase() === String(form.coupon_code).trim().toUpperCase()
})

const applyCoupon = async () => {
    const code = String(form.coupon_code ?? '').trim()
    couponError.value = ''
    if (!code) {
        appliedCoupon.value = null
        waivesShipping.value = false
        return
    }
    applyingCoupon.value = true
    try {
        const { data } = await axios.post(route('order.couponPreview'), {
            coupon_code: code,
            subtotal: subtotal.value,
            customer_id: form.customer_id,
        })
        appliedCoupon.value = { code: data.coupon_code, discount: Number(data.discount || 0) }
        waivesShipping.value = Boolean(data.waives_shipping)
        form.coupon_code = data.coupon_code
    } catch (error) {
        const errors = error.response?.data?.errors
        couponError.value = errors?.coupon_code?.[0] ?? translate('order::admin.coupon_apply_failed')
    } finally {
        applyingCoupon.value = false
    }
}

const effectiveDiscount = computed(() => {
    if (!form.coupon_code) return Number(form.discount || 0)
    return isCouponApplied.value ? appliedCoupon.value.discount : 0
})

watch(
    () => form.coupon_code,
    (value) => {
        if (!String(value ?? '').trim()) {
            appliedCoupon.value = null
            waivesShipping.value = false
            couponError.value = ''
            form.discount = 0
        }
    }
)

const previewShipping = computed(() =>
    isCouponApplied.value && waivesShipping.value ? 0 : Number(form.shipping || 0)
)

const previewTotal = computed(() =>
    Math.max(0, Number(subtotal.value) + previewShipping.value - effectiveDiscount.value)
)

const previewDue = computed(() => Math.max(0, previewTotal.value - Number(form.paid || 0)))

const isPaid = computed(() => Number(form.paid || 0) >= previewTotal.value)

const submitForm = () => {
    form.post(route('order.store'), { preserveScroll: true })
}
</script>
