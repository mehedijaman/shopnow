<template>
    <div class="rounded-xl border border-skin-neutral-4 bg-skin-neutral-2 p-4 shadow-xs mb-5">
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <!-- Status Filter -->
            <div>
                <AppLabel for="status-filter">{{ __('order::admin.order_status') }}</AppLabel>
                <select
                    id="status-filter"
                    v-model="filters.status"
                    class="mt-1 block w-full rounded-md border-0 bg-skin-neutral-1 px-3 py-2 text-skin-neutral-12 placeholder-skin-neutral-9 shadow-xs ring-1 ring-inset ring-skin-neutral-7 focus:ring-2 focus:ring-inset focus:ring-skin-primary-6 sm:text-sm sm:leading-6"
                >
                    <option value="">{{ __('common.status.all') }}</option>
                    <option v-for="s in statuses" :key="s" :value="s">
                        {{ orderStatusText(s) }}
                    </option>
                </select>
            </div>

            <!-- Payment Status Filter -->
            <div>
                <AppLabel for="payment-filter">{{ __('order::admin.payment_status') }}</AppLabel>
                <select
                    id="payment-filter"
                    v-model="filters.payment_status"
                    class="mt-1 block w-full rounded-md border-0 bg-skin-neutral-1 px-3 py-2 text-skin-neutral-12 placeholder-skin-neutral-9 shadow-xs ring-1 ring-inset ring-skin-neutral-7 focus:ring-2 focus:ring-inset focus:ring-skin-primary-6 sm:text-sm sm:leading-6"
                >
                    <option value="">{{ __('common.filter.all_payments') }}</option>
                    <option value="paid">{{ __('order::enums.payment_status.paid') }}</option>
                    <option value="unpaid">{{ __('order::enums.payment_status.unpaid') }}</option>
                </select>
            </div>

            <!-- Payment Method Filter -->
            <div>
                <AppLabel for="payment-method-filter">{{ __('order::admin.payment_method') }}</AppLabel>
                <select
                    id="payment-method-filter"
                    v-model="filters.payment_method"
                    class="mt-1 block w-full rounded-md border-0 bg-skin-neutral-1 px-3 py-2 text-skin-neutral-12 placeholder-skin-neutral-9 shadow-xs ring-1 ring-inset ring-skin-neutral-7 focus:ring-2 focus:ring-inset focus:ring-skin-primary-6 sm:text-sm sm:leading-6"
                >
                    <option value="">{{ __('common.filter.all_methods') }}</option>
                    <option v-for="m in paymentMethods" :key="m" :value="m">
                        {{ paymentMethodText(m) }}
                    </option>
                </select>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="mt-4 flex justify-end gap-2 border-t border-skin-neutral-3 pt-3">
            <AppButton
                type="button"
                class="btn btn-secondary text-sm"
                @click="clear"
            >
                {{ __('common.clear_filter') }}
            </AppButton>
            <AppButton
                type="button"
                class="btn btn-primary text-sm"
                @click="apply"
            >
                {{ __('common.apply_filter') }}
            </AppButton>
        </div>
    </div>
</template>

<script setup>
import { ref, watch, inject } from 'vue'

const props = defineProps({
    statuses: { type: Array, default: () => [] },
    paymentMethods: { type: Array, default: () => [] },
    initialFilters: { type: Object, default: () => ({}) },
})

const emit = defineEmits(['apply', 'clear'])

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

const PAYMENT_METHOD_KEYS = {
    cod: 'order::admin.payment_method_cod',
    card: 'order::admin.payment_method_card',
    mobile: 'order::admin.payment_method_mobile',
}
const paymentMethodText = (method) => translate(PAYMENT_METHOD_KEYS[method] ?? method)

const filters = ref({
    status: props.initialFilters?.status ?? '',
    payment_status: props.initialFilters?.payment_status ?? '',
    payment_method: props.initialFilters?.payment_method ?? '',
})

// Synchronize local state with initialFilters prop updates
watch(() => props.initialFilters, (newVal) => {
    filters.value = {
        status: newVal?.status ?? '',
        payment_status: newVal?.payment_status ?? '',
        payment_method: newVal?.payment_method ?? '',
    }
}, { deep: true })

const apply = () => {
    emit('apply', { ...filters.value })
}

const clear = () => {
    filters.value = {
        status: '',
        payment_status: '',
        payment_method: '',
    }
    emit('clear')
}
</script>
