<template>
    <div class="space-y-6">
        <div class="rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
            Changes here affect production tracking and ad attribution. Update carefully and verify in Meta Events Manager.
        </div>

        <div class="divide-y divide-skin-neutral-3">
            <div class="flex items-center justify-between gap-4 py-4">
                <div>
                    <p class="text-sm font-medium text-skin-neutral-12">Enable Meta Pixel</p>
                    <p class="mt-0.5 text-xs text-skin-neutral-9">Master switch for browser and server Meta events.</p>
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

            <div class="flex items-center justify-between gap-4 py-4">
                <div>
                    <p class="text-sm font-medium text-skin-neutral-12">Require Consent</p>
                    <p class="mt-0.5 text-xs text-skin-neutral-9">Pixel events are blocked until users grant consent.</p>
                </div>
                <button
                    type="button"
                    role="switch"
                    :aria-checked="form.require_consent"
                    class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer items-center rounded-full transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-skin-primary-8 focus:ring-offset-2"
                    :class="form.require_consent ? 'bg-skin-primary-10' : 'bg-skin-neutral-5'"
                    @click="form.require_consent = !form.require_consent"
                >
                    <span
                        class="inline-block h-4 w-4 transform rounded-full bg-white shadow-sm transition-transform duration-200"
                        :class="form.require_consent ? 'translate-x-6' : 'translate-x-1'"
                    />
                </button>
            </div>

            <div class="flex items-center justify-between gap-4 py-4">
                <div>
                    <p class="text-sm font-medium text-skin-neutral-12">Enable in Non-Production</p>
                    <p class="mt-0.5 text-xs text-skin-neutral-9">Allow tracking on local/staging environments for testing.</p>
                </div>
                <button
                    type="button"
                    role="switch"
                    :aria-checked="form.enable_non_production"
                    class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer items-center rounded-full transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-skin-primary-8 focus:ring-offset-2"
                    :class="form.enable_non_production ? 'bg-skin-primary-10' : 'bg-skin-neutral-5'"
                    @click="form.enable_non_production = !form.enable_non_production"
                >
                    <span
                        class="inline-block h-4 w-4 transform rounded-full bg-white shadow-sm transition-transform duration-200"
                        :class="form.enable_non_production ? 'translate-x-6' : 'translate-x-1'"
                    />
                </button>
            </div>

            <div class="flex items-center justify-between gap-4 py-4">
                <div>
                    <p class="text-sm font-medium text-skin-neutral-12">Enable Conversions API (CAPI)</p>
                    <p class="mt-0.5 text-xs text-skin-neutral-9">Send server-side events for resilient conversion measurement.</p>
                </div>
                <button
                    type="button"
                    role="switch"
                    :aria-checked="form.capi_enabled"
                    class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer items-center rounded-full transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-skin-primary-8 focus:ring-offset-2"
                    :class="form.capi_enabled ? 'bg-skin-primary-10' : 'bg-skin-neutral-5'"
                    @click="form.capi_enabled = !form.capi_enabled"
                >
                    <span
                        class="inline-block h-4 w-4 transform rounded-full bg-white shadow-sm transition-transform duration-200"
                        :class="form.capi_enabled ? 'translate-x-6' : 'translate-x-1'"
                    />
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
            <div>
                <AppLabel for="meta_pixel_id" :value="__('Meta Pixel ID')" />
                <p class="mb-1 text-xs text-skin-neutral-9">Numeric Pixel ID from Meta Events Manager.</p>
                <AppInputText
                    id="meta_pixel_id"
                    v-model="form.meta_pixel_id"
                    placeholder="123456789012345"
                    :class="{ 'input-error': errorsFields.includes('meta_pixel_id') }"
                />
                <p v-if="errorsFields.includes('meta_pixel_id')" class="mt-1 text-sm text-red-500">
                    {{ errors.meta_pixel_id }}
                </p>
            </div>

            <div>
                <AppLabel for="api_version" :value="__('Meta Graph API Version')" />
                <p class="mb-1 text-xs text-skin-neutral-9">Example: v23.0</p>
                <AppInputText
                    id="api_version"
                    v-model="form.api_version"
                    placeholder="v23.0"
                    :class="{ 'input-error': errorsFields.includes('api_version') }"
                />
                <p v-if="errorsFields.includes('api_version')" class="mt-1 text-sm text-red-500">
                    {{ errors.api_version }}
                </p>
            </div>

            <div class="sm:col-span-2">
                <AppLabel for="capi_access_token" :value="__('CAPI Access Token')" />
                <p class="mb-1 text-xs text-skin-neutral-9">Stored server-side and used only for secure CAPI calls.</p>
                <AppInputText
                    id="capi_access_token"
                    v-model="form.capi_access_token"
                    type="password"
                    placeholder="EAABsbCS1iHgBA..."
                    :class="{ 'input-error': errorsFields.includes('capi_access_token') }"
                />
                <p v-if="errorsFields.includes('capi_access_token')" class="mt-1 text-sm text-red-500">
                    {{ errors.capi_access_token }}
                </p>
            </div>

            <div class="sm:col-span-2">
                <AppLabel for="test_event_code" :value="__('Test Event Code (Optional)')" />
                <p class="mb-1 text-xs text-skin-neutral-9">Used for validating events in non-production with Meta Test Events.</p>
                <AppInputText
                    id="test_event_code"
                    v-model="form.test_event_code"
                    placeholder="TEST12345"
                    :class="{ 'input-error': errorsFields.includes('test_event_code') }"
                />
                <p v-if="errorsFields.includes('test_event_code')" class="mt-1 text-sm text-red-500">
                    {{ errors.test_event_code }}
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
