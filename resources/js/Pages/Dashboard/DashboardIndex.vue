<template>
    <Head title="Dashboard"></Head>
    <div
        class="shadow-xs mt-6 flex flex-col justify-between rounded-md border border-skin-neutral-4 bg-skin-neutral-2 px-4 py-2 md:flex-row"
    >
        <div>
            <i class="ri-megaphone-line"></i>
            Welcome
            <span class="font-bold">{{ $page.props.auth.user.name }}</span> !
        </div>
        <div>
            <i class="ri-calendar-2-line mr-2"></i>
            {{ $page.props.datetime.now }}
        </div>
    </div>

    <div class="my-6 grid grid-cols-1 gap-6 md:grid-cols-4">
        <!-- Product Count Card -->
        <div
            class="shadow-xs flex items-center rounded-lg border border-skin-neutral-4 bg-skin-neutral-2 p-4 hover:cursor-pointer hover:bg-skin-neutral-1"
            @click="$inertia.visit(route('product.index'))"
            v-if="can('product-list')"
        >
            <div
                class="mr-4 rounded-full bg-orange-100 px-3 py-2 text-orange-500 dark:bg-orange-500 dark:text-orange-100"
            >
                <i class="ri-user-fill text-2xl"></i>
            </div>
            <div>
                <p
                    class="mb-2 text-sm font-medium text-gray-600 dark:text-gray-400"
                >
                    Total Products
                </p>
                <p
                    class="text-lg font-semibold text-gray-700 dark:text-gray-200"
                >
                    {{ props.count['totalProducts'] }}
                </p>
            </div>
        </div>

        <!-- Active Product Count Card -->
        <div
            class="shadow-xs flex items-center rounded-lg border border-skin-neutral-4 bg-skin-neutral-2 p-4 hover:cursor-pointer hover:bg-skin-neutral-1"
            @click="$inertia.visit(route('product.index'))"
            v-if="can('product-list')"
        >
            <div
                class="mr-4 rounded-full bg-green-100 px-3 py-2 text-green-500 dark:bg-green-500 dark:text-green-100"
            >
                <i class="ri-organization-chart text-2xl"></i>
            </div>
            <div>
                <p
                    class="mb-2 text-sm font-medium text-gray-600 dark:text-gray-400"
                >
                    Active Products
                </p>
                <p
                    class="text-lg font-semibold text-gray-700 dark:text-gray-200"
                >
                    {{ props.count['activeProducts'] }}
                </p>
            </div>
        </div>

        <!-- Inactive Product Count Card -->
        <div
            class="shadow-xs flex items-center rounded-lg border border-skin-neutral-4 bg-skin-neutral-2 p-4 hover:cursor-pointer hover:bg-skin-neutral-1"
            @click="$inertia.visit(route('product.index'))"
            v-if="can('product-list')"
        >
            <div
                class="mr-4 rounded-full bg-teal-100 px-3 py-2 text-teal-500 dark:bg-teal-500 dark:text-teal-100"
            >
                <i class="ri-road-map-fill text-2xl"></i>
            </div>
            <div>
                <p
                    class="mb-2 text-sm font-medium text-gray-600 dark:text-gray-400"
                >
                    Inactive Products
                </p>
                <p
                    class="text-lg font-semibold text-gray-700 dark:text-gray-200"
                >
                    {{ props.count['inactiveProducts'] }}
                </p>
            </div>
        </div>

        <!-- Featured Products Count Card -->
        <div
            class="shadow-xs flex items-center rounded-lg border border-skin-neutral-4 bg-skin-neutral-2 p-4 hover:cursor-pointer hover:bg-skin-neutral-1"
            @click="$inertia.visit(route('product.index'))"
            v-if="can('product-list')"
        >
            <div
                class="mr-4 rounded-full bg-blue-100 px-3 py-2 text-blue-500 dark:bg-blue-500 dark:text-blue-100"
            >
                <i class="ri-building-fill text-2xl"></i>
            </div>
            <div>
                <p
                    class="mb-2 text-sm font-medium text-gray-600 dark:text-gray-400"
                >
                    Featured Products
                </p>
                <p
                    class="text-lg font-semibold text-gray-700 dark:text-gray-200"
                >
                    {{ props.count['featuredProducts'] }}
                </p>
            </div>
        </div>

        <!-- Product Category Count Card -->
        <div
            class="shadow-xs flex items-center rounded-lg border border-skin-neutral-4 bg-skin-neutral-2 p-4 hover:cursor-pointer hover:bg-skin-neutral-1"
            @click="$inertia.visit(route('productCategory.index'))"
            v-if="can('product-category-list')"
        >
            <div
                class="mr-4 rounded-full bg-orange-100 px-3 py-2 text-orange-500 dark:bg-orange-500 dark:text-orange-100"
            >
                <i class="ri-user-fill text-2xl"></i>
            </div>
            <div>
                <p
                    class="mb-2 text-sm font-medium text-gray-600 dark:text-gray-400"
                >
                    Total Product Categories
                </p>
                <p
                    class="text-lg font-semibold text-gray-700 dark:text-gray-200"
                >
                    {{ props.count['totalProductCategories'] }}
                </p>
            </div>
        </div>

        <!-- Active Product Category Count Card -->
        <div
            class="shadow-xs flex items-center rounded-lg border border-skin-neutral-4 bg-skin-neutral-2 p-4 hover:cursor-pointer hover:bg-skin-neutral-1"
            @click="$inertia.visit(route('productCategory.index'))"
            v-if="can('product-category-list')"
        >
            <div
                class="mr-4 rounded-full bg-green-100 px-3 py-2 text-green-500 dark:bg-green-500 dark:text-green-100"
            >
                <i class="ri-organization-chart text-2xl"></i>
            </div>
            <div>
                <p
                    class="mb-2 text-sm font-medium text-gray-600 dark:text-gray-400"
                >
                    Active Product Categories
                </p>
                <p
                    class="text-lg font-semibold text-gray-700 dark:text-gray-200"
                >
                    {{ props.count['activeProductCategories'] }}
                </p>
            </div>
        </div>

        <!-- Inactive Product Category Count Card -->
        <div
            class="shadow-xs flex items-center rounded-lg border border-skin-neutral-4 bg-skin-neutral-2 p-4 hover:cursor-pointer hover:bg-skin-neutral-1"
            @click="$inertia.visit(route('productCategory.index'))"
            v-if="can('product-category-list')"
        >
            <div
                class="mr-4 rounded-full bg-teal-100 px-3 py-2 text-teal-500 dark:bg-teal-500 dark:text-teal-100"
            >
                <i class="ri-road-map-fill text-2xl"></i>
            </div>
            <div>
                <p
                    class="mb-2 text-sm font-medium text-gray-600 dark:text-gray-400"
                >
                    Inactive Product Categories
                </p>
                <p
                    class="text-lg font-semibold text-gray-700 dark:text-gray-200"
                >
                    {{ props.count['inactiveProductCategories'] }}
                </p>
            </div>
        </div>

        <!-- Featured Products Count Card -->
        <div
            class="shadow-xs flex items-center rounded-lg border border-skin-neutral-4 bg-skin-neutral-2 p-4 hover:cursor-pointer hover:bg-skin-neutral-1"
            @click="$inertia.visit(route('productCategory.index'))"
            v-if="can('product-category-list')"
        >
            <div
                class="mr-4 rounded-full bg-blue-100 px-3 py-2 text-blue-500 dark:bg-blue-500 dark:text-blue-100"
            >
                <i class="ri-building-fill text-2xl"></i>
            </div>
            <div>
                <p
                    class="mb-2 text-sm font-medium text-gray-600 dark:text-gray-400"
                >
                    Featured Product Categories
                </p>
                <p
                    class="text-lg font-semibold text-gray-700 dark:text-gray-200"
                >
                    {{ props.count['featuredProductCategories'] }}
                </p>
            </div>
        </div>

        <!-- Product Tags Count Card -->
        <div
            class="shadow-xs flex items-center rounded-lg border border-skin-neutral-4 bg-skin-neutral-2 p-4 hover:cursor-pointer hover:bg-skin-neutral-1"
            @click="$inertia.visit(route('productTag.index'))"
            v-if="can('product-tag-list')"
        >
            <div
                class="mr-4 rounded-full bg-blue-100 px-3 py-2 text-blue-500 dark:bg-blue-500 dark:text-blue-100"
            >
                <i class="ri-user-2-fill text-2xl"></i>
            </div>
            <div>
                <p
                    class="mb-2 text-sm font-medium text-gray-600 dark:text-gray-400"
                >
                    Total Product Tags
                </p>
                <p
                    class="text-lg font-semibold text-gray-700 dark:text-gray-200"
                >
                    {{ props.count['totalProductTags'] }}
                </p>
            </div>
        </div>

        <!-- Product Brand Count Card -->
        <div
            class="shadow-xs flex items-center rounded-lg border border-skin-neutral-4 bg-skin-neutral-2 p-4 hover:cursor-pointer hover:bg-skin-neutral-1"
            @click="$inertia.visit(route('productBrand.index'))"
            v-if="can('product-brand-list')"
        >
            <div
                class="mr-4 rounded-full bg-blue-100 px-3 py-2 text-blue-500 dark:bg-blue-500 dark:text-blue-100"
            >
                <i class="ri-user-2-fill text-2xl"></i>
            </div>
            <div>
                <p
                    class="mb-2 text-sm font-medium text-gray-600 dark:text-gray-400"
                >
                    Total Product Brands
                </p>
                <p
                    class="text-lg font-semibold text-gray-700 dark:text-gray-200"
                >
                    {{ props.count['totalProductBrands'] }}
                </p>
            </div>
        </div>

        <!-- User Count Card -->
        <div
            class="shadow-xs flex items-center rounded-lg border border-skin-neutral-4 bg-skin-neutral-2 p-4 hover:cursor-pointer hover:bg-skin-neutral-1"
            @click="$inertia.visit(route('user.index'))"
            v-if="can('Acl')"
        >
            <div
                class="mr-4 rounded-full bg-green-100 px-3 py-2 text-green-500 dark:bg-green-500 dark:text-green-100"
            >
                <i class="ri-user-fill text-2xl"></i>
            </div>
            <div>
                <p
                    class="mb-2 text-sm font-medium text-gray-600 dark:text-gray-400"
                >
                    Users
                </p>
                <p
                    class="text-lg font-semibold text-gray-700 dark:text-gray-200"
                >
                    {{ props.count['users'] }}
                </p>
            </div>
        </div>

        <!-- Role Count Card -->
        <div
            class="shadow-xs flex items-center rounded-lg border border-skin-neutral-4 bg-skin-neutral-2 p-4 hover:cursor-pointer hover:bg-skin-neutral-1"
            @click="$inertia.visit(route('aclRole.index'))"
            v-if="can('Acl')"
        >
            <div
                class="mr-4 rounded-full bg-blue-100 px-3 py-2 text-blue-500 dark:bg-blue-500 dark:text-blue-100"
            >
                <i class="ri-user-settings-line text-2xl"></i>
            </div>
            <div>
                <p
                    class="mb-2 text-sm font-medium text-gray-600 dark:text-gray-400"
                >
                    Roles
                </p>
                <p
                    class="text-lg font-semibold text-gray-700 dark:text-gray-200"
                >
                    {{ props.count['roles'] }}
                </p>
            </div>
        </div>

        <!-- Permission Count Card -->
        <div
            class="shadow-xs flex items-center rounded-lg border border-skin-neutral-4 bg-skin-neutral-2 p-4 hover:cursor-pointer hover:bg-skin-neutral-1"
            @click="$inertia.visit(route('aclPermission.index'))"
            v-if="can('Acl')"
        >
            <div
                class="mr-4 rounded-full bg-orange-100 px-3 py-2 text-orange-500 dark:bg-orange-500 dark:text-orange-100"
            >
                <i class="ri-key-fill text-2xl"></i>
            </div>
            <div>
                <p
                    class="mb-2 text-sm font-medium text-gray-600 dark:text-gray-400"
                >
                    Permissions
                </p>
                <p
                    class="text-lg font-semibold text-gray-700 dark:text-gray-200"
                >
                    {{ props.count['permissions'] }}
                </p>
            </div>
        </div>
    </div>

    <!-- <AppButton
        v-if="can('Attendance - Self Create')"
        class="btn btn-primary"
        @click="$inertia.visit(route('attendance.selfCreate'))"
    >
        <i class="ri-fingerprint-fill mr-1"></i>
        Punch
    </AppButton> -->
</template>

<script setup>
import { ref } from 'vue'
import { Head } from '@inertiajs/vue3'
import useAuthCan from '@/Composables/useAuthCan'

const { can } = useAuthCan()

const props = defineProps({
    count: {
        type: Object
    }
})
</script>
