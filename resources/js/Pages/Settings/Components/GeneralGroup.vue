<template>
    <div class="space-y-6">
        <div>
            <AppLabel for="site_description" :value="__('settings::admin.site_description')" />
            <p class="mb-1 text-xs text-skin-neutral-9">{{ __('settings::admin.site_description_hint') }}</p>
            <AppTextArea
                id="site_description"
                v-model="form.site_description"
                :placeholder="__('settings::admin.site_description_placeholder')"
                :class="{ 'input-error': errorsFields.includes('site_description') }"
            />
            <p v-if="errorsFields.includes('site_description')" class="mt-1 text-sm text-red-500">
                {{ errors.site_description }}
            </p>
        </div>

        <div>
            <AppLabel for="admin_email" :value="__('settings::admin.admin_email')" />
            <p class="mb-1 text-xs text-skin-neutral-9">{{ __('settings::admin.admin_email_hint') }}</p>
            <AppInputText
                id="admin_email"
                type="email"
                v-model="form.admin_email"
                :placeholder="__('settings::admin.admin_email_placeholder')"
                :class="{ 'input-error': errorsFields.includes('admin_email') }"
            />
            <p v-if="errorsFields.includes('admin_email')" class="mt-1 text-sm text-red-500">
                {{ errors.admin_email }}
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
