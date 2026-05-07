<template>
    <div class="space-y-6">
        <div>
            <AppLabel for="site_description" :value="__('Site Description')" />
            <p class="mb-1 text-xs text-skin-neutral-9">{{ __('A short summary of your site shown in listings and previews.') }}</p>
            <AppTextArea
                id="site_description"
                v-model="form.site_description"
                :placeholder="__('e.g. The best place to shop online')"
                :class="{ 'input-error': errorsFields.includes('site_description') }"
            />
            <p v-if="errorsFields.includes('site_description')" class="mt-1 text-sm text-red-500">
                {{ errors.site_description }}
            </p>
        </div>

        <div>
            <AppLabel for="admin_email" :value="__('Admin Email')" />
            <p class="mb-1 text-xs text-skin-neutral-9">{{ __('Receives new order notification emails.') }}</p>
            <AppInputText
                id="admin_email"
                type="email"
                v-model="form.admin_email"
                :placeholder="__('e.g. admin@example.com')"
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
