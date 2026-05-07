<template>
    <div class="mt-6 sm:mt-8 lg:flex lg:items-start lg:gap-12 xl:gap-16">
        <div class="min-w-0 flex-1 space-y-8">
            <div class="space-y-4">
                <h2
                    class="border-b border-gray-200 pb-2 text-xl font-semibold text-gray-900 dark:border-gray-700 dark:text-white"
                >
                    Delivery Details
                </h2>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div class="col-span-2">
                        <label
                            for="name"
                            class="mb-2 block text-sm font-medium text-gray-900 dark:text-white"
                        >
                            Name
                        </label>
                        <input
                            v-model="form.name"
                            type="text"
                            id="name"
                            class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder:text-gray-400 dark:focus:border-primary-500 dark:focus:ring-primary-500"
                            required
                        />
                    </div>

                    <div>
                        <label
                            for="phone"
                            class="mb-2 block text-sm font-medium text-gray-900 dark:text-white"
                        >
                            Phone No
                        </label>
                        <input
                            v-model="form.phone"
                            type="text"
                            id="phone"
                            class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder:text-gray-400 dark:focus:border-primary-500 dark:focus:ring-primary-500"
                            required
                        />
                    </div>

                    <div>
                        <label
                            for="email"
                            class="mb-2 block text-sm font-medium text-gray-900 dark:text-white"
                        >
                            Email
                        </label>
                        <input
                            v-model="form.email"
                            type="email"
                            id="email"
                            class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder:text-gray-400 dark:focus:border-primary-500 dark:focus:ring-primary-500"
                            placeholder="name@flowbite.com"
                            required
                        />
                    </div>

                    <div>
                        <label
                            for="district"
                            class="mb-2 block text-sm font-medium text-gray-900 dark:text-white"
                        >
                            District
                        </label>
                        <input
                            v-model="form.district"
                            type="text"
                            id="district"
                            placeholder="e.g. Dhaka"
                            class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder:text-gray-400 dark:focus:border-primary-500 dark:focus:ring-primary-500"
                        />
                    </div>

                    <div>
                        <label
                            for="upazila"
                            class="mb-2 block text-sm font-medium text-gray-900 dark:text-white"
                        >
                            Upazila/Thana
                        </label>
                        <input
                            v-model="form.upazila"
                            type="text"
                            id="upazila"
                            placeholder="e.g. Mirpur"
                            class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder:text-gray-400 dark:focus:border-primary-500 dark:focus:ring-primary-500"
                        />
                    </div>

                    <div class="col-span-2">
                        <label
                            for="email"
                            class="mb-2 block text-sm font-medium text-gray-900 dark:text-white"
                        >
                            Address
                        </label>

                        <textarea
                            required
                            name="address"
                            v-model="form.address"
                            class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder:text-gray-400 dark:focus:border-primary-500 dark:focus:ring-primary-500"
                        ></textarea>
                    </div>
                </div>
            </div>

            <div class="space-y-4">
                <h3
                    class="border-b border-gray-200 pb-2 text-xl font-semibold text-gray-900 dark:text-white"
                >
                    Payment Method
                </h3>

                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div
                        @click="form.payment_method = 'cod'"
                        :class="
                            form.payment_method == 'cod'
                                ? 'bg-primary-100 font-bold'
                                : ''
                        "
                        class="cursor-pointer rounded-lg border border-gray-200 bg-gray-50 p-4 ps-4 text-lg dark:border-gray-700 dark:bg-gray-800"
                    >
                        Cash on Delivery
                    </div>

                    <div
                        @click="form.payment_method = 'sslcommerz'"
                        :class="
                            form.payment_method == 'sslcommerz'
                                ? 'bg-primary-100 font-bold'
                                : ''
                        "
                        class="cursor-pointer rounded-lg border border-gray-200 bg-gray-50 p-4 ps-4 text-lg dark:border-gray-700 dark:bg-gray-800"
                    >
                        SSLCommerz (Online Payment)
                    </div>
                </div>
            </div>
        </div>

        <div
            class="mt-6 w-full space-y-6 sm:mt-8 lg:mt-0 lg:max-w-xs xl:max-w-md"
        >
            <div class="flow-root">
                <div
                    class="-my-3 divide-y divide-gray-200 dark:divide-gray-800"
                >
                    <dl class="flex items-center justify-between gap-4 py-3">
                        <dt
                            class="text-base font-normal text-gray-500 dark:text-gray-400"
                        >
                            Subtotal
                        </dt>
                        <dd class="text-base text-gray-900 dark:text-white">
                            {{ cartStore.subtotal }} Tk.
                        </dd>
                    </dl>

                    <dl class="flex items-center justify-between gap-4 py-3">
                        <dt
                            class="text-base font-normal text-gray-500 dark:text-gray-400"
                        >
                            Shipping
                        </dt>
                        <dd class="text-base text-gray-900 dark:text-white">
                            <span v-if="shippingCharge === 0" class="text-green-600 font-medium">Free</span>
                            <span v-else>{{ shippingCharge }} Tk.</span>
                        </dd>
                    </dl>

                    <dl class="flex items-center justify-between gap-4 py-3">
                        <dt
                            class="text-base font-normal text-gray-500 dark:text-gray-400"
                        >
                            Tax
                        </dt>
                        <dd class="text-base text-gray-900 dark:text-white">
                            {{ cartStore.tax }} Tk.
                        </dd>
                    </dl>

                    <dl class="flex items-center justify-between gap-4 py-3">
                        <dt
                            class="text-base font-bold text-gray-900 dark:text-white"
                        >
                            Total
                        </dt>
                        <dd
                            class="text-base font-bold text-gray-900 dark:text-white"
                        >
                            {{ orderTotal }} Tk.
                        </dd>
                    </dl>
                </div>
            </div>

            <hr class="" />

            <div class="my-6">
                <label
                    for="voucher"
                    class="mb-2 block text-sm font-medium text-gray-900 dark:text-white"
                >
                    Enter a gift card, voucher or promotional code
                </label>
                <div class="flex max-w-md items-center gap-4">
                    <input
                        type="text"
                        id="voucher"
                        class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder:text-gray-400 dark:focus:border-primary-500 dark:focus:ring-primary-500"
                        placeholder=""
                        required
                    />
                    <button
                        type="button"
                        class="flex items-center justify-center rounded-lg bg-primary-700 px-5 py-2.5 text-sm font-medium text-white hover:bg-primary-800 focus:outline-hidden focus:ring-4 focus:ring-primary-300 dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800"
                    >
                        Apply
                    </button>
                </div>
            </div>

            <div class="space-y-4">
                <label
                    for="voucher"
                    class="mb-2 block text-sm font-medium text-gray-900 dark:text-white"
                >
                    Special note
                </label>

                <div>
                    <textarea
                        required
                        name="note"
                        v-model="form.note"
                        class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder:text-gray-400 dark:focus:border-primary-500 dark:focus:ring-primary-500"
                    ></textarea>
                </div>
            </div>

            <div class="space-y-3">
                <button
                    @click="submitForm"
                    type="button"
                    class="flex w-full items-center justify-center rounded-lg bg-primary-700 px-5 py-2.5 text-sm font-medium text-white hover:bg-primary-800 focus:outline-hidden focus:ring-4 focus:ring-primary-300 dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800"
                >
                    Proceed
                </button>

                <!-- <p class="text-sm font-normal text-gray-500 dark:text-gray-400">
                    One or more items in your cart require an account.
                    <a
                        href="#"
                        title=""
                        class="font-medium text-primary-700 underline hover:no-underline dark:text-primary-500"
                    >
                        Sign in or create an account now.
                    </a>
                    .
                </p> -->
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue'
import { useCartStore } from '../Stores/CartStore'
import axios from 'axios'
import { useForm } from '@inertiajs/vue3'

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

