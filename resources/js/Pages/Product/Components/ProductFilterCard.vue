<template>
    <div class="rounded-xl border border-skin-neutral-4 bg-skin-neutral-2 p-4 shadow-sm">
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
            <!-- Status Filter -->
            <div>
                <AppLabel for="status-filter">Status</AppLabel>
                <select
                    id="status-filter"
                    v-model="filters.active"
                    class="mt-1 block w-full rounded-md border-0 bg-skin-neutral-1 px-3 py-2 text-skin-neutral-12 placeholder-skin-neutral-9 shadow-xs ring-1 ring-inset ring-skin-neutral-7 focus:ring-2 focus:ring-inset focus:ring-skin-neutral-7 sm:text-sm sm:leading-6"
                >
                    <option value="">All Status</option>
                    <option value="1">Active</option>
                    <option value="0">Inactive</option>
                </select>
            </div>

            <!-- Stock Filter -->
            <div>
                <AppLabel for="stock-filter">Stock</AppLabel>
                <select
                    id="stock-filter"
                    v-model="filters.stock"
                    class="mt-1 block w-full rounded-md border-0 bg-skin-neutral-1 px-3 py-2 text-skin-neutral-12 placeholder-skin-neutral-9 shadow-xs ring-1 ring-inset ring-skin-neutral-7 focus:ring-2 focus:ring-inset focus:ring-skin-neutral-7 sm:text-sm sm:leading-6"
                >
                    <option value="">All Stock</option>
                    <option value="low">Low Stock (&lt;10)</option>
                    <option value="out">Out of Stock</option>
                </select>
            </div>

            <!-- Featured Filter -->
            <div>
                <AppLabel for="featured-filter">Featured</AppLabel>
                <select
                    id="featured-filter"
                    v-model="filters.featured"
                    class="mt-1 block w-full rounded-md border-0 bg-skin-neutral-1 px-3 py-2 text-skin-neutral-12 placeholder-skin-neutral-9 shadow-xs ring-1 ring-inset ring-skin-neutral-7 focus:ring-2 focus:ring-inset focus:ring-skin-neutral-7 sm:text-sm sm:leading-6"
                >
                    <option value="">All Products</option>
                    <option value="1">Featured Only</option>
                </select>
            </div>

            <!-- Brand Filter -->
            <div>
                <AppLabel for="brand-filter">Brand</AppLabel>
                <select
                    id="brand-filter"
                    v-model="filters.brand"
                    class="mt-1 block w-full rounded-md border-0 bg-skin-neutral-1 px-3 py-2 text-skin-neutral-12 placeholder-skin-neutral-9 shadow-xs ring-1 ring-inset ring-skin-neutral-7 focus:ring-2 focus:ring-inset focus:ring-skin-neutral-7 sm:text-sm sm:leading-6"
                >
                    <option value="">All Brands</option>
                    <option v-for="brand in brands" :key="brand.value" :value="brand.value">
                        {{ brand.label }}
                    </option>
                </select>
            </div>

            <!-- Category Filter -->
            <div>
                <AppLabel for="category-filter">Category</AppLabel>
                <select
                    id="category-filter"
                    v-model="filters.category"
                    class="mt-1 block w-full rounded-md border-0 bg-skin-neutral-1 px-3 py-2 text-skin-neutral-12 placeholder-skin-neutral-9 shadow-xs ring-1 ring-inset ring-skin-neutral-7 focus:ring-2 focus:ring-inset focus:ring-skin-neutral-7 sm:text-sm sm:leading-6"
                >
                    <option value="">All Categories</option>
                    <option v-for="category in categories" :key="category.value" :value="category.value">
                        {{ category.label }}
                    </option>
                </select>
            </div>

            <!-- Tag Filter -->
            <div>
                <AppLabel for="tag-filter">Tag</AppLabel>
                <select
                    id="tag-filter"
                    v-model="filters.tag"
                    class="mt-1 block w-full rounded-md border-0 bg-skin-neutral-1 px-3 py-2 text-skin-neutral-12 placeholder-skin-neutral-9 shadow-xs ring-1 ring-inset ring-skin-neutral-7 focus:ring-2 focus:ring-inset focus:ring-skin-neutral-7 sm:text-sm sm:leading-6"
                >
                    <option value="">All Tags</option>
                    <option v-for="tag in tags" :key="tag.value" :value="tag.value">
                        {{ tag.label }}
                    </option>
                </select>
            </div>

            <!-- Attribute Filter -->
            <div>
                <AppLabel for="attribute-filter">Attribute</AppLabel>
                <select
                    id="attribute-filter"
                    v-model="filters.attribute"
                    class="mt-1 block w-full rounded-md border-0 bg-skin-neutral-1 px-3 py-2 text-skin-neutral-12 placeholder-skin-neutral-9 shadow-xs ring-1 ring-inset ring-skin-neutral-7 focus:ring-2 focus:ring-inset focus:ring-skin-neutral-7 sm:text-sm sm:leading-6"
                    @change="onAttributeChange"
                >
                    <option value="">All Attributes</option>
                    <option v-for="attr in attributes" :key="attr.id" :value="attr.id">
                        {{ attr.name }}
                    </option>
                </select>
            </div>

            <!-- Attribute Value Filter -->
            <div>
                <AppLabel for="attribute-value-filter">Attribute Value</AppLabel>
                <select
                    id="attribute-value-filter"
                    v-model="filters.attribute_value"
                    :disabled="!filters.attribute"
                    class="mt-1 block w-full rounded-md border-0 bg-skin-neutral-1 px-3 py-2 text-skin-neutral-12 placeholder-skin-neutral-9 shadow-xs ring-1 ring-inset ring-skin-neutral-7 focus:ring-2 focus:ring-inset focus:ring-skin-neutral-7 sm:text-sm sm:leading-6 disabled:opacity-50"
                >
                    <option value="">All Values</option>
                    <option v-for="val in availableValues" :key="val.id" :value="val.id">
                        {{ val.value }}
                    </option>
                </select>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="mt-4 flex justify-end gap-2 border-t border-skin-neutral-3 pt-3">
            <AppButton
                type="button"
                class="btn btn-secondary text-sm"
                @click="clear"
            >
                Clear Filter
            </AppButton>
            <AppButton
                type="button"
                class="btn btn-primary text-sm"
                @click="apply"
            >
                Apply Filter
            </AppButton>
        </div>
    </div>
