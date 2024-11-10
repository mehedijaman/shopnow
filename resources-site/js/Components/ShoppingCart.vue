<template>
    <section
        v-if="cartStore.totalQuantity"
        class="bg-white antialiased dark:bg-gray-900"
    >
        <div class="mx-auto max-w-screen-xl 2xl:px-0">
            <h2
                class="text-xl font-semibold text-gray-900 dark:text-white sm:text-2xl"
            >
                Shopping Cart
            </h2>

            <div class="mt-6 sm:mt-8 md:gap-6 lg:flex lg:items-start xl:gap-8">
                <div class="mx-auto w-full flex-none lg:max-w-2xl xl:max-w-4xl">
                    <div class="space-y-6">
                        <div
                            v-for="item in cartStore.items"
                            :key="item.id"
                            class="rounded-lg border border-gray-200 bg-skin-neutral-1 p-4 shadow-sm hover:bg-skin-primary-2 dark:border-gray-700 dark:bg-gray-800 md:p-6"
                        >
                            <div
                                class="space-y-4 md:flex md:items-center md:justify-between md:gap-6 md:space-y-0"
                            >
                                <a
                                    :href="`/shop/product/${item.item.id}/${item.item.slug}`"
                                    class="shrink-0 md:order-1"
                                >
                                    <img
                                        class="h-20 w-20"
                                        :src="item.item.image_url"
                                        :alt="item.item.name"
                                    />
                                </a>

                                <label for="counter-input" class="sr-only">
                                    Choose quantity:
                                </label>
                                <div
                                    class="flex items-center justify-between md:order-3 md:justify-end"
                                >
                                    <div class="flex items-center">
                                        <button
                                            @click="
                                                cartStore.decreaseQuantity(
                                                    item.item
                                                )
                                            "
                                            type="button"
                                            id="decrement-button"
                                            data-input-counter-decrement="counter-input"
                                            class="inline-flex h-5 w-5 shrink-0 items-center justify-center rounded-md border border-gray-300 bg-gray-100 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-100 dark:border-gray-600 dark:bg-gray-700 dark:hover:bg-gray-600 dark:focus:ring-gray-700"
                                        >
                                            <i class="ri-subtract-line"></i>
                                        </button>
                                        <span
                                            class="w-10 shrink-0 border-0 bg-transparent text-center text-sm font-medium text-gray-900 focus:outline-none focus:ring-0 dark:text-white"
                                        >
                                            {{ item.quantity }}
                                        </span>
                                        <button
                                            @click="
                                                cartStore.increaseQuantity(
                                                    item.item
                                                )
                                            "
                                            type="button"
                                            id="increment-button"
                                            data-input-counter-increment="counter-input"
                                            class="inline-flex h-5 w-5 shrink-0 items-center justify-center rounded-md border border-gray-300 bg-gray-100 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-100 dark:border-gray-600 dark:bg-gray-700 dark:hover:bg-gray-600 dark:focus:ring-gray-700"
                                        >
                                            <i class="ri-add-line"></i>
                                        </button>
                                    </div>
                                    <div class="text-end md:order-4 md:w-32">
                                        <p
                                            class="text-base font-bold text-gray-900 dark:text-white"
                                        >
                                            <svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                viewBox="0 0 384 512"
                                                class="inline h-3 w-3"
                                            >
                                                <path
                                                    d="M36 32.3C18.4 30.1 2.4 42.5 .2 60S10.5 93.6 28 95.8l7.9 1c16 2 28 15.6 28 31.8L64 160l-32 0c-17.7 0-32 14.3-32 32s14.3 32 32 32l32 0 0 160c0 53 43 96 96 96l32 0c106 0 192-86 192-192l0-32c0-53-43-96-96-96l-16 0c-17.7 0-32 14.3-32 32s14.3 32 32 32l16 0c17.7 0 32 14.3 32 32l0 32c0 70.7-57.3 128-128 128l-32 0c-17.7 0-32-14.3-32-32l0-160 32 0c17.7 0 32-14.3 32-32s-14.3-32-32-32l-32 0 0-31.5c0-48.4-36.1-89.3-84.1-95.3l-7.9-1z"
                                                />
                                            </svg>
                                            {{
                                                item.item.price * item.quantity
                                            }}
                                        </p>
                                    </div>
                                </div>

                                <div
                                    class="w-full min-w-0 flex-1 space-y-4 md:order-2 md:max-w-md"
                                >
                                    <a
                                        :href="`/shop/product/${item.item.id}/${item.item.slug}`"
                                        class="text-base font-medium text-gray-900 hover:underline dark:text-white"
                                    >
                                        {{ item.item.name }}
                                    </a>

                                    <div class="flex items-center gap-4">
                                        <button
                                            type="button"
                                            class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-gray-900 hover:underline dark:text-gray-400 dark:hover:text-white"
                                        >
                                            <i
                                                class="ri-heart-line mr-1 text-xl"
                                            ></i>
                                            Add to Favorites
                                        </button>

                                        <button
                                            @click="
                                                cartStore.removeItem(item.item)
                                            "
                                            type="button"
                                            class="inline-flex items-center text-sm font-medium text-red-600 hover:underline dark:text-red-500"
                                        >
                                            <i
                                                class="ri-close-line mr-1 text-2xl"
                                            ></i>
                                            Remove
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div
                    class="mx-auto mt-6 max-w-4xl flex-1 space-y-6 lg:mt-0 lg:w-full"
                >
                    <div
                        class="space-y-4 rounded-lg border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800 sm:p-6"
                    >
                        <p
                            class="text-xl font-semibold text-gray-900 dark:text-white"
                        >
                            Order summary
                        </p>

                        <div class="space-y-4">
                            <div class="space-y-2">
                                <dl
                                    class="flex items-center justify-between gap-4"
                                >
                                    <dt
                                        class="text-base font-normal text-gray-500 dark:text-gray-400"
                                    >
                                        Subtotal
                                    </dt>
                                    <dd
                                        class="text-base font-medium text-gray-900 dark:text-white"
                                    >
                                        {{ cartStore.totalAmount }}
                                    </dd>
                                </dl>

                                <dl
                                    class="flex items-center justify-between gap-4"
                                >
                                    <dt
                                        class="text-base font-normal text-gray-500 dark:text-gray-400"
                                    >
                                        Shipping
                                    </dt>
                                    <dd
                                        class="text-base font-medium text-green-600"
                                    >
                                        0
                                    </dd>
                                </dl>

                                <dl
                                    class="flex items-center justify-between gap-4"
                                >
                                    <dt
                                        class="text-base font-normal text-gray-500 dark:text-gray-400"
                                    >
                                        Discount
                                    </dt>
                                    <dd
                                        class="text-base font-medium text-gray-900 dark:text-white"
                                    >
                                        0
                                    </dd>
                                </dl>
                            </div>

                            <dl
                                class="flex items-center justify-between gap-4 border-t border-gray-200 pt-2 dark:border-gray-700"
                            >
                                <dt
                                    class="text-base font-bold text-gray-900 dark:text-white"
                                >
                                    Total
                                </dt>
                                <dd
                                    class="text-base font-bold text-gray-900 dark:text-white"
                                >
                                    {{ cartStore.totalAmount }}
                                </dd>
                            </dl>
                        </div>

                        <a
                            href="#"
                            class="flex w-full items-center justify-center rounded-lg bg-primary-700 px-5 py-2.5 text-sm font-medium text-white hover:bg-primary-800 focus:outline-none focus:ring-4 focus:ring-primary-300 dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800"
                        >
                            Proceed to Checkout
                        </a>

                        <div class="flex items-center justify-center gap-2">
                            <span
                                class="text-sm font-normal text-gray-500 dark:text-gray-400"
                            >
                                or
                            </span>
                            <a
                                href="/products"
                                title=""
                                class="inline-flex items-center gap-2 text-sm font-medium text-primary-700 underline hover:no-underline dark:text-primary-500"
                            >
                                Continue Shopping
                                <svg
                                    class="h-5 w-5"
                                    aria-hidden="true"
                                    xmlns="http://www.w3.org/2000/svg"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke="currentColor"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M19 12H5m14 0-4 4m4-4-4-4"
                                    />
                                </svg>
                            </a>
                        </div>
                    </div>

                    <div
                        class="space-y-4 rounded-lg border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800 sm:p-6"
                    >
                        <form class="space-y-4">
                            <div>
                                <label
                                    for="voucher"
                                    class="mb-2 block text-sm font-medium text-gray-900 dark:text-white"
                                >
                                    Do you have a voucher or gift card?
                                </label>
                                <input
                                    type="text"
                                    id="voucher"
                                    class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder:text-gray-400 dark:focus:border-primary-500 dark:focus:ring-primary-500"
                                    placeholder=""
                                    required
                                />
                            </div>
                            <button
                                type="submit"
                                class="flex w-full items-center justify-center rounded-lg bg-primary-700 px-5 py-2.5 text-sm font-medium text-white hover:bg-primary-800 focus:outline-none focus:ring-4 focus:ring-primary-300 dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800"
                            >
                                Apply Code
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div
        v-else
        class="w-full rounded-lg border border-gray-200 bg-white p-6 text-center shadow dark:border-gray-700 dark:bg-gray-800"
    >
        <div class="text-bold mb-8 text-6xl">
            <i class="ri-shopping-cart-2-line"></i>
        </div>
        <h5
            class="mb-6 text-2xl font-bold tracking-tight text-gray-900 dark:text-white"
        >
            Your shopping cart is empty.
        </h5>
        <a
            href="/shop"
            class="inline-flex items-center rounded-lg bg-blue-700 px-3 py-2 text-center text-sm font-medium text-white hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800"
        >
            Continue shopping
            <i class="ri-arrow-right-line ml-2"></i>
        </a>
    </div>
</template>
<script setup>
import { useCartStore } from '../Stores/CartStore'

const cartStore = useCartStore()
</script>
