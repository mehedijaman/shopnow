<template>
    <div class="space-y-6">
        <p class="text-sm text-skin-neutral-9">{{ __('Enter the full URL (including https://) for each platform you want to link.') }}</p>
        <div v-for="platform in platforms" :key="platform.key">
            <AppLabel :for="platform.key" :value="platform.label" />
            <div class="mt-1 flex items-center gap-2">
                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-md bg-skin-neutral-3 text-lg">
                    <i :class="platform.icon"></i>
                </span>
                <AppInputText
                    :id="platform.key"
                    v-model="form[platform.key]"
                    :placeholder="platform.placeholder"
                    :class="['flex-1', { 'input-error': errorsFields.includes(platform.key) }]"
                />
            </div>
            <p v-if="errorsFields.includes(platform.key)" class="mt-1 text-sm text-red-500">
                {{ errors[platform.key] }}
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

const platforms = [
    { key: 'facebook', label: 'Facebook', icon: 'ri-facebook-fill', placeholder: 'https://facebook.com/yourpage' },
    { key: 'x', label: 'X (Twitter)', icon: 'ri-twitter-x-fill', placeholder: 'https://x.com/yourhandle' },
    { key: 'instagram', label: 'Instagram', icon: 'ri-instagram-line', placeholder: 'https://instagram.com/yourprofile' },
    { key: 'youtube', label: 'YouTube', icon: 'ri-youtube-line', placeholder: 'https://youtube.com/yourchannel' },
    { key: 'linkedin', label: 'LinkedIn', icon: 'ri-linkedin-fill', placeholder: 'https://linkedin.com/company/yourcompany' },
    { key: 'tiktok', label: 'TikTok', icon: 'ri-tiktok-line', placeholder: 'https://tiktok.com/@yourprofile' },
    { key: 'github', label: 'GitHub', icon: 'ri-github-fill', placeholder: 'https://github.com/yourorg' },
]
</script>
