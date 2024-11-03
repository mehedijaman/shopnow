<template>
    <div>
        <AppLabel for="name">Name</AppLabel>
        <AppInputText
            id="name"
            v-model="categoryStore.category.name"
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
            v-model="categoryStore.category.description"
            editor-id="description"
            :class="{
                'app-tip-tap-error': errorsFields.includes('description')
            }"
            :file-upload-url="route('blogCategory.uploadEditorImage')"
        />
    </div>

    <div class="mt-5 flex items-center">
        <AppCheckbox
            id="active"
            v-model="categoryStore.category.active"
            name="active"
            :value="true"
        />
        <AppLabel for="active" class="ml-3"> Active </AppLabel>
    </div>

    <div class="mt-5 flex items-center">
        <AppCheckbox
            id="featured"
            v-model="categoryStore.category.featured"
            name="featured"
            :value="true"
        />
        <AppLabel for="featured" class="ml-3"> Featured Category </AppLabel>
    </div>
</template>

<script setup>
import useFormErrors from '@/Composables/useFormErrors'
import { useProductCategoryStore } from '../ProductCategoryStore'
const categoryStore = useProductCategoryStore()
const { errorsFields } = useFormErrors()
</script>
