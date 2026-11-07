<template>
    <!-- Cart has items -->
    <section v-if="cartStore.totalQuantity">
        <!-- Page header -->
        <div class="mb-6 flex items-start justify-between">
            <div>
                <h1 class="text-2xl font-bold text-site-text dark:text-site-text sm:text-3xl">
                    {{ __('cart::site.cart.checkout') }}
                </h1>
                <p class="mt-1 text-sm text-site-muted dark:text-site-muted">
                    {{
                        __('cart::site.cart.items_in_cart', {
                            count: cartStore.totalQuantity
                        })
                    }}
                </p>
            </div>
            <a
                href="/shop"
                class="hidden items-center gap-1.5 text-sm text-site-link hover:underline sm:inline-flex"
            >
                <i class="ri-arrow-left-line"></i>
                {{ __('cart::site.cart.continue_shopping') }}
            </a>
        </div>

        <!-- ── Cart Items ── -->
        <div class="mb-8">
            <!-- Desktop column headers -->
            <div
                class="mb-2 hidden grid-cols-12 gap-4 px-4 py-2 text-xs font-semibold uppercase tracking-wider text-site-muted sm:grid"
            >
                <div class="col-span-6">
                    {{ __('cart::site.cart.col_product') }}
                </div>
                <div class="col-span-2 text-center">
                    {{ __('cart::site.cart.col_price') }}
                </div>
                <div class="col-span-2 text-center">
                    {{ __('cart::site.cart.col_qty') }}
                </div>
                <div class="col-span-2 text-right">
                    {{ __('cart::site.cart.col_total') }}
                </div>
            </div>

            <!-- Items list -->
            <div
                class="divide-y divide-site-divider dark:divide-site-divider overflow-hidden rounded-2xl border border-site-divider dark:border-site-divider bg-site-surface dark:bg-site-surface-muted shadow-sm"
            >
                <div
                    v-for="item in cartStore.items"
                    :key="item.id"
                    class="transition-colors hover:bg-site-surface-muted/60 dark:hover:bg-site-surface-muted/60"
                >
                    <!-- Mobile card -->
                    <div class="flex gap-3 p-4 sm:hidden">
                        <a
                            :href="`/shop/product/${item.item.id}/${item.item.slug}`"
                            class="flex h-[72px] w-[72px] shrink-0 overflow-hidden rounded-xl border border-site-divider dark:border-site-divider bg-site-surface-muted dark:bg-site-surface-muted"
                        >
                            <img
                                :src="getItemImage(item)"
                                :alt="item.item.name"
                                @error="onItemImageError"
                                class="h-full w-full object-contain p-1"
                            />
                        </a>
                        <div class="min-w-0 flex-1">
                            <a
                                :href="`/shop/product/${item.item.id}/${item.item.slug}`"
                                class="line-clamp-2 text-sm font-semibold leading-snug text-site-text dark:text-site-text hover:text-site-link"
                                >{{ item.item.name }}</a
                            >
                            <p
                                v-if="item.variation_label"
                                class="mt-0.5 text-xs text-site-muted"
                            >
                                {{ item.variation_label }}
                            </p>
                            <div
                                class="mt-0.5 flex flex-wrap items-baseline gap-1.5"
                            >
                                <template v-if="getItemSalePrice(item)">
                                    <span
                                        class="text-xs text-site-muted line-through"
                                        >{{
                                            getItemRegularPrice(item)
                                        }}
                                        Tk.</span
                                    >
                                    <span
                                        class="text-sm font-bold text-site-link"
                                        >{{ getItemSalePrice(item) }} Tk.</span
                                    >
                                </template>
                                <template v-else>
                                    <span
                                        class="text-sm font-bold text-site-link"
                                        >{{
                                            getItemRegularPrice(item)
                                        }}
                                        Tk.</span
                                    >
                                </template>
                            </div>

                            <div class="mt-2 flex items-center justify-between">
                                <div
                                    class="flex items-center overflow-hidden rounded-lg border border-site-divider dark:border-site-divider bg-site-surface-muted dark:bg-site-surface-muted"
                                >
                                    <button
                                        @click="
                                            cartStore.decreaseQuantity(
                                                item.item
                                            )
                                        "
                                        type="button"
                                        class="flex h-8 w-8 items-center justify-center text-site-muted dark:text-site-muted transition-colors hover:bg-site-primary-soft hover:text-site-link focus:outline-none active:bg-site-surface-muted dark:active:bg-site-surface-muted"
                                    >
                                        <i class="ri-subtract-line text-xs"></i>
                                    </button>
                                    <span
                                        class="w-8 text-center text-sm font-bold text-site-text dark:text-site-text"
                                        >{{ item.quantity }}</span
                                    >
                                    <button
                                        @click="
                                            cartStore.increaseQuantity(
                                                item.item
                                            )
                                        "
                                        type="button"
                                        class="flex h-8 w-8 items-center justify-center text-site-muted dark:text-site-muted transition-colors hover:bg-site-primary-soft hover:text-site-link focus:outline-none active:bg-site-surface-muted dark:active:bg-site-surface-muted"
                                    >
                                        <i class="ri-add-line text-xs"></i>
                                    </button>
                                </div>
                                <div class="text-right">
                                    <span
                                        v-if="getItemSalePrice(item)"
                                        class="block text-xs text-site-muted line-through"
                                    >
                                        {{
                                            getItemRegularPrice(item) *
                                            item.quantity
                                        }}
                                        Tk.
                                    </span>
                                    <span
                                        class="text-sm font-extrabold text-site-text dark:text-site-text"
                                        >{{
                                            getItemEffectivePrice(item) *
                                            item.quantity
                                        }}
                                        Tk.</span
                                    >
                                </div>
                                <button
                                    @click="cartStore.removeItem(item.item)"
                                    type="button"
                                    class="flex h-8 w-8 items-center justify-center rounded-lg text-site-muted transition-colors hover:bg-red-50 hover:text-red-500 focus:outline-none"
                                >
                                    <i class="ri-delete-bin-line"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Desktop row -->
                    <div
                        class="hidden grid-cols-12 items-center gap-4 p-4 sm:grid sm:p-5"
                    >
                        <div class="col-span-6 flex items-center gap-4">
                            <a
                                :href="`/shop/product/${item.item.id}/${item.item.slug}`"
                                class="flex h-20 w-20 shrink-0 overflow-hidden rounded-xl border border-site-divider dark:border-site-divider bg-site-surface-muted dark:bg-site-surface-muted"
                            >
                                <img
                                    :src="getItemImage(item)"
                                    :alt="item.item.name"
                                    @error="onItemImageError"
                                    class="h-full w-full object-contain p-1.5"
                                />
                            </a>
                            <div class="min-w-0">
                                <a
                                    :href="`/shop/product/${item.item.id}/${item.item.slug}`"
                                    class="line-clamp-2 text-sm font-semibold leading-snug text-site-text dark:text-site-text hover:text-site-link"
                                    >{{ item.item.name }}</a
                                >
                                <p
                                    v-if="item.variation_label"
                                    class="mt-0.5 text-xs text-site-muted"
                                >
                                    {{ item.variation_label }}
                                </p>
                                <button
                                    @click="cartStore.removeItem(item.item)"
                                    type="button"
                                    class="mt-1.5 flex items-center gap-1 text-xs text-site-muted transition-colors hover:text-red-500"
                                >
                                    <i class="ri-delete-bin-line"></i>
                                    {{ __('cart::site.cart.remove') }}
                                </button>
                            </div>
                        </div>
                        <div class="col-span-2 text-center text-sm font-medium">
                            <template v-if="getItemSalePrice(item)">
                                <span
                                    class="block text-xs text-site-muted line-through"
                                    >{{ getItemRegularPrice(item) }} Tk.</span
                                >
                                <span class="font-bold text-site-link"
                                    >{{ getItemSalePrice(item) }} Tk.</span
                                >
                            </template>
                            <template v-else>
                                <span class="text-site-muted dark:text-site-text"
                                    >{{ getItemRegularPrice(item) }} Tk.</span
                                >
                            </template>
                        </div>
                        <div class="col-span-2 flex justify-center">
                            <div
                                class="flex items-center overflow-hidden rounded-lg border border-site-divider dark:border-site-divider bg-site-surface-muted dark:bg-site-surface-muted"
                            >
                                <button
                                    @click="
                                        cartStore.decreaseQuantity(item.item)
                                    "
                                    type="button"
                                    class="flex h-9 w-9 items-center justify-center text-site-muted dark:text-site-muted transition-colors hover:bg-site-primary-soft hover:text-site-link focus:outline-none active:bg-site-surface-muted dark:active:bg-site-surface-muted"
                                >
                                    <i class="ri-subtract-line text-sm"></i>
                                </button>
                                <span
                                    class="w-9 text-center text-sm font-bold text-site-text dark:text-site-text"
                                    >{{ item.quantity }}</span
                                >
                                <button
                                    @click="
                                        cartStore.increaseQuantity(item.item)
                                    "
                                    type="button"
                                    class="flex h-9 w-9 items-center justify-center text-site-muted dark:text-site-muted transition-colors hover:bg-site-primary-soft hover:text-site-link focus:outline-none active:bg-site-surface-muted dark:active:bg-site-surface-muted"
                                >
                                    <i class="ri-add-line text-sm"></i>
                                </button>
                            </div>
                        </div>
                        <div
                            class="col-span-2 text-right text-base font-extrabold text-site-text dark:text-site-text"
                        >
                            <span
                                v-if="getItemSalePrice(item)"
                                class="block text-xs font-normal text-site-muted line-through"
                            >
                                {{
                                    getItemRegularPrice(item) * item.quantity
                                }}
                                Tk.
                            </span>
                            <span
                                >{{
                                    getItemEffectivePrice(item) * item.quantity
                                }}
                                Tk.</span
                            >
                        </div>
                    </div>
                </div>
            </div>

            <!-- Mobile: continue shopping -->
            <div class="mt-4 sm:hidden">
                <a
                    href="/shop"
                    class="inline-flex items-center gap-1.5 text-sm text-site-link hover:underline"
                >
                    <i class="ri-arrow-left-line"></i>
                    {{ __('cart::site.cart.continue_shopping') }}
                </a>
            </div>
        </div>

        <!-- ── Delivery Details + Order Summary ── -->
        <div class="flex flex-col lg:grid lg:grid-cols-12 lg:gap-8">
            <!-- Left: Delivery Form (below summary on mobile) -->
            <div class="order-2 lg:order-1 lg:col-span-8">
                <!-- General error banner -->
                <div
                    v-if="generalError"
                    class="mb-6 flex items-start gap-3 rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="mt-0.5 h-5 w-5 shrink-0 text-red-500"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                    >
                        <path
                            fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                            clip-rule="evenodd"
                        />
                    </svg>
                    <span>{{ generalError }}</span>
                </div>

                <!-- Delivery Details -->
                <div v-if="requiresShipping" class="space-y-4">
                    <div class="flex flex-col gap-4 md:grid md:grid-cols-2">
                        <!-- Shipping Options -->
                        <div
                            v-if="shippingOptions.length > 0"
                            class="col-span-2 space-y-3 pt-2"
                        >
                            <label
                                class="block text-sm font-medium text-site-text dark:text-site-text"
                            >
                                {{ __('cart::site.checkout.delivery_option') }}
                                <span class="text-red-500">*</span>
                            </label>
                            <div class="space-y-2">
                                <label
                                    v-for="option in shippingOptions"
                                    :key="option.id || option.name"
                                    :class="[
                                        'flex cursor-pointer items-center justify-between rounded-xl border-2 p-4 transition-all',
                                        selectedShippingOption?.name ===
                                        option.name
                                            ? 'border-site-focus bg-site-primary-soft'
                                            : 'border-site-divider bg-site-surface hover:border-site-divider'
                                    ]"
                                >
                                    <div class="flex items-center gap-3">
                                        <div
                                            :class="[
                                                'flex h-5 w-5 shrink-0 items-center justify-center rounded-full border-2',
                                                selectedShippingOption?.name ===
                                                option.name
                                                    ? 'border-site-focus bg-site-primary'
                                                    : 'border-site-divider'
                                            ]"
                                        >
                                            <div
                                                v-if="
                                                    selectedShippingOption?.name ===
                                                    option.name
                                                "
                                                class="h-2 w-2 rounded-full bg-site-surface dark:bg-site-surface-muted"
                                            ></div>
                                        </div>
                                        <span
                                            class="text-sm font-semibold text-site-text dark:text-site-text"
                                            >{{ option.name }}</span
                                        >
                                    </div>
                                    <span
                                        v-if="isFreeShipping"
                                        class="text-sm font-bold text-green-600"
                                        >{{ __('cart::site.cart.free') }}</span
                                    >
                                    <span
                                        v-else
                                        class="text-sm font-bold text-site-text dark:text-site-text"
                                        >{{ option.price }} Tk.</span
                                    >
                                    <input
                                        type="radio"
                                        :value="option"
                                        v-model="selectedShippingOption"
                                        class="sr-only"
                                    />
                                </label>
                            </div>
                        </div>

                        <!-- Name -->
                        <div>
                            <label
                                for="name"
                                class="mb-1.5 block text-sm font-medium text-site-text dark:text-site-text"
                            >
                                {{ __('cart::site.checkout.your_name') }}
                                <span class="text-red-500">*</span>
                            </label>
                            <input
                                v-model="form.name"
                                type="text"
                                id="name"
                                :class="inputClass('name')"
                                @input="clearError('name')"
                            />
                            <p
                                v-if="errors.name"
                                class="mt-1.5 text-xs text-red-600"
                            >
                                {{ errors.name }}
                            </p>
                        </div>

                        <!-- Phone -->
                        <div>
                            <label
                                for="phone"
                                class="mb-1.5 block text-sm font-medium text-site-text dark:text-site-text"
                            >
                                {{ __('cart::site.checkout.phone_number') }}
                                <span class="text-red-500">*</span>
                            </label>
                            <input
                                v-model="form.phone"
                                type="tel"
                                id="phone"
                                :class="inputClass('phone')"
                                @input="clearError('phone')"
                            />
                            <p
                                v-if="errors.phone"
                                class="mt-1.5 text-xs text-red-600"
                            >
                                {{ errors.phone }}
                            </p>
                        </div>

                        <!-- Street Address -->
                        <div class="col-span-2">
                            <label
                                for="address"
                                class="mb-1.5 block text-sm font-medium text-site-text dark:text-site-text"
                            >
                                {{ __('cart::site.checkout.street_address') }}
                                <span class="text-red-500">*</span>
                            </label>
                            <textarea
                                v-model="form.address"
                                id="address"
                                rows="3"
                                :placeholder="
                                    __(
                                        'cart::site.checkout.address_placeholder'
                                    )
                                "
                                :class="inputClass('address')"
                                @input="clearError('address')"
                            ></textarea>
                            <p
                                v-if="errors.address"
                                class="mt-1.5 text-xs text-red-600"
                            >
                                {{ errors.address }}
                            </p>
                        </div>

                        <!-- Saved Addresses -->
                        <div
                            v-if="addresses.length > 0"
                            class="col-span-2 space-y-2"
                        >
                            <label
                                class="mb-1.5 block text-sm font-medium text-site-text dark:text-site-text"
                            >
                                {{ __('cart::site.checkout.saved_addresses') }}
                            </label>
                            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                                <button
                                    v-for="addr in addresses"
                                    :key="addr.id"
                                    type="button"
                                    @click="selectAddress(addr)"
                                    :class="[
                                        'flex flex-col justify-between rounded-xl border-2 p-4 text-left transition-all',
                                        selectedAddressId === addr.id
                                            ? 'border-site-focus bg-site-primary-soft text-site-link'
                                            : 'border-site-divider bg-site-surface text-site-text hover:border-site-divider'
                                    ]"
                                >
                                    <div
                                        class="flex items-center gap-1.5 text-sm font-semibold"
                                    >
                                        <span
                                            v-if="addr.default"
                                            class="text-2xs rounded bg-site-primary-soft px-1.5 py-0.5 font-semibold text-site-link"
                                            >{{
                                                __(
                                                    'cart::site.checkout.default'
                                                )
                                            }}</span
                                        >
                                        <span class="truncate">{{
                                            addr.address
                                        }}</span>
                                    </div>
                                    <p class="mt-1 text-xs text-site-muted dark:text-site-muted">
                                        {{
                                            addr.union_name
                                                ? addr.union_name + ', '
                                                : ''
                                        }}
                                        {{
                                            addr.upazilla_name
                                                ? addr.upazilla_name + ', '
                                                : ''
                                        }}
                                        {{
                                            addr.district_name
                                                ? addr.district_name + ', '
                                                : ''
                                        }}
                                        {{ addr.division_name }}
                                    </p>
                                </button>
                                <button
                                    type="button"
                                    @click="selectCustomAddress"
                                    :class="[
                                        'flex min-h-[90px] flex-col items-center justify-center rounded-xl border-2 border-dashed p-4 text-center transition-all',
                                        selectedAddressId === 'new'
                                            ? 'border-site-focus bg-site-primary-soft text-site-link'
                                            : 'border-site-divider bg-site-surface text-site-muted hover:border-site-divider hover:bg-site-surface-muted'
                                    ]"
                                >
                                    <span class="text-lg font-bold">+</span>
                                    <span class="text-xs font-semibold">{{
                                        __(
                                            'cart::site.checkout.use_custom_address'
                                        )
                                    }}</span>
                                </button>
                            </div>
                        </div>

                        <!-- Special Note, Place Order, Trust badges (mobile: under Shipping Options) -->
                        <div class="col-span-2 mt-2 space-y-4 lg:hidden">
                            <div>
                                <label
                                    for="note-mobile"
                                    class="mb-1.5 block text-sm font-medium text-site-text dark:text-site-text"
                                    >{{
                                        __('cart::site.checkout.special_note')
                                    }}
                                </label>
                                <textarea
                                    v-model="form.note"
                                    id="note-mobile"
                                    rows="2"
                                    :placeholder="
                                        __(
                                            'cart::site.checkout.note_placeholder'
                                        )
                                    "
                                    class="block w-full rounded-lg border border-site-border dark:border-site-border bg-site-surface-muted dark:bg-site-surface-muted px-3 py-2 text-sm text-site-text dark:text-site-text focus:border-site-focus focus:outline-none focus:ring-1 focus:ring-site-focus"
                                ></textarea>
                            </div>

                            <button
                                @click="submitForm"
                                type="button"
                                :disabled="submitting"
                                class="flex w-full items-center justify-center gap-2 rounded-xl bg-site-primary py-3.5 text-sm font-bold text-site-on-primary shadow-sm transition-all hover:bg-site-primary-hover hover:shadow-md focus:outline-none focus:ring-2 focus:ring-site-focus focus:ring-offset-2 active:scale-[0.98] disabled:cursor-not-allowed disabled:opacity-60"
                            >
                                <svg
                                    v-if="submitting"
                                    class="h-4 w-4 animate-spin"
                                    xmlns="http://www.w3.org/2000/svg"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                >
                                    <circle
                                        class="opacity-25"
                                        cx="12"
                                        cy="12"
                                        r="10"
                                        stroke="currentColor"
                                        stroke-width="4"
                                    ></circle>
                                    <path
                                        class="opacity-75"
                                        fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"
                                    ></path>
                                </svg>
                                <i v-else class="ri-lock-line"></i>
                                {{
                                    submitting
                                        ? __('cart::site.cart.placing_order')
                                        : __('cart::site.cart.place_order')
                                }}
                            </button>

                            <div
                                class="flex items-center justify-center gap-3 text-xs text-site-muted"
                            >
                                <span class="flex items-center gap-1">
                                    <i class="ri-shield-check-line"></i>
                                    {{ __('cart::site.cart.secure') }}
                                </span>
                                <span class="text-site-text">|</span>
                                <span class="flex items-center gap-1">
                                    <i class="ri-refresh-line"></i>
                                    {{ __('cart::site.cart.easy_returns') }}
                                </span>
                                <span class="text-site-text">|</span>
                                <span class="flex items-center gap-1">
                                    <i class="ri-headphone-line"></i>
                                    {{ __('cart::site.cart.support') }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right: Order Summary (above form on mobile) -->
            <div class="order-1 mt-6 lg:order-2 lg:col-span-4 lg:mt-0">
                <div
                    class="sticky top-6 overflow-hidden rounded-2xl border border-site-divider dark:border-site-divider bg-site-surface dark:bg-site-surface-muted shadow-sm"
                >
                    <!-- Shipping progress -->
                    <div
                        v-if="freeShippingThreshold > 0 && shippingCharge > 0"
                        class="bg-amber-50 px-5 pb-3 pt-4"
                    >
                        <div
                            class="mb-1.5 flex items-center justify-between text-xs font-semibold text-amber-700"
                        >
                            <span
                                ><i class="ri-truck-line mr-1"></i
                                >{{
                                    __('cart::site.cart.free_shipping_progress')
                                }}</span
                            >
                            <span
                                >{{ cartStore.subtotal }} /
                                {{ freeShippingThreshold }} Tk.</span
                            >
                        </div>
                        <div
                            class="h-2 w-full overflow-hidden rounded-full bg-amber-100"
                        >
                            <div
                                class="h-full rounded-full bg-amber-400 transition-all duration-500"
                                :style="{
                                    width:
                                        Math.min(
                                            (cartStore.subtotal /
                                                freeShippingThreshold) *
                                                100,
                                            100
                                        ) + '%'
                                }"
                            ></div>
                        </div>
                        <p class="mt-1.5 text-xs text-amber-600">
                            {{ __('cart::site.cart.add') }}
                            <span class="font-bold"
                                >{{
                                    freeShippingThreshold - cartStore.subtotal
                                }}
                                Tk.</span
                            >
                            {{ __('cart::site.cart.more_to_unlock') }}
                            <span class="font-bold">{{
                                __('cart::site.cart.free_shipping')
                            }}</span>
                        </p>
                    </div>

                    <!-- Shipping unlocked -->
                    <div
                        v-else-if="
                            shippingCharge === 0 && cartStore.subtotal > 0
                        "
                        class="flex items-center gap-2 bg-green-50 px-5 py-3"
                    >
                        <i
                            class="ri-checkbox-circle-fill text-base text-green-500"
                        ></i>
                        <p class="text-xs font-semibold text-green-700">
                            {{
                                cartStore.coupon?.waives_shipping
                                    ? __('cart::site.cart.shipping_waived')
                                    : __(
                                          'cart::site.cart.free_shipping_unlocked'
                                      )
                            }}
                        </p>
                    </div>

                    <div class="p-5">
                        <h2
                            class="mb-4 text-xs font-bold uppercase tracking-widest text-site-muted"
                        >
                            {{ __('cart::site.cart.order_summary') }}
                        </h2>

                        <div class="space-y-3">
                            <div
                                class="flex items-center justify-between text-sm"
                            >
                                <span class="text-site-muted dark:text-site-muted"
                                    >{{ __('cart::site.cart.subtotal') }}
                                    <span class="text-xs text-site-muted"
                                        >({{
                                            __('cart::site.cart.items_label', {
                                                count: cartStore.totalQuantity
                                            })
                                        }})</span
                                    ></span
                                >
                                <span class="font-semibold text-site-text dark:text-site-text"
                                    >{{ cartStore.subtotal }} Tk.</span
                                >
                            </div>
                            <div
                                class="flex items-center justify-between text-sm"
                            >
                                <span class="text-site-muted dark:text-site-muted">{{
                                    __('cart::site.cart.shipping')
                                }}</span>
                                <span
                                    v-if="shippingCharge === 0"
                                    class="font-semibold text-green-600"
                                    >{{ __('cart::site.cart.free') }}</span
                                >
                                <span v-else class="font-semibold text-site-text dark:text-site-text"
                                    >{{ shippingCharge }} Tk.</span
                                >
                            </div>
                            <div
                                v-if="cartStore.discount > 0"
                                class="flex items-center justify-between text-sm"
                            >
                                <span class="text-site-muted dark:text-site-muted"
                                    >{{ __('cart::site.cart.discount') }}
                                    <span
                                        v-if="cartStore.coupon"
                                        class="text-xs font-semibold text-green-600"
                                        >({{ cartStore.coupon.code }})</span
                                    ></span
                                >
                                <span class="font-semibold text-green-600"
                                    >-{{ cartStore.discount }} Tk.</span
                                >
                            </div>
                        </div>

                        <!-- Promo code -->
                        <div class="mt-4">
                            <div v-if="!cartStore.coupon">
                                <label
                                    for="promo-code"
                                    class="mb-1.5 block text-sm font-medium text-site-text dark:text-site-text"
                                    >{{ __('cart::site.checkout.promo_code') }}
                                </label>
                                <div class="flex gap-2">
                                    <input
                                        id="promo-code"
                                        v-model="promoInput"
                                        type="text"
                                        :placeholder="
                                            __(
                                                'cart::site.checkout.promo_placeholder'
                                            )
                                        "
                                        class="block w-full rounded-lg border border-site-border dark:border-site-border bg-site-surface-muted dark:bg-site-surface-muted px-3 py-2.5 text-sm uppercase text-site-text dark:text-site-text focus:border-site-focus focus:outline-none focus:ring-1 focus:ring-site-focus"
                                        @keyup.enter="applyPromo"
                                    />
                                    <button
                                        type="button"
                                        @click="applyPromo"
                                        :disabled="
                                            applyingPromo || !promoInput.trim()
                                        "
                                        class="shrink-0 rounded-lg bg-site-primary px-4 py-2.5 text-sm font-bold text-site-on-primary transition-colors hover:bg-site-primary-hover disabled:cursor-not-allowed disabled:opacity-60"
                                    >
                                        {{
                                            applyingPromo
                                                ? '...'
                                                : __(
                                                      'cart::site.checkout.apply'
                                                  )
                                        }}
                                    </button>
                                </div>
                                <p
                                    v-if="promoError"
                                    class="mt-1.5 text-xs font-medium text-red-500"
                                >
                                    {{ promoError }}
                                </p>
                            </div>

                            <div
                                v-else
                                class="flex items-center justify-between rounded-xl bg-green-50 px-3.5 py-3 ring-1 ring-green-100"
                            >
                                <span
                                    class="flex items-center gap-1.5 text-sm font-bold text-green-700"
                                >
                                    <i class="ri-price-tag-3-line"></i
                                    >{{ cartStore.coupon.code }}
                                    <span
                                        class="text-xs font-semibold text-green-600"
                                        >{{
                                            __(
                                                'cart::site.checkout.promo_applied'
                                            )
                                        }}</span
                                    >
                                </span>
                                <button
                                    type="button"
                                    @click="removePromo"
                                    :disabled="removingPromo"
                                    class="text-xs font-bold text-red-500 transition-colors hover:text-red-600 disabled:opacity-60"
                                >
                                    {{ __('cart::site.checkout.remove') }}
                                </button>
                            </div>
                        </div>

                        <div class="my-4 h-px bg-site-surface-muted dark:bg-site-surface-muted"></div>

                        <div class="flex items-center justify-between">
                            <span class="text-sm font-bold text-site-text dark:text-site-text">{{
                                __('cart::site.cart.total')
                            }}</span>
                            <div class="text-right">
                                <span
                                    class="text-2xl font-extrabold text-site-link"
                                    >{{ orderTotal }}</span
                                >
                                <span
                                    class="ml-1 text-sm font-bold text-site-link"
                                    >Tk.</span
                                >
                            </div>
                        </div>

                        <!-- Special Note, Place Order, Trust badges (desktop only) -->
                        <div class="hidden lg:block">
                            <div class="mt-4">
                                <label
                                    for="note"
                                    class="mb-1.5 block text-sm font-medium text-site-text dark:text-site-text"
                                    >{{
                                        __('cart::site.checkout.special_note')
                                    }}
                                </label>
                                <textarea
                                    v-model="form.note"
                                    id="note"
                                    rows="2"
                                    :placeholder="
                                        __(
                                            'cart::site.checkout.note_placeholder'
                                        )
                                    "
                                    class="block w-full rounded-lg border border-site-border dark:border-site-border bg-site-surface-muted dark:bg-site-surface-muted px-3 py-2 text-sm text-site-text dark:text-site-text focus:border-site-focus focus:outline-none focus:ring-1 focus:ring-site-focus"
                                ></textarea>
                            </div>

                            <button
                                @click="submitForm"
                                type="button"
                                :disabled="submitting"
                                class="mt-4 flex w-full items-center justify-center gap-2 rounded-xl bg-site-primary py-3.5 text-sm font-bold text-site-on-primary shadow-sm transition-all hover:bg-site-primary-hover hover:shadow-md focus:outline-none focus:ring-2 focus:ring-site-focus focus:ring-offset-2 active:scale-[0.98] disabled:cursor-not-allowed disabled:opacity-60"
                            >
                                <svg
                                    v-if="submitting"
                                    class="h-4 w-4 animate-spin"
                                    xmlns="http://www.w3.org/2000/svg"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                >
                                    <circle
                                        class="opacity-25"
                                        cx="12"
                                        cy="12"
                                        r="10"
                                        stroke="currentColor"
                                        stroke-width="4"
                                    ></circle>
                                    <path
                                        class="opacity-75"
                                        fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"
                                    ></path>
                                </svg>
                                <i v-else class="ri-lock-line"></i>
                                {{
                                    submitting
                                        ? __('cart::site.cart.placing_order')
                                        : __('cart::site.cart.place_order')
                                }}
                            </button>

                            <div
                                class="mt-4 flex items-center justify-center gap-3 text-xs text-site-muted"
                            >
                                <span class="flex items-center gap-1">
                                    <i class="ri-shield-check-line"></i>
                                    {{ __('cart::site.cart.secure') }}
                                </span>
                                <span class="text-site-text">|</span>
                                <span class="flex items-center gap-1">
                                    <i class="ri-refresh-line"></i>
                                    {{ __('cart::site.cart.easy_returns') }}
                                </span>
                                <span class="text-site-text">|</span>
                                <span class="flex items-center gap-1">
                                    <i class="ri-headphone-line"></i>
                                    {{ __('cart::site.cart.support') }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Empty cart -->
    <div
        v-else
        class="flex flex-col items-center justify-center py-24 text-center"
    >
        <div
            class="mb-6 flex h-28 w-28 items-center justify-center rounded-full bg-site-surface-muted dark:bg-site-surface-muted"
        >
            <i class="ri-shopping-cart-2-line text-5xl text-site-text"></i>
        </div>
        <h2 class="mb-2 text-2xl font-bold text-site-text dark:text-site-text">
            {{ __('cart::site.cart.empty_heading') }}
        </h2>
        <p class="mb-8 max-w-xs text-sm text-site-muted dark:text-site-muted">
            {{ __('cart::site.cart.empty_body') }}
        </p>
        <a
            href="/shop"
            class="inline-flex items-center gap-2 rounded-xl bg-site-primary px-6 py-3 text-sm font-semibold text-site-on-primary shadow-sm transition-colors hover:bg-site-primary-hover"
        >
            <i class="ri-store-2-line"></i>
            {{ __('cart::site.cart.start_shopping') }}
        </a>
    </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted, watch, inject } from 'vue'
