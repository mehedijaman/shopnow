<template>
    <AppSectionHeader :title="__('promo-code::admin.promo_codes')" :bread-crumb="breadCrumb"></AppSectionHeader>

    <AppCard class="w-full md:w-3/4 xl:w-1/2">
        <template #title>{{ title }}</template>
        <template #content>
            <AppFormErrors class="mb-4" />
            <form class="space-y-5 pt-4" @submit.prevent="submitForm">
                <div>
                    <AppLabel for="code">{{ __('promo-code::admin.code') }}</AppLabel>
                    <AppInputText
                        id="code"
                        v-model="form.code"
                        type="text"
                        placeholder="e.g. SAVE10"
                        autocomplete="off"
                        :class="{ 'input-error': errorsFields.includes('code') }"
                    />
                    <p class="mt-1 text-xs text-skin-neutral-7">
                        {{ __('promo-code::admin.code_hint') }}
                    </p>
                </div>

                <div>
                    <AppLabel>{{ __('promo-code::admin.discount_type') }}</AppLabel>
                    <div class="mt-1 flex flex-wrap gap-x-6">
                        <AppRadioButton
                            id="discount_type_percentage"
                            v-model="form.discount_type"
                            value="percentage"
                        >
                            {{ __('promo-code::admin.percentage') }}
                        </AppRadioButton>
                        <AppRadioButton
                            id="discount_type_fixed"
                            v-model="form.discount_type"
                            value="fixed_amount"
                        >
                            {{ __('promo-code::admin.fixed_amount') }}
                        </AppRadioButton>
                        <AppRadioButton
                            id="discount_type_shipping"
                            v-model="form.discount_type"
                            value="free_shipping"
                        >
                            {{ __('promo-code::admin.free_shipping') }}
                        </AppRadioButton>
                    </div>
                </div>

                <div v-if="form.discount_type !== 'free_shipping'">
                    <AppLabel for="discount_value">
                        {{ form.discount_type === 'percentage' ? __('promo-code::admin.discount_percent') : __('promo-code::admin.discount_amount_tk') }}
                    </AppLabel>
                    <AppInputText
                        id="discount_value"
                        v-model="form.discount_value"
                        type="number"
                        min="0"
                        :max="form.discount_type === 'percentage' ? 100 : null"
                        :step="form.discount_type === 'percentage' ? 1 : 0.01"
                        :class="{ 'input-error': errorsFields.includes('discount_value') }"
                    />
                </div>

                <div v-if="form.discount_type === 'percentage'">
                    <AppLabel for="maximum_discount_amount">
                        {{ __('promo-code::admin.maximum_discount') }}
                        <span class="text-xs font-normal text-skin-neutral-7">{{ __('promo-code::admin.optional_cap') }}</span>
                    </AppLabel>
                    <AppInputText
                        id="maximum_discount_amount"
                        v-model="form.maximum_discount_amount"
                        type="number"
                        min="0"
                        step="0.01"
                        :class="{ 'input-error': errorsFields.includes('maximum_discount_amount') }"
                    />
                </div>

                <div>
                    <AppLabel for="minimum_order_amount">
                        {{ __('promo-code::admin.minimum_order_amount') }}
                        <span class="text-xs font-normal text-skin-neutral-7">{{ __('promo-code::admin.optional_hint') }}</span>
                    </AppLabel>
                    <AppInputText
                        id="minimum_order_amount"
                        v-model="form.minimum_order_amount"
                        type="number"
                        min="0"
                        step="0.01"
                        :class="{ 'input-error': errorsFields.includes('minimum_order_amount') }"
                    />
                </div>

                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <div>
                        <AppLabel for="usage_limit">
                            {{ __('promo-code::admin.total_usage_limit') }}
                            <span class="text-xs font-normal text-skin-neutral-7">{{ __('promo-code::admin.optional_hint') }}</span>
                        </AppLabel>
                        <AppInputText
                            id="usage_limit"
                            v-model="form.usage_limit"
                            type="number"
                            min="1"
                            step="1"
                            :placeholder="__('promo-code::admin.unlimited')"
                            :class="{ 'input-error': errorsFields.includes('usage_limit') }"
                        />
                    </div>
                    <div>
                        <AppLabel for="per_customer_limit">
                            {{ __('promo-code::admin.per_customer_limit') }}
                            <span class="text-xs font-normal text-skin-neutral-7">{{ __('promo-code::admin.optional_hint') }}</span>
                        </AppLabel>
                        <AppInputText
                            id="per_customer_limit"
                            v-model="form.per_customer_limit"
                            type="number"
                            min="1"
                            step="1"
                            :placeholder="__('promo-code::admin.unlimited')"
                            :class="{ 'input-error': errorsFields.includes('per_customer_limit') }"
                        />
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <div>
                        <AppLabel for="starts_at">{{ __('promo-code::admin.starts_at') }}</AppLabel>
                        <AppInputText
                            id="starts_at"
                            v-model="form.starts_at"
                            type="datetime-local"
                            :class="{ 'input-error': errorsFields.includes('starts_at') }"
                        />
                    </div>
                    <div>
                        <AppLabel for="expires_at">{{ __('promo-code::admin.expires_at') }}</AppLabel>
                        <AppInputText
                            id="expires_at"
                            v-model="form.expires_at"
                            type="datetime-local"
                            :class="{ 'input-error': errorsFields.includes('expires_at') }"
                        />
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <AppCheckbox id="active" v-model="form.active" />
                    <AppLabel for="active" class="mb-0 hover:cursor-pointer">{{ __('common.field.active') }}</AppLabel>
                </div>

                <p v-if="promoCode" class="text-xs text-skin-neutral-7">
                    {{ __('promo-code::admin.used_in', { count: promoCode.used_count }) }}
                    <template v-if="promoCode.usage_limit">{{ __('promo-code::admin.of_allowed', { limit: promoCode.usage_limit }) }}</template>.
                </p>
            </form>
        </template>
        <template #footer>
            <AppButton class="btn btn-primary" @click="submitForm">
                {{ __('common.save') }}
            </AppButton>
        </template>
    </AppCard>
