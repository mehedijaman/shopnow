<template>
    <div class="space-y-6">
        <div class="rounded-md bg-amber-50 p-4 text-sm text-amber-700 ring-1 ring-amber-200">
            <i class="ri-information-line mr-1"></i>
            {{ __('SMTP credentials are stored encrypted and never exposed publicly.') }}
        </div>

        <!-- Enable SMTP settings checkbox -->
        <div class="flex items-start gap-3 rounded-md bg-skin-neutral-2 p-4 ring-1 ring-skin-neutral-4">
            <div class="pt-0.5">
                <AppCheckbox
                    id="enable_smtp"
                    v-model="form.enable_smtp"
                    name="enable_smtp"
                />
            </div>
            <div>
                <AppLabel for="enable_smtp" class="font-semibold text-skin-neutral-12 cursor-pointer">
                    {{ __('Enable SMTP Settings') }}
                </AppLabel>
                <p class="text-xs text-skin-neutral-9 mt-1">
                    {{ __('When enabled, custom SMTP settings from the database will be used. When disabled, the fallback environment settings (.env) will be used.') }}
                </p>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
            <div>
                <AppLabel for="from_name" :value="__('From Name')" />
                <AppInputText
                    id="from_name"
                    v-model="form.from_name"
                    :disabled="!form.enable_smtp"
                    :class="[
                        { 'input-error': errorsFields.includes('from_name') },
                        !form.enable_smtp ? 'opacity-50 cursor-not-allowed bg-skin-neutral-2' : ''
                    ]"
                />
                <p v-if="errorsFields.includes('from_name')" class="mt-1 text-sm text-red-500">
                    {{ errors.from_name }}
                </p>
            </div>

            <div>
                <AppLabel for="from_address" :value="__('From Address')" />
                <AppInputText
                    id="from_address"
                    v-model="form.from_address"
                    type="email"
                    :disabled="!form.enable_smtp"
                    :class="[
                        { 'input-error': errorsFields.includes('from_address') },
                        !form.enable_smtp ? 'opacity-50 cursor-not-allowed bg-skin-neutral-2' : ''
                    ]"
                />
                <p v-if="errorsFields.includes('from_address')" class="mt-1 text-sm text-red-500">
                    {{ errors.from_address }}
                </p>
            </div>

            <div>
                <AppLabel for="host" :value="__('SMTP Host')" />
                <AppInputText
                    id="host"
                    v-model="form.host"
                    placeholder="smtp.example.com"
                    :disabled="!form.enable_smtp"
                    :class="[
                        { 'input-error': errorsFields.includes('host') },
                        !form.enable_smtp ? 'opacity-50 cursor-not-allowed bg-skin-neutral-2' : ''
                    ]"
                />
            </div>

            <div>
                <AppLabel for="port" :value="__('SMTP Port')" />
                <AppInputText
                    id="port"
                    v-model="form.port"
                    type="number"
                    placeholder="587"
                    :disabled="!form.enable_smtp"
                    :class="[
                        { 'input-error': errorsFields.includes('port') },
                        !form.enable_smtp ? 'opacity-50 cursor-not-allowed bg-skin-neutral-2' : ''
                    ]"
                />
            </div>

            <div>
                <AppLabel for="username" :value="__('SMTP Username')" />
                <AppInputText
                    id="username"
                    v-model="form.username"
                    autocomplete="off"
                    :disabled="!form.enable_smtp"
                    :class="[
                        { 'input-error': errorsFields.includes('username') },
                        !form.enable_smtp ? 'opacity-50 cursor-not-allowed bg-skin-neutral-2' : ''
                    ]"
                />
            </div>

            <div>
                <AppLabel for="password" :value="__('SMTP Password')" />
                <AppInputText
                    id="password"
                    v-model="form.password"
                    type="password"
                    autocomplete="new-password"
                    :disabled="!form.enable_smtp"
                    :class="[
                        { 'input-error': errorsFields.includes('password') },
                        !form.enable_smtp ? 'opacity-50 cursor-not-allowed bg-skin-neutral-2' : ''
                    ]"
                />
            </div>

            <div>
                <AppLabel for="encryption" :value="__('Encryption')" />
                <select
                    id="encryption"
                    v-model="form.encryption"
                    :disabled="!form.enable_smtp"
                    class="block w-full rounded-md bg-skin-neutral-1 px-3 py-2 text-sm ring-1 ring-skin-neutral-7 focus:outline-hidden focus:ring-2 focus:ring-skin-primary-10"
                    :class="[
                        { 'input-error': errorsFields.includes('encryption') },
                        !form.enable_smtp ? 'opacity-50 cursor-not-allowed bg-skin-neutral-3' : ''
                    ]"
                >
                    <option value="tls">TLS</option>
                    <option value="ssl">SSL</option>
                    <option value="starttls">STARTTLS</option>
                </select>
            </div>
        </div>

        <!-- Send Test Email Section -->
        <div class="mt-8 border-t border-skin-neutral-4 pt-8">
            <h3 class="text-base font-semibold text-skin-neutral-12 mb-1">
                {{ __('Send Test Email') }}
            </h3>
            <p class="text-sm text-skin-neutral-9 mb-4">
                {{ __('Test your SMTP configuration by sending a test email to a recipient.') }}
            </p>

            <div class="space-y-4 max-w-xl">
                <div>
                    <AppLabel for="test_recipient" :value="__('Recipient Email')" />
                    <AppInputText
                        id="test_recipient"
                        v-model="testMail.recipient"
                        type="email"
                        placeholder="recipient@example.com"
                        :class="{ 'input-error': testErrors.recipient }"
                    />
                    <p v-if="testErrors.recipient" class="mt-1 text-sm text-red-500">
                        {{ testErrors.recipient }}
                    </p>
                </div>

                <div>
                    <AppLabel for="test_message" :value="__('Message Body')" />
                    <textarea
                        id="test_message"
                        v-model="testMail.message"
                        rows="3"
                        class="mt-1 block w-full rounded-md border-0 bg-skin-neutral-1 px-3 py-2 text-skin-neutral-12 placeholder-skin-neutral-9 shadow-xs ring-1 ring-inset ring-skin-neutral-7 focus:ring-2 focus:ring-inset focus:ring-skin-neutral-7 sm:text-sm sm:leading-6"
                        placeholder="This is a test email message."
                        :class="{ 'input-error': testErrors.message }"
                    ></textarea>
                    <p v-if="testErrors.message" class="mt-1 text-sm text-red-500">
                        {{ testErrors.message }}
                    </p>
                </div>

                <div v-if="testResult" class="p-4 rounded-md text-sm ring-1" :class="testResult.success ? 'bg-green-50 text-green-700 ring-green-200' : 'bg-red-50 text-red-700 ring-red-200'">
                    <i :class="testResult.success ? 'ri-checkbox-circle-line' : 'ri-error-warning-line'" class="mr-1"></i>
                    {{ testResult.message }}
                </div>

                <div class="flex justify-start">
                    <AppButton
                        type="button"
                        class="btn btn-secondary"
                        :loading="sendingTestMail"
                        @click="sendTestMail"
                    >
                        {{ __('Send Test Email') }}
                    </AppButton>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { inject, ref } from 'vue'