import { useCartStore } from '../Stores/CartStore'
import { pushViewCart, pushBeginCheckout } from '../analytics/datalayer'
import axios from 'axios'

const cartStore = useCartStore()
const translate = inject('translate')

function getItemRegularPrice(item) {
    if (item.regular_price !== undefined && item.regular_price !== null) {
        return Number(item.regular_price)
    }
    return Number(item.item?.price || 0)
}

function getItemSalePrice(item) {
    const reg = getItemRegularPrice(item)
    if (
        item.sale_price !== undefined &&
        item.sale_price !== null &&
        Number(item.sale_price) > 0 &&
        Number(item.sale_price) < reg
    ) {
        return Number(item.sale_price)
    }
    if (
        item.item?.sale_price &&
        Number(item.item.sale_price) > 0 &&
        Number(item.item.sale_price) < reg
    ) {
        return Number(item.item.sale_price)
    }
    return null
}

function getItemEffectivePrice(item) {
    const sale = getItemSalePrice(item)
    if (sale !== null) return sale
    if (item.unit_price !== undefined && item.unit_price !== null) {
        return Number(item.unit_price)
    }
    return getItemRegularPrice(item)
}

function fallbackLogo() {
    return document.querySelector('header img')?.src || '/logo.png'
}

function getItemImage(item) {
    return item.item?.image_url || fallbackLogo()
}

