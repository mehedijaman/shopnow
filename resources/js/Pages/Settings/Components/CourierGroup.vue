<template>
    <div class="space-y-6">
        <div class="rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
            Courier credentials are stored server-side and never exposed to customers. Enable sandbox mode for new
            integrations and register https://your-domain/webhooks/courier/{provider} in each courier dashboard to
            receive status updates.
        </div>

        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
            <div>
                <AppLabel for="default_courier" :value="__('Default Courier')" />
                <p class="mb-1 text-xs text-skin-neutral-9">Courier provider used for shipment booking.</p>
                <select
                    id="default_courier"
                    v-model="form.default_courier"
                    class="block w-full rounded-md bg-skin-neutral-1 px-3 py-2 text-sm ring-1 ring-skin-neutral-7 focus:outline-hidden focus:ring-2 focus:ring-skin-primary-10"
                    :class="{ 'input-error': errorsFields.includes('default_courier') }"
                >
                    <option v-for="c in couriers" :key="c.key" :value="c.key">{{ c.label }}</option>
                </select>
                <p v-if="errorsFields.includes('default_courier')" class="mt-1 text-sm text-red-500">
                    {{ errors.default_courier }}
                </p>
            </div>

            <div>
                <AppLabel for="default_weight_kg" :value="__('Default Parcel Weight (kg)')" />
                <p class="mb-1 text-xs text-skin-neutral-9">Sent to couriers when booking. Products do not store weight.</p>
                <AppInputText
                    id="default_weight_kg"
                    v-model="form.default_weight_kg"
                    type="number"
                    step="0.1"
                    min="0.1"
                    placeholder="1"
                    :class="{ 'input-error': errorsFields.includes('default_weight_kg') }"
                />
                <p v-if="errorsFields.includes('default_weight_kg')" class="mt-1 text-sm text-red-500">
                    {{ errors.default_weight_kg }}
                </p>
            </div>
        </div>

        <div
            v-for="courier in couriers"
            :key="courier.key"
            class="divide-y divide-skin-neutral-3 rounded-lg border border-skin-neutral-4"
        >
            <div class="flex flex-wrap items-center justify-between gap-3 px-4 py-3">
                <div>
                    <p class="text-sm font-semibold text-skin-neutral-12">{{ courier.label }}</p>
                    <p class="mt-0.5 text-xs text-skin-neutral-9">API credentials for booking and tracking.</p>
                </div>
                <div class="flex items-center gap-5">
                    <label v-if="courier.sandbox" class="flex items-center gap-2 text-sm text-skin-neutral-11">
                        <input
                            v-model="form[`${courier.key}_sandbox`]"
                            type="checkbox"
                            class="h-4 w-4 rounded border-skin-neutral-7 text-skin-primary-9 focus:ring-skin-primary-8"
                        />
                        {{ __('Sandbox') }}
                    </label>
                    <button
                        type="button"
                        role="switch"
                        :aria-checked="form[`${courier.key}_enabled`]"
                        class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer items-center rounded-full transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-skin-primary-8 focus:ring-offset-2"
                        :class="form[`${courier.key}_enabled`] ? 'bg-skin-primary-10' : 'bg-skin-neutral-5'"
                        @click="form[`${courier.key}_enabled`] = !form[`${courier.key}_enabled`]"
                    >
                        <span
                            class="inline-block h-4 w-4 transform rounded-full bg-white shadow-sm transition-transform duration-200"
                            :class="form[`${courier.key}_enabled`] ? 'translate-x-6' : 'translate-x-1'"
                        />
                    </button>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-6 px-4 py-4 sm:grid-cols-2">
                <div v-for="field in courier.fields" :key="field.key">
                    <AppLabel :for="`${courier.key}_${field.key}`" :value="__(field.label)" />
                    <AppInputText
                        :id="`${courier.key}_${field.key}`"
                        v-model="form[`${courier.key}_${field.key}`]"
                        :type="field.type || 'text'"
                        :class="{ 'input-error': errorsFields.includes(`${courier.key}_${field.key}`) }"
                    />
                    <p
                        v-if="errorsFields.includes(`${courier.key}_${field.key}`)"
                        class="mt-1 text-sm text-red-500"
                    >
                        {{ errors[`${courier.key}_${field.key}`] }}
                    </p>
                </div>

                <div>
                    <AppLabel :for="`${courier.key}_webhook_secret`" :value="__('Webhook Secret')" />
                    <p class="mb-1 text-xs text-skin-neutral-9">Verifies incoming status webhooks via HMAC signature.</p>
                    <AppInputText
                        :id="`${courier.key}_webhook_secret`"
                        v-model="form[`${courier.key}_webhook_secret`]"
                        type="password"
                        autocomplete="new-password"
                        :class="{ 'input-error': errorsFields.includes(`${courier.key}_webhook_secret`) }"
                    />
                    <p
                        v-if="errorsFields.includes(`${courier.key}_webhook_secret`)"
                        class="mt-1 text-sm text-red-500"
                    >
                        {{ errors[`${courier.key}_webhook_secret`] }}
                    </p>
                </div>

                <div>
                    <AppLabel :for="`${courier.key}_tracking_url`" :value="__('Tracking URL Template')" />
                    <p class="mb-1 text-xs text-skin-neutral-9">Tracking link containing {tracking}, e.g. https://…/track?code={tracking}</p>
                    <AppInputText
                        :id="`${courier.key}_tracking_url`"
                        v-model="form[`${courier.key}_tracking_url`]"
                        placeholder="https://…/track/{tracking}"
                        :class="{ 'input-error': errorsFields.includes(`${courier.key}_tracking_url`) }"
                    />
                    <p
                        v-if="errorsFields.includes(`${courier.key}_tracking_url`)"
                        class="mt-1 text-sm text-red-500"
                    >
                        {{ errors[`${courier.key}_tracking_url`] }}
                    </p>
                </div>
            </div>
        </div>

        <div class="rounded-lg border border-skin-neutral-4">
            <div class="flex items-center justify-between gap-4 border-b border-skin-neutral-4 px-4 py-3">
                <div>
                    <p class="text-sm font-semibold text-skin-neutral-12">{{ __('COD Fraud Checks') }}</p>
                    <p class="mt-0.5 text-xs text-skin-neutral-9">
                        Check a customer's cancel history across couriers after placing an order. High-risk orders are
                        flagged and require confirmation before booking.
                    </p>
                </div>
                <button
                    type="button"
                    role="switch"
                    :aria-checked="form.fraud_enabled"
                    class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer items-center rounded-full transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-skin-primary-8 focus:ring-offset-2"
                    :class="form.fraud_enabled ? 'bg-skin-primary-10' : 'bg-skin-neutral-5'"
                    @click="form.fraud_enabled = !form.fraud_enabled"
                >
                    <span
                        class="inline-block h-4 w-4 transform rounded-full bg-white shadow-sm transition-transform duration-200"
                        :class="form.fraud_enabled ? 'translate-x-6' : 'translate-x-1'"
                    />
                </button>
            </div>

            <div class="grid grid-cols-1 gap-6 px-4 py-4 sm:grid-cols-2">
                <div>
                    <AppLabel for="fraud_min_deliveries" :value="__('Minimum Deliveries Before Flagging')" />
                    <p class="mb-1 text-xs text-skin-neutral-9">Customers below this delivery count are never flagged.</p>
                    <AppInputText
                        id="fraud_min_deliveries"
                        v-model="form.fraud_min_deliveries"
                        type="number"
                        min="0"
                        :class="{ 'input-error': errorsFields.includes('fraud_min_deliveries') }"
                    />
                    <p v-if="errorsFields.includes('fraud_min_deliveries')" class="mt-1 text-sm text-red-500">
                        {{ errors.fraud_min_deliveries }}
                    </p>
                </div>

                <div>
                    <AppLabel for="fraud_cancel_ratio_threshold" :value="__('Cancel Ratio Threshold (%)')" />
                    <p class="mb-1 text-xs text-skin-neutral-9">Flag as high risk at or above this cancel ratio.</p>
                    <AppInputText
                        id="fraud_cancel_ratio_threshold"
                        v-model="form.fraud_cancel_ratio_threshold"
                        type="number"
                        min="0"
                        max="100"
                        :class="{ 'input-error': errorsFields.includes('fraud_cancel_ratio_threshold') }"
                    />
                    <p v-if="errorsFields.includes('fraud_cancel_ratio_threshold')" class="mt-1 text-sm text-red-500">
                        {{ errors.fraud_cancel_ratio_threshold }}
                    </p>
                </div>

                <div v-for="portal in fraudPortals" :key="portal.key" class="sm:col-span-2">
                    <p class="mb-2 text-sm font-medium text-skin-neutral-12">{{ portal.label }}</p>
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div>
                            <AppLabel :for="`fraud_${portal.key}_${portal.userKey}`" :value="portal.userLabel" />
                            <AppInputText
                                :id="`fraud_${portal.key}_${portal.userKey}`"
                                v-model="form[`fraud_${portal.key}_${portal.userKey}`]"
                                :class="{
                                    'input-error': errorsFields.includes(`fraud_${portal.key}_${portal.userKey}`),
                                }"
                            />
                            <p
                                v-if="errorsFields.includes(`fraud_${portal.key}_${portal.userKey}`)"
                                class="mt-1 text-sm text-red-500"
                            >
                                {{ errors[`fraud_${portal.key}_${portal.userKey}`] }}
                            </p>
                        </div>
                        <div>
                            <AppLabel :for="`fraud_${portal.key}_password`" :value="__('Password')" />
                            <AppInputText
                                :id="`fraud_${portal.key}_password`"
                                v-model="form[`fraud_${portal.key}_password`]"
                                type="password"
                                autocomplete="new-password"
                                :class="{ 'input-error': errorsFields.includes(`fraud_${portal.key}_password`) }"
                            />
                            <p
                                v-if="errorsFields.includes(`fraud_${portal.key}_password`)"
                                class="mt-1 text-sm text-red-500"
                            >
                                {{ errors[`fraud_${portal.key}_password`] }}
                            </p>
                        </div>
                    </div>
                </div>
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

