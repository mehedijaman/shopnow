<template>
    <div class="space-y-6">
        <div>
            <AppLabel for="meta_title" :value="__('Meta Title')" />
            <p class="mb-1 text-xs text-skin-neutral-9">{{ __('Recommended max 60 characters. Appears in search results and browser tabs.') }}</p>
            <AppInputText
                id="meta_title"
                v-model="form.meta_title"
                :placeholder="__('e.g. ShopNow — Best Products Online')"
                :class="{ 'input-error': errorsFields.includes('meta_title') }"
                @input="syncTitleLength"
            />
            <p class="mt-1 text-xs" :class="titleLength > 60 ? 'text-red-500' : 'text-skin-neutral-8'">
                {{ titleLength }} / 60 {{ __('characters') }}
            </p>
            <p v-if="errorsFields.includes('meta_title')" class="mt-1 text-sm text-red-500">
                {{ errors.meta_title }}
            </p>
        </div>

        <div>
            <AppLabel for="meta_description" :value="__('Meta Description')" />
            <p class="mb-1 text-xs text-skin-neutral-9">{{ __('Recommended max 160 characters. Shown as a snippet in search results.') }}</p>
            <AppTextArea
                id="meta_description"
                v-model="form.meta_description"
                :placeholder="__('A short, compelling description of your site...')"
                :class="{ 'input-error': errorsFields.includes('meta_description') }"
                @input="syncDescLength"
            />
            <p class="mt-1 text-xs" :class="descLength > 160 ? 'text-red-500' : 'text-skin-neutral-8'">
                {{ descLength }} / 160 {{ __('characters') }}
            </p>
        </div>

        <div>
            <AppLabel for="meta_keywords" :value="__('Meta Keywords')" />
            <p class="mb-1 text-xs text-skin-neutral-9">{{ __('Comma-separated list of keywords. (Low SEO impact, optional)') }}</p>
            <AppInputText
                id="meta_keywords"
                v-model="form.meta_keywords"
                :placeholder="__('e.g. shop, products, best deals')"
                :class="{ 'input-error': errorsFields.includes('meta_keywords') }"
            />
        </div>

        <!-- OG / Social Share Image -->
        <div>
            <AppLabel :value="__('Default Social Share Image (OG Image)')" />
            <p class="mb-2 text-xs text-skin-neutral-9">{{ __('Recommended: 1200×630 px, JPG or PNG, max 2 MB. Used when pages are shared on social media.') }}</p>
            <div v-if="urls.og_image_url && !form.remove_previous_og_image" class="mb-3 flex items-center gap-4">
                <img
                    :src="urls.og_image_url"
                    alt="OG Image"
                    class="h-20 rounded-sm border border-skin-neutral-4 object-cover"
                />
                <button
                    type="button"
                    class="text-sm text-red-500 hover:text-red-700"
                    @click="removeOgImage"
                >
                    {{ __('Remove Image') }}
                </button>
            </div>
            <AppInputFile
                v-model="form.og_image"
                :class="{ 'input-error': errorsFields.includes('og_image') }"
                @remove-file="form.og_image = null"
            />
            <p v-if="errorsFields.includes('og_image')" class="mt-1 text-sm text-red-500">
                {{ errors.og_image }}
            </p>
        </div>

        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
            <div>
                <AppLabel for="twitter_handle" :value="__('Twitter / X Handle')" />
                <p class="mb-1 text-xs text-skin-neutral-9">{{ __('Your @handle used in Twitter Card meta tags.') }}</p>
                <AppInputText
                    id="twitter_handle"
                    v-model="form.twitter_handle"
                    placeholder="@yourhandle"
                    :class="{ 'input-error': errorsFields.includes('twitter_handle') }"
                />
            </div>

            <div>
                <AppLabel for="robots_default" :value="__('Default Robots Directive')" />
                <p class="mb-1 text-xs text-skin-neutral-9">{{ __('Controls how search engines index your site by default.') }}</p>
                <select
                    id="robots_default"
                    v-model="form.robots_default"
                    class="mt-1 block w-full rounded-md border-0 bg-skin-neutral-1 px-3 py-2 text-sm ring-1 ring-inset ring-skin-neutral-7 focus:ring-2 focus:ring-inset focus:ring-skin-neutral-7"
                >
                    <option value="index, follow">index, follow (recommended)</option>
                    <option value="noindex, follow">noindex, follow</option>
                    <option value="index, nofollow">index, nofollow</option>
                    <option value="noindex, nofollow">noindex, nofollow</option>
                </select>
            </div>

            <div>
                <AppLabel for="canonical_domain" :value="__('Canonical Domain')" />
                <p class="mb-1 text-xs text-skin-neutral-9">{{ __('Used to build canonical URLs (e.g. https://example.com). Leave empty to use APP_URL.') }}</p>
                <AppInputText
                    id="canonical_domain"
                    v-model="form.canonical_domain"
                    placeholder="https://example.com"
                    :class="{ 'input-error': errorsFields.includes('canonical_domain') }"
                />
            </div>
        </div>
    </div>
</template>

<script setup>
import { inject, ref, watch } from 'vue'
import useFormErrors from '@/Composables/useFormErrors'

defineProps({
    errorsFields: { type: Array, default: () => [] },
})

const form = inject('settingsForm')
const urls = inject('settingsUrls')
const { errors } = useFormErrors()

const titleLength = ref((form.meta_title ?? '').length)
const descLength = ref((form.meta_description ?? '').length)

const syncTitleLength = () => {
    titleLength.value = (form.meta_title ?? '').length
}
const syncDescLength = () => {
    descLength.value = (form.meta_description ?? '').length
}

watch(() => form.meta_title, (v) => { titleLength.value = (v ?? '').length })
watch(() => form.meta_description, (v) => { descLength.value = (v ?? '').length })

const removeOgImage = () => {
    form.og_image = null
    form.remove_previous_og_image = true
}
</script>