function onItemImageError(event) {
    const img = event.target
    if (img.dataset.fallbackApplied) return
    img.dataset.fallbackApplied = '1'
    img.src = fallbackLogo()
}

const props = defineProps({
    shippingOptions: {
        type: Array,
        default: () => []
    },
    freeShippingThreshold: {
        type: Number,
        default: 1000
    },
    requiresShipping: {
        type: Boolean,
        default: true
    },
    customer: {
        type: Object,
        default: () => null
    },
    addresses: {
        type: Array,
        default: () => []
    }
})

const selectedShippingOption = ref(props.shippingOptions[0] || null)

const isFreeShipping = computed(() => {
    if (cartStore.coupon?.waives_shipping) return true
    return (
        props.freeShippingThreshold > 0 &&
        cartStore.subtotal >= props.freeShippingThreshold
    )
})

const shippingCharge = computed(() => {
    if (!selectedShippingOption.value) return 0
    if (isFreeShipping.value) return 0
    return cartStore.subtotal > 0
        ? Number(selectedShippingOption.value.price || 0)
        : 0
})

const orderTotal = computed(
    () =>
        cartStore.subtotal +
        shippingCharge.value +
        cartStore.tax -
        cartStore.discount
)

// ── Promo code ──
const promoInput = ref('')
const applyingPromo = ref(false)
const removingPromo = ref(false)
const promoError = ref('')

