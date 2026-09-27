<template>
    <Head :title="title"></Head>

    <AppSectionHeader :title="title" :bread-crumb="breadCrumb">
        <template #right>
            <div class="flex flex-wrap items-center gap-2">
                <AppButton class="btn btn-secondary" @click="$inertia.visit(route('order.show', order.id))">
                    <i class="ri-arrow-left-line mr-1"></i> Back
                </AppButton>
                <AppButton class="btn btn-primary" :disabled="form.processing" @click="submitForm">
                    <i class="ri-save-3-line mr-1"></i> {{ form.processing ? 'Saving…' : __('Save') }}
                </AppButton>
            </div>
        </template>
    </AppSectionHeader>

    <AppFormErrors class="mb-4" />

    <AppAlert v-if="lockedItems" type="warning" class="mb-6">
        Line items and totals are locked because this order is shipped, completed or booked with a courier.
        Customer details and notes can still be updated.
    </AppAlert>

    <!-- ── Invoice Document ── -->
    <div class="mx-auto w-full max-w-6xl overflow-hidden rounded-2xl border border-skin-neutral-4 bg-white shadow-sm dark:bg-skin-neutral-1">
        <!-- Invoice head -->
        <div class="flex flex-col gap-4 border-b border-skin-neutral-4 bg-skin-neutral-2 px-5 py-5 sm:flex-row sm:items-start sm:justify-between sm:px-8">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.2em] text-skin-neutral-9">Invoice</p>
                <h2 class="mt-1 text-2xl font-extrabold tracking-tight text-skin-neutral-12">Order #{{ order.id }}</h2>
                <p class="mt-1 text-sm text-skin-neutral-9">Placed on {{ order.created_at }}</p>
            </div>
            <div class="flex flex-col items-start gap-2 sm:items-end">
                <div class="flex flex-wrap gap-2">
                    <span class="rounded-full px-2.5 py-0.5 text-xs font-medium capitalize" :class="statusBadgeClass">
                        {{ order.status }}
                    </span>
                    <span class="rounded-full px-2.5 py-0.5 text-xs font-medium capitalize"
                        :class="order.payment_status === 'paid' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700'">
                        {{ order.payment_status }}
                    </span>
                </div>
                <p class="text-sm capitalize text-skin-neutral-9">
                    {{ order.payment_method ? 'Payment via ' + order.payment_method : 'No payment method' }}
                </p>
            </div>
        </div>

        <!-- Bill To -->
        <div class="grid grid-cols-1 gap-6 border-b border-skin-neutral-4 px-5 py-6 sm:grid-cols-3 sm:px-8">
            <div>
                <label for="name" class="block text-xs font-semibold uppercase tracking-wider text-skin-neutral-9">Customer Name</label>
                <AppInputText id="name" v-model="form.name" type="text"
                    :class="{ 'input-error': errorsFields.includes('name') }" />
            </div>
            <div>
                <label for="phone" class="block text-xs font-semibold uppercase tracking-wider text-skin-neutral-9">Phone</label>
                <AppInputText id="phone" v-model="form.phone" type="text"
                    :class="{ 'input-error': errorsFields.includes('phone') }" />
            </div>
            <div>
                <label for="address" class="block text-xs font-semibold uppercase tracking-wider text-skin-neutral-9">Address</label>
                <AppInputText id="address" v-model="form.address" type="text"
                    placeholder="House, road, area, city"
                    :class="{ 'input-error': errorsFields.includes('address') }" />
            </div>
        </div>

        <!-- Items table -->
        <div class="px-5 py-6 sm:px-8">
            <div class="flex items-center justify-between">
                <p class="text-xs font-bold uppercase tracking-[0.2em] text-skin-neutral-9">Items</p>
                <AppButton v-if="!lockedItems" class="btn btn-neutral btn-sm" @click="addRow">
                    <i class="ri-add-line mr-1"></i> Add Item
                </AppButton>
            </div>

            <div class="mt-4 overflow-x-auto">
                <table class="w-full min-w-[760px] border-collapse text-sm">
                    <thead>
                        <tr class="border-y border-skin-neutral-4 bg-skin-neutral-2 text-xs font-semibold uppercase tracking-wider text-skin-neutral-9">
                            <th class="w-10 px-3 py-2.5 text-left">#</th>
                            <th class="px-3 py-2.5 text-left">Description</th>
                            <th class="w-24 px-3 py-2.5 text-center">Qty</th>
                            <th class="w-36 px-3 py-2.5 text-right">Unit Price</th>
                            <th class="w-36 px-3 py-2.5 text-right">Amount</th>
                            <th class="w-12 px-3 py-2.5"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-skin-neutral-3">
                        <tr v-if="form.items.length === 0">
                            <td colspan="6" class="px-3 py-8 text-center text-sm text-skin-neutral-9">
                                No items on this order yet.
                            </td>
                        </tr>
                        <tr v-for="(row, index) in form.items" :key="row.id ?? 'new-' + index" class="align-top">
                            <td class="px-3 py-3 text-skin-neutral-9">{{ index + 1 }}</td>
                            <td class="min-w-[280px] px-3 py-3">
                                <AppCombobox
                                    :model-value="productOption(row)"
                                    :options="productOptions"
                                    combo-label="Select product"
                                    search-placeholder="Search products"
                                    :class="{
                                        'input-error': errorsFields.includes(`items.${index}.product_id`),
                                        'pointer-events-none opacity-60': lockedItems,
                                    }"
                                    @update:model-value="onProductSelect(row, $event)"
                                />

                                <AppCombobox
                                    v-if="rowType(row) === 'variable'"
                                    :model-value="variationOption(row)"
                                    :options="variationOptions(row)"
                                    combo-label="Select variation"
                                    search-placeholder="Search variations"
                                    class="mt-2"
                                    :class="{ 'pointer-events-none opacity-60': lockedItems }"
                                    @update:model-value="onVariationSelect(row, $event)"
                                />

                                <p v-if="rowVariationLabel(row)" class="mt-1.5 text-xs text-skin-neutral-9">
                                    {{ rowVariationLabel(row) }}
                                </p>
                                <ul v-if="rowBundleChildren(row).length" class="mt-1.5 space-y-0.5 text-xs text-skin-neutral-9">
                                    <li v-for="child in rowBundleChildren(row)" :key="child.name">
                                        {{ child.name }} — qty {{ child.quantity }}
                                    </li>
                                </ul>
                            </td>
                            <td class="px-3 py-3 text-center">
                                <div class="mx-auto w-20">
                                    <AppInputText v-model="row.quantity" type="number" min="1" step="1"
                                        class="text-center" :disabled="lockedItems"
                                        :class="{ 'input-error': errorsFields.includes(`items.${index}.quantity`) }" />
                                </div>
                            </td>
                            <td class="px-3 py-3 text-right">
                                <div class="ml-auto w-28">
                                    <AppInputText v-model="row.unit_price" type="number" min="0" step="0.01"
                                        class="text-right" :disabled="lockedItems"
                                        :class="{ 'input-error': errorsFields.includes(`items.${index}.unit_price`) }" />
                                </div>
                            </td>
                            <td class="whitespace-nowrap px-3 py-3 text-right font-semibold text-skin-neutral-12">
                                {{ formatMoney(lineTotal(row)) }}
                            </td>
                            <td class="px-3 py-3 text-center">
                                <button v-if="!lockedItems" type="button"
                                    class="flex h-7 w-7 items-center justify-center rounded-md text-red-500 hover:bg-red-50 hover:text-red-600 disabled:opacity-40"
                                    :disabled="form.items.length <= 1" title="Remove item"
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
                <p class="text-xs font-bold uppercase tracking-[0.2em] text-skin-neutral-9">Adjustments &amp; Notes</p>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label for="coupon_code" class="block text-xs font-semibold uppercase tracking-wider text-skin-neutral-9">Coupon Code</label>
                        <div class="flex items-start gap-2">
                            <div class="min-w-0 flex-1">
                                <AppInputText id="coupon_code" v-model="form.coupon_code" type="text"
                                    placeholder="None" :disabled="lockedItems"
                                    :class="{ 'input-error': errorsFields.includes('coupon_code') }"
                                    @update:model-value="couponError = ''" />
                            </div>
                            <AppButton class="btn btn-neutral btn-sm mt-1"
                                :disabled="lockedItems || applyingCoupon" @click="applyCoupon">
                                {{ applyingCoupon ? 'Applying…' : 'Apply' }}
                            </AppButton>
                        </div>
                        <p v-if="couponError" class="mt-1 text-xs text-red-600">{{ couponError }}</p>
                        <p v-else-if="form.coupon_code && !isCouponApplied" class="mt-1 text-xs text-amber-600">
                            Click Apply to calculate this code.
                        </p>
                        <p v-else-if="isCouponApplied && waivesShipping" class="mt-1 text-xs text-skin-neutral-7">
                            Shipping will be waived when you save.
                        </p>
                    </div>
                    <div>
                        <label for="discount" class="block text-xs font-semibold uppercase tracking-wider text-skin-neutral-9">Discount (Tk)</label>
                        <AppInputText id="discount" v-model="form.discount" type="number" min="0" step="0.01"
                            :disabled="lockedItems || !!form.coupon_code"
                            :class="{ 'input-error': errorsFields.includes('discount') }" />
                        <p v-if="form.coupon_code" class="mt-1 text-xs text-skin-neutral-7">
                            Calculated from the promo code.
                        </p>
                    </div>
                    <div>
                        <label for="shipping" class="block text-xs font-semibold uppercase tracking-wider text-skin-neutral-9">Shipping (Tk)</label>
                        <AppInputText id="shipping" v-model="form.shipping" type="number" min="0" step="0.01"
                            :disabled="lockedItems" :class="{ 'input-error': errorsFields.includes('shipping') }" />
                    </div>
                </div>

                <div>
                    <label for="notes" class="block text-xs font-semibold uppercase tracking-wider text-skin-neutral-9">Notes</label>
                    <AppTextArea id="notes" v-model="form.notes" :auto-resize="false"
                        placeholder="Internal notes for this order"
                        :class="{ 'input-error': errorsFields.includes('notes') }"></AppTextArea>
                </div>
            </div>

            <div class="border-t border-skin-neutral-4 bg-skin-neutral-2 px-5 py-6 sm:px-8 lg:border-l lg:border-t-0">
                <p class="text-xs font-bold uppercase tracking-[0.2em] text-skin-neutral-9">Summary</p>

                <dl class="mt-4 space-y-2.5 text-sm">
                    <div class="flex justify-between gap-4">
                        <dt class="text-skin-neutral-9">Subtotal</dt>
                        <dd class="font-semibold text-skin-neutral-12">{{ formatMoney(subtotal) }}</dd>
                    </div>
                    <div class="flex justify-between gap-4">
                        <dt class="text-skin-neutral-9">Shipping</dt>
                        <dd class="font-semibold text-skin-neutral-12">{{ formatMoney(previewShipping) }}</dd>
                    </div>
                    <div class="flex justify-between gap-4">
                        <dt class="text-skin-neutral-9">Tax</dt>
                        <dd class="font-semibold text-skin-neutral-12">{{ formatMoney(Number(form.tax || 0)) }}</dd>
                    </div>
                    <div class="flex justify-between gap-4">
                        <dt class="text-skin-neutral-9">Discount</dt>
                        <dd class="font-semibold text-red-600">−{{ formatMoney(effectiveDiscount) }}</dd>
                    </div>
                </dl>

                <div class="mt-4 flex items-center justify-between gap-4 rounded-lg bg-skin-primary-9 px-4 py-3 text-skin-primary-1">
                    <span class="text-sm font-bold uppercase tracking-wider">Grand Total</span>
                    <span class="text-lg font-extrabold">{{ formatMoney(previewTotal) }}</span>
                </div>

                <dl class="mt-4 space-y-2.5 text-sm">
                    <div class="flex justify-between gap-4">
                        <dt class="text-skin-neutral-9">Paid</dt>
                        <dd class="font-semibold text-green-600">{{ formatMoney(order.paid) }}</dd>
                    </div>
                    <div class="flex justify-between gap-4">
                        <dt class="font-semibold text-skin-neutral-12">Balance Due</dt>
                        <dd class="font-bold" :class="previewDue > 0 ? 'text-red-600' : 'text-green-600'">
                            {{ formatMoney(previewDue) }}
                        </dd>
                    </div>
                </dl>

                <p class="mt-4 text-xs text-skin-neutral-7">
                    Totals are recalculated on the server when you save.
                </p>

                <AppButton class="btn btn-primary mt-5 w-full justify-center" :disabled="form.processing" @click="submitForm">
                    {{ form.processing ? 'Saving…' : __('Save Changes') }}
                </AppButton>
            </div>
        </div>
    </div>
