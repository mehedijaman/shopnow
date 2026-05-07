<template>
    <div class="space-y-6">
        <div>
            <AppLabel for="site_name" :value="__('Site Name')" />
            <p class="mb-1 text-xs text-skin-neutral-9">{{ __('The name of your website, used in the browser title and emails.') }}</p>
            <AppInputText
                id="site_name"
                v-model="form.site_name"
                :placeholder="__('e.g. My Awesome Store')"
                :class="{ 'input-error': errorsFields.includes('site_name') }"
            />
            <p v-if="errorsFields.includes('site_name')" class="mt-1 text-sm text-red-500">
                {{ errors.site_name }}
            </p>
        </div>

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