async function applyPromo() {
    const code = promoInput.value.trim()
    if (!code || applyingPromo.value) return

    applyingPromo.value = true
    promoError.value = ''

    try {
        await cartStore.applyCoupon(code)
        promoInput.value = ''
    } catch (error) {
        const serverErrors = error.response?.data?.errors
        const message = serverErrors?.coupon_code
            ? Array.isArray(serverErrors.coupon_code)
                ? serverErrors.coupon_code[0]
                : serverErrors.coupon_code
            : error.response?.data?.message
        promoError.value =
            message || translate('cart::site.checkout.promo_error')
    } finally {
        applyingPromo.value = false
    }
}

async function removePromo() {
    if (removingPromo.value) return

    removingPromo.value = true
    await cartStore.removeCoupon()
    removingPromo.value = false
}

// ── Analytics ──
const trackViewCart = () => {
    if (cartStore.items && cartStore.items.length > 0) {
        pushViewCart(cartStore.items, cartStore.subtotal)
    }
}

onMounted(() => {
    if (cartStore.loaded) {
        trackViewCart()
    } else {
        const unwatch = watch(
            () => cartStore.loaded,
            (loaded) => {
                if (loaded) {
                    trackViewCart()
                    unwatch()
                }
            }
        )
    }
})

// ── Delivery form ──
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
    payment_method: 'cod'
})

