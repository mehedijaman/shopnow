<template>
    <Head title="Login"></Head>
    <AppAuthShell>

        <!-- Logo -->
        <div class="mb-6 flex justify-center">
            <AppAuthLogo />
        </div>

        <!-- Card -->
        <div class="w-full max-w-md rounded-2xl bg-skin-neutral-1 p-8 shadow-md ring-1 ring-skin-neutral-4">

            <h2 class="mb-1 text-center text-2xl font-bold tracking-tight text-skin-neutral-12">
                Welcome back
            </h2>
            <p class="mb-8 text-center text-sm text-skin-neutral-9">
                Sign in to your admin account
            </p>

            <AppFormErrors class="mb-5" />

            <form class="space-y-5" @submit.prevent="submitForm">

                <div>
                    <AppLabel for="email" value="Email address" />
                    <AppInputText
                        id="email"
                        v-model="form.email"
                        name="email"
                        type="email"
                        class="mt-1 w-full"
                        autocomplete="email"
                        placeholder="you@example.com"
                        :class="{ 'input-error': errorsFields.includes('email') }"
                    />
                </div>

                <div>
                    <div class="flex items-center justify-between">
                        <AppLabel for="password" value="Password" />
                        <AppLink :href="route('adminAuth.forgotPassword')" class="text-xs">
                            Forgot password?
                        </AppLink>
                    </div>
                    <AppInputPassword
                        id="password"
                        v-model="form.password"
                        name="password"
                        class="mt-1 w-full"
                        autocomplete="current-password"
                        :class="{ 'input-error': errorsFields.includes('password') }"
                    />
                </div>

                <div class="flex items-center gap-2">
                    <AppCheckbox
                        id="remember"
                        v-model="form.remember"
                        name="remember"
                        :value="true"
                    />
                    <AppLabel for="remember" value="Remember me" class="cursor-pointer" />
                </div>

                <AppButton
                    class="btn btn-primary w-full justify-center"
                    type="submit"
                    :disabled="form.processing"
                >
                    <i v-if="form.processing" class="ri-loader-4-line mr-1.5 animate-spin"></i>
                    Sign in
                </AppButton>

            </form>
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
    email: 'user@example.com',
    password: 'password',
    remember: false
})

function submitForm() {
    form.post(route('adminAuth.login'))
}

const { errorsFields } = useFormErrors()
</script>