const couriers = [
    {
        key: 'pathao',
        label: 'Pathao',
        sandbox: true,
        fields: [
            { key: 'client_id', label: 'Client ID' },
            { key: 'client_secret', label: 'Client Secret', type: 'password' },
            { key: 'username', label: 'Username' },
            { key: 'password', label: 'Password', type: 'password' },
            { key: 'store_id', label: 'Store ID' },
        ],
    },
    {
        key: 'steadfast',
        label: 'Steadfast',
        sandbox: false,
        fields: [
            { key: 'api_key', label: 'API Key' },
            { key: 'secret_key', label: 'Secret Key', type: 'password' },
        ],
    },
    {
        key: 'redx',
        label: 'RedX',
        sandbox: true,
        fields: [{ key: 'access_token', label: 'Access Token', type: 'password' }],
    },
    {
        key: 'ecourier',
        label: 'eCourier',
        sandbox: true,
        fields: [
            { key: 'api_key', label: 'API Key' },
            { key: 'api_secret', label: 'API Secret', type: 'password' },
            { key: 'user_id', label: 'User ID' },
        ],
    },
    {
        key: 'paperfly',
        label: 'Paperfly',
        sandbox: false,
        fields: [
            { key: 'username', label: 'Username' },
            { key: 'password', label: 'Password', type: 'password' },
            { key: 'api_key', label: 'API Key' },
        ],
    },
]

const fraudPortals = [
    { key: 'steadfast', userKey: 'user', label: 'Steadfast Portal', userLabel: 'Email' },
    { key: 'pathao', userKey: 'user', label: 'Pathao Portal', userLabel: 'Email' },
    { key: 'redx', userKey: 'phone', label: 'RedX Portal', userLabel: 'Phone' },
    { key: 'paperfly', userKey: 'user', label: 'Paperfly Portal', userLabel: 'Username' },
    { key: 'carrybee', userKey: 'phone', label: 'Carrybee Portal', userLabel: 'Phone' },
]
</script>
