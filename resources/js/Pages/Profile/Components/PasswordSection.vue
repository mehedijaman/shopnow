<template>
    <form class="space-y-6" @submit.prevent>
        <input
            type="email"
            autocomplete="username"
            :value="profileUser.email"
            class="sr-only"
            tabindex="-1"
            aria-hidden="true"
            readonly
        />

        <div>
            <AppLabel for="current_password" value="Current Password" />
            <p class="mb-1 text-xs text-skin-neutral-9">Enter your existing password to verify your identity.</p>
            <AppInputPassword
                id="current_password"
                v-model="form.current_password"
                autocomplete="current-password"
                :class="{ 'input-error': form.errors.current_password }"
            />
            <p v-if="form.errors.current_password" class="mt-1 text-sm text-skin-error">{{ form.errors.current_password }}</p>
        </div>

        <div>
            <AppLabel for="password" value="New Password" />
            <p class="mb-1 text-xs text-skin-neutral-9">Minimum 8 characters. Choose something strong.</p>
            <AppInputPassword
                id="password"
                v-model="form.password"
                autocomplete="new-password"
                :class="{ 'input-error': form.errors.password }"
            />
            <p v-if="form.errors.password" class="mt-1 text-sm text-skin-error">{{ form.errors.password }}</p>
        </div>

        <div>
            <AppLabel for="password_confirmation" value="Confirm New Password" />
            <p class="mb-1 text-xs text-skin-neutral-9">Re-enter your new password to confirm.</p>
            <AppInputPassword
                id="password_confirmation"
                v-model="form.password_confirmation"
                autocomplete="new-password"
                :class="{ 'input-error': form.errors.password_confirmation }"
            />
            <p v-if="form.errors.password_confirmation" class="mt-1 text-sm text-skin-error">{{ form.errors.password_confirmation }}</p>
        </div>

        <!-- Strength hint -->
        <div v-if="form.password" class="rounded-md bg-skin-neutral-3 p-3 text-xs text-skin-neutral-9">
            <p class="mb-1 font-medium text-skin-neutral-11">Password strength tips:</p>
            <ul class="list-inside list-disc space-y-0.5">
                <li :class="form.password.length >= 8 ? 'text-green-600' : 'text-red-500'">At least 8 characters</li>
                <li :class="/[A-Z]/.test(form.password) ? 'text-green-600' : 'text-skin-neutral-9'">Contains uppercase letter</li>
                <li :class="/[0-9]/.test(form.password) ? 'text-green-600' : 'text-skin-neutral-9'">Contains a number</li>
                <li :class="/[^A-Za-z0-9]/.test(form.password) ? 'text-green-600' : 'text-skin-neutral-9'">Contains a special character</li>
            </ul>
        </div>

    </form>
</template>

<script setup>
import { inject } from 'vue'

defineProps({
    profileUser: { type: Object, required: true },
    errorsFields: { type: Array, default: () => [] },
})

const form = inject('passwordForm')
</script>
