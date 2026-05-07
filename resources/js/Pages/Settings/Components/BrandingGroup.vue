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
            <AppLabel for="site_slogan" :value="__('Site Slogan')" />
            <AppInputText
                id="site_slogan"
                v-model="form.site_slogan"
                :class="{ 'input-error': errorsFields.includes('site_slogan') }"
            />
        </div>

        <!-- Logo -->
        <div>
            <AppLabel :value="__('Logo')" />
            <p class="mb-2 text-xs text-skin-neutral-9">{{ __('Recommended: PNG or SVG, max 2 MB') }}</p>
            <div v-if="urls.logo_url && !form.remove_previous_logo" class="mb-3 flex items-center gap-4">
                <img
                    :src="urls.logo_url"
                    alt="Logo"
                    class="h-14 rounded border border-skin-neutral-4 object-contain p-1"
                />
                <button
                    type="button"
                    class="text-sm text-red-500 hover:text-red-700"
                    @click="removeLogo"
                >
                    {{ __('Remove Logo') }}
                </button>
            </div>
            <div v-if="!urls.logo_url && !form.logo" class="mb-3">
                <img src="/logo.png" alt="Default Logo" class="h-14 rounded border border-skin-neutral-4 object-contain p-1 opacity-40" />
                <p class="mt-1 text-xs text-skin-neutral-8">{{ __('Fallback logo shown — no logo uploaded yet') }}</p>
            </div>
            <AppInputFile
                v-model="form.logo"
                :class="{ 'input-error': errorsFields.includes('logo') }"
                @remove-file="form.logo = null"
            />
            <p v-if="errorsFields.includes('logo')" class="mt-1 text-sm text-red-500">
                {{ errors.logo }}
            </p>
        </div>

        <!-- Favicon -->
        <div>
            <AppLabel :value="__('Favicon')" />
            <p class="mb-2 text-xs text-skin-neutral-9">{{ __('Recommended: ICO or PNG 32×32, max 512 KB') }}</p>
            <div v-if="urls.favicon_url && !form.remove_previous_favicon" class="mb-3 flex items-center gap-4">
                <img
                    :src="urls.favicon_url"
                    alt="Favicon"
                    class="h-8 w-8 rounded border border-skin-neutral-4 object-contain p-0.5"
                />
                <button
                    type="button"
                    class="text-sm text-red-500 hover:text-red-700"
                    @click="removeFavicon"
                >
                    {{ __('Remove Favicon') }}
                </button>
            </div>
            <AppInputFile
                v-model="form.favicon"
                :class="{ 'input-error': errorsFields.includes('favicon') }"
                @remove-file="form.favicon = null"
            />
            <p v-if="errorsFields.includes('favicon')" class="mt-1 text-sm text-red-500">
                {{ errors.favicon }}
            </p>
        </div>

        <!-- Dark Logo -->
        <div>
            <AppLabel :value="__('Dark Logo')" />
            <p class="mb-2 text-xs text-skin-neutral-9">{{ __('Used on dark backgrounds, max 2 MB') }}</p>
            <div v-if="urls.dark_logo_url && !form.remove_previous_dark_logo" class="mb-3 flex items-center gap-4">
                <img
                    :src="urls.dark_logo_url"
                    alt="Dark Logo"
                    class="h-14 rounded border border-skin-neutral-4 bg-gray-800 object-contain p-1"
                />
                <button
                    type="button"
                    class="text-sm text-red-500 hover:text-red-700"
                    @click="removeDarkLogo"
                >
                    {{ __('Remove Dark Logo') }}
                </button>
            </div>
            <AppInputFile
                v-model="form.dark_logo"
                :class="{ 'input-error': errorsFields.includes('dark_logo') }"
                @remove-file="form.dark_logo = null"
            />
            <p v-if="errorsFields.includes('dark_logo')" class="mt-1 text-sm text-red-500">
                {{ errors.dark_logo }}
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
const urls = inject('settingsUrls')
const { errors } = useFormErrors()

const removeLogo = () => {
    form.logo = null
    form.remove_previous_logo = true
}

const removeFavicon = () => {
    form.favicon = null
    form.remove_previous_favicon = true
}

const removeDarkLogo = () => {
    form.dark_logo = null
    form.remove_previous_dark_logo = true
}
</script>
