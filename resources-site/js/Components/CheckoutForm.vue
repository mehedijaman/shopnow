<template>
    <div class="mt-6 sm:mt-8 lg:flex lg:items-start lg:gap-12 xl:gap-16">

        <!-- Left: Form -->
        <div class="min-w-0 flex-1 space-y-8">

            <!-- General error banner -->
            <div v-if="generalError" class="flex items-start gap-3 rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                <svg xmlns="http://www.w3.org/2000/svg" class="mt-0.5 h-5 w-5 shrink-0 text-red-500" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                </svg>
                <span>{{ generalError }}</span>
            </div>

            <!-- Delivery Details -->
            <div class="space-y-4">
                <h2 class="border-b border-gray-200 pb-2 text-xl font-semibold text-gray-900">
                    Delivery Details
                </h2>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">

                    <!-- Name -->
                    <div class="col-span-2">
                        <label for="name" class="mb-1.5 block text-sm font-medium text-gray-700">
                            Full Name <span class="text-red-500">*</span>
                        </label>
                        <input
                            v-model="form.name"
                            type="text"
                            id="name"
                            placeholder="e.g. Mehedi Hasan"
                            :class="inputClass('name')"
                            @input="clearError('name')"
                        />
                        <p v-if="errors.name" class="mt-1.5 text-xs text-red-600">{{ errors.name }}</p>
                    </div>

                    <!-- Phone -->
                    <div>
                        <label for="phone" class="mb-1.5 block text-sm font-medium text-gray-700">
                            Phone Number <span class="text-red-500">*</span>
                        </label>
                        <input
                            v-model="form.phone"
                            type="tel"
                            id="phone"
                            placeholder="e.g. 01712345678"
                            :class="inputClass('phone')"
                            @input="clearError('phone')"
                        />
                        <p v-if="errors.phone" class="mt-1.5 text-xs text-red-600">{{ errors.phone }}</p>
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="mb-1.5 block text-sm font-medium text-gray-700">
                            Email Address
                        </label>
                        <input
                            v-model="form.email"
                            type="email"
                            id="email"
                            placeholder="e.g. you@example.com"
                            :class="inputClass('email')"
                            @input="clearError('email')"
                        />
                        <p v-if="errors.email" class="mt-1.5 text-xs text-red-600">{{ errors.email }}</p>
                    </div>

                    <!-- District -->
                    <div>
                        <label for="district" class="mb-1.5 block text-sm font-medium text-gray-700">
                            District
                        </label>
                        <input
                            v-model="form.district"
                            type="text"
                            id="district"
                            placeholder="e.g. Dhaka"
                            :class="inputClass('district')"
                        />
                        <p v-if="errors.district" class="mt-1.5 text-xs text-red-600">{{ errors.district }}</p>
                    </div>

                    <!-- Upazila -->
                    <div>
                        <label for="upazila" class="mb-1.5 block text-sm font-medium text-gray-700">
                            Upazila / Thana
                        </label>
                        <input
                            v-model="form.upazila"
                            type="text"
                            id="upazila"
                            placeholder="e.g. Mirpur"
                            :class="inputClass('upazila')"
                        />
                        <p v-if="errors.upazila" class="mt-1.5 text-xs text-red-600">{{ errors.upazila }}</p>
                    </div>

                    <!-- Address -->
                    <div class="col-span-2">
                        <label for="address" class="mb-1.5 block text-sm font-medium text-gray-700">
                            Full Address
                        </label>
                        <textarea
                            v-model="form.address"
                            id="address"
                            rows="3"
                            placeholder="House/flat number, road, area..."
                            :class="inputClass('address')"
                        ></textarea>
                        <p v-if="errors.address" class="mt-1.5 text-xs text-red-600">{{ errors.address }}</p>
                    </div>

                </div>
            </div>

            <!-- Payment Method -->
            <div class="space-y-4">
                <h2 class="border-b border-gray-200 pb-2 text-xl font-semibold text-gray-900">
                    Payment Method <span class="text-red-500 text-base">*</span>
                </h2>

                <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
                    <button
                        type="button"
                        @click="selectPayment('cod')"
                        :class="[
                            'flex items-center gap-3 rounded-xl border-2 p-4 text-left transition-all',
                            form.payment_method === 'cod'
                                ? 'border-primary-500 bg-primary-50 text-primary-700'
                                : 'border-gray-200 bg-white text-gray-700 hover:border-gray-300 hover:bg-gray-50'
                        ]"
                    >
                        <span :class="[
                            'flex h-10 w-10 shrink-0 items-center justify-center rounded-full text-xl',
                            form.payment_method === 'cod' ? 'bg-primary-100' : 'bg-gray-100'
                        ]">💵</span>
                        <div>
                            <p class="font-semibold">Cash on Delivery</p>
                            <p class="text-xs text-gray-500">Pay when you receive</p>
                        </div>
                        <div class="ml-auto">
                            <div :class="[
                                'h-5 w-5 rounded-full border-2 flex items-center justify-center',
                                form.payment_method === 'cod' ? 'border-primary-500 bg-primary-500' : 'border-gray-300'
                            ]">
                                <svg v-if="form.payment_method === 'cod'" class="h-3 w-3 text-white" viewBox="0 0 12 12" fill="currentColor">
                                    <path d="M10 3L5 8.5 2 5.5" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
                                </svg>
                            </div>
                        </div>
                    </button>

                    <!-- <button
                        type="button"
                        @click="selectPayment('sslcommerz')"
                        :class="[
                            'flex items-center gap-3 rounded-xl border-2 p-4 text-left transition-all',
                            form.payment_method === 'sslcommerz'
                                ? 'border-primary-500 bg-primary-50 text-primary-700'
                                : 'border-gray-200 bg-white text-gray-700 hover:border-gray-300 hover:bg-gray-50'
                        ]"
                    >
                        <span :class="[
                            'flex h-10 w-10 shrink-0 items-center justify-center rounded-full text-xl',
                            form.payment_method === 'sslcommerz' ? 'bg-primary-100' : 'bg-gray-100'
                        ]">💳</span>
                        <div>
                            <p class="font-semibold">Online Payment</p>
                            <p class="text-xs text-gray-500">SSLCommerz secured</p>
                        </div>
                        <div class="ml-auto">
                            <div :class="[
                                'h-5 w-5 rounded-full border-2 flex items-center justify-center',
                                form.payment_method === 'sslcommerz' ? 'border-primary-500 bg-primary-500' : 'border-gray-300'
                            ]">
                                <svg v-if="form.payment_method === 'sslcommerz'" class="h-3 w-3 text-white" viewBox="0 0 12 12" fill="currentColor">
                                    <path d="M10 3L5 8.5 2 5.5" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
                                </svg>
                            </div>
                        </div>
                    </button> -->
                </div>
                <p v-if="errors.payment_method" class="text-xs text-red-600">{{ errors.payment_method }}</p>
            </div>
        </div>

        <!-- Right: Order Summary -->
        <div class="mt-6 w-full sm:mt-8 lg:mt-0 lg:max-w-xs xl:max-w-sm">
            <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-100 px-5 py-4">
                    <h3 class="font-semibold text-gray-900">Order Summary</h3>
                </div>

                <div class="divide-y divide-gray-100 px-5">
                    <div class="flex items-center justify-between py-3 text-sm">
                        <span class="text-gray-500">Subtotal</span>
                        <span class="font-medium text-gray-900">{{ cartStore.subtotal }} Tk.</span>
                    </div>
                    <div class="flex items-center justify-between py-3 text-sm">
                        <span class="text-gray-500">Shipping</span>
                        <span v-if="shippingCharge === 0" class="font-medium text-green-600">Free</span>
                        <span v-else class="font-medium text-gray-900">{{ shippingCharge }} Tk.</span>
                    </div>
                    <div class="flex items-center justify-between py-3 text-sm">
                        <span class="text-gray-500">Tax</span>
                        <span class="font-medium text-gray-900">{{ cartStore.tax }} Tk.</span>
                    </div>
                    <div class="flex items-center justify-between py-4 text-base font-bold">
                        <span class="text-gray-900">Total</span>
                        <span class="text-primary-700">{{ orderTotal }} Tk.</span>
                    </div>
                </div>

                <!-- Voucher -->
                <div class="border-t border-gray-100 px-5 py-4">
                    <label class="mb-1.5 block text-sm font-medium text-gray-700">Promo / Voucher Code</label>
                    <div class="flex gap-2">
                        <input
                            type="text"
                            v-model="voucherCode"
                            placeholder="Enter code"
                            class="block w-full rounded-lg border border-gray-300 bg-gray-50 px-3 py-2 text-sm text-gray-900 focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
                        />
                        <button
                            type="button"
                            class="shrink-0 rounded-lg bg-gray-800 px-4 py-2 text-sm font-medium text-white hover:bg-gray-700"
                        >
                            Apply
                        </button>
                    </div>
                </div>

                <!-- Special Note -->
                <div class="border-t border-gray-100 px-5 py-4">
                    <label for="note" class="mb-1.5 block text-sm font-medium text-gray-700">Special Note</label>
                    <textarea
                        v-model="form.note"
                        id="note"
                        rows="3"
                        placeholder="Any instructions for your order..."
                        class="block w-full rounded-lg border border-gray-300 bg-gray-50 px-3 py-2 text-sm text-gray-900 focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
                    ></textarea>
                </div>

                <!-- Submit -->
                <div class="border-t border-gray-100 px-5 py-4">
                    <p class="mb-3 text-xs text-gray-500">
                        Fields marked <span class="text-red-500 font-semibold">*</span> are required.
                    </p>
                    <button
                        @click="submitForm"
                        type="button"
                        :disabled="submitting"
                        class="flex w-full items-center justify-center gap-2 rounded-xl bg-primary-700 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-primary-800 focus:outline-none focus:ring-4 focus:ring-primary-300 disabled:cursor-not-allowed disabled:opacity-60"
                    >
                        <svg v-if="submitting" class="h-4 w-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                        {{ submitting ? 'Placing Order...' : 'Place Order' }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, reactive, computed } from 'vue'
