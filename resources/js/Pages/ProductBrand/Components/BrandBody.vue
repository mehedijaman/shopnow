<template>
    <div>
        <AppLabel for="name">Name</AppLabel>
        <AppInputText
            id="name"
            v-model="brandStore.brand.name"
            type="text"
            :class="{
                'input-error': errorsFields.includes('name')
            }"
            autocomplete="off"
        />
    </div>

    <div class="mt-5">
        <AppLabel for="description">Description</AppLabel>
        <AppTipTapEditor
            v-model="brandStore.brand.description"
            editor-id="description"
            :class="{
                'app-tip-tap-error': errorsFields.includes('description')
            }"
            :file-upload-url="route('productBrand.uploadEditorImage')"
        />
    </div>

    <div class="mt-5 flex items-center">
        <AppCheckbox
            id="active"
            v-model="brandStore.brand.active"
            name="active"
            :value="true"
        />
        <AppLabel for="active" class="ml-3"> Active </AppLabel>
    </div>

    <div class="mt-5 flex items-center">
        <AppCheckbox
            id="featured"
            v-model="brandStore.brand.featured"
            name="featured"
            :value="true"
        />
        <AppLabel for="featured" class="ml-3"> Featured Brand </AppLabel>
    </div>
</template>

<script setup>
import useFormErrors from '@/Composables/useFormErrors'
import { useProductBrandStore } from '../ProductBrandStore'

const brandStore = useProductBrandStore()
const { errorsFields } = useFormErrors()
</script>
