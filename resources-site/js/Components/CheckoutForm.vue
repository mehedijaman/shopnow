<template>
    <div class="mt-6 sm:mt-8 lg:flex lg:items-start lg:gap-12 xl:gap-16">

        <!-- Left: Form -->
        <div class="min-w-0 flex-1 space-y-8">

            <!-- General error banner -->
            <div v-if="generalError"
                class="flex items-start gap-3 rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                <svg xmlns="http://www.w3.org/2000/svg" class="mt-0.5 h-5 w-5 shrink-0 text-red-500" viewBox="0 0 20 20"
                    fill="currentColor">
                    <path fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                        clip-rule="evenodd" />
                </svg>
                <span>{{ generalError }}</span>
            </div>

            <!-- Delivery Details -->
            <div v-if="requiresShipping" class="space-y-4">
                <h2 class="border-b border-gray-200 pb-2 text-xl font-semibold text-gray-900">
                    Delivery Details
                </h2>

                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">

                    <!-- Name -->
                    <div class="col-span-2">

                        <label for="name" class="mb-1.5 block text-sm font-medium text-gray-700">
                            Full Name <span class="text-red-500">*</span>

                        </label>
                        <input v-model="form.name" type="text" id="name" placeholder="e.g. Mehedi Hasan"
                            :class="inputClass('name')" @input="clearError('name')" />
                        <p v-if="errors.name" class="mt-1.5 text-xs text-red-600">{{ errors.name }}</p>
                    </div>

                    <div class="col-span-2 grid grid-cols-1 gap-4 md:grid-cols-2">
                        <!-- Phone -->
                        <div>

                            <label for="phone" class="mb-1.5 block text-sm font-medium text-gray-700">
                                Phone Number <span class="text-red-500">*</span>

                            </label>
                            <input v-model="form.phone" type="tel" id="phone" placeholder="e.g. 01712345678"
                                :class="inputClass('phone')" @input="clearError('phone')" />
                            <p v-if="errors.phone" class="mt-1.5 text-xs text-red-600">{{ errors.phone }}</p>
                        </div>

                        <!-- Email -->
                        <div>

                            <label for="email" class="mb-1.5 block text-sm font-medium text-gray-700">
                                Email Address (Optional)

                            </label>
                            <input v-model="form.email" type="email" id="email" placeholder="e.g. you@example.com"
                                :class="inputClass('email')" @input="clearError('email')" />
                            <p v-if="errors.email" class="mt-1.5 text-xs text-red-600">{{ errors.email }}</p>
                        </div>
                    </div>


                    <!-- Saved Addresses selection (if customer has saved addresses) -->
                    <div v-if="addresses.length > 0" class="col-span-2 space-y-2">

                        <label class="mb-1.5 block text-sm font-medium text-gray-700">
                            Shipping Address <span class="text-red-500">*</span>

                        </label>
                        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                            <button v-for="addr in addresses" :key="addr.id" type="button" @click="selectAddress(addr)"
                                :class="[
                                    'flex flex-col justify-between rounded-xl border-2 p-4 text-left transition-all',
                                    selectedAddressId === addr.id
                                        ? 'border-primary-500 bg-primary-50 text-primary-700'
                                        : 'border-gray-200 bg-white text-gray-700 hover:border-gray-300'
                                ]">
                                <div class="text-sm font-semibold flex items-center gap-1.5">
                                    <span v-if="addr.default"
                                        class="rounded bg-primary-100 px-1.5 py-0.5 text-2xs font-semibold text-primary-800">Default</span>
                                    <span class="truncate">{{ addr.address }}</span>
                                </div>
                                <p class="mt-1 text-xs text-gray-500">
                                    {{ addr.union_name ? addr.union_name + ', ' : '' }}
                                    {{ addr.upazilla_name ? addr.upazilla_name + ', ' : '' }}
                                    {{ addr.district_name ? addr.district_name + ', ' : '' }}
                                    {{ addr.division_name }}
                                </p>
                            </button>
                            <button type="button" @click="selectCustomAddress" :class="[
                                'flex flex-col justify-center items-center rounded-xl border-2 border-dashed p-4 text-center transition-all min-h-[90px]',
                                selectedAddressId === 'new'
                                    ? 'border-primary-500 bg-primary-50 text-primary-700'
                                    : 'border-gray-200 bg-white text-gray-500 hover:border-gray-300 hover:bg-gray-50'
                            ]">
                                <span class="text-lg font-bold">+</span>
                                <span class="text-xs font-semibold">Use Custom Address</span>
                            </button>
                        </div>
                    </div>

                    <!-- Geocode dropdown selectors and textarea (shown if custom address is selected or no saved addresses) -->
                    <div v-if="selectedAddressId === 'new'"
                        class="col-span-2 grid grid-cols-1 gap-4 md:grid-cols-2 border border-gray-150 rounded-xl p-4 bg-gray-50/55">
                        <div class="col-span-2">
                            <h3 class="text-sm font-semibold text-gray-900">Custom Shipping Address</h3>
                        </div>



                        <!-- District -->
                        <div ref="districtContainerRef" class="relative">
                            <div class="mb-1.5 flex items-center justify-between">
                                <label for="district" class="block text-sm font-medium text-gray-700">
                                    District <span class="text-red-500">*</span>
                                </label>
                                <button type="button" @click="toggleManualDistrict"
                                    class="text-xs font-medium text-primary-600 hover:text-primary-800 hover:underline focus:outline-none">
                                    {{ isManualDistrict ? 'Select from list' : "Can't find district?" }}
                                </button>
                            </div>
                            <template v-if="!isManualDistrict">
                                <div class="relative">
                                    <button type="button" @click="toggleDistrictDropdown"
                                        :class="[inputClass('district'), 'flex items-center justify-between text-left cursor-pointer bg-white']">
                                        <span :class="selectedDistrictName ? 'text-gray-900 font-medium' : 'text-gray-500'">
                                            {{ selectedDistrictName || 'Select District' }}
                                        </span>
                                        <svg class="h-4 w-4 text-gray-400 transition-transform" :class="{ 'rotate-180': isDistrictDropdownOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </button>

                                    <!-- Dropdown Menu -->
                                    <div v-if="isDistrictDropdownOpen"
                                        class="absolute z-30 mt-1 w-full rounded-lg border border-gray-200 bg-white p-2 shadow-lg">
                                        <!-- Search Input -->
                                        <div class="relative mb-2">
                                            <input v-model="districtSearchQuery" type="text" placeholder="Type to search district..." ref="districtSearchInput"
                                                class="w-full rounded-md border border-gray-300 px-3 py-1.5 pl-8 text-xs text-gray-900 focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500" />
                                            <svg class="absolute left-2.5 top-2 h-3.5 w-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                            </svg>
                                        </div>

                                        <!-- Options List -->
                                        <ul class="max-h-48 overflow-y-auto divide-y divide-gray-50 text-xs">
                                            <li v-for="d in filteredDistricts" :key="d.id"
                                                @click="selectDistrictItem(d)"
                                                :class="[
                                                    'cursor-pointer px-3 py-2 transition-colors rounded-md hover:bg-primary-50 hover:text-primary-700',
                                                    selectedDistrictId == d.id ? 'bg-primary-50 font-semibold text-primary-700' : 'text-gray-700'
                                                ]">
                                                {{ d.name }}
                                            </li>
                                            <li v-if="filteredDistricts.length === 0" class="px-3 py-3 text-center text-gray-500">
                                                No district found matching "{{ districtSearchQuery }}".
                                                <button type="button" @click="toggleManualDistrict" class="mt-1 block w-full text-center text-xs font-semibold text-primary-600 hover:underline">
                                                    Enter manually
                                                </button>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </template>
                            <template v-else>
                                <input v-model="form.district" type="text" id="district"
                                    placeholder="Enter district name" :class="inputClass('district')"
                                    @input="clearError('district')" />
                            </template>
                            <p v-if="errors.district" class="mt-1.5 text-xs text-red-600">{{ errors.district }}</p>
                        </div>

                        <!-- Upazila (optional) -->
                        <div ref="upazilaContainerRef" class="relative">
                            <div class="mb-1.5 flex items-center justify-between">
                                <label for="upazila" class="block text-sm font-medium text-gray-700">
                                    Upazila / Thana
                                    <span class="text-xs text-gray-400">(Optional)</span>
                                </label>
                                <button v-if="!isManualDistrict && selectedDistrictId" type="button"
                                    @click="toggleManualUpazila"
                                    class="text-xs font-medium text-primary-600 hover:text-primary-800 hover:underline focus:outline-none">
                                    {{ isManualUpazila ? 'Select from list' : "Can't find upazila?" }}
                                </button>
                            </div>
                            <template v-if="!isManualUpazila && !isManualDistrict">
                                <div class="relative">
                                    <button type="button" 
                                        @click="toggleUpazilaDropdown"
                                        :disabled="!selectedDistrictId"
                                        :class="[
                                            inputClass('upazila'), 
                                            'flex items-center justify-between text-left bg-white',
                                            !selectedDistrictId ? 'cursor-not-allowed opacity-60' : 'cursor-pointer'
                                        ]">
                                        <span :class="selectedUpazilaName ? 'text-gray-900 font-medium' : 'text-gray-500'">
                                            {{ selectedUpazilaName || (selectedDistrictId ? 'Select Upazila' : 'Select District First') }}
                                        </span>
                                        <svg class="h-4 w-4 text-gray-400 transition-transform" :class="{ 'rotate-180': isUpazilaDropdownOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </button>

                                    <!-- Dropdown Menu -->
                                    <div v-if="isUpazilaDropdownOpen && selectedDistrictId"
                                        class="absolute z-30 mt-1 w-full rounded-lg border border-gray-200 bg-white p-2 shadow-lg">
                                        <!-- Search Input -->
                                        <div class="relative mb-2">
                                            <input v-model="upazilaSearchQuery" type="text" placeholder="Type to search upazila..." ref="upazilaSearchInput"
                                                class="w-full rounded-md border border-gray-300 px-3 py-1.5 pl-8 text-xs text-gray-900 focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500" />
                                            <svg class="absolute left-2.5 top-2 h-3.5 w-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                            </svg>
                                        </div>

                                        <!-- Options List -->
                                        <ul class="max-h-48 overflow-y-auto divide-y divide-gray-50 text-xs">
                                            <li @click="selectUpazilaItem({ id: '', name: '' })"
                                                class="cursor-pointer px-3 py-2 transition-colors rounded-md text-gray-400 hover:bg-gray-50">
                                                None / Clear Selection
                                            </li>
                                            <li v-for="u in filteredUpazilas" :key="u.id"
                                                @click="selectUpazilaItem(u)"
                                                :class="[
                                                    'cursor-pointer px-3 py-2 transition-colors rounded-md hover:bg-primary-50 hover:text-primary-700',
                                                    selectedUpazilaId == u.id ? 'bg-primary-50 font-semibold text-primary-700' : 'text-gray-700'
                                                ]">
                                                {{ u.name }}
                                            </li>
                                            <li v-if="filteredUpazilas.length === 0" class="px-3 py-3 text-center text-gray-500">
                                                No upazila found matching "{{ upazilaSearchQuery }}".
                                                <button type="button" @click="toggleManualUpazila" class="mt-1 block w-full text-center text-xs font-semibold text-primary-600 hover:underline">
                                                    Enter manually
                                                </button>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </template>
                            <template v-else>
                                <input v-model="form.upazila" type="text" id="upazila"
                                    placeholder="Enter upazila/thana name" :class="inputClass('upazila')"
                                    @input="clearError('upazila')" />
                            </template>
                            <p v-if="errors.upazila" class="mt-1.5 text-xs text-red-600">{{ errors.upazila }}</p>
                        </div>

                        <!-- Union (optional) -->
                        <!-- <div>
                            <label for="union" class="mb-1.5 block text-sm font-medium text-gray-700">
                                Union
                                <span class="text-xs text-gray-400">(Optional)</span>
                            </label>
                            <select v-model="selectedUnionId" id="union" :class="inputClass('union')"
                                :disabled="!selectedUpazilaId" @change="handleUnionChange">
                                <option value="">Select Union</option>
                                <option v-for="u in unions" :key="u.id" :value="u.id">{{ u.name }}</option>
                            </select>
                        </div> -->

                        <!-- Street Address -->
                        <div class="col-span-2">

                            <label for="address" class="mb-1.5 block text-sm font-medium text-gray-700">
                                Street Address <span class="text-red-500">*</span>

                            </label>
                            <textarea v-model="form.address" id="address" rows="3"
                                placeholder="House/flat number, road, area..."
                                :class="inputClass('address')"></textarea>
                            <p v-if="errors.address" class="mt-1.5 text-xs text-red-600">{{ errors.address }}</p>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Payment Method -->
            <!-- <div class="space-y-4">
                <h2 class="border-b border-gray-200 pb-2 text-xl font-semibold text-gray-900">
                    Payment Method <span class="text-red-500 text-base">*</span>
                </h2>

                <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
                    <button type="button" @click="selectPayment('cod')" :class="[
                        'flex items-center gap-3 rounded-xl border-2 p-4 text-left transition-all',
                        form.payment_method === 'cod'
                            ? 'border-primary-500 bg-primary-50 text-primary-700'
                            : 'border-gray-200 bg-white text-gray-700 hover:border-gray-300 hover:bg-gray-50'
                    ]">
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
                                <svg v-if="form.payment_method === 'cod'" class="h-3 w-3 text-white" viewBox="0 0 12 12"
                                    fill="currentColor">
                                    <path d="M10 3L5 8.5 2 5.5" stroke="white" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round" fill="none" />
                                </svg>
                            </div>
                        </div>
                    </button>

                    <button
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
                    </button>
                </div>
                <p v-if="errors.payment_method" class="text-xs text-red-600">{{ errors.payment_method }}</p>
            </div> -->
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

                    <label class="mb-1.5 block text-sm font-medium text-gray-700">Promo / Voucher Code

                    </label>
                    <div class="flex gap-2">
                        <input type="text" v-model="voucherCode" placeholder="Enter code"
                            class="block w-full rounded-lg border border-gray-300 bg-gray-50 px-3 py-2 text-sm text-gray-900 focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500" />
                        <button type="button"
                            class="shrink-0 rounded-lg bg-gray-800 px-4 py-2 text-sm font-medium text-white hover:bg-gray-700">
                            Apply
                        </button>
                    </div>
                </div>

                <!-- Special Note -->
                <div class="border-t border-gray-100 px-5 py-4">

                    <label for="note" class="mb-1.5 block text-sm font-medium text-gray-700">Special Note

                    </label>
                    <textarea v-model="form.note" id="note" rows="3" placeholder="Any instructions for your order..."
                        class="block w-full rounded-lg border border-gray-300 bg-gray-50 px-3 py-2 text-sm text-gray-900 focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"></textarea>
                </div>

                <!-- Submit -->
                <div class="border-t border-gray-100 px-5 py-4">
                    <p class="mb-3 text-xs text-gray-500">
                        Fields marked <span class="text-red-500 font-semibold">*</span> are required.
                    </p>
                    <button @click="submitForm" type="button" :disabled="submitting"
                        class="flex w-full items-center justify-center gap-2 rounded-xl bg-primary-700 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-primary-800 focus:outline-none focus:ring-4 focus:ring-primary-300 disabled:cursor-not-allowed disabled:opacity-60">
                        <svg v-if="submitting" class="h-4 w-4 animate-spin" xmlns="http://www.w3.org/2000/svg"
                            fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                            </circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                        {{ submitting ? 'Placing Order...' : 'Place Order' }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted, onUnmounted, nextTick } from 'vue'
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
    requiresShipping: {
        type: Boolean,
        default: true,
    },
    customer: {
        type: Object,
        default: () => null,
    },
    addresses: {
        type: Array,
        default: () => [],
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
    division: '',
    district: '',
    upazila: '',
    union: '',
    address: '',
    note: '',
    payment_method: 'cod',
})

