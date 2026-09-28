<template>
    <div class="space-y-6">
        <div class="rounded-lg border border-blue-200 bg-blue-50 px-4 py-3 text-sm text-blue-800">
            {{ __('settings::admin.analytics_notice') }}
        </div>

        <div class="divide-y divide-skin-neutral-3">
            <div class="flex items-center justify-between gap-4 py-4">
                <div>
                    <p class="text-sm font-medium text-skin-neutral-12">{{ __('settings::admin.enable_analytics') }}</p>
                    <p class="mt-0.5 text-xs text-skin-neutral-9">{{ __('settings::admin.enable_analytics_hint') }}</p>
                </div>
                <button
                    type="button"
                    role="switch"
                    :aria-checked="form.enabled"
                    class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer items-center rounded-full transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-skin-primary-8 focus:ring-offset-2"
                    :class="form.enabled ? 'bg-skin-primary-10' : 'bg-skin-neutral-5'"
                    @click="form.enabled = !form.enabled"
                >
                    <span
                        class="inline-block h-4 w-4 transform rounded-full bg-white shadow-sm transition-transform duration-200"
                        :class="form.enabled ? 'translate-x-6' : 'translate-x-1'"
                    />
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
            <div class="sm:col-span-2">
                <AppLabel for="ga_measurement_id" :value="__('Google Analytics Measurement ID')" />
                <p class="mb-1 text-xs text-skin-neutral-9">{{ __('settings::admin.ga_measurement_id_hint') }}</p>
                <AppInputText
                    id="ga_measurement_id"
                    v-model="form.ga_measurement_id"
                    placeholder="G-XXXXXXXXXX"
                    :class="{ 'input-error': errorsFields.includes('ga_measurement_id') }"
                />
                <p v-if="errorsFields.includes('ga_measurement_id')" class="mt-1 text-sm text-red-500">
                    {{ errors.ga_measurement_id }}
                </p>
            </div>

            <div class="sm:col-span-2">
                <AppLabel for="gtm_container_id" :value="__('Google Tag Manager Container ID')" />
                <p class="mb-1 text-xs text-skin-neutral-9">{{ __('settings::admin.gtm_container_hint') }}</p>
                <AppInputText
                    id="gtm_container_id"
                    v-model="form.gtm_container_id"
                    placeholder="GTM-XXXXXXX"
                    :class="{ 'input-error': errorsFields.includes('gtm_container_id') }"
                />
                <p v-if="errorsFields.includes('gtm_container_id')" class="mt-1 text-sm text-red-500">
                    {{ errors.gtm_container_id }}
                </p>
            </div>
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
