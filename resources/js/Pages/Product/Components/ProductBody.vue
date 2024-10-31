<template>
    <div>
        <AppLabel for="name">Name</AppLabel>
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
        <AppLabel for="price">Price</AppLabel>
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
        <AppLabel for="sale_price">Sale Price</AppLabel>
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
        <AppLabel for="quantity">Quantity</AppLabel>
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
        <AppLabel for="unit">Unit</AppLabel>
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
        <AppLabel for="active" class="ml-3"> Active </AppLabel>
    </div>

    <div class="mt-5 flex items-center">
        <AppCheckbox
            id="featured"
            v-model="productStore.product.featured"
            name="featured"
            :value="true"
        />
        <AppLabel for="featured" class="ml-3"> Featured </AppLabel>
    </div>

    <div class="mt-5">
        <AppLabel for="description">Description</AppLabel>
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