const selectedAddressId = ref(null)

const districts = ref([])
const upazilas = ref([])
const unions = ref([])

const selectedDistrictId = ref('')
const selectedUpazilaId = ref('')
const selectedUnionId = ref('')

const isManualDistrict = ref(false)
const isManualUpazila = ref(false)

const districtContainerRef = ref(null)
const upazilaContainerRef = ref(null)
const districtSearchInput = ref(null)
const upazilaSearchInput = ref(null)

const isDistrictDropdownOpen = ref(false)
const isUpazilaDropdownOpen = ref(false)

const districtSearchQuery = ref('')
const upazilaSearchQuery = ref('')

const filteredDistricts = computed(() => {
    if (!districtSearchQuery.value.trim()) return districts.value
    const q = districtSearchQuery.value.toLowerCase().trim()
    return districts.value.filter(d => d.name.toLowerCase().includes(q))
})

const filteredUpazilas = computed(() => {
    if (!upazilaSearchQuery.value.trim()) return upazilas.value
    const q = upazilaSearchQuery.value.toLowerCase().trim()
    return upazilas.value.filter(u => u.name.toLowerCase().includes(q))
})

const selectedDistrictName = computed(() => {
    if (!selectedDistrictId.value) return ''
    const found = districts.value.find(d => d.id == selectedDistrictId.value)
    return found ? found.name : form.district || ''
})

