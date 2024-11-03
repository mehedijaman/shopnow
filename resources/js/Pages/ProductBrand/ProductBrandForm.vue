<template>
    <AppSectionHeader title="Product Brands" :bread-crumb="breadCrumb">
    </AppSectionHeader>

    <AppCard class="w-full md:w-3/4 xl:w-1/2">
        <template #title> {{ title }} </template>
        <template #content>
            <AppFormErrors class="mb-4" />
            <form class="pt-4">
                <BrandBody />

                <BrandImage />

                <BrandSeo />
            </form>
        </template>
        <template #footer>
            <AppButton class="btn btn-primary" @click="submitForm">
                Save
            </AppButton>
        </template>
    </AppCard>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3'
import { onUnmounted } from 'vue'
import useTitle from '@/Composables/useTitle'
import useFormContext from '@/Composables/useFormContext'
import useFormErrors from '@/Composables/useFormErrors'
import BrandBody from './Components/BrandBody.vue'
import BrandImage from './Components/BrandImage.vue'
import BrandSeo from './Components/BrandSeo.vue'
import { useProductBrandStore } from './ProductBrandStore'
const brandStore = useProductBrandStore()

const props = defineProps({
    brand: {
        type: Object,
        default: null
    }
})

if (props.brand) {
    brandStore.setBrand(props.brand)
}

onUnmounted(() => {
    brandStore.$reset()
})

const breadCrumb = [
    { label: 'Home', href: route('dashboard.index') },
    { label: 'Brands', href: route('productBrand.index') },
    { label: 'Brand', last: true }
]

const { title } = useTitle('Brand')
const { isCreate } = useFormContext()

const submitForm = () => {
    const form = useForm(brandStore.brand)

    if (isCreate.value) {
        form.post(route('productBrand.store'))
    } else {
        form.transform((data) => ({
            ...data,
            _method: 'PUT'
        })).post(route('productBrand.update', props.brand.id))
    }
}

const { errorsFields } = useFormErrors()
</script>