const selectedAddressId = ref(null)

const submitting = ref(false)
const errors = reactive({})
const generalError = ref('')

onMounted(async () => {
    if (props.customer) {
        form.name = props.customer.name || ''
        form.email = props.customer.email || ''
        form.phone = props.customer.phone || ''
    }

    if (
        props.requiresShipping &&
        props.addresses &&
        props.addresses.length > 0
    ) {
        const defaultAddr =
            props.addresses.find((a) => a.default) || props.addresses[0]
        selectAddress(defaultAddr)
    } else if (props.requiresShipping) {
        selectedAddressId.value = 'new'
    }

    pushBeginCheckout(cartStore.items, orderTotal.value)
})

function selectCustomAddress() {
    selectedAddressId.value = 'new'
    form.division = ''
    form.district = ''
    form.upazila = ''
    form.union = ''
    form.address = ''
}

function selectAddress(addr) {
    selectedAddressId.value = addr.id
    form.division = addr.division_name || ''
    form.district = addr.district_name || ''
    form.upazila = addr.upazilla_name || ''
    form.union = addr.union_name || ''
    form.address = addr.address || ''
}

const baseInputClass =
    'block w-full rounded-lg border px-3 py-2.5 text-sm text-site-text focus:outline-none focus:ring-1 transition'