import useFormErrors from '@/Composables/useFormErrors'
import axios from 'axios'

defineProps({
    errorsFields: { type: Array, default: () => [] },
})

const form = inject('settingsForm')
const { errors } = useFormErrors()

const sendingTestMail = ref(false)
const testResult = ref(null)
const testErrors = ref({})
const testMail = ref({
    recipient: '',
    message: 'This is a test email message from ShopNow.'
})

const sendTestMail = () => {
    testErrors.value = {}
    testResult.value = null

    if (!testMail.value.recipient) {
        testErrors.value.recipient = 'Recipient email is required.'
    }
    if (!testMail.value.message) {
        testErrors.value.message = 'Message body is required.'
    }

    if (Object.keys(testErrors.value).length > 0) {
        return
    }

    sendingTestMail.value = true

    axios.post(route('settings.mail.test'), {
        recipient: testMail.value.recipient,
        message: testMail.value.message,
        enable_smtp: form.enable_smtp,
        from_name: form.from_name,
        from_address: form.from_address,
        host: form.host,
        port: form.port,
        username: form.username,
        password: form.password,
        encryption: form.encryption
    })
    .then(response => {
        testResult.value = {
            success: true,
            message: response.data.message || 'Test email sent successfully!'
        }
    })
    .catch(error => {
        if (error.response && error.response.status === 422) {
            const backendErrors = error.response.data.errors
            Object.keys(backendErrors).forEach(key => {
                testErrors.value[key] = backendErrors[key][0]
            })
        } else {
            testResult.value = {
                success: false,
                message: error.response?.data?.message || 'Failed to send test email. Please check your SMTP settings.'
            }
        }
    })
    .finally(() => {
        sendingTestMail.value = false
    })
}
</script>
