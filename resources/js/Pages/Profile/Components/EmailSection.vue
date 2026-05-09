<template>
    <form class="space-y-6" @submit.prevent>

        <div>
            <AppLabel for="email" value="New Email Address" />
            <p class="mb-1 text-xs text-skin-neutral-9">Enter the new email address you want to use for your account.</p>
            <AppInputText
                id="email"
                v-model="form.email"
                type="email"
                autocomplete="username"
                placeholder="e.g. new@example.com"
                :class="{ 'input-error': form.errors.email }"
            />
            <p v-if="form.errors.email" class="mt-1 text-sm text-skin-error">{{ form.errors.email }}</p>
        </div>

        <div>
            <AppLabel for="email_current_password" value="Current Password" />
            <p class="mb-1 text-xs text-skin-neutral-9">Enter your current password to confirm the email change.</p>
            <AppInputPassword
                id="email_current_password"
                v-model="form.current_password"
                autocomplete="current-password"
                :class="{ 'input-error': form.errors.current_password }"
            />
            <p v-if="form.errors.current_password" class="mt-1 text-sm text-skin-error">{{ form.errors.current_password }}</p>
        </div>

    </form>
</template>

<script setup>
import { inject } from 'vue'

defineProps({
    profileUser: { type: Object, required: true },
    errorsFields: { type: Array, default: () => [] },
})

const form = inject('emailForm')
</script>
