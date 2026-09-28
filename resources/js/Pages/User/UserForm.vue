<template>
    <Head :title="title"></Head>
    <AppSectionHeader :title="title" :bread-crumb="breadCrumb">
    </AppSectionHeader>

    <AppCard class="w-full md:w-3/4 xl:w-1/2">
        <template #title> {{ title }} </template>
        <template #content>
            <AppFormErrors class="mb-4" />
            <form>
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

                <div class="mt-6">
                    <AppLabel for="email">{{ __('user::admin.email') }}</AppLabel>
                    <AppInputText
                        id="email"
                        v-model="form.email"
                        type="text"
                        :class="{
                            'input-error': errorsFields.includes('email')
                        }"
                        autocomplete="off"
                    />
                </div>

                <div class="mt-6">
                    <AppLabel for="email">{{ __('common.field.password') }}</AppLabel>
                    <AppInputPassword
                        id="password"
                        v-model="form.password"
                        :class="{
                            'input-error': errorsFields.includes('password')
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

const { title } = useTitle(translate('user::admin.user'))

const props = defineProps({
    user: {
        type: Object,
        default: null
    }
})

const breadCrumb = [
    { label: translate('common.home'), href: route('dashboard.index') },
    { label: translate('user::admin.users'), href: route('user.index') },
    { label: translate('user::admin.user'), last: true }
]

const form = useForm({
    name: props.user ? props.user.name : '',
    email: props.user ? props.user.email : '',
    password: ''
})

const { isCreate } = useFormContext()

const submitForm = () => {
    if (isCreate.value) {
        form.post(route('user.store'))
    } else {
        form.put(route('user.update', props.user.id))
    }
}

const { errorsFields } = useFormErrors()
</script>
