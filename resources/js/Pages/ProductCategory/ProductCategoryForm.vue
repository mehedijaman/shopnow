<template>
    <AppSectionHeader title="Categories" :bread-crumb="breadCrumb">
    </AppSectionHeader>

    <AppCard class="w-full md:w-3/4 xl:w-1/2">
        <template #title> {{ title }} </template>
        <template #content>
            <AppFormErrors class="mb-4" />
            <form class="pt-4">
                <CategoryBody />

                <CategoryImage />

                <CategorySeo />
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
import CategoryBody from './Components/CategoryBody.vue'
import CategoryImage from './Components/CategoryImage.vue'
import CategorySeo from './Components/CategorySeo.vue'
import { useProductCategoryStore } from './ProductCategoryStore'
const categoryStore = useProductCategoryStore()

const props = defineProps({
    category: {
        type: Object,
        default: null
    }
})

if (props.category) {
    categoryStore.setCategory(props.category)
}

onUnmounted(() => {
    categoryStore.$reset()
})

const breadCrumb = [
    { label: 'Home', href: route('dashboard.index') },
    { label: 'Categories', href: route('productCategory.index') },
    { label: 'Category', last: true }
]

const { title } = useTitle('Category')
const { isCreate } = useFormContext()

const submitForm = () => {
    const form = useForm(categoryStore.category)

    if (isCreate.value) {
        form.post(route('productCategory.store'))
    } else {
        form.transform((data) => ({
            ...data,
            _method: 'PUT'
        })).post(route('productCategory.update', props.category.id))
    }
}

const { errorsFields } = useFormErrors()
</script>
