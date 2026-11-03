<script setup>
import { computed, inject, onUnmounted, ref, watch } from 'vue'
import { Link } from '@inertiajs/vue3'
import axios from 'axios'

const form = inject('settingsForm')
const meta = inject('smsMeta')
const t = inject('translate')
const locale = ref('en')
const recipient = ref('')
const testMessage = ref('')
const sending = ref(false)
const notice = ref('')
const failed = ref(false)
const statuses = [
    'pending',
    'processing',
    'shipped',
    'delivered',
    'completed',
    'cancelled'
]
const fields = computed(() => [
    ...(meta.value.providers[form.provider]?.fields ?? []),
    ...(meta.value.providers[form.provider]?.optional_fields ?? [])
])
const templates = computed(() => [
    { key: `placement_${locale.value}`, label: t('settings::sms.placement') },
    ...statuses.map((status) => ({
        key: `status_${status}_${locale.value}`,
        label: t(`order::enums.order_status.${status}`)
    })),
    { key: `otp_${locale.value}`, label: t('settings::sms.otp') }
])
const canReadHistory = computed(
    () => meta.value.can_view_activity || meta.value.can_test
)
const controller = new AbortController()

watch(
    () => form.provider,
    () => {
        form.credentials = {}
        form.remove_credentials = false
    }
)

function estimate(text) {
    const basic = new Set(
        `@£$¥èéùìòÇ\nØø\rÅåΔ_ΦΓΛΩΠΨΣΘΞÆæßÉ !"#¤%&'()*+,-./0123456789:;<=>?¡ABCDEFGHIJKLMNOPQRSTUVWXYZÄÖÑÜ§¿abcdefghijklmnopqrstuvwxyzäöñüà`
    )
    const extended = new Set('^{}\\[~]|€\f')
    let units = 0
    let gsm = true
    for (const character of text) {
        if (basic.has(character)) units += 1
        else if (extended.has(character)) units += 2
        else {
            gsm = false
            break
        }
    }
    const length = gsm ? units : text.length
    const single = gsm ? 160 : 70
    return length <= single
        ? length
            ? 1
            : 0
        : Math.ceil(length / (gsm ? 153 : 67))
}

async function sendTest() {
    if (sending.value) return
    sending.value = true
    notice.value = ''
    failed.value = false
    try {
        const response = await axios.post(
            route('settings.sms.test'),
            { phone: recipient.value, message: testMessage.value },
            { signal: controller.signal }
        )
        notice.value = response.data.message
    } catch (error) {
        if (!axios.isCancel(error)) {
            failed.value = true
            notice.value =
                Object.values(error.response?.data?.errors ?? {})
                    .flat()
                    .join(' ') || t('settings::sms.request_failed')
        }
    } finally {
        sending.value = false
    }
}

onUnmounted(() => controller.abort())
</script>

