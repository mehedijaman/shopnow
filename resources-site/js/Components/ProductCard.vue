<template>
    <div
        class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800"
    >
        <div class="h-56 w-full">
            <a :href="productDetailsRoute">
                <img
                    class="mx-auto h-full dark:hidden"
                    :src="product.image_url"
                    alt=""
                />
            </a>
        </div>
        <div class="pt-6">
            <div class="mb-4 flex items-center justify-between gap-4">
                <!-- <span
                    class="bg-primary-100 text-primary-800 dark:bg-primary-900 dark:text-primary-300 me-2 rounded px-2.5 py-0.5 text-xs font-medium"
                >
                    <span v-for="tag in product.tags" :key="tag">
                        {{ tag.name }},
                    </span>
                </span> -->

                <div class="flex items-center justify-end gap-1">
                    <button
                        type="button"
                        data-tooltip-target="tooltip-quick-look"
                        class="rounded-lg p-2 text-gray-500 hover:bg-gray-100 hover:text-gray-900 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white"
                    >
                        <span class="sr-only">Quick look</span>
                        <svg
                            class="h-5 w-5"
                            aria-hidden="true"
                            xmlns="http://www.w3.org/2000/svg"
                            width="24"
                            height="24"
                            fill="none"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke="currentColor"
                                stroke-width="2"
                                d="M21 12c0 1.2-4.03 6-9 6s-9-4.8-9-6c0-1.2 4.03-6 9-6s9 4.8 9 6Z"
                            />
                            <path
                                stroke="currentColor"
                                stroke-width="2"
                                d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"
                            />
                        </svg>
                    </button>
                    <div
                        id="tooltip-quick-look"
                        role="tooltip"
                        class="tooltip invisible absolute z-10 inline-block rounded-lg bg-gray-900 px-3 py-2 text-sm font-medium text-white opacity-0 shadow-sm transition-opacity duration-300 dark:bg-gray-700"
                        data-popper-placement="top"
                    >
                        Quick look
                        <div class="tooltip-arrow" data-popper-arrow=""></div>
                    </div>

                    <button
                        type="button"
                        data-tooltip-target="tooltip-add-to-favorites"
                        class="rounded-lg p-2 text-gray-500 hover:bg-gray-100 hover:text-gray-900 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white"
                    >
                        <span class="sr-only"> Add to Favorites </span>
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
                                d="M12 6C6.5 1 1 8 5.8 13l6.2 7 6.2-7C23 8 17.5 1 12 6Z"
                            />
                        </svg>
                    </button>
                    <div
                        id="tooltip-add-to-favorites"
                        role="tooltip"
                        class="tooltip invisible absolute z-10 inline-block rounded-lg bg-gray-900 px-3 py-2 text-sm font-medium text-white opacity-0 shadow-sm transition-opacity duration-300 dark:bg-gray-700"
                        data-popper-placement="top"
                    >
                        Add to favorites
                        <div class="tooltip-arrow" data-popper-arrow=""></div>
                    </div>
                </div>
            </div>

            <a
                :href="productDetailsRoute"
                class="text-lg font-semibold leading-tight text-gray-900 hover:underline dark:text-white"
            >
                {{ product.name }}
            </a>

            <ul class="mt-2 flex items-center gap-4">
                <svg
                    class="h-4 w-4 text-gray-500 dark:text-gray-400"
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
                        d="M13 7h6l2 4m-8-4v8m0-8V6a1 1 0 0 0-1-1H4a1 1 0 0 0-1 1v9h2m8 0H9m4 0h2m4 0h2v-4m0 0h-5m3.5 5.5a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0Zm-10 0a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0Z"
                    />
                </svg>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">
                    {{ product.category?.name }}
                </p>
            </ul>

            <div class="mt-4 flex items-center justify-between gap-4">
                <p
                    class="text-2xl font-extrabold leading-tight text-gray-900 dark:text-white"
                >
                    {{ product.price }}
                </p>

                <button
                    @click="addToCart(product)"
                    type="button"
                    class="inline-flex items-center rounded-lg bg-blue-700 px-3 py-2 text-center text-xs font-medium text-white hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800"
                >
                    Add to cart
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
const props = defineProps({
    product: {
        type: Object,
        required: true
    }
})

const productDetailsRoute = '/products/' + props.product.id

function addToCart(product) {
    localStorage.setItem('cart', JSON.stringify(product))
}
</script>
