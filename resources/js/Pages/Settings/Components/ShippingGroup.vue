<template>
    <div class="space-y-6">
        <div>
            <AppLabel for="flat_rate" :value="__('Flat Rate Shipping (Tk)')" />
            <p class="mb-1 text-xs text-skin-neutral-9">{{ __('Default shipping charge added to every order.') }}</p>
            <AppInputText
                id="flat_rate"
                type="number"
                min="0"
                v-model="form.flat_rate"
                :placeholder="__('e.g. 60')"
                :class="{ 'input-error': errorsFields.includes('flat_rate') }"
            />
            <p v-if="errorsFields.includes('flat_rate')" class="mt-1 text-sm text-red-500">
                {{ errors.flat_rate }}
            </p>
        </div>

        <div>
            <AppLabel for="free_shipping_threshold" :value="__('Free Shipping Threshold (Tk)')" />
            <p class="mb-1 text-xs text-skin-neutral-9">{{ __('Orders above this amount qualify for free shipping. Set 0 to disable free shipping.') }}</p>
            <AppInputText
                id="free_shipping_threshold"
                type="number"
                min="0"
                v-model="form.free_shipping_threshold"
                :placeholder="__('e.g. 1000')"
                :class="{ 'input-error': errorsFields.includes('free_shipping_threshold') }"
            />
            <p v-if="errorsFields.includes('free_shipping_threshold')" class="mt-1 text-sm text-red-500">
                {{ errors.free_shipping_threshold }}
            </p>
        </div>
    </div>
</template>

<script setup>
import { inject } from 'vue'
import useFormErrors from '@/Composables/useFormErrors'

defineProps({
    errorsFields: { type: Array, default: () => [] },
})

const form = inject('settingsForm')
const { errors } = useFormErrors()
</script>
