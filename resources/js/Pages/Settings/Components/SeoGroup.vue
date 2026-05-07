<template>
    <div class="space-y-6">
        <div>
            <AppLabel for="meta_title" :value="__('Meta Title')" />
            <AppInputText
                id="meta_title"
                v-model="form.meta_title"
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
            <AppTextArea
                id="meta_description"
                v-model="form.meta_description"
                :class="{ 'input-error': errorsFields.includes('meta_description') }"
                @input="syncDescLength"
            />
            <p class="mt-1 text-xs" :class="descLength > 160 ? 'text-red-500' : 'text-skin-neutral-8'">
                {{ descLength }} / 160 {{ __('characters') }}
            </p>
        </div>

        <div>
            <AppLabel for="meta_keywords" :value="__('Meta Keywords')" />
            <p class="mb-1 text-xs text-skin-neutral-9">{{ __('Comma-separated list of keywords') }}</p>
            <AppInputText
                id="meta_keywords"
                v-model="form.meta_keywords"
                :class="{ 'input-error': errorsFields.includes('meta_keywords') }"
            />
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
</script>