</template>

<script setup>
import { ref, watch, computed } from 'vue'

const props = defineProps({
    brands: { type: Array, default: () => [] },
    categories: { type: Array, default: () => [] },
    tags: { type: Array, default: () => [] },
    attributes: { type: Array, default: () => [] },
    initialFilters: { type: Object, default: () => ({}) },
})

const emit = defineEmits(['apply', 'clear'])

const filters = ref({
    active: props.initialFilters?.active ?? '',
    featured: props.initialFilters?.featured ?? '',
    stock: props.initialFilters?.stock ?? '',
    brand: props.initialFilters?.brand ?? '',
    category: props.initialFilters?.category ?? '',
    tag: props.initialFilters?.tag ?? '',
    attribute: props.initialFilters?.attribute ?? '',
    attribute_value: props.initialFilters?.attribute_value ?? '',
})

// Compute values that belong to the currently selected attribute
const availableValues = computed(() => {
    if (!filters.value.attribute) {
        return []
    }
    const selectedAttr = props.attributes.find(
        (attr) => String(attr.id) === String(filters.value.attribute)
    )
    return selectedAttr ? selectedAttr.values : []
})

// Reset attribute value selection if selected attribute changes
const onAttributeChange = () => {
    filters.value.attribute_value = ''
}

// Synchronize local state with initialFilters prop updates
watch(() => props.initialFilters, (newVal) => {
    filters.value = {
        active: newVal?.active ?? '',
        featured: newVal?.featured ?? '',
        stock: newVal?.stock ?? '',
        brand: newVal?.brand ?? '',
        category: newVal?.category ?? '',
        tag: newVal?.tag ?? '',
        attribute: newVal?.attribute ?? '',
        attribute_value: newVal?.attribute_value ?? '',
    }
}, { deep: true })

const apply = () => {
    emit('apply', { ...filters.value })
}

const clear = () => {
    filters.value = {
        active: '',
        featured: '',
        stock: '',
        brand: '',
        category: '',
        tag: '',
        attribute: '',
        attribute_value: '',
    }
    emit('clear')
}
</script>
