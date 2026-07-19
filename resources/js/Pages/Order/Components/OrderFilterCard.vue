<template>
    <div class="rounded-xl border border-skin-neutral-4 bg-skin-neutral-2 p-4 shadow-xs mb-5">
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <!-- Status Filter -->
            <div>
                <AppLabel for="status-filter">Order Status</AppLabel>
                <select
                    id="status-filter"
                    v-model="filters.status"
                    class="mt-1 block w-full rounded-md border-0 bg-skin-neutral-1 px-3 py-2 text-skin-neutral-12 placeholder-skin-neutral-9 shadow-xs ring-1 ring-inset ring-skin-neutral-7 focus:ring-2 focus:ring-inset focus:ring-skin-primary-6 sm:text-sm sm:leading-6"
                >
                    <option value="">All Statuses</option>
                    <option v-for="s in statuses" :key="s" :value="s">
                        {{ s.charAt(0).toUpperCase() + s.slice(1) }}
                    </option>
                </select>
            </div>

            <!-- Payment Status Filter -->
            <div>
                <AppLabel for="payment-filter">Payment Status</AppLabel>
                <select
                    id="payment-filter"
                    v-model="filters.payment_status"
                    class="mt-1 block w-full rounded-md border-0 bg-skin-neutral-1 px-3 py-2 text-skin-neutral-12 placeholder-skin-neutral-9 shadow-xs ring-1 ring-inset ring-skin-neutral-7 focus:ring-2 focus:ring-inset focus:ring-skin-primary-6 sm:text-sm sm:leading-6"
                >
                    <option value="">All Payments</option>
                    <option value="paid">Paid</option>
                    <option value="unpaid">Unpaid</option>
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
                Clear Filter
            </AppButton>
            <AppButton
                type="button"
                class="btn btn-primary text-sm"
                @click="apply"
            >
                Apply Filter
            </AppButton>
        </div>
    </div>
</template>

<script setup>
import { ref, watch } from 'vue'

const props = defineProps({
    statuses: { type: Array, default: () => [] },
    initialFilters: { type: Object, default: () => ({}) },
})

const emit = defineEmits(['apply', 'clear'])

const filters = ref({
    status: props.initialFilters?.status ?? '',
    payment_status: props.initialFilters?.payment_status ?? '',
})

// Synchronize local state with initialFilters prop updates
watch(() => props.initialFilters, (newVal) => {
    filters.value = {
        status: newVal?.status ?? '',
        payment_status: newVal?.payment_status ?? '',
    }
}, { deep: true })

const apply = () => {
    emit('apply', { ...filters.value })
}

const clear = () => {
    filters.value = {
        status: '',
        payment_status: '',
    }
    emit('clear')
}
</script>