</template>

<script setup>
import { inject } from 'vue'
import { useForm } from '@inertiajs/vue3'

import useTitle from '@/Composables/useTitle'
import useFormContext from '@/Composables/useFormContext'
import useFormErrors from '@/Composables/useFormErrors'

const props = defineProps({
    promoCode: {
        type: Object,
        default: null,
    },
})

const translate = inject('translate')

const breadCrumb = [
    { label: translate('common.home'), href: route('dashboard.index') },
    { label: translate('promo-code::admin.promo_codes'), href: route('promoCode.index') },
    { label: translate('promo-code::admin.promo_code'), last: true },
]

const { title } = useTitle(translate('promo-code::admin.promo_code'))

const form = useForm({
    code: props.promoCode?.code ?? '',
    discount_type: props.promoCode?.discount_type ?? 'percentage',
    discount_value: props.promoCode?.discount_value ?? '',
    minimum_order_amount: props.promoCode?.minimum_order_amount ?? '',
    maximum_discount_amount: props.promoCode?.maximum_discount_amount ?? '',
    usage_limit: props.promoCode?.usage_limit ?? '',
    per_customer_limit: props.promoCode?.per_customer_limit ?? '',
    starts_at: props.promoCode?.starts_at ?? '',
    expires_at: props.promoCode?.expires_at ?? '',
    active: props.promoCode ? Boolean(props.promoCode.active) : true,
})

const { isCreate } = useFormContext()

const submitForm = () => {
    if (form.discount_type === 'free_shipping') {
        form.discount_value = 0
    }

    if (isCreate.value) {
        form.post(route('promoCode.store'))
    } else {
        form.put(route('promoCode.update', props.promoCode.id))
    }
}

const { errorsFields } = useFormErrors()
</script>
