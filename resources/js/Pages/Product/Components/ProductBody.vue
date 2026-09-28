<template>
    <div>
        <AppLabel for="name">{{ __('common.field.name') }}</AppLabel>
        <AppInputText
            id="name"
            v-model="productStore.product.name"
            type="text"
            :class="{
                'input-error': errorsFields.includes('name')
            }"
        />
    </div>

    <div>
        <AppLabel for="price">{{ __('common.field.price') }}</AppLabel>
        <AppInputText
            id="price"
            v-model="productStore.product.price"
            type="number"
            :class="{
                'input-error': errorsFields.includes('price')
            }"
        />
    </div>

    <div>
        <AppLabel for="sale_price">{{ __('common.field.sale_price') }}</AppLabel>
        <AppInputText
            id="sale_price"
            v-model="productStore.product.sale_price"
            type="number"
            :class="{
                'input-error': errorsFields.includes('sale_price')
            }"
        />
    </div>

    <div>
        <AppLabel for="quantity">{{ __('common.field.quantity') }}</AppLabel>
        <AppInputText
            id="quantity"
            v-model="productStore.product.quantity"
            type="number"
            :class="{
                'input-error': errorsFields.includes('quantity')
            }"
        />
    </div>

    <div>
        <AppLabel for="unit">{{ __('common.field.unit') }}</AppLabel>
        <AppInputText
            id="unit"
            v-model="productStore.product.unit"
            type="number"
            :class="{
                'input-error': errorsFields.includes('unit')
            }"
        />
    </div>

    <div class="mt-5 flex items-center">
        <AppCheckbox
            id="active"
            v-model="productStore.product.active"
            name="active"
            :value="true"
        />
        <AppLabel for="active" class="ml-3"> {{ __('common.field.active') }} </AppLabel>
    </div>

    <div class="mt-5 flex items-center">
        <AppCheckbox
            id="featured"
            v-model="productStore.product.featured"
            name="featured"
            :value="true"
        />
        <AppLabel for="featured" class="ml-3"> {{ __('common.field.featured') }} </AppLabel>
    </div>

    <div class="mt-5">
        <AppLabel for="summary">{{ __('common.field.product_summary') }}</AppLabel>
        <AppInputText
            id="summary"
            v-model="productStore.product.summary"
            type="text"
            :class="{
                'input-error': errorsFields.includes('summary')
            }"
        />
    </div>

    <div class="mt-5">
        <AppLabel for="description">{{ __('common.field.description') }}</AppLabel>
        <AppTipTapEditor
            v-model="productStore.product.description"
            editor-id="description"
            :class="{
                'app-tip-tap-error': errorsFields.includes('description')
            }"
            :file-upload-url="route('product.uploadEditorImage')"
        />
    </div>
</template>

<script setup>
import useFormErrors from '@/Composables/useFormErrors'
import { useProductStore } from '../ProductStore'
const productStore = useProductStore()
const { errorsFields } = useFormErrors()
</script>
