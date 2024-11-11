<template>
    <Head :title="title"></Head>
    <AppSectionHeader :title="title" :bread-crumb="breadCrumb">
    </AppSectionHeader>

    <AppCard class="w-full md:w-3/4 xl:w-1/2">
        <template #title>
            {{ title }}
        </template>
        <template #content>
            <AppFormErrors class="mb-4" />
            <form class="grid grid-cols-1 gap-2 md:grid-cols-2">
                <div>
                    <AppLabel for="name">
                        {{ __('Name') }}
                    </AppLabel>
                    <AppInputText
                        id="name"
                        v-model="form.name"
                        type="text"
                        :class="{
                            'input-error': errorsFields.includes('name')
                        }"
                    />
                </div>

                <div>
                    <AppLabel for="phone"> {{ __('Phone') }} </AppLabel>
                    <AppInputText
                        id="phone"
                        v-model="form.phone"
                        type="text"
                        :class="{
                            'input-error': errorsFields.includes('phone')
                        }"
                    />
                </div>

                <div>
                    <AppLabel for="email"> {{ __('Email') }} </AppLabel>
                    <AppInputText
                        id="email"
                        v-model="form.email"
                        type="email"
                        :class="{
                            'input-error': errorsFields.includes('email')
                        }"
                    />
                </div>

                <div>
                    <AppLabel for="password"> {{ __('Password') }} </AppLabel>
                    <AppInputText
                        id="password"
                        v-model="form.password"
                        type="password"
                        :class="{
                            'input-error': errorsFields.includes('password')
                        }"
                    />
                </div>

                <div>
                    <AppLabel for="confirm_password">
                        {{ __('Confirm Password') }}
                    </AppLabel>
                    <AppInputText
                        id="confirm_password"
                        v-model="form.confirm_password"
                        type="password"
                        :class="{
                            'input-error':
                                errorsFields.includes('confirm_password')
                        }"
                    />
                </div>

                <div class="mt-5 flex items-center">
                    <AppCheckbox
                        id="active"
                        v-model="form.active"
                        name="active"
                        :value="true"
                    />
                    <AppLabel for="active" class="ml-3"> Active </AppLabel>
                </div>
            </form>
        </template>
        <template #footer>
            <AppButton class="btn btn-primary" @click="submitForm">
                {{ __('Save') }}
            </AppButton>
        </template>
    </AppCard>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3'
import { Head } from '@inertiajs/vue3'
import useTitle from '@/Composables/useTitle'
import useFormContext from '@/Composables/useFormContext'
import useFormErrors from '@/Composables/useFormErrors'
import { onMounted } from 'vue'

const props = defineProps({
    customer: {
        type: Object,
        default: null
    }
})

const { title } = useTitle('Customer')

const breadCrumb = [
    { label: 'Home', href: route('dashboard.index') },
    { label: 'Customers', href: route('customer.index') },
    { label: title, last: true }
]

const form = useForm({
    name: props.customer ? props.customer.name : '',
    email: props.customer ? props.customer.email : '',
    phone: props.customer ? props.customer.phone : '',
    password: '',
    confirm_password: '',
    active: props.customer ? props.customer.active : '',
    address: props.customer ? props.customer.address : null
})

const { isCreate } = useFormContext()

const submitForm = () => {
    const postData = (data) => {
        const commonData = {
            ...data
        }

        return isCreate.value ? commonData : { ...commonData, _method: 'PUT' }
    }

    if (isCreate.value) {
        form.transform(postData).post(route('customer.store'))
    } else {
        form.transform(postData).post(
            route('customer.update', props.customer.id)
        )
    }
}

const { errorsFields } = useFormErrors()
</script>
