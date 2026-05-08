<template>
    <!-- Cart has items -->
    <section v-if="cartStore.totalQuantity">

        <!-- Page header -->
        <div class="mb-6 flex items-start justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 sm:text-3xl">Shopping Cart</h1>
                <p class="mt-1 text-sm text-gray-500">{{ cartStore.totalQuantity }} item{{ cartStore.totalQuantity !== 1 ? 's' : '' }} in your cart</p>
            </div>
            <a href="/shop" class="hidden items-center gap-1.5 text-sm text-primary-600 hover:underline sm:inline-flex">
                <i class="ri-arrow-left-line"></i>
                Continue Shopping
            </a>
        </div>

        <div class="lg:grid lg:grid-cols-12 lg:gap-8">

            <!-- ── Left: Cart Items ── -->
            <div class="lg:col-span-8">

                <!-- Desktop column headers -->
                <div class="mb-2 hidden grid-cols-12 gap-4 px-4 py-2 text-xs font-semibold uppercase tracking-wider text-gray-400 sm:grid">
                    <div class="col-span-6">Product</div>
                    <div class="col-span-2 text-center">Price</div>
                    <div class="col-span-2 text-center">Qty</div>
                    <div class="col-span-2 text-right">Total</div>
                </div>

                <!-- Items list -->
                <div class="divide-y divide-gray-100 overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm">
                    <div
                        v-for="item in cartStore.items"
                        :key="item.id"
                        class="transition-colors hover:bg-gray-50/60"
                    >
                        <!-- ── Mobile card ── -->
                        <div class="flex gap-3 p-4 sm:hidden">
                            <a
                                :href="`/shop/product/${item.item.id}/${item.item.slug}`"
                                class="flex h-[72px] w-[72px] shrink-0 overflow-hidden rounded-xl border border-gray-100 bg-gray-50"
                            >
                                <img :src="item.item.image_url" :alt="item.item.name" class="h-full w-full object-contain p-1" />
                            </a>
                            <div class="min-w-0 flex-1">
                                <a
                                    :href="`/shop/product/${item.item.id}/${item.item.slug}`"
                                    class="line-clamp-2 text-sm font-semibold leading-snug text-gray-900 hover:text-primary-600"
                                >{{ item.item.name }}</a>
                                <p class="mt-0.5 text-sm font-bold text-primary-600">{{ item.item.price }} Tk.</p>

                                <div class="mt-2 flex items-center justify-between">
                                    <div class="flex items-center overflow-hidden rounded-lg border border-gray-200 bg-gray-50">
                                        <button @click="cartStore.decreaseQuantity(item.item)" type="button"
                                            class="flex h-8 w-8 items-center justify-center text-gray-500 transition-colors hover:bg-primary-50 hover:text-primary-600 active:bg-gray-200 focus:outline-none">
                                            <i class="ri-subtract-line text-xs"></i>
                                        </button>
                                        <span class="w-8 text-center text-sm font-bold text-gray-900">{{ item.quantity }}</span>
                                        <button @click="cartStore.increaseQuantity(item.item)" type="button"
                                            class="flex h-8 w-8 items-center justify-center text-gray-500 transition-colors hover:bg-primary-50 hover:text-primary-600 active:bg-gray-200 focus:outline-none">
                                            <i class="ri-add-line text-xs"></i>
                                        </button>
                                    </div>
                                    <span class="text-sm font-extrabold text-gray-900">{{ item.item.price * item.quantity }} Tk.</span>
                                    <button @click="cartStore.removeItem(item.item)" type="button"
                                        class="flex h-8 w-8 items-center justify-center rounded-lg text-gray-400 transition-colors hover:bg-red-50 hover:text-red-500 focus:outline-none">
                                        <i class="ri-delete-bin-line"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- ── Desktop row (12-col grid) ── -->
                        <div class="hidden grid-cols-12 items-center gap-4 p-4 sm:grid sm:p-5">
                            <!-- Product -->
                            <div class="col-span-6 flex items-center gap-4">
                                <a
                                    :href="`/shop/product/${item.item.id}/${item.item.slug}`"
                                    class="flex h-20 w-20 shrink-0 overflow-hidden rounded-xl border border-gray-100 bg-gray-50"
                                >
                                    <img :src="item.item.image_url" :alt="item.item.name" class="h-full w-full object-contain p-1.5" />
                                </a>
                                <div class="min-w-0">
                                    <a
                                        :href="`/shop/product/${item.item.id}/${item.item.slug}`"
                                        class="line-clamp-2 text-sm font-semibold leading-snug text-gray-900 hover:text-primary-600"
                                    >{{ item.item.name }}</a>
                                    <button @click="cartStore.removeItem(item.item)" type="button"
                                        class="mt-1.5 flex items-center gap-1 text-xs text-gray-400 transition-colors hover:text-red-500">
                                        <i class="ri-delete-bin-line"></i> Remove
                                    </button>
                                </div>
                            </div>
                            <!-- Unit price -->
                            <div class="col-span-2 text-center text-sm font-medium text-gray-600">
                                {{ item.item.price }} Tk.
                            </div>
                            <!-- Qty stepper -->
                            <div class="col-span-2 flex justify-center">
                                <div class="flex items-center overflow-hidden rounded-lg border border-gray-200 bg-gray-50">
                                    <button @click="cartStore.decreaseQuantity(item.item)" type="button"
                                        class="flex h-9 w-9 items-center justify-center text-gray-500 transition-colors hover:bg-primary-50 hover:text-primary-600 active:bg-gray-200 focus:outline-none">
                                        <i class="ri-subtract-line text-sm"></i>
                                    </button>
                                    <span class="w-9 text-center text-sm font-bold text-gray-900">{{ item.quantity }}</span>
                                    <button @click="cartStore.increaseQuantity(item.item)" type="button"
                                        class="flex h-9 w-9 items-center justify-center text-gray-500 transition-colors hover:bg-primary-50 hover:text-primary-600 active:bg-gray-200 focus:outline-none">
                                        <i class="ri-add-line text-sm"></i>
                                    </button>
                                </div>
                            </div>
                            <!-- Line total -->
                            <div class="col-span-2 text-right text-base font-extrabold text-gray-900">
                                {{ item.item.price * item.quantity }} Tk.
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Mobile: continue shopping -->
                <div class="mt-4 sm:hidden">
                    <a href="/shop" class="inline-flex items-center gap-1.5 text-sm text-primary-600 hover:underline">
                        <i class="ri-arrow-left-line"></i>
                        Continue Shopping
                    </a>
                </div>
            </div>

            <!-- ── Right: Order Summary ── -->
            <div class="mt-6 lg:col-span-4 lg:mt-0">
                <div class="sticky top-6 overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm">

                    <!-- Shipping progress: not yet unlocked -->
                    <div v-if="freeShippingThreshold > 0 && shippingCharge > 0" class="bg-amber-50 px-5 pt-4 pb-3">
                        <div class="mb-1.5 flex items-center justify-between text-xs font-semibold text-amber-700">
                            <span><i class="ri-truck-line mr-1"></i>Free shipping progress</span>
                            <span>{{ cartStore.subtotal }} / {{ freeShippingThreshold }} Tk.</span>
                        </div>
                        <div class="h-2 w-full overflow-hidden rounded-full bg-amber-100">
                            <div class="h-full rounded-full bg-amber-400 transition-all duration-500"
                                :style="{ width: Math.min((cartStore.subtotal / freeShippingThreshold) * 100, 100) + '%' }">
                            </div>
                        </div>
                        <p class="mt-1.5 text-xs text-amber-600">
                            Add <span class="font-bold">{{ freeShippingThreshold - cartStore.subtotal }} Tk.</span> more to unlock <span class="font-bold">FREE shipping</span>
                        </p>
                    </div>

                    <!-- Shipping unlocked -->
                    <div v-else-if="shippingCharge === 0 && cartStore.subtotal > 0"
                        class="flex items-center gap-2 bg-green-50 px-5 py-3">
                        <i class="ri-checkbox-circle-fill text-base text-green-500"></i>
                        <p class="text-xs font-semibold text-green-700">You unlocked FREE shipping!</p>
                    </div>

                    <div class="p-5">
                        <h2 class="mb-4 text-xs font-bold uppercase tracking-widest text-gray-400">Order Summary</h2>

                        <div class="space-y-3">
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-500">Subtotal <span class="text-xs text-gray-400">({{ cartStore.totalQuantity }} items)</span></span>
                                <span class="font-semibold text-gray-900">{{ cartStore.subtotal }} Tk.</span>
                            </div>
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-500">Shipping</span>
                                <span v-if="shippingCharge === 0" class="font-semibold text-green-600">Free</span>
                                <span v-else class="font-semibold text-gray-900">{{ shippingCharge }} Tk.</span>
                            </div>
                        </div>

                        <div class="my-4 h-px bg-gray-100"></div>

                        <div class="flex items-center justify-between">
                            <span class="text-sm font-bold text-gray-900">Total</span>
                            <div class="text-right">
                                <span class="text-2xl font-extrabold text-primary-600">{{ orderTotal }}</span>
                                <span class="ml-1 text-sm font-bold text-primary-600">Tk.</span>
                            </div>
                        </div>

                        <a href="/checkout"
                            class="mt-5 flex w-full items-center justify-center gap-2 rounded-xl bg-primary-600 py-3.5 text-sm font-bold text-white shadow-sm transition-all hover:bg-primary-700 hover:shadow-md active:scale-[0.98] focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                            <i class="ri-lock-line"></i>
                            Proceed to Checkout
                        </a>

                        <!-- Trust badges -->
                        <div class="mt-4 flex items-center justify-center gap-3 text-xs text-gray-400">
                            <span class="flex items-center gap-1">
                                <i class="ri-shield-check-line"></i> Secure
                            </span>
                            <span class="text-gray-200">|</span>
                            <span class="flex items-center gap-1">
                                <i class="ri-refresh-line"></i> Easy Returns
                            </span>
                            <span class="text-gray-200">|</span>
                            <span class="flex items-center gap-1">
                                <i class="ri-headphone-line"></i> Support
                            </span>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>

    <!-- Empty cart -->
    <div v-else class="flex flex-col items-center justify-center py-24 text-center">
        <div class="mb-6 flex h-28 w-28 items-center justify-center rounded-full bg-gray-100">
            <i class="ri-shopping-cart-2-line text-5xl text-gray-300"></i>
        </div>
        <h2 class="mb-2 text-2xl font-bold text-gray-900">Your cart is empty</h2>
        <p class="mb-8 max-w-xs text-sm text-gray-500">Looks like you haven't added anything yet. Browse our products and find something you love!</p>
        <a href="/shop"
            class="inline-flex items-center gap-2 rounded-xl bg-primary-600 px-6 py-3 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-primary-700">
            <i class="ri-store-2-line"></i>
            Start Shopping
        </a>
    </div>
</template>
<script setup>
import { computed } from 'vue'
import { useCartStore } from '../Stores/CartStore'

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

const cartStore = useCartStore()

const shippingCharge = computed(() => {
    if (props.freeShippingThreshold > 0 && cartStore.subtotal >= props.freeShippingThreshold) {
        return 0
    }
    return cartStore.subtotal > 0 ? props.shippingFlatRate : 0
})

const orderTotal = computed(() => cartStore.subtotal + shippingCharge.value + cartStore.tax)
</script>
