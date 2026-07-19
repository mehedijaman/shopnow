<template>
    <div class="space-y-6">
        <div>
            <AppLabel for="default_expiry_days" :value="__('Default Expiry Days')" />
            <p class="mb-1 text-xs text-skin-neutral-9">{{ __('Set to 0 for no expiry.') }}</p>
            <AppInputText
                id="default_expiry_days"
                type="number"
                min="0"
                v-model="form.default_expiry_days"
                :placeholder="__('e.g. 30')"
                :class="{ 'input-error': errorsFields.includes('default_expiry_days') }"
            />
            <p v-if="errorsFields.includes('default_expiry_days')" class="mt-1 text-sm text-red-500">
                {{ errors.default_expiry_days }}
            </p>
        </div>

        <div>
            <AppLabel for="default_limit" :value="__('Default Download Limit')" />
            <p class="mb-1 text-xs text-skin-neutral-9">{{ __('Maximum number of times a file can be downloaded. Set to 0 for unlimited.') }}</p>
            <AppInputText
                id="default_limit"
                type="number"
                min="0"
                v-model="form.default_limit"
                :placeholder="__('e.g. 10')"
                :class="{ 'input-error': errorsFields.includes('default_limit') }"
            />
            <p v-if="errorsFields.includes('default_limit')" class="mt-1 text-sm text-red-500">
                {{ errors.default_limit }}
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