const selectedUpazilaName = computed(() => {
    if (!selectedUpazilaId.value) return ''
    const found = upazilas.value.find(u => u.id == selectedUpazilaId.value)
    return found ? found.name : form.upazila || ''
})

const cachedDivisions = ref([])

const voucherCode = ref('')
const submitting = ref(false)
const errors = reactive({})
const generalError = ref('')

onMounted(async () => {
    // Prefill customer details if logged in
    if (props.customer) {
        form.name = props.customer.name || ''
        form.email = props.customer.email || ''
        form.phone = props.customer.phone || ''
    }

    // Set up initial address selection
    if (props.requiresShipping) {
        if (props.addresses && props.addresses.length > 0) {
            const defaultAddr = props.addresses.find(a => a.default) || props.addresses[0]
            selectAddress(defaultAddr)
        } else {
            selectedAddressId.value = 'new'
            await loadDistricts()
        }
    }

    if (!window.ShopNowTracking) {
        return
    }

    window.addEventListener('click', handleOutsideClick)

    window.ShopNowTracking.track('InitiateCheckout', {
        content_ids: cartStore.items.map((cartItem) => String(cartItem.item.id)),
        content_type: 'product',
        num_items: cartStore.items.reduce((total, cartItem) => total + Number(cartItem.quantity || 0), 0),
        value: Number(orderTotal.value || 0),
        currency: 'BDT',
    })
    window.ShopNowTracking.trackGa('begin_checkout', {
        currency: 'BDT',
        value: Number(orderTotal.value || 0),
        items: cartStore.items.map((cartItem) => ({
            item_id: String(cartItem.item.id),
            item_name: cartItem.item.name,
            price: Number(cartItem.item.price || 0),
            item_variant: cartItem.variation_label || undefined,
            quantity: Number(cartItem.quantity || 1),
        }))
    })
})

