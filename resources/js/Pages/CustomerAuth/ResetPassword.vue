<template>
    <AppAuthShell>
        <AppCustomerAuthLogo />

        <AppCard class="min-w-[360px] space-y-6 px-16">
            <template #title>
                <h3 class="text-2xl font-semibold tracking-tight">
                    {{ __('Password Reset') }}
                </h3>
            </template>

            <template #description>
                {{ __('Fill the form below to reset your password') }}.
            </template>

            <template #content>
                <AppFormErrors class="mb-4" />

                <form>
                    <div>
                        <AppLabel for="email">{{ __('Email') }}</AppLabel>
                        <AppInputText
                            id="email"
                            v-model="form.email"
                            type="text"
                            class="w-full"
                            :class="{
                                'input-error': errorsFields.includes('email')
                            }"
                        />
                    </div>

                    <div class="mt-6">
                        <AppLabel for="email">{{ __('Password') }}</AppLabel>
                        <AppInputPassword
                            id="password"
                            v-model="form.password"
                            name="password"
                            class="w-full"
                            autocomplete="current-password"
                            :class="{
                                'input-error': errorsFields.includes('password')
                            }"
                        />
                    </div>

                    <div class="mt-6">
                        <AppLabel for="password_confirmation">{{
                            __('Password Confirmation')
                        }}</AppLabel>
                        <AppInputPassword
                            id="password_confirmation"
                            v-model="form.password_confirmation"
                            name="password_confirmation"
                            class="w-full"
                            :class="{
                                'input-error': errorsFields.includes('password')
                            }"
                        />
                    </div>
                </form>
            </template>

            <template #footer>
                <AppButton class="btn btn-primary" @click="submitForm">
                    {{ __('Save') }}
                </AppButton>
            </template>
        </AppCard>
    </AppAuthShell>
</template>

<script>
import GuestLayout from '@/Layouts/GuestLayout.vue'
import AppCustomerAuthLogo from '@/Components/Auth/AppCustomerAuthLogo.vue'

export default {
    layout: GuestLayout
}
</script>

<script setup>
import { useForm } from '@inertiajs/vue3'
import useFormErrors from '@/Composables/useFormErrors'

const props = defineProps({
    email: {
        type: String,
        required: true
    },
    token: {
        type: String,
        required: true
    }
})

const form = useForm({
    email: props.email,
    token: props.token,
    password: '',
    password_confirmation: ''
})

function submitForm() {
    form.post(route('customerAuth.resetPassword'))
}

const { errorsFields } = useFormErrors()
</script>
