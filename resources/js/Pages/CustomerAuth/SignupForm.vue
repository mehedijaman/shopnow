<template>
    <Head title="Register"></Head>
    <AppAuthShell>
        <AppCustomerAuthLogo />

        <form @submit.prevent="submitForm">
            <AppCard class="w-80 space-y-2 bg-skin-neutral-2">
                <template #title>
                    <h3
                        class="text-center text-lg font-semibold tracking-tight"
                    >
                        {{ __('Sign up your account') }}
                    </h3>
                </template>

                <template #content>
                    <AppFormErrors class="mb-4" />

                    <div>
                        <AppLabel for="name">{{ __('Name') }}</AppLabel>
                        <AppInputText
                            id="name"
                            v-model="form.name"
                            name="name"
                            type="text"
                            class="w-full"
                            autocomplete="name"
                            :class="{
                                'input-error': errorsFields.includes('name')
                            }"
                        />
                    </div>

                    <div>
                        <AppLabel for="email">{{ __('Email') }}</AppLabel>
                        <AppInputText
                            id="email"
                            v-model="form.email"
                            name="email"
                            type="text"
                            class="w-full"
                            autocomplete="email"
                            :class="{
                                'input-error': errorsFields.includes('email')
                            }"
                        />
                    </div>

                    <div>
                        <AppLabel for="phone">{{ __('Phone') }}</AppLabel>
                        <AppInputText
                            id="phone"
                            v-model="form.phone"
                            name="phone"
                            type="text"
                            class="w-full"
                            autocomplete="phone"
                            :class="{
                                'input-error': errorsFields.includes('phone')
                            }"
                        />
                    </div>

                    <div class="mt-6">
                        <AppLabel for="password">{{ __('Password') }}</AppLabel>
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
                        <AppLabel for="confirm_password">{{
                            __('Confirm Password')
                        }}</AppLabel>
                        <AppInputPassword
                            id="confirm_password"
                            v-model="form.confirm_password"
                            name="confirm_password"
                            class="w-full"
                            autocomplete="confirm_password"
                            :class="{
                                'input-error':
                                    errorsFields.includes('confirm_password')
                            }"
                        />
                    </div>
                </template>

                <template #footer>
                    <AppButton
                        class="btn btn-primary flex w-full justify-center"
                        aria-label="botao submit"
                        type="submit"
                        @click="submitForm"
                        >{{ __('Sign up') }}</AppButton
                    >

                    <p class="mt-3">
                        <AppLink :href="route('customerAuth.loginForm')">
                            {{ __('Already have an account? Sing in') }}
                        </AppLink>
                    </p>
                </template>
            </AppCard>
        </form>
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
import { Head } from '@inertiajs/vue3'
import { useForm } from '@inertiajs/vue3'
import useFormErrors from '@/Composables/useFormErrors'

const form = useForm({
    name: '',
    email: '',
    phone: '',
    password: '',
    confirm_password: ''
})

function submitForm() {
    form.post(route('customerAuth.signup'))
}

const { errorsFields } = useFormErrors()
</script>
