<template>
    <Head :title="title"></Head>
    <AppSectionHeader :title="title" :bread-crumb="breadCrumb">
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
import { inject } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { Head } from '@inertiajs/vue3'
import useTitle from '@/Composables/useTitle'
import useFormContext from '@/Composables/useFormContext'
import useFormErrors from '@/Composables/useFormErrors'

const translate = inject('translate')

const { title } = useTitle(translate('acl::admin.permission'))

const props = defineProps({
    permission: {
        type: Object,
        default: null
    }
})

const breadCrumb = [
    { label: translate('common.home'), href: route('dashboard.index') },
    { label: translate('acl::admin.permissions'), href: route('aclPermission.index') },
    { label: translate('acl::admin.permission'), last: true }
]

const form = useForm({
    name: props.permission ? props.permission.name : ''
})

const { isCreate } = useFormContext()

const submitForm = () => {
    if (isCreate.value) {
        form.post(route('aclPermission.store'))
    } else {
        form.put(route('aclPermission.update', props.permission.id))
    }
}

const { errorsFields } = useFormErrors()
</script>