</template>

<script setup>
import axios from 'axios'
import { computed, ref, watch } from 'vue'
import { Head, useForm } from '@inertiajs/vue3'

import useTitle from '@/Composables/useTitle'
import useFormErrors from '@/Composables/useFormErrors'
import { formatMoney } from '@/Utils/formatMoney'

const props = defineProps({
    order: { type: Object, required: true },
    products: { type: Array, default: () => [] },
    lockedItems: { type: Boolean, default: false },
})

const breadCrumb = [
    { label: 'Home', href: route('dashboard.index') },
    { label: 'Orders', href: route('order.index') },
    { label: 'Order #' + props.order.id, href: route('order.show', props.order.id) },
    { label: 'Edit', last: true },
]

const { title } = useTitle('Order')
const { errorsFields } = useFormErrors()

const statusBadgeClass = computed(() => {
    const map = {
        pending: 'bg-yellow-100 text-yellow-700',
        processing: 'bg-blue-100 text-blue-700',
        shipped: 'bg-purple-100 text-purple-700',
        delivered: 'bg-indigo-100 text-indigo-700',
        completed: 'bg-green-100 text-green-700',
        cancelled: 'bg-red-100 text-red-700',
    }
    return map[props.order.status] ?? 'bg-skin-neutral-3 text-skin-neutral-11'
})

