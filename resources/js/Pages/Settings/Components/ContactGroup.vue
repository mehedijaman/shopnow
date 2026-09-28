<template>
    <div class="space-y-8">
        <RepeaterField
            v-model="form.phone"
            :label="__('settings::admin.phone_numbers')"
            :placeholder="__('settings::admin.phone_placeholder')"
            :add-label="__('settings::admin.add_phone')"
        />

        <RepeaterField
            v-model="form.email"
            :label="__('settings::admin.email_addresses')"
            :placeholder="__('settings::admin.email_placeholder')"
            :add-label="__('settings::admin.add_email')"
        />

        <RepeaterField
            v-model="form.address"
            :label="__('settings::admin.office_addresses')"
            :placeholder="__('settings::admin.address_placeholder')"
            :add-label="__('settings::admin.add_address')"
        />

        <RepeaterField
            v-model="form.whatsapp"
            :label="__('settings::admin.whatsapp_numbers')"
            :placeholder="__('settings::admin.phone_placeholder')"
            :add-label="__('settings::admin.add_whatsapp')"
        />

        <RepeaterField
            v-model="form.working_hours"
            :label="__('settings::admin.working_hours')"
            :placeholder="__('settings::admin.working_hours_placeholder')"
            :add-label="__('settings::admin.add_working_hours')"
        />

        <div>
            <AppLabel for="google_map" :value="__('settings::admin.google_map')" />
            <p class="mb-2 text-xs text-skin-neutral-9">
                {{ __('settings::admin.google_map_hint') }}
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
