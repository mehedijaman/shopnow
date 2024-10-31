<template>
    <AppSectionHeader title="Products" :bread-crumb="breadCrumb">
        <AppButton
            v-if="can(isCreate ? 'product-create' : 'product-edit')"
            class="btn btn-primary mt-6"
            @click="submitForm"
        >
            Save
        </AppButton>
    </AppSectionHeader>

    <div class="flex flex-col xl:flex-row">
        <AppCard class="w-full xl:w-8/12">
            <template #title> {{ title }} </template>
            <template #content>
                <AppFormErrors class="mb-4" />
                <form class="pt-4">
                    <ProductBody />

                    <ProductImage />

                    <!-- <ProductSeo /> -->
                </form>
            </template>
        </AppCard>

        <AppCard class="mt-4 w-full xl:ml-5 xl:mt-0 xl:w-4/12">
            <template #title> Product Info </template>
            <template #content>
                <!-- <ProductPublishDate /> -->

                <ProductCategory :categories="categories" />

                <ProductTags :tags="tags" />

                <!-- <ProductAuthor :authors="authors" /> -->
            </template>
        </AppCard>
    </div>

    <AppButton
        v-if="can(isCreate ? 'product-create' : 'product-edit')"
        class="btn btn-primary mt-6"
        @click="submitForm"
    >
        Save
    </AppButton>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3'
import useAuthCan from '@/Composables/useAuthCan'

import { onUnmounted } from 'vue'

import useTitle from '@/Composables/useTitle'
import useFormContext from '@/Composables/useFormContext'
import ProductBody from './Components/ProductBody.vue'
import ProductImage from './Components/ProductImage.vue'
import ProductSeo from './Components/ProductSeo.vue'
import ProductPublishDate from './Components/ProductPublishDate.vue'
import ProductCategory from './Components/ProductCategory.vue'
import ProductTags from './Components/ProductTags.vue'
import ProductAuthor from './Components/ProductAuthor.vue'
import { useProductStore } from './ProductStore'

const productStore = useProductStore()
const { can } = useAuthCan()

const props = defineProps({
    product: {
        type: Object,
        default: null
    },

    categories: {
        type: Object,
        default: () => {}
    },

    tags: {
        type: Object,
        default: () => {}
    }

    // authors: {
    //     type: Object,
    //     default: () => {}
    // }
})

if (props.product) {
    productStore.setProduct(props.product)
}

onUnmounted(() => {
    productStore.$reset()
})

const breadCrumb = [
    { label: 'Home', href: route('dashboard.index') },
    { label: 'Products', href: route('product.index') },
    { label: 'Product', last: true }
]

const { title } = useTitle('Product')
const { isCreate } = useFormContext()

const getValueFromKey = (data, key) => {
    return data[key] ? data[key].value : null
}

const submitForm = () => {
    const form = useForm(productStore.product)

    const productData = (data) => {
        const commonData = {
            ...data,
            category_id: getValueFromKey(data, 'category_id')
            // blog_author_id: getValueFromKey(data, 'blog_author_id')
        }

        return isCreate.value ? commonData : { ...commonData, _method: 'PUT' }
    }

    if (isCreate.value) {
        form.transform(productData).post(route('product.store'))
    } else {
        form.transform(productData).post(
            route('product.update', props.product.id)
        )
    }
}
</script>