const mapRow = (row) => ({
    id: row.id,
    product_id: row.product_id,
    product_variation_id: row.product_variation_id ?? null,
    quantity: row.quantity,
    unit_price: row.unit_price,
    discount: row.discount ?? 0,
    product_type: row.product_type ?? null,
    variation_label: row.variation_label ?? null,
    bundle_items: row.bundle_items ?? [],
})

const form = useForm({
    name: props.order.name ?? '',
    phone: props.order.phone ?? '',
    address: props.order.address ?? '',
    notes: props.order.notes ?? '',
    shipping: props.order.shipping ?? 0,
    tax: props.order.tax ?? 0,
    discount: props.order.discount ?? 0,
    coupon_code: props.order.coupon_code ?? '',
    items: (props.order.orderProducts ?? []).map(mapRow),
})

const productsById = computed(() =>
    Object.fromEntries(props.products.map((p) => [p.id, p]))
)

const productLabel = (p) => p.name + (p.type !== 'simple' ? ' (' + p.type + ')' : '')

const productOptions = computed(() =>
    props.products.map((p) => ({ value: p.id, label: productLabel(p) }))
)

const productOption = (row) => {
    if (!row.product_id) return null
    const product = productsById.value[row.product_id]
    if (!product) return { value: row.product_id, label: 'Product #' + row.product_id }
    return { value: row.product_id, label: productLabel(product) }
}