const orderTotal = computed(() => {
    return cartStore.subtotal + shippingCharge.value + cartStore.tax
})

const form = useForm({
    name: '',
    phone: '',
    email: '',
    district: '',
    upazila: '',
    address: '',
    note: '',
    payment_method: null,

    items: cartStore.items,
    subtotal: cartStore.subtotal,
    tax: cartStore.tax,
    shipping: shippingCharge,
    total: orderTotal,
    paid: 0,
    due: orderTotal,
})

const submitForm = async () => {
    // Sync reactive values before submitting
    form.subtotal = cartStore.subtotal
    form.shipping = shippingCharge.value
    form.total = orderTotal.value
    form.due = orderTotal.value
    form.items = cartStore.items

    try {
        const response = await axios.post('/site-order-store', form.data())

        cartStore.clearCart()
        form.reset()

        window.location.href = '/order-confirm/' + response.data.order_id
    } catch (error) {
        if (error.response) {
            console.error('Error Response:', error.response.data)
            alert('Failed to place the order. Please check the form and try again.')
        } else if (error.request) {
            console.error('No Response:', error.request)
            alert('Failed to communicate with the server. Please try again.')
        } else {
            console.error('Error Setting Up Request:', error.message)
            alert('An unexpected error occurred. Please try again.')
        }
    }
}
</script>
