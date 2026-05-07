<template>
    <div class="space-y-8">
        <RepeaterField
            v-model="form.phone"
            :label="__('Phone Numbers')"
            :placeholder="__('e.g. +880 1712 345678')"
            :add-label="__('Add Phone')"
        />

        <RepeaterField
            v-model="form.email"
            :label="__('Email Addresses')"
            :placeholder="__('e.g. info@example.com')"
            :add-label="__('Add Email')"
        />

        <RepeaterField
            v-model="form.address"
            :label="__('Office Addresses')"
            :placeholder="__('e.g. 123 Main St, Dhaka')"
            :add-label="__('Add Address')"
        />

        <RepeaterField
            v-model="form.whatsapp"
            :label="__('WhatsApp Numbers')"
            :placeholder="__('e.g. +880 1712 345678')"
            :add-label="__('Add WhatsApp')"
        />

        <div>
            <AppLabel for="google_map" :value="__('Google Map Embed Code')" />
            <p class="mb-2 text-xs text-skin-neutral-9">
                {{ __('Paste the full iframe embed code from Google Maps') }}
            </p>
            <AppTextArea
                id="google_map"
                v-model="form.google_map"
                :class="{ 'input-error': errorsFields.includes('google_map') }"
            />
        </div>
    </div>
</template>

<script setup>
import { inject } from 'vue'
import RepeaterField from './RepeaterField.vue'

defineProps({
    errorsFields: { type: Array, default: () => [] },
})

const form = inject('settingsForm')
</script>
