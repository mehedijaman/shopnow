<template>
    <div class="space-y-6">
        <div>
            <AppLabel for="site_name" :value="__('Site Name')" />
            <AppInputText
                id="site_name"
                v-model="form.site_name"
                :class="{ 'input-error': errorsFields.includes('site_name') }"
            />
            <p v-if="errorsFields.includes('site_name')" class="mt-1 text-sm text-red-500">
                {{ errors.site_name }}
            </p>
        </div>

        <div>
            <AppLabel for="site_description" :value="__('Site Description')" />
            <AppTextArea
                id="site_description"
                v-model="form.site_description"
                :class="{ 'input-error': errorsFields.includes('site_description') }"
            />
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
