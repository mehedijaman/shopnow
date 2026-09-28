<template>
    <Head :title="__('admin-auth::admin.reset_title')"></Head>
    <AppAuthShell>

        <!-- Logo -->
        <div class="mb-6 flex justify-center">
            <AppAuthLogo />
        </div>

        <!-- Card -->
        <div class="w-full max-w-md rounded-2xl bg-skin-neutral-1 p-8 shadow-md ring-1 ring-skin-neutral-4">

            <h2 class="mb-1 text-center text-2xl font-bold tracking-tight text-skin-neutral-12">
                {{ __('admin-auth::admin.reset_heading') }}
            </h2>
            <p class="mb-8 text-center text-sm text-skin-neutral-9">
                {{ __('admin-auth::admin.reset_subtitle') }}
            </p>

            <AppFormErrors class="mb-5" />

            <form class="space-y-5" @submit.prevent="submitForm">

                <div>
                    <AppLabel for="email" :value="__('common.field.email_address')" />
                    <AppInputText
                        id="email"
                        v-model="form.email"
                        type="email"
                        class="mt-1 w-full"
                        autocomplete="username"
                        :class="{ 'input-error': errorsFields.includes('email') }"
                    />
                </div>

                <div>
                    <AppLabel for="password" :value="__('common.field.new_password')" />
                    <AppInputPassword
                        id="password"
                        v-model="form.password"
                        name="password"
                        class="mt-1 w-full"
                        autocomplete="new-password"
                        :class="{ 'input-error': errorsFields.includes('password') }"
                    />
                </div>

                <div>
                    <AppLabel for="password_confirmation" :value="__('common.field.confirm_new_password')" />
                    <AppInputPassword
                        id="password_confirmation"
                        v-model="form.password_confirmation"
                        name="password_confirmation"
                        class="mt-1 w-full"
                        autocomplete="new-password"
                        :class="{ 'input-error': errorsFields.includes('password_confirmation') }"
                    />
                </div>

                <AppButton
                    class="btn btn-primary w-full justify-center"
                    type="submit"
                    :disabled="form.processing"
                >
                    <i v-if="form.processing" class="ri-loader-4-line mr-1.5 animate-spin"></i>
                    {{ __('admin-auth::admin.reset_password') }}
                </AppButton>

            </form>

            <p class="mt-6 text-center text-sm text-skin-neutral-9">
                {{ __('admin-auth::admin.remembered_it') }}
                <AppLink :href="route('adminAuth.loginForm')" class="ml-1 font-medium">
                    {{ __('admin-auth::admin.back_to_login') }}
                </AppLink>
            </p>

        </div>

    </AppAuthShell>
</template>

<script>
import GuestLayout from '@/Layouts/GuestLayout.vue'

export default {
    layout: GuestLayout
}
</script>

<script setup>
import { Head, useForm } from '@inertiajs/vue3'
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
    form.post(route('adminAuth.resetPassword'))
}

const { errorsFields } = useFormErrors()
</script>