async function selectCustomAddress() {
    selectedAddressId.value = 'new'
    form.division = ''
    form.district = ''
    form.upazila = ''
    form.union = ''
    form.address = ''
    selectedDistrictId.value = ''
    selectedUpazilaId.value = ''
    selectedUnionId.value = ''
    isManualDistrict.value = false
    isManualUpazila.value = false
    upazilas.value = []
    unions.value = []
    if (districts.value.length === 0) {
        await loadDistricts()
    }
}

function selectAddress(addr) {
    selectedAddressId.value = addr.id
    form.division = addr.division_name || ''
    form.district = addr.district_name || ''
    form.upazila = addr.upazilla_name || ''
    form.union = addr.union_name || ''
    form.address = addr.address || ''
}

async function loadDistricts() {
    try {
        const [distRes, divRes] = await Promise.all([
            axios.get('/geocode/districts'),
            axios.get('/geocode/divisions'),
        ])
        districts.value = distRes.data
        cachedDivisions.value = divRes.data
    } catch (e) {
        console.error(e)
    }
}

onUnmounted(() => {
    window.removeEventListener('click', handleOutsideClick)
})

function handleOutsideClick(event) {
    if (districtContainerRef.value && !districtContainerRef.value.contains(event.target)) {
        isDistrictDropdownOpen.value = false
    }
    if (upazilaContainerRef.value && !upazilaContainerRef.value.contains(event.target)) {
        isUpazilaDropdownOpen.value = false
    }
}

