<template>
    <div class="space-y-6">
        <!-- Security notice -->
        <AppAlert type="warning">
            <span class="text-sm leading-6">
                <span class="font-semibold">{{ __('Credentials are stored server-side') }}</span>
                {{ __('and never exposed to customers. Enable sandbox mode while testing, and register') }}
                <code class="rounded bg-skin-neutral-1 px-1 py-0.5 text-xs font-semibold">{{ __('/webhooks/courier/{provider}') }}</code>
                {{ __('in each courier dashboard to receive status updates.') }}
            </span>
        </AppAlert>

        <!-- ── Shipment defaults ── -->
        <section>
            <div class="mb-3">
                <h3 class="text-sm font-semibold text-skin-neutral-12">{{ __('Shipment Defaults') }}</h3>
                <p class="mt-0.5 text-xs text-skin-neutral-9">
                    {{ __('Applied to every booking when no per-order value is provided.') }}
                </p>
            </div>

            <div class="grid grid-cols-1 gap-5 rounded-lg border border-skin-neutral-4 bg-skin-neutral-2/40 p-4 sm:grid-cols-2">
                <div>
                    <AppLabel for="default_courier" :value="__('Default Courier')" />
                    <p class="mb-1 text-xs text-skin-neutral-9">{{ __('Courier provider used for shipment booking.') }}</p>
                    <select
                        id="default_courier"
                        v-model="form.default_courier"
                        class="block w-full rounded-md border-0 bg-skin-neutral-1 px-3 py-2 text-sm text-skin-neutral-12 shadow-xs ring-1 ring-inset ring-skin-neutral-7 focus:ring-2 focus:ring-inset focus:ring-skin-primary-9"
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
                    <p class="mb-1 text-xs text-skin-neutral-9">
                        {{ __('Sent to couriers when booking. Products do not store weight.') }}
                    </p>
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
        </section>

        <!-- ── Courier providers ── -->
        <section>
            <div class="mb-3 flex flex-wrap items-center justify-between gap-2">
                <div>
                    <h3 class="text-sm font-semibold text-skin-neutral-12">{{ __('Courier Providers') }}</h3>
                    <p class="mt-0.5 text-xs text-skin-neutral-9">
                        {{ __('Connect the courier accounts you book shipments with.') }}
                    </p>
                </div>
                <span
                    class="rounded-full border border-skin-neutral-4 bg-skin-neutral-2 px-2.5 py-1 text-[11px] font-semibold text-skin-neutral-11"
                >
                    {{ enabledCount }} / {{ couriers.length }} {{ __('enabled') }}
                </span>
            </div>

            <div class="space-y-4">
                <article
                    v-for="courier in couriers"
                    :key="courier.key"
                    class="overflow-hidden rounded-lg border transition-colors"
                    :class="isEnabled(courier.key) ? 'border-skin-neutral-5' : 'border-skin-neutral-4 bg-skin-neutral-2/40'"
                >
                    <header
                        class="flex flex-wrap items-center gap-3 border-b px-4 py-3"
                        :class="isEnabled(courier.key) ? 'border-skin-neutral-4 bg-skin-neutral-2' : 'border-skin-neutral-4 bg-skin-neutral-3/60'"
                    >
                        <span
                            class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-skin-neutral-1 text-skin-neutral-11 ring-1 ring-skin-neutral-4"
                        >
                            <i :class="courier.icon" class="text-lg"></i>
                        </span>

                        <div class="min-w-0 flex-1">
                            <div class="flex flex-wrap items-center gap-2">
                                <p class="text-sm font-semibold text-skin-neutral-12">{{ courier.label }}</p>
                                <span
                                    v-if="form.default_courier === courier.key"
                                    class="rounded-full bg-skin-primary-10 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wide text-skin-neutral-1"
                                >
                                    {{ __('Default') }}
                                </span>
                            </div>
                            <p class="mt-0.5 text-xs text-skin-neutral-9">{{ courier.description }}</p>
                        </div>

                        <div class="flex items-center gap-3">
                            <label
                                v-if="courier.sandbox"
                                class="flex cursor-pointer items-center gap-2 text-xs font-medium text-skin-neutral-11"
                            >
                                <AppCheckbox v-model="form[`${courier.key}_sandbox`]" />
                                {{ __('Sandbox') }}
                            </label>
                            <span
                                class="rounded-full px-2 py-1 text-[11px] font-semibold"
                                :class="isEnabled(courier.key) ? 'bg-skin-success-light text-skin-success' : 'bg-skin-neutral-4 text-skin-neutral-11'"
                            >
                                {{ isEnabled(courier.key) ? __('Enabled') : __('Disabled') }}
                            </span>
                            <AppSwitch v-model="form[`${courier.key}_enabled`]" />
                        </div>
                    </header>

                    <div class="p-4">
                        <p class="mb-3 text-xs font-semibold uppercase tracking-wide text-skin-neutral-9">
                            {{ __('API Credentials') }}
                        </p>
                        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                            <div v-for="field in courier.fields" :key="field.key">
                                <AppLabel :for="`${courier.key}_${field.key}`" :value="__(field.label)" />
                                <p v-if="field.hint" class="mb-1 text-xs text-skin-neutral-9">{{ field.hint }}</p>
                                <AppInputText
                                    :id="`${courier.key}_${field.key}`"
                                    v-model="form[`${courier.key}_${field.key}`]"
                                    :type="field.type || 'text'"
                                    :placeholder="field.placeholder || ''"
                                    :autocomplete="field.type === 'password' ? 'new-password' : undefined"
                                    :class="{ 'input-error': errorsFields.includes(`${courier.key}_${field.key}`) }"
                                />
                                <p
                                    v-if="errorsFields.includes(`${courier.key}_${field.key}`)"
                                    class="mt-1 text-sm text-red-500"
                                >
                                    {{ errors[`${courier.key}_${field.key}`] }}
                                </p>
                            </div>
                        </div>

                        <div class="mt-5 border-t border-skin-neutral-4 pt-4">
                            <p class="mb-3 text-xs font-semibold uppercase tracking-wide text-skin-neutral-9">
                                <i class="ri-webhook-line mr-1"></i>{{ __('Webhooks & Tracking') }}
                            </p>
                            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                                <div>
                                    <AppLabel :for="`${courier.key}_webhook_secret`" :value="__('Webhook Secret')" />
                                    <p class="mb-1 text-xs text-skin-neutral-9">
                                        {{ __('Verifies incoming status webhooks via HMAC signature.') }}
                                    </p>
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
                                    <p class="mb-1 text-xs text-skin-neutral-9">
                                        {{ __('Tracking link containing {tracking}, e.g. https://…/track?code={tracking}') }}
                                    </p>
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
                    </div>
                </article>
            </div>
        </section>

        <!-- ── COD fraud checks ── -->
        <section>
            <div class="mb-3">
                <h3 class="text-sm font-semibold text-skin-neutral-12">{{ __('COD Fraud Checks') }}</h3>
                <p class="mt-0.5 text-xs text-skin-neutral-9">
                    {{ __('Flag high-risk COD customers and confirm them before booking.') }}
                </p>
            </div>

            <div class="overflow-hidden rounded-lg border border-skin-neutral-4 bg-skin-neutral-2/40">
                <header
                    class="flex flex-wrap items-center justify-between gap-4 border-b border-skin-neutral-4 bg-skin-neutral-3/60 px-4 py-3"
                >
                    <div class="flex min-w-0 items-center gap-3">
                        <span
                            class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-skin-neutral-1 text-skin-neutral-11 ring-1 ring-skin-neutral-4"
                        >
                            <i class="ri-shield-check-line text-lg"></i>
                        </span>
                        <p class="max-w-2xl text-xs text-skin-neutral-9">
                            {{ __('Check a customer\'s cancel history across couriers after placing an order. High-risk orders are flagged and require confirmation before booking.') }}
                        </p>
                    </div>
                    <div class="flex items-center gap-3">
                        <span
                            class="rounded-full px-2 py-1 text-[11px] font-semibold"
                            :class="form.fraud_enabled ? 'bg-skin-success-light text-skin-success' : 'bg-skin-neutral-4 text-skin-neutral-11'"
                        >
                            {{ form.fraud_enabled ? __('Enabled') : __('Disabled') }}
                        </span>
                        <AppSwitch v-model="form.fraud_enabled" />
                    </div>
                </header>

                <div class="space-y-5 p-4">
                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                        <div>
                            <AppLabel for="fraud_min_deliveries" :value="__('Minimum Deliveries Before Flagging')" />
                            <p class="mb-1 text-xs text-skin-neutral-9">
                                {{ __('Customers below this delivery count are never flagged.') }}
                            </p>
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
                            <p class="mb-1 text-xs text-skin-neutral-9">
                                {{ __('Flag as high risk at or above this cancel ratio.') }}
                            </p>
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
                    </div>

                    <div class="border-t border-skin-neutral-4 pt-4">
                        <p class="text-xs font-semibold uppercase tracking-wide text-skin-neutral-9">
                            {{ __('Fraud Data Sources') }}
                        </p>
                        <p class="mb-3 mt-0.5 text-xs text-skin-neutral-9">
                            {{ __('Each enabled source is checked independently — a clean result from one never masks a risky result from another.') }}
                        </p>

                        <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
                            <div class="rounded-lg border border-skin-neutral-4 bg-skin-neutral-1">
                                <header class="flex flex-wrap items-start justify-between gap-3 p-4">
                                    <div class="flex min-w-0 items-start gap-3">
                                        <span
                                            class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-skin-neutral-2 text-skin-neutral-11 ring-1 ring-skin-neutral-4"
                                        >
                                            <i class="ri-flashlight-line text-lg"></i>
                                        </span>
                                        <div class="min-w-0">
                                            <p class="text-sm font-semibold text-skin-neutral-12">
                                                {{ __('SteadFast API') }}
                                            </p>
                                            <p class="mt-0.5 text-xs text-skin-neutral-9">
                                                {{ __('Fraud score endpoint queried with the SteadFast API keys from the provider card above.') }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="flex shrink-0 items-center gap-3">
                                        <span
                                            class="rounded-full px-2 py-1 text-[11px] font-semibold"
                                            :class="steadfastFraudReady ? 'bg-skin-success-light text-skin-success' : 'bg-skin-neutral-4 text-skin-neutral-11'"
                                        >
                                            {{ steadfastFraudReady ? __('Ready') : __('Missing API keys') }}
                                        </span>
                                        <AppSwitch v-model="form.fraud_steadfast_enabled" />
                                    </div>
                                </header>
                            </div>

                            <div class="rounded-lg border border-skin-neutral-4 bg-skin-neutral-1">
                                <header class="flex flex-wrap items-start justify-between gap-3 p-4">
                                    <div class="flex min-w-0 items-start gap-3">
                                        <span
                                            class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-skin-neutral-2 text-skin-neutral-11 ring-1 ring-skin-neutral-4"
                                        >
                                            <i class="ri-search-eye-line text-lg"></i>
                                        </span>
                                        <div class="min-w-0">
                                            <p class="text-sm font-semibold text-skin-neutral-12">
                                                {{ __('BD Courier API') }}
                                            </p>
                                            <p class="mt-0.5 text-xs text-skin-neutral-9">
                                                {{ __('Per-courier delivery stats and fraud reports for the customer\'s phone number.') }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="flex shrink-0 items-center gap-3">
                                        <span
                                            class="rounded-full px-2 py-1 text-[11px] font-semibold"
                                            :class="bdcourierFraudReady ? 'bg-skin-success-light text-skin-success' : 'bg-skin-neutral-4 text-skin-neutral-11'"
                                        >
                                            {{ bdcourierFraudReady ? __('Ready') : __('Missing API key') }}
                                        </span>
                                        <AppSwitch v-model="form.fraud_bdcourier_enabled" />
                                    </div>
                                </header>

                                <div class="grid grid-cols-1 gap-4 border-t border-skin-neutral-4 p-4 sm:grid-cols-2">
                                    <div>
                                        <AppLabel for="bdcourier_api_key" :value="__('API Key')" />
                                        <p class="mb-1 text-xs text-skin-neutral-9">
                                            {{ __('Bearer key for /courier-check — never exposed to customers.') }}
                                        </p>
                                        <AppInputText
                                            id="bdcourier_api_key"
                                            v-model="form.bdcourier_api_key"
                                            type="password"
                                            autocomplete="new-password"
                                            placeholder="••••••••"
                                            :class="{ 'input-error': errorsFields.includes('bdcourier_api_key') }"
                                        />
                                        <p v-if="errorsFields.includes('bdcourier_api_key')" class="mt-1 text-sm text-red-500">
                                            {{ errors.bdcourier_api_key }}
                                        </p>
                                    </div>

                                    <div>
                                        <AppLabel for="bdcourier_endpoint" :value="__('API Endpoint')" />
                                        <p class="mb-1 text-xs text-skin-neutral-9">
                                            {{ __('Base URL of the BD Courier API.') }}
                                        </p>
                                        <AppInputText
                                            id="bdcourier_endpoint"
                                            v-model="form.bdcourier_endpoint"
                                            placeholder="https://api.bdcourier.com"
                                            :class="{ 'input-error': errorsFields.includes('bdcourier_endpoint') }"
                                        />
                                        <p v-if="errorsFields.includes('bdcourier_endpoint')" class="mt-1 text-sm text-red-500">
                                            {{ errors.bdcourier_endpoint }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</template>

<script setup>
import { computed, inject } from 'vue'
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
        icon: 'ri-e-bike-line',
        description: 'City and nationwide delivery — requires Pathao store credentials.',
        sandbox: true,
        fields: [
            { key: 'client_id', label: 'Client ID', placeholder: 'Pathao client ID' },
            { key: 'client_secret', label: 'Client Secret', type: 'password', placeholder: '••••••••' },
            { key: 'username', label: 'Username', placeholder: 'Portal username' },
            { key: 'password', label: 'Password', type: 'password', placeholder: '••••••••' },
            { key: 'store_id', label: 'Store ID', placeholder: 'Store ID' },
        ],
    },
    {
        key: 'steadfast',
        label: 'Steadfast',
        icon: 'ri-flashlight-line',
        description: 'Nationwide COD delivery — requires an API key and secret key.',
        sandbox: false,
        fields: [
            { key: 'api_key', label: 'API Key', placeholder: 'Steadfast API key' },
            { key: 'secret_key', label: 'Secret Key', type: 'password', placeholder: '••••••••' },
            {
                key: 'base_url',
                label: 'API Base URL',
                hint: 'Leave empty to use the default Steadfast endpoint.',
                placeholder: 'https://portal.steadfast.com.bd/api/v1',
            },
        ],
    },
    {
        key: 'redx',
        label: 'RedX',
        icon: 'ri-flight-takeoff-line',
        description: 'Nationwide parcel delivery — requires an access token.',
        sandbox: true,
        fields: [{ key: 'access_token', label: 'Access Token', type: 'password', placeholder: '••••••••' }],
    },
    {
        key: 'ecourier',
        label: 'eCourier',
        icon: 'ri-truck-line',
        description: 'Corporate courier service — requires API key, secret and user ID.',
        sandbox: true,
        fields: [
            { key: 'api_key', label: 'API Key', placeholder: 'eCourier API key' },
            { key: 'api_secret', label: 'API Secret', type: 'password', placeholder: '••••••••' },
            { key: 'user_id', label: 'User ID', placeholder: 'User ID' },
        ],
    },
    {
        key: 'paperfly',
        label: 'Paperfly',
        icon: 'ri-send-plane-2-line',
        description: 'Nationwide delivery — requires portal username, password and API key.',
        sandbox: false,
        fields: [
            { key: 'username', label: 'Username', placeholder: 'Portal username' },
            { key: 'password', label: 'Password', type: 'password', placeholder: '••••••••' },
            { key: 'api_key', label: 'API Key', placeholder: 'Paperfly API key' },
        ],
    },
]

function isEnabled(key) {
    return Boolean(form[`${key}_enabled`])
}

const enabledCount = computed(() => couriers.filter((courier) => isEnabled(courier.key)).length)

const steadfastFraudReady = computed(() => Boolean(form.steadfast_api_key && form.steadfast_secret_key))

const bdcourierFraudReady = computed(() => Boolean(form.bdcourier_api_key && form.bdcourier_endpoint))
</script>