const selectedProduct = (row) => productsById.value[row.product_id] ?? null

const rowType = (row) => selectedProduct(row)?.type ?? row.product_type ?? null

const rowVariations = (row) => selectedProduct(row)?.variations ?? []

const rowVariationLabel = (row) => {
    if (!row.product_variation_id) return null
    const variation = rowVariations(row).find((v) => v.id === row.product_variation_id)
    return variation?.label ? 'Option: ' + variation.label : row.variation_label
}

const variationLabel = (v) =>
    (v.label || 'Variation #' + v.id) +
    ' (' +
    formatMoney(v.sale_price > 0 ? v.sale_price : v.price) +
    ', stock ' +
    v.quantity +
    ')'

const variationOptions = (row) =>
    rowVariations(row).map((v) => ({ value: v.id, label: variationLabel(v) }))

const variationOption = (row) => {
    if (!row.product_variation_id) return null
    const variation = rowVariations(row).find((v) => v.id === row.product_variation_id)
    return {
        value: row.product_variation_id,
        label: variation ? variationLabel(variation) : row.variation_label || 'Variation #' + row.product_variation_id,
    }
}

const onVariationSelect = (row, option) => {
    row.product_variation_id = option?.value ?? null
    onVariationChange(row)
}

const rowBundleChildren = (row) => {
    if (rowType(row) !== 'bundle') return []
    const product = selectedProduct(row)
    if (!product) {
        return (row.bundle_items ?? []).map((bi) => ({ name: bi.name, quantity: bi.quantity }))
    }
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

const addRow = () => {
    form.items.push({
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
}

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

const appliedCoupon = ref(
    props.order.coupon_code
        ? { code: props.order.coupon_code, discount: Number(props.order.discount || 0) }
        : null
)
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
        const { data } = await axios.put(route('order.applyCoupon', props.order.id), {
            coupon_code: code,
            subtotal: subtotal.value,
        })
        appliedCoupon.value = { code: data.coupon_code, discount: Number(data.discount || 0) }
        waivesShipping.value = Boolean(data.waives_shipping)
        form.coupon_code = data.coupon_code
    } catch (error) {
        const errors = error.response?.data?.errors
        couponError.value = errors?.coupon_code?.[0] ?? 'Could not apply this coupon code.'
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
    Math.max(
        0,
        Number(subtotal.value) + Number(form.tax || 0) + previewShipping.value - effectiveDiscount.value
    )
)

const previewDue = computed(() =>
    Math.max(0, previewTotal.value - Number(props.order.paid || 0))
)

const submitForm = () => {
    form.put(route('order.update', props.order.id), { preserveScroll: true })
}
</script>