function toggleDistrictDropdown() {
    isDistrictDropdownOpen.value = !isDistrictDropdownOpen.value
    if (isDistrictDropdownOpen.value) {
        isUpazilaDropdownOpen.value = false
        districtSearchQuery.value = ''
        nextTick(() => {
            districtSearchInput.value?.focus()
        })
    }
}

function toggleUpazilaDropdown() {
    if (!selectedDistrictId.value) return
    isUpazilaDropdownOpen.value = !isUpazilaDropdownOpen.value
    if (isUpazilaDropdownOpen.value) {
        isDistrictDropdownOpen.value = false
        upazilaSearchQuery.value = ''
        nextTick(() => {
            upazilaSearchInput.value?.focus()
        })
    }
}

function selectDistrictItem(d) {
    selectedDistrictId.value = d.id
    isDistrictDropdownOpen.value = false
    districtSearchQuery.value = ''
    handleDistrictChange()
}

function selectUpazilaItem(u) {
    selectedUpazilaId.value = u.id
    isUpazilaDropdownOpen.value = false
    upazilaSearchQuery.value = ''
    handleUpazilaChange()
}

function toggleManualDistrict() {
    isManualDistrict.value = !isManualDistrict.value
    isDistrictDropdownOpen.value = false
    isUpazilaDropdownOpen.value = false
    delete errors.district
    delete errors.upazila
    if (isManualDistrict.value) {
        selectedDistrictId.value = ''
        selectedUpazilaId.value = ''
        form.district = ''
        form.upazila = ''
        upazilas.value = []
        isManualUpazila.value = true
    } else {
        form.district = ''
        form.upazila = ''
        isManualUpazila.value = false
    }
}

