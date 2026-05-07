<template>
    <div class="space-y-6">
        <div class="rounded-md bg-amber-50 p-4 text-sm text-amber-700 ring-1 ring-amber-200">
            <i class="ri-information-line mr-1"></i>
            {{ __('SMTP credentials are stored encrypted and never exposed publicly.') }}
        </div>

        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
            <div>
                <AppLabel for="from_name" :value="__('From Name')" />
                <AppInputText
                    id="from_name"
                    v-model="form.from_name"
                    :class="{ 'input-error': errorsFields.includes('from_name') }"
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
                    :class="{ 'input-error': errorsFields.includes('from_address') }"
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
                    :class="{ 'input-error': errorsFields.includes('host') }"
                />
            </div>

            <div>
                <AppLabel for="port" :value="__('SMTP Port')" />
                <AppInputText
                    id="port"
                    v-model="form.port"
                    type="number"
                    placeholder="587"
                    :class="{ 'input-error': errorsFields.includes('port') }"
                />
            </div>

            <div>
                <AppLabel for="username" :value="__('SMTP Username')" />
                <AppInputText
                    id="username"
                    v-model="form.username"
                    autocomplete="off"
                    :class="{ 'input-error': errorsFields.includes('username') }"
                />
            </div>

            <div>
                <AppLabel for="password" :value="__('SMTP Password')" />
                <AppInputText
                    id="password"
                    v-model="form.password"
                    type="password"
                    autocomplete="new-password"
                    :class="{ 'input-error': errorsFields.includes('password') }"
                />
            </div>

            <div>
                <AppLabel for="encryption" :value="__('Encryption')" />
                <select
                    id="encryption"
                    v-model="form.encryption"
                    class="block w-full rounded-md bg-skin-neutral-1 px-3 py-2 text-sm ring-1 ring-skin-neutral-7 focus:outline-none focus:ring-2 focus:ring-skin-primary-10"
                    :class="{ 'input-error': errorsFields.includes('encryption') }"
                >
                    <option value="tls">TLS</option>
                    <option value="ssl">SSL</option>
                    <option value="starttls">STARTTLS</option>
                </select>
            </div>
        </div>
    </div>
</template>

<script setup>
import { inject } from 'vue'
import useFormErrors from '@/Composables/useFormErrors'

defineProps({
    errorsFields: { type: Array, default: () => [] },
})

const form = inject('settingsForm')
const { errors } = useFormErrors()
</script>
