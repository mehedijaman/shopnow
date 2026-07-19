<template>
    <div class="rounded-xl border border-skin-neutral-4 bg-skin-neutral-2 p-4 shadow-xs mb-5">
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <!-- Status Filter -->
            <div>
                <AppLabel for="status-filter">Status</AppLabel>
                <select
                    id="status-filter"
                    v-model="filters.active"
                    class="mt-1 block w-full rounded-md border-0 bg-skin-neutral-1 px-3 py-2 text-skin-neutral-12 placeholder-skin-neutral-9 shadow-xs ring-1 ring-inset ring-skin-neutral-7 focus:ring-2 focus:ring-inset focus:ring-skin-primary-6 sm:text-sm sm:leading-6"
                >
                    <option value="">All Statuses</option>
                    <option value="1">Active</option>
                    <option value="0">Inactive</option>
                </select>
            </div>

            <!-- Gender Filter -->
            <div>
                <AppLabel for="gender-filter">Gender</AppLabel>
                <select
                    id="gender-filter"
                    v-model="filters.gender"
                    class="mt-1 block w-full rounded-md border-0 bg-skin-neutral-1 px-3 py-2 text-skin-neutral-12 placeholder-skin-neutral-9 shadow-xs ring-1 ring-inset ring-skin-neutral-7 focus:ring-2 focus:ring-inset focus:ring-skin-primary-6 sm:text-sm sm:leading-6"
                >
                    <option value="">All Genders</option>
                    <option value="male">Male</option>
                    <option value="female">Female</option>
                    <option value="other">Other</option>
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
    initialFilters: { type: Object, default: () => ({}) },
})

const emit = defineEmits(['apply', 'clear'])

const filters = ref({
    active: props.initialFilters?.active ?? '',
    gender: props.initialFilters?.gender ?? '',
})

// Synchronize local state with initialFilters prop updates
watch(() => props.initialFilters, (newVal) => {
    filters.value = {
        active: newVal?.active ?? '',
        gender: newVal?.gender ?? '',
    }
}, { deep: true })

const apply = () => {
    emit('apply', { ...filters.value })
}

const clear = () => {
    filters.value = {
        active: '',
        gender: '',
    }
    emit('clear')
}
</script>