const validInputClass = `${baseInputClass} border-gray-300 bg-gray-50 focus:border-primary-500 focus:ring-primary-500`
const errorInputClass = `${baseInputClass} border-red-400 bg-red-50 focus:border-red-500 focus:ring-red-400`

function inputClass(field) {
    return errors[field] ? errorInputClass : validInputClass
}

function clearError(field) {
    delete errors[field]
    generalError.value = ''
}

function validate() {
    Object.keys(errors).forEach((k) => delete errors[k])
    generalError.value = ''

    let valid = true

    if (!form.name.trim()) {
        errors.name = translate('cart::site.checkout.err_name')
        valid = false
    }

    if (!form.phone.trim()) {
        errors.phone = translate('cart::site.checkout.err_phone')
        valid = false
    }

    if (form.email && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(form.email)) {
        errors.email = translate('cart::site.checkout.err_email')
        valid = false
    }

    if (props.requiresShipping && !form.address.trim()) {
        errors.address = translate('cart::site.checkout.err_address')
        valid = false
    }

    if (!form.payment_method) {
        errors.payment_method = translate('cart::site.checkout.err_payment')
        valid = false
    }

    return valid
}

async function submitForm() {
    if (!validate()) {
        const firstError = document.querySelector('.border-red-400')
        if (firstError) {
            firstError.scrollIntoView({ behavior: 'smooth', block: 'center' })
        }
        return
    }

    submitting.value = true

    try {
        const items = cartStore.items.map((cartItem) => ({
            ...cartItem,
            variation_label: cartItem.variation_label || null
        }))

        const payload = {
            ...form,
            selected_address_id: selectedAddressId.value || null,
            division_id: null,
            district_id: null,
            upazila_id: null,
            union_id: null,
            items: items,
            subtotal: cartStore.subtotal,
            tax: cartStore.tax,
            shipping: shippingCharge.value,
            shipping_method: selectedShippingOption.value?.name || null,
            coupon_code: cartStore.coupon?.code ?? null,
            total: orderTotal.value,
            paid: 0,
            due: orderTotal.value
        }

        const response = await axios.post('/site-order-store', payload)

        await cartStore.clearCart()

        window.location.href = '/order-confirm/' + response.data.order_id
    } catch (error) {
        console.error('Checkout error response:', error.response?.data)
        if (error.response?.status === 422) {
            const serverErrors = error.response.data.errors ?? {}
            Object.keys(serverErrors).forEach((field) => {
                errors[field] = Array.isArray(serverErrors[field])
                    ? serverErrors[field][0]
                    : serverErrors[field]
            })
            generalError.value = translate('cart::site.checkout.fix_errors')
            const firstError = document.querySelector('.border-red-400')
            if (firstError) {
                firstError.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                })
            }
        } else {
            generalError.value =
                error.response?.data?.message ??
                translate('cart::site.checkout.place_order_failed')
        }
    } finally {
        submitting.value = false
    }
}
</script>