<template>
    <div class="space-y-8">
        <div v-if="canReadHistory" class="flex justify-end">
            <Link
                :href="route('settings.sms.history')"
                class="btn btn-secondary"
            >
                <i class="ri-history-line mr-1" aria-hidden="true"></i>
                {{ t('settings::sms.view_history') }}
            </Link>
        </div>
        <div class="space-y-4">
            <label
                class="flex items-center gap-3 text-sm font-semibold text-skin-neutral-12"
            >
                <AppCheckbox v-model="form.enabled" />
                {{ t('settings::sms.enabled') }}
            </label>
            <div>
                <AppLabel
                    for="sms-provider"
                    :value="t('settings::sms.provider')"
                />
                <select
                    id="sms-provider"
                    v-model="form.provider"
                    class="w-full rounded-md border border-skin-neutral-6 bg-skin-neutral-1 p-2 text-sm text-skin-neutral-12"
                >
                    <option
                        v-for="(provider, key) in meta.providers"
                        :key="key"
                        :value="key"
                        :disabled="Boolean(provider.unavailable_reason)"
                    >
                        {{ provider.name
                        }}{{
                            provider.unavailable_reason
                                ? ` — ${t(`settings::sms.${provider.unavailable_reason}`)}`
                                : ''
                        }}
                    </option>
                </select>
            </div>
            <p class="text-sm text-skin-neutral-9">
                {{ t('settings::sms.credentials_hint') }}
            </p>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div v-for="field in fields" :key="`${form.provider}-${field}`">
                    <AppLabel :for="`sms-${field}`" :value="field" />
                    <AppInputText
                        :id="`sms-${field}`"
                        v-model="form.credentials[field]"
                        :type="
                            /key|token|secret|password|passwd|pwd/i.test(field)
                                ? 'password'
                                : 'text'
                        "
                        autocomplete="new-password"
                        :placeholder="
                            meta.providers[form.provider]?.defaults?.[field] ??
                            t(
                                meta.configured[form.provider]?.[field]
                                    ? 'settings::sms.configured'
                                    : 'settings::sms.not_configured'
                            )
                        "
                    />
                    <p
                        v-if="form.errors[`credentials.${field}`]"
                        class="mt-1 text-sm text-red-500"
                    >
                        {{ form.errors[`credentials.${field}`] }}
                    </p>
                </div>
            </div>
            <label class="flex items-center gap-3 text-sm text-skin-neutral-11">
                <AppCheckbox v-model="form.remove_credentials" />
                {{ t('settings::sms.remove_credentials') }}
            </label>
        </div>
        <div
            v-if="meta.can_test"
            class="space-y-4 border-t border-skin-neutral-4 pt-6"
        >
            <h3 class="font-semibold text-skin-neutral-12">
                {{ t('settings::sms.test') }}
            </h3>
            <p class="text-xs text-skin-neutral-9">
                {{ t('settings::sms.test_hint') }}
            </p>
            <div>
                <AppLabel for="test-phone" :value="t('settings::sms.phone')" />
                <AppInputText
                    id="test-phone"
                    v-model="recipient"
                    type="tel"
                    placeholder="01XXXXXXXXX"
                />
            </div>
            <div>
                <AppLabel
                    for="test-message"
                    :value="t('settings::sms.message')"
                />
                <textarea
                    id="test-message"
                    v-model="testMessage"
                    rows="2"
                    maxlength="480"
                    class="w-full rounded-md border border-skin-neutral-6 bg-skin-neutral-1 p-3 text-sm text-skin-neutral-12"
                ></textarea>
            </div>
            <AppButton
                type="button"
                class="btn btn-primary disabled:cursor-not-allowed disabled:opacity-70"
                :disabled="sending || !recipient || !testMessage"
                @click="sendTest"
            >
                <i
                    :class="
                        sending
                            ? 'ri-loader-4-line animate-spin'
                            : 'ri-send-plane-line'
                    "
                    class="mr-2"
                    aria-hidden="true"
                ></i>
                {{
                    t(sending ? 'settings::sms.sending' : 'settings::sms.send')
                }}
            </AppButton>
            <p
                v-if="notice"
                role="status"
                :class="failed ? 'text-red-600' : 'text-green-600'"
                class="text-sm"
            >
                {{ notice }}
            </p>
        </div>
        <div class="space-y-4 border-t border-skin-neutral-4 pt-6">
            <label
                v-for="key in ['order_placed', 'order_updated', 'otp_enabled']"
                :key="key"
                class="flex items-center gap-3 text-sm font-medium text-skin-neutral-12"
            >
                <AppCheckbox v-model="form[key]" />
                {{ t(`settings::sms.${key}`) }}
            </label>
            <p class="text-xs text-skin-neutral-9">
                {{ t('settings::sms.otp_hint') }}
            </p>
            <fieldset class="space-y-3">
                <legend class="text-sm font-semibold text-skin-neutral-12">
                    {{ t('settings::sms.statuses') }}
                </legend>
                <div class="flex flex-wrap gap-4">
                    <label
                        v-for="status in statuses"
                        :key="status"
                        class="flex items-center gap-2 text-sm text-skin-neutral-11"
                    >
                        <input
                            v-model="form.statuses"
                            type="checkbox"
                            :value="status"
                            class="rounded border-skin-neutral-6"
                        />
                        {{ t(`order::enums.order_status.${status}`) }}
                    </label>
                </div>
            </fieldset>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                <div
                    v-for="key in [
                        'otp_expiry_minutes',
                        'otp_cooldown_seconds',
                        'otp_max_attempts'
                    ]"
                    :key="key"
                >
                    <AppLabel :for="key" :value="t(`settings::sms.${key}`)" />
                    <AppInputText :id="key" v-model="form[key]" type="number" />
                </div>
            </div>
        </div>
        <div class="space-y-4 border-t border-skin-neutral-4 pt-6">
            <div class="flex items-center justify-between gap-3">
                <h3 class="font-semibold text-skin-neutral-12">
                    {{ t('settings::sms.templates') }}
                </h3>
                <select
                    v-model="locale"
                    :aria-label="t('settings::sms.template_language')"
                    class="rounded-md border border-skin-neutral-6 bg-skin-neutral-1 p-2 text-sm text-skin-neutral-12"
                >
                    <option value="en">
                        {{ t('settings::sms.language_en') }}
                    </option>
                    <option value="bn">
                        {{ t('settings::sms.language_bn') }}
                    </option>
                </select>
            </div>
            <p class="text-xs text-skin-neutral-9">
                {{ t('settings::sms.placeholders') }}
            </p>
            <div v-for="template in templates" :key="template.key">
                <AppLabel :for="template.key" :value="template.label" />
                <textarea
                    :id="template.key"
                    v-model="form[template.key]"
                    rows="3"
                    maxlength="480"
                    class="w-full rounded-md border border-skin-neutral-6 bg-skin-neutral-1 p-3 text-sm text-skin-neutral-12"
                ></textarea>
                <p class="text-xs text-skin-neutral-9">
                    {{ form[template.key]?.length ?? 0 }}
                    {{ t('settings::sms.characters') }} ·
                    {{ estimate(form[template.key] ?? '') }}
                    {{ t('settings::sms.segments') }}
                </p>
            </div>
        </div>
        <div
            v-if="Object.keys(form.errors).length"
            role="alert"
            class="rounded-md bg-red-50 p-4 text-sm text-red-700"
        >
            <p v-for="(error, key) in form.errors" :key="key">{{ error }}</p>
        </div>
    </div>
</template>
