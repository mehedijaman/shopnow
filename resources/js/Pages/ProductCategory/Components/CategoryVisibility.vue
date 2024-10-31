<template>
    <div class="mt-5 flex items-center">
        <AppCheckbox
            id="term"
            v-model="categoryStore.category.active"
            name="term"
            :value="true"
        />
        <AppLabel for="term" class="ml-3"> Active </AppLabel>
    </div>
</template>

<script setup>
import { useProductCategoryStore } from '../ProductCategoryStore'
const categoryStore = useProductCategoryStore()
</script>