import { useCartStore } from '../Stores/CartStore'
import axios from 'axios'

const cartStore = useCartStore()

const props = defineProps({
    shippingFlatRate: {
        type: Number,
        default: 60,
    },
    freeShippingThreshold: {
        type: Number,
        default: 1000,
    },
})

const shippingCharge = computed(() => {
    if (props.freeShippingThreshold > 0 && cartStore.subtotal >= props.freeShippingThreshold) {
        return 0
    }
    return props.shippingFlatRate
})

const orderTotal = computed(() => cartStore.subtotal + shippingCharge.value + cartStore.tax)

const form = reactive({
    name: '',
    phone: '',
    email: '',
    district: '',
    upazila: '',
    address: '',
    note: '',
    payment_method: null,
})

const voucherCode = ref('')
const submitting = ref(false)
const errors = reactive({})
const generalError = ref('')

const baseInputClass = 'block w-full rounded-lg border px-3 py-2.5 text-sm text-gray-900 focus:outline-none focus:ring-1 transition'
const validInputClass = `${baseInputClass} border-gray-300 bg-gray-50 focus:border-primary-500 focus:ring-primary-500`
const errorInputClass = `${baseInputClass} border-red-400 bg-red-50 focus:border-red-500 focus:ring-red-400`

function inputClass(field) {
    return errors[field] ? errorInputClass : validInputClass
}

