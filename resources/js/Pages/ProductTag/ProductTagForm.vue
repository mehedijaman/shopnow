<template>
    <AppSectionHeader :title="__('product::admin.tag')" :bread-crumb="breadCrumb">
    </AppSectionHeader>

    <AppCard class="w-full md:w-3/4 xl:w-1/2">
        <template #title> {{ title }} </template>
        <template #content>
            <AppFormErrors class="mb-4" />
            <form @submit.prevent="submitForm">
                <div>
                    <AppLabel for="name">{{ __('common.field.name') }}</AppLabel>
                    <AppInputText
                        id="name"
                        v-model="form.name"
                        type="text"
                        :class="{
                            'input-error': errorsFields.includes('name')
                        }"
                        autocomplete="off"
                    />
                </div>
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
import { inject } from 'vue'

import useTitle from '@/Composables/useTitle'
import useFormContext from '@/Composables/useFormContext'
import useFormErrors from '@/Composables/useFormErrors'

const props = defineProps({
    tag: {
        type: Object,
        default: null
    }
})

const translate = inject('translate')

const breadCrumb = [
    { label: translate('common.home'), href: route('dashboard.index') },
    { label: translate('common.menu.product_tags'), href: route('productTag.index') },
    { label: translate('common.field.tag'), last: true }
]

const { title } = useTitle(translate('product::admin.tag'))

const form = useForm({
    name: props.tag ? props.tag.name : ''
})

const { isCreate } = useFormContext()

const submitForm = () => {
    if (isCreate.value) {
        form.post(route('productTag.store'))
    } else {
        form.put(route('productTag.update', props.tag.id))
    }
}

const { errorsFields } = useFormErrors()
</script>
