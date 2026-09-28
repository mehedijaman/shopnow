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

                <CategoryVisibility />
            </form>
        </template>
        <template #footer>
            <AppButton class="btn btn-primary" @click="submitForm">
                {{ __('common.save') }}
            </AppButton>
        </template>
    </AppCard>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3'
import { inject, onUnmounted } from 'vue'
import useTitle from '@/Composables/useTitle'
import useFormContext from '@/Composables/useFormContext'
import useFormErrors from '@/Composables/useFormErrors'
import CategoryBody from './Components/CategoryBody.vue'
import CategoryImage from './Components/CategoryImage.vue'
import CategorySeo from './Components/CategorySeo.vue'
import CategoryVisibility from './Components/CategoryVisibility.vue'
import { useCategoryStore } from './CategoryStore'
const categoryStore = useCategoryStore()

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

const translate = inject('translate')

const breadCrumb = [
    { label: translate('common.home'), href: route('dashboard.index') },
    { label: translate('blog::admin.categories'), href: route('blogCategory.index') },
    { label: translate('blog::admin.category'), last: true }
]

const { title } = useTitle(translate('blog::admin.category'))
const { isCreate } = useFormContext()

const submitForm = () => {
    const form = useForm(categoryStore.category)

    if (isCreate.value) {
        form.post(route('blogCategory.store'))
    } else {
        form.transform((data) => ({
            ...data,
            _method: 'PUT'
        })).post(route('blogCategory.update', props.category.id))
    }
}

const { errorsFields } = useFormErrors()
</script>