function clearError(field) {
    delete errors[field]
    generalError.value = ''
}

function selectPayment(method) {
    form.payment_method = method
    delete errors.payment_method
}

function validate() {
    // Clear all errors
    Object.keys(errors).forEach((k) => delete errors[k])
    generalError.value = ''

    let valid = true

    if (!form.name.trim()) {
        errors.name = 'Full name is required.'
        valid = false
    }

    if (!form.phone.trim()) {
        errors.phone = 'Phone number is required.'
        valid = false
    }

    if (form.email && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(form.email)) {
        errors.email = 'Please enter a valid email address.'
        valid = false
    }

    return valid
}

async function submitForm() {
    if (!validate()) {
        // Scroll to the first error
        const firstError = document.querySelector('.border-red-400')
        if (firstError) { firstError.scrollIntoView({ behavior: 'smooth', block: 'center' }) }
        return
    }

    submitting.value = true

    try {
        const payload = {
            ...form,
            items: cartStore.items,
            subtotal: cartStore.subtotal,
            tax: cartStore.tax,
            shipping: shippingCharge.value,
            total: orderTotal.value,
            paid: 0,
            due: orderTotal.value,
        }

        const response = await axios.post('/site-order-store', payload)

        cartStore.clearCart()

        window.location.href = '/order-confirm/' + response.data.order_id
    } catch (error) {
        if (error.response?.status === 422) {
            // Server validation errors
            const serverErrors = error.response.data.errors ?? {}
            Object.keys(serverErrors).forEach((field) => {
                errors[field] = Array.isArray(serverErrors[field])
                    ? serverErrors[field][0]
                    : serverErrors[field]
            })
            generalError.value = 'Please fix the errors below and try again.'
            const firstError = document.querySelector('.border-red-400')
            if (firstError) { firstError.scrollIntoView({ behavior: 'smooth', block: 'center' }) }
        } else {
            generalError.value = error.response?.data?.message
                ?? 'Failed to place the order. Please try again.'
        }
    } finally {
        submitting.value = false
    }
}
</script>