function toggleManualUpazila() {
    isManualUpazila.value = !isManualUpazila.value
    isUpazilaDropdownOpen.value = false
    delete errors.upazila
    if (isManualUpazila.value) {
        selectedUpazilaId.value = ''
        form.upazila = ''
    } else {
        form.upazila = ''
    }
}

async function handleDistrictChange() {
    isManualDistrict.value = false
    isManualUpazila.value = false
    selectedUpazilaId.value = ''
    selectedUnionId.value = ''
    upazilas.value = []
    unions.value = []
    form.upazila = ''
    form.union = ''
    delete errors.district

    if (!selectedDistrictId.value) {
        form.district = ''
        form.division = ''
        return
    }

    const districtObj = districts.value.find(d => d.id == selectedDistrictId.value)
    form.district = districtObj ? districtObj.name : ''
    // Auto-derive division name from district's division_id using cached divisions
    if (districtObj?.division_id && cachedDivisions.value.length) {
        const divObj = cachedDivisions.value.find(d => d.id == districtObj.division_id)
        form.division = divObj ? divObj.name : ''
    }

    try {
        const response = await axios.get(`/geocode/upazilas?district_id=${selectedDistrictId.value}`)
        upazilas.value = response.data
    } catch (e) {
        console.error(e)
    }
}

async function handleUpazilaChange() {
    isManualUpazila.value = false
    selectedUnionId.value = ''
    unions.value = []
    form.union = ''
    delete errors.upazila

    if (!selectedUpazilaId.value) {
        form.upazila = ''
        return
    }

    const upazilaObj = upazilas.value.find(u => u.id == selectedUpazilaId.value)
    form.upazila = upazilaObj ? upazilaObj.name : ''

    try {
        const response = await axios.get(`/geocode/unions?upazila_id=${selectedUpazilaId.value}`)
        unions.value = response.data
    } catch (e) {
        console.error(e)
    }
}

function handleUnionChange() {
    delete errors.union
    if (!selectedUnionId.value) {
        form.union = ''
        return
    }

    const unionObj = unions.value.find(u => u.id == selectedUnionId.value)
    form.union = unionObj ? unionObj.name : ''
}

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

    if (props.requiresShipping) {
        if (!form.district.trim()) {
            errors.district = 'District is required.'
            valid = false
        }

        if (!form.address.trim()) {
            errors.address = 'Street address is required.'
            valid = false
        }
    }

    if (!form.payment_method) {
        errors.payment_method = 'Please select a payment method.'
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
        const items = cartStore.items.map((cartItem) => ({
            ...cartItem,
            variation_label: cartItem.variation_label || null,
        }))

        const payload = {
            ...form,
            selected_address_id: selectedAddressId.value || null,
            division_id: isManualDistrict.value ? null : (districts.value.find(d => d.id == selectedDistrictId.value)?.division_id || null),
            district_id: isManualDistrict.value ? null : (selectedDistrictId.value || null),
            upazila_id: (isManualDistrict.value || isManualUpazila.value) ? null : (selectedUpazilaId.value || null),
            union_id: selectedUnionId.value || null,
            items: items,
            subtotal: cartStore.subtotal,
            tax: cartStore.tax,
            shipping: shippingCharge.value,
            total: orderTotal.value,
            paid: 0,
            due: orderTotal.value,
        }

        const response = await axios.post('/site-order-store', payload)

        if (window.ShopNowTracking) {
            window.ShopNowTracking.track('Purchase', {
                content_ids: cartStore.items.map((cartItem) => String(cartItem.item.id)),
                content_type: 'product',
                num_items: cartStore.items.reduce((total, cartItem) => total + Number(cartItem.quantity || 0), 0),
                value: Number(orderTotal.value || 0),
                currency: 'BDT',
            }, {
                eventID: 'purchase_' + response.data.order_id,
            })
        }

        await cartStore.clearCart()

        window.location.href = '/order-confirm/' + response.data.order_id
    } catch (error) {
        console.error('Checkout error response:', error.response?.data);
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
