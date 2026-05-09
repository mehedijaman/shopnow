<template>
    <Head title="Forgot Password"></Head>
    <AppAuthShell>

        <!-- Logo -->
        <div class="mb-6 flex justify-center">
            <AppAuthLogo />
        </div>

        <!-- Card -->
        <div class="w-full max-w-md rounded-2xl bg-skin-neutral-1 p-8 shadow-md ring-1 ring-skin-neutral-4">

            <h2 class="mb-1 text-center text-2xl font-bold tracking-tight text-skin-neutral-12">
                Forgot your password?
            </h2>
            <p class="mb-8 text-center text-sm text-skin-neutral-9">
                Enter your email and we'll send you a link to reset your password.
            </p>

            <AppFormErrors class="mb-5" />

            <form class="space-y-5" @submit.prevent="submitForm">

                <div>
                    <AppLabel for="email" value="Email address" />
                    <AppInputText
                        id="email"
                        v-model="form.email"
                        type="email"
                        class="mt-1 w-full"
                        autocomplete="email"
                        placeholder="you@example.com"
                        :class="{ 'input-error': errorsFields.includes('email') }"
                    />
                </div>

                <AppButton
                    class="btn btn-primary w-full justify-center"
                    type="submit"
                    :disabled="form.processing"
                >
                    <i v-if="form.processing" class="ri-loader-4-line mr-1.5 animate-spin"></i>
                    Send Reset Link
                </AppButton>

            </form>

            <p class="mt-6 text-center text-sm text-skin-neutral-9">
                Remember your password?
                <AppLink :href="route('adminAuth.loginForm')" class="ml-1 font-medium">
                    Back to login
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

const form = useForm({
    email: ''
})

function submitForm() {
    form.post(route('adminAuth.sendResetLinkEmail'))
}

const { errorsFields } = useFormErrors()
</script>
