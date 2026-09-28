<template>
    <div class="space-y-6">
        <div>
            <AppLabel for="meta_title" :value="__('settings::admin.meta_title')" />
            <p class="mb-1 text-xs text-skin-neutral-9">{{ __('settings::admin.meta_title_hint') }}</p>
            <AppInputText
                id="meta_title"
                v-model="form.meta_title"
                :placeholder="__('settings::admin.meta_title_placeholder')"
                :class="{ 'input-error': errorsFields.includes('meta_title') }"
                @input="syncTitleLength"
            />
            <p class="mt-1 text-xs" :class="titleLength > 60 ? 'text-red-500' : 'text-skin-neutral-8'">
                {{ titleLength }} / 60 {{ __('settings::admin.characters') }}
            </p>
            <p v-if="errorsFields.includes('meta_title')" class="mt-1 text-sm text-red-500">
                {{ errors.meta_title }}
            </p>
        </div>

        <div>
            <AppLabel for="meta_description" :value="__('settings::admin.meta_description')" />
            <p class="mb-1 text-xs text-skin-neutral-9">{{ __('settings::admin.meta_description_hint') }}</p>
            <AppTextArea
                id="meta_description"
                v-model="form.meta_description"
                :placeholder="__('settings::admin.meta_description_placeholder')"
                :class="{ 'input-error': errorsFields.includes('meta_description') }"
                @input="syncDescLength"
            />
            <p class="mt-1 text-xs" :class="descLength > 160 ? 'text-red-500' : 'text-skin-neutral-8'">
                {{ descLength }} / 160 {{ __('settings::admin.characters') }}
            </p>
        </div>

        <div>
            <AppLabel for="meta_keywords" :value="__('settings::admin.meta_keywords')" />
            <p class="mb-1 text-xs text-skin-neutral-9">{{ __('settings::admin.meta_keywords_hint') }}</p>
            <AppInputText
                id="meta_keywords"
                v-model="form.meta_keywords"
                :placeholder="__('settings::admin.meta_keywords_placeholder')"
                :class="{ 'input-error': errorsFields.includes('meta_keywords') }"
            />
        </div>

        <!-- OG / Social Share Image -->
        <div>
            <AppLabel :value="__('settings::admin.og_image')" />
            <p class="mb-2 text-xs text-skin-neutral-9">{{ __('settings::admin.og_image_hint') }}</p>
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
                    {{ __('settings::admin.remove_image') }}
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
                <AppLabel for="twitter_handle" :value="__('settings::admin.twitter_handle')" />
                <p class="mb-1 text-xs text-skin-neutral-9">{{ __('settings::admin.twitter_handle_hint') }}</p>
                <AppInputText
                    id="twitter_handle"
                    v-model="form.twitter_handle"
                    placeholder="@yourhandle"
                    :class="{ 'input-error': errorsFields.includes('twitter_handle') }"
                />
            </div>

            <div>
                <AppLabel for="robots_default" :value="__('settings::admin.robots_directive')" />
                <p class="mb-1 text-xs text-skin-neutral-9">{{ __('settings::admin.robots_hint') }}</p>
                <select
                    id="robots_default"
                    v-model="form.robots_default"
                    class="mt-1 block w-full rounded-md border-0 bg-skin-neutral-1 px-3 py-2 text-sm ring-1 ring-inset ring-skin-neutral-7 focus:ring-2 focus:ring-inset focus:ring-skin-neutral-7"
                >
                    <option value="index, follow">{{ __('settings::admin.index_follow') }}</option>
                    <option value="noindex, follow">{{ __('settings::admin.noindex_follow') }}</option>
                    <option value="index, nofollow">{{ __('settings::admin.index_nofollow') }}</option>
                    <option value="noindex, nofollow">{{ __('settings::admin.noindex_nofollow') }}</option>
                </select>
            </div>

            <div>
                <AppLabel for="canonical_domain" :value="__('settings::admin.canonical_domain')" />
                <p class="mb-1 text-xs text-skin-neutral-9">{{ __('settings::admin.canonical_domain_hint') }}</p>
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
