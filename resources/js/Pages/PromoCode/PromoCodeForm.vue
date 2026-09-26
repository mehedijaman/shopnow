<template>
    <AppSectionHeader title="Promo Codes" :bread-crumb="breadCrumb"></AppSectionHeader>

    <AppCard class="w-full md:w-3/4 xl:w-1/2">
        <template #title>{{ title }}</template>
        <template #content>
            <AppFormErrors class="mb-4" />
            <form class="space-y-5 pt-4" @submit.prevent="submitForm">
                <div>
                    <AppLabel for="code">Code</AppLabel>
                    <AppInputText
                        id="code"
                        v-model="form.code"
                        type="text"
                        placeholder="e.g. SAVE10"
                        autocomplete="off"
                        :class="{ 'input-error': errorsFields.includes('code') }"
                    />
                    <p class="mt-1 text-xs text-skin-neutral-7">
                        Customers enter this code at checkout. Letters, numbers, dashes and underscores only.
                    </p>
                </div>

                <div>
                    <AppLabel>Discount Type</AppLabel>
                    <div class="mt-1 flex flex-wrap gap-x-6">
                        <AppRadioButton
                            id="discount_type_percentage"
                            v-model="form.discount_type"
                            value="percentage"
                        >
                            Percentage
                        </AppRadioButton>
                        <AppRadioButton
                            id="discount_type_fixed"
                            v-model="form.discount_type"
                            value="fixed_amount"
                        >
                            Fixed Amount
                        </AppRadioButton>
                        <AppRadioButton
                            id="discount_type_shipping"
                            v-model="form.discount_type"
                            value="free_shipping"
                        >
                            Free Shipping
                        </AppRadioButton>
                    </div>
                </div>

                <div v-if="form.discount_type !== 'free_shipping'">
                    <AppLabel for="discount_value">
                        {{ form.discount_type === 'percentage' ? 'Discount (%)' : 'Discount Amount (Tk)' }}
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
                        Maximum Discount (Tk)
                        <span class="text-xs font-normal text-skin-neutral-7">(optional cap)</span>
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
                        Minimum Order Amount (Tk)
                        <span class="text-xs font-normal text-skin-neutral-7">(optional)</span>
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
                            Total Usage Limit
                            <span class="text-xs font-normal text-skin-neutral-7">(optional)</span>
                        </AppLabel>
                        <AppInputText
                            id="usage_limit"
                            v-model="form.usage_limit"
                            type="number"
                            min="1"
                            step="1"
                            placeholder="Unlimited"
                            :class="{ 'input-error': errorsFields.includes('usage_limit') }"
                        />
                    </div>
                    <div>
                        <AppLabel for="per_customer_limit">
                            Per Customer Limit
                            <span class="text-xs font-normal text-skin-neutral-7">(optional)</span>
                        </AppLabel>
                        <AppInputText
                            id="per_customer_limit"
                            v-model="form.per_customer_limit"
                            type="number"
                            min="1"
                            step="1"
                            placeholder="Unlimited"
                            :class="{ 'input-error': errorsFields.includes('per_customer_limit') }"
                        />
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <div>
                        <AppLabel for="starts_at">Starts At</AppLabel>
                        <AppInputText
                            id="starts_at"
                            v-model="form.starts_at"
                            type="datetime-local"
                            :class="{ 'input-error': errorsFields.includes('starts_at') }"
                        />
                    </div>
                    <div>
                        <AppLabel for="expires_at">Expires At</AppLabel>
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
                    <AppLabel for="active" class="mb-0 hover:cursor-pointer">Active</AppLabel>
                </div>

                <p v-if="promoCode" class="text-xs text-skin-neutral-7">
                    Used in <span class="font-semibold">{{ promoCode.used_count }}</span> order{{ promoCode.used_count === 1 ? '' : 's' }}
                    <template v-if="promoCode.usage_limit"> of {{ promoCode.usage_limit }} allowed</template>.
                </p>
            </form>
        </template>
        <template #footer>
            <AppButton class="btn btn-primary" @click="submitForm">
                {{ __('Save') }}
            </AppButton>
        </template>
    </AppCard>
</template>

<script setup>
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

const breadCrumb = [
    { label: 'Home', href: route('dashboard.index') },
    { label: 'Promo Codes', href: route('promoCode.index') },
    { label: 'Promo Code', last: true },
]

const { title } = useTitle('Promo Code')

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
