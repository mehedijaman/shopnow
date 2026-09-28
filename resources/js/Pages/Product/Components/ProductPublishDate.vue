<template>
    <div class="mt-4">
        <AppInputDate
            v-model="productStore.product.published_at"
            :label="__('common.field.publish_date')"
            name="publish_date"
        ></AppInputDate>
    </div>
</template>

<script setup>
// import useFormErrors from '@/Composables/useFormErrors'
import { useProductStore } from '../ProductStore'
const productStore = useProductStore()
// const { errorsFields } = useFormErrors()
</script>
