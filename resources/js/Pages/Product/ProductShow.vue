<template>
    <Head :title="product.name"></Head>
    <AppSectionHeader :title="product.name" :bread-crumb="breadCrumb">
        <template #right>
            <div class="flex gap-2">
                <AppButton
                    v-if="can('product-edit')"
                    class="btn btn-primary"
                    @click="$inertia.visit(route('product.edit', product.id))"
                >
                    <i class="ri-edit-line mr-1"></i> Edit Product
                </AppButton>
                <AppButton
                    class="btn btn-secondary"
                    @click="$inertia.visit(route('product.index'))"
                >
                    <i class="ri-arrow-left-line mr-1"></i> Back to List
                </AppButton>
            </div>
        </template>
    </AppSectionHeader>

    <div class="grid grid-cols-1 gap-5 xl:grid-cols-3">
        <!-- Left / Main column -->
        <div class="xl:col-span-2 space-y-5">

            <!-- Basic Info Card -->
            <AppCard>
                <template #title>
                    <div class="flex items-center gap-2">
                        <i class="ri-information-line text-skin-primary-9"></i>
                        Product Information
                    </div>
                </template>
                <template #content>
                    <div class="space-y-4">
                        <!-- Type Badge -->
                        <div class="flex items-center gap-2">
                            <span
                                class="rounded-md px-2.5 py-1 text-xs font-semibold uppercase"
                                :class="typeBadgeClass(product.type)"
                            >
                                {{ product.type }}
                            </span>
                            <span v-if="product.active" class="rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-700">Active</span>
                            <span v-else class="rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-medium text-red-600">Inactive</span>
                            <span v-if="product.featured" class="rounded-full bg-yellow-100 px-2.5 py-0.5 text-xs font-medium text-yellow-700">Featured</span>
                            <span v-if="product.is_virtual" class="rounded-full bg-purple-100 px-2.5 py-0.5 text-xs font-medium text-purple-700">Virtual</span>
                            <span v-if="product.is_downloadable" class="rounded-full bg-cyan-100 px-2.5 py-0.5 text-xs font-medium text-cyan-700">Downloadable</span>
                        </div>

                        <!-- Summary -->
                        <div v-if="product.summary">
                            <p class="text-sm text-skin-neutral-9">Summary</p>
                            <p class="text-sm text-skin-neutral-12">{{ product.summary }}</p>
                        </div>

                        <!-- Pricing (simple) -->
                        <div v-if="product.type === 'simple'" class="grid grid-cols-3 gap-4">
                            <div>
                                <p class="text-xs text-skin-neutral-9">Price</p>
                                <p class="text-lg font-bold text-skin-neutral-12">৳{{ Number(product.price).toFixed(2) }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-skin-neutral-9">Sale Price</p>
                                <p v-if="product.sale_price" class="text-lg font-bold text-green-700">৳{{ Number(product.sale_price).toFixed(2) }}</p>
                                <p v-else class="text-sm text-skin-neutral-9">—</p>
                            </div>
                            <div>
                                <p class="text-xs text-skin-neutral-9">Stock</p>
                                <p class="text-lg font-bold" :class="product.quantity <= 0 ? 'text-red-600' : product.quantity < 10 ? 'text-yellow-600' : 'text-green-600'">
                                    {{ product.quantity }}
                                </p>
                            </div>
                        </div>

                        <!-- Pricing (variable) -->
                        <div v-if="product.type === 'variable'" class="grid grid-cols-3 gap-4">
                            <div>
                                <p class="text-xs text-skin-neutral-9">Price Range</p>
                                <p v-if="variations.length" class="text-lg font-bold text-skin-neutral-12">
                                    ৳{{ priceRange.min }} – ৳{{ priceRange.max }}
                                </p>
                                <p v-else class="text-sm text-skin-neutral-9 italic">No variations</p>
                            </div>
                            <div>
                                <p class="text-xs text-skin-neutral-9">Total Variations</p>
                                <p class="text-lg font-bold text-skin-neutral-12">{{ variations.length }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-skin-neutral-9">In Stock Variations</p>
                                <p class="text-lg font-bold" :class="inStockCount > 0 ? 'text-green-600' : 'text-red-600'">
                                    {{ inStockCount }}
                                </p>
                            </div>
                        </div>

                        <!-- Pricing (bundle) -->
                        <div v-if="product.type === 'bundle'" class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-xs text-skin-neutral-9">Price</p>
                                <p class="text-lg font-bold text-skin-neutral-12">৳{{ Number(product.price).toFixed(2) }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-skin-neutral-9">Sale Price</p>
                                <p v-if="product.sale_price" class="text-lg font-bold text-green-700">৳{{ Number(product.sale_price).toFixed(2) }}</p>
                                <p v-else class="text-sm text-skin-neutral-9">—</p>
                            </div>
                        </div>

                        <!-- Meta -->
                        <div class="grid grid-cols-2 gap-4 border-t border-skin-neutral-4 pt-4">
                            <div>
                                <p class="text-xs text-skin-neutral-9">Category</p>
                                <p class="text-sm font-medium text-skin-neutral-12">{{ product.category?.name ?? '—' }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-skin-neutral-9">Brand</p>
                                <p class="text-sm font-medium text-skin-neutral-12">{{ product.brand?.name ?? '—' }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-skin-neutral-9">Unit</p>
                                <p class="text-sm font-medium text-skin-neutral-12">{{ product.unit ?? '—' }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-skin-neutral-9">Min Order</p>
                                <p class="text-sm font-medium text-skin-neutral-12">{{ product.min_order ?? '—' }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-skin-neutral-9">SKU</p>
                                <p class="text-sm font-medium text-skin-neutral-12">{{ product.sku ?? '—' }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-skin-neutral-9">Slug</p>
                                <p class="text-sm font-medium text-skin-neutral-12">{{ product.slug }}</p>
                            </div>
                        </div>
                    </div>
                </template>
            </AppCard>

            <!-- Description Card -->
            <AppCard v-if="product.description">
                <template #title>
                    <div class="flex items-center gap-2">
                        <i class="ri-file-text-line text-skin-primary-9"></i>
                        Description
                    </div>
                </template>
                <template #content>
                    <div class="prose prose-sm max-w-none text-skin-neutral-11" v-html="product.description"></div>
                </template>
            </AppCard>

            <!-- Variations Table -->
            <AppCard v-if="product.type === 'variable' && variations.length">
                <template #title>
                    <div class="flex items-center gap-2">
                        <i class="ri-list-ordered text-skin-primary-9"></i>
                        Variations ({{ variations.length }})
                    </div>
                </template>
                <template #content>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b border-skin-neutral-4 text-left text-xs font-semibold uppercase tracking-wider text-skin-neutral-8">
                                    <th class="px-3 py-2">#</th>
                                    <th class="px-3 py-2">Attributes</th>
                                    <th class="px-3 py-2">SKU</th>
                                    <th class="px-3 py-2 text-right">Price</th>
                                    <th class="px-3 py-2 text-right">Sale</th>
                                    <th class="px-3 py-2 text-center">Stock</th>
                                    <th class="px-3 py-2 text-center">Active</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-skin-neutral-3">
                                <tr v-for="(v, i) in variations" :key="v.id" class="transition-colors hover:bg-skin-neutral-2">
                                    <td class="px-3 py-2 text-xs text-skin-neutral-9">{{ i + 1 }}</td>
                                    <td class="px-3 py-2 text-xs text-skin-neutral-11">
                                        {{ v.attributes.map(a => `${a.attribute}: ${a.value}`).join(' / ') }}
                                    </td>
                                    <td class="px-3 py-2 text-xs text-skin-neutral-9">{{ v.sku || '—' }}</td>
                                    <td class="px-3 py-2 text-right text-sm font-medium">৳{{ Number(v.price).toFixed(2) }}</td>
                                    <td class="px-3 py-2 text-right text-sm">
                                        <span v-if="v.sale_price" class="font-medium text-green-700">৳{{ Number(v.sale_price).toFixed(2) }}</span>
                                        <span v-else class="text-skin-neutral-9">—</span>
                                    </td>
                                    <td class="px-3 py-2 text-center">
                                        <span
                                            class="rounded-full px-2 py-0.5 text-xs font-bold"
                                            :class="v.quantity <= 0 ? 'bg-red-100 text-red-700' : v.quantity < 10 ? 'bg-yellow-100 text-yellow-700' : 'bg-green-100 text-green-700'"
                                        >
                                            {{ v.quantity }}
                                        </span>
                                    </td>
                                    <td class="px-3 py-2 text-center">
                                        <span
                                            class="rounded-full px-2 py-0.5 text-xs font-medium"
                                            :class="v.active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-600'"
                                        >
                                            {{ v.active ? 'Yes' : 'No' }}
                                        </span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </template>
            </AppCard>

            <!-- Bundle Items -->
            <AppCard v-if="product.type === 'bundle' && bundleItems.length">
                <template #title>
                    <div class="flex items-center gap-2">
                        <i class="ri-gift-line text-skin-primary-9"></i>
                        Bundle Items ({{ bundleItems.length }})
                    </div>
                </template>
                <template #content>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b border-skin-neutral-4 text-left text-xs font-semibold uppercase tracking-wider text-skin-neutral-8">
                                    <th class="px-3 py-2">Product</th>
                                    <th class="px-3 py-2 text-center">Qty</th>
                                    <th class="px-3 py-2 text-right">Price</th>
                                    <th class="px-3 py-2 text-center">Optional</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-skin-neutral-3">
                                <tr v-for="bi in bundleItems" :key="bi.id" class="transition-colors hover:bg-skin-neutral-2">
                                    <td class="px-3 py-2 text-sm font-medium text-skin-neutral-12">{{ bi.child_product_name }}</td>
                                    <td class="px-3 py-2 text-center text-sm">{{ bi.quantity }}</td>
                                    <td class="px-3 py-2 text-right text-sm">
                                        <template v-if="bi.price_override">
                                            ৳{{ Number(bi.price_override).toFixed(2) }}
                                        </template>
                                        <template v-else>
                                            ৳{{ Number(bi.child_product_price).toFixed(2) }}
                                        </template>
                                    </td>
                                    <td class="px-3 py-2 text-center">
                                        <span v-if="bi.is_optional" class="text-xs text-skin-primary-9">Optional</span>
                                        <span v-else class="text-xs text-skin-neutral-9">Required</span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </template>
            </AppCard>
        </div>

        <!-- Right sidebar -->
        <div class="space-y-5">

            <!-- Featured Image + Gallery -->
            <AppCard>
                <template #title>
                    <div class="flex items-center gap-2">
                        <i class="ri-image-line text-skin-primary-9"></i>
                        Media
                    </div>
                </template>
                <template #content>
                    <div class="space-y-3">
                        <!-- Main Image -->
                        <div v-if="product.image_url" class="overflow-hidden rounded-lg border border-skin-neutral-4">
                            <img :src="product.image_url" :alt="product.name" class="h-48 w-full object-cover" />
                        </div>
                        <div v-else class="flex h-48 items-center justify-center rounded-lg border border-dashed border-skin-neutral-5 bg-skin-neutral-3">
                            <span class="text-sm text-skin-neutral-9">No image</span>
                        </div>

                        <!-- Gallery -->
                        <div v-if="gallery.length > 1" class="flex gap-2 overflow-x-auto">
                            <div v-for="img in gallery" :key="img.id" class="h-16 w-16 shrink-0 overflow-hidden rounded-md border border-skin-neutral-4">
                                <img :src="img.url" :alt="img.name" class="h-full w-full object-cover" />
                            </div>
                        </div>
                    </div>
                </template>
            </AppCard>

            <!-- Tags -->
            <AppCard v-if="product.tags?.length">
                <template #title>
                    <div class="flex items-center gap-2">
                        <i class="ri-price-tag-3-line text-skin-primary-9"></i>
                        Tags
                    </div>
                </template>
                <template #content>
                    <div class="flex flex-wrap gap-1.5">
                        <span
                            v-for="tag in product.tags"
                            :key="tag.id"
                            class="rounded-full bg-skin-neutral-3 px-2.5 py-0.5 text-xs font-medium text-skin-neutral-11"
                        >
                            {{ tag.name }}
                        </span>
                    </div>
                </template>
            </AppCard>

            <!-- Downloadable Files -->
            <AppCard v-if="productFiles.length">
                <template #title>
                    <div class="flex items-center gap-2">
                        <i class="ri-download-2-line text-skin-primary-9"></i>
                        Downloadable Files
                    </div>
                </template>
                <template #content>
                    <div class="space-y-2">
                        <div v-for="file in productFiles" :key="file.id" class="flex items-center justify-between rounded-md bg-skin-neutral-3 px-3 py-2">
                            <div class="min-w-0">
                                <p class="truncate text-sm font-medium text-skin-neutral-12">{{ file.name }}</p>
                                <p class="text-xs text-skin-neutral-9">{{ file.file_name }} · {{ file.file_size }}</p>
                            </div>
                            <a v-if="file.file_url" :href="file.file_url" target="_blank" class="shrink-0 text-skin-primary-9 hover:underline">
                                <i class="ri-download-line"></i>
                            </a>
                        </div>
                    </div>
                </template>
            </AppCard>

            <!-- SEO Info -->
            <AppCard v-if="product.meta_tag_title || product.meta_tag_description">
                <template #title>
                    <div class="flex items-center gap-2">
                        <i class="ri-search-line text-skin-primary-9"></i>
                        SEO
                    </div>
                </template>
                <template #content>
                    <div class="space-y-2">
                        <div v-if="product.meta_tag_title">
                            <p class="text-xs text-skin-neutral-9">Meta Title</p>
                            <p class="text-sm text-skin-neutral-12">{{ product.meta_tag_title }}</p>
                        </div>
                        <div v-if="product.meta_tag_description">
                            <p class="text-xs text-skin-neutral-9">Meta Description</p>
                            <p class="text-sm text-skin-neutral-12">{{ product.meta_tag_description }}</p>
                        </div>
                    </div>
                </template>
            </AppCard>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue'
import { Head } from '@inertiajs/vue3'
import useAuthCan from '@/Composables/useAuthCan'

const { can } = useAuthCan()

const props = defineProps({
    product: { type: Object, required: true },
    gallery: { type: Array, default: () => [] },
    productFiles: { type: Array, default: () => [] },
    variations: { type: Array, default: () => [] },
    bundleItems: { type: Array, default: () => [] },
})

const breadCrumb = [
    { label: 'Home', href: route('dashboard.index') },
    { label: 'Products', href: route('product.index') },
    { label: props.product.name, last: true },
]

const priceRange = computed(() => {
    const active = props.variations.filter(v => v.active && v.price > 0)
    if (!active.length) return { min: null, max: null }
    const prices = active.map(v => v.sale_price || v.price)
    return {
        min: Math.min(...prices).toFixed(2),
        max: Math.max(...prices).toFixed(2),
    }
})

const inStockCount = computed(() => {
    return props.variations.filter(v => v.active && v.quantity > 0).length
})

function typeBadgeClass(type) {
    const classes = {
        simple: 'bg-skin-neutral-3 text-skin-neutral-10',
        variable: 'bg-purple-100 text-purple-700',
        bundle: 'bg-blue-100 text-blue-700',
    }
    return classes[type] || classes.simple
}
</script>
