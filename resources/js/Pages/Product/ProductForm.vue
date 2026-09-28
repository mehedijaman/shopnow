<template>
    <AppSectionHeader :title="__('common.menu.products')" :bread-crumb="breadCrumb">
        <AppButton
            v-if="can(isCreate ? 'product-create' : 'product-edit')"
            class="btn btn-primary mt-6"
            :disabled="saving"
            @click="submitForm"
        >
            <i class="ri-save-line mr-1"></i>
            {{ saving ? __('common.saving') : __('product::admin.save_product') }}
        </AppButton>
    </AppSectionHeader>

    <div class="grid grid-cols-1 gap-5 xl:grid-cols-3">

        <!-- Left / Main column -->
        <div class="xl:col-span-2 space-y-5">

            <!-- Basic Info card -->
            <AppCard>
                <template #title>
                    <div class="flex items-center gap-2">
                        <i class="ri-information-line text-skin-primary-9"></i>
                        {{ __('product::admin.basic_information') }}
                    </div>
                </template>
                <template #content>
                    <AppFormErrors class="mb-4" />
                    <div class="space-y-4">
                        <div>
                            <AppLabel for="name">{{ __('common.field.product_name') }} <span class="text-red-500">*</span></AppLabel>
                            <AppInputText
                                id="name"
                                v-model="productStore.product.name"
                                type="text"
                                placeholder="e.g. Wireless Headphones Pro"
                                :class="{ 'input-error': errorsFields.includes('name') }"
                            />
                        </div>

                        <div>
                            <AppLabel for="summary">{{ __('common.field.short_summary') }}</AppLabel>
                            <AppInputText
                                id="summary"
                                v-model="productStore.product.summary"
                                type="text"
                                :placeholder="__('product::admin.summary_placeholder')"
                                :class="{ 'input-error': errorsFields.includes('summary') }"
                            />
                        </div>

                        <!-- Price / Stock — only for simple products (variable: on variations, bundle: from bundle items) -->
                        <div v-if="productStore.product.type === 'simple'" class="grid grid-cols-3 gap-4">
                            <div>
                                <AppLabel for="price">{{ __('common.field.price') }} <span class="text-red-500">*</span></AppLabel>
                                <AppInputText
                                    id="price"
                                    v-model="productStore.product.price"
                                    type="number"
                                    placeholder="0.00"
                                    :class="{ 'input-error': errorsFields.includes('price') }"
                                />
                            </div>
                            <div>
                                <AppLabel for="sale_price">{{ __('common.field.sale_price') }}</AppLabel>
                                <AppInputText
                                    id="sale_price"
                                    v-model="productStore.product.sale_price"
                                    type="number"
                                    placeholder="0.00"
                                    :class="{ 'input-error': errorsFields.includes('sale_price') }"
                                />
                            </div>
                            <div>
                                <AppLabel for="quantity">{{ __('common.field.stock_qty') }} <span class="text-red-500">*</span></AppLabel>
                                <AppInputText
                                    id="quantity"
                                    v-model="productStore.product.quantity"
                                    type="number"
                                    placeholder="0"
                                    :class="{ 'input-error': errorsFields.includes('quantity') }"
                                />
                            </div>
                        </div>

                        <div v-if="productStore.product.type !== 'bundle'" class="grid grid-cols-2 gap-4">
                            <div>
                                <AppLabel for="unit">{{ __('common.field.unit') }}</AppLabel>
                                <AppInputText
                                    id="unit"
                                    v-model="productStore.product.unit"
                                    type="text"
                                    placeholder="e.g. pcs, kg, pack"
                                    :class="{ 'input-error': errorsFields.includes('unit') }"
                                />
                            </div>
                            <div>
                                <AppLabel for="min_order">{{ __('common.field.min_order') }}</AppLabel>
                                <AppInputText
                                    id="min_order"
                                    v-model="productStore.product.min_order"
                                    type="number"
                                    placeholder="1"
                                    :class="{ 'input-error': errorsFields.includes('min_order') }"
                                />
                            </div>
                        </div>

                        <!-- Price hint for variable/bundle types -->
                        <div v-if="productStore.product.type === 'variable'" class="rounded-md bg-skin-info-light px-3 py-2 text-xs text-skin-info-dark">
                            <i class="ri-information-line mr-1"></i>
                            {{ __('product::admin.pricing_hint_variable') }}
                        </div>
                        <div v-if="productStore.product.type === 'bundle'" class="rounded-md bg-skin-info-light px-3 py-2 text-xs text-skin-info-dark">
                            <i class="ri-information-line mr-1"></i>
                            {{ __('product::admin.pricing_hint_bundle') }}
                        </div>

                        <div class="flex items-center gap-6 pt-1">
                            <label class="flex cursor-pointer items-center gap-2">
                                <AppCheckbox
                                    id="active"
                                    v-model="productStore.product.active"
                                    name="active"
                                    :value="true"
                                />
                                <span class="text-sm font-medium">{{ __('common.field.active') }}</span>
                            </label>
                            <label class="flex cursor-pointer items-center gap-2">
                                <AppCheckbox
                                    id="featured"
                                    v-model="productStore.product.featured"
                                    name="featured"
                                    :value="true"
                                />
                                <span class="text-sm font-medium">{{ __('common.field.featured') }}</span>
                            </label>
                            <label v-if="productStore.product.type !== 'bundle'" class="flex cursor-pointer items-center gap-2">
                                <AppCheckbox
                                    id="is_virtual"
                                    v-model="productStore.product.is_virtual"
                                    name="is_virtual"
                                    :value="true"
                                />
                                <span class="text-sm font-medium">{{ __('common.field.virtual') }}</span>
                            </label>
                            <label v-if="productStore.product.type !== 'bundle'" class="flex cursor-pointer items-center gap-2">
                                <AppCheckbox
                                    id="is_downloadable"
                                    v-model="productStore.product.is_downloadable"
                                    name="is_downloadable"
                                    :value="true"
                                />
                                <span class="text-sm font-medium">{{ __('common.field.downloadable') }}</span>
                            </label>
                        </div>
                    </div>
                </template>
            </AppCard>

            <!-- Description card -->
            <AppCard>
                <template #title>
                    <div class="flex items-center gap-2">
                        <i class="ri-file-text-line text-skin-primary-9"></i>
                        {{ __('common.field.description') }}
                    </div>
                </template>
                <template #content>
                    <AppTipTapEditor
                        v-model="productStore.product.description"
                        editor-id="description"
                        :class="{ 'app-tip-tap-error': errorsFields.includes('description') }"
                        :file-upload-url="route('product.uploadEditorImage')"
                    />
                </template>
            </AppCard>

            

            <!-- Variations card -->
            <AppCard v-if="productStore.product.type === 'variable'">
                <template #title>
                    <div class="flex items-center gap-2">
                        <i class="ri-organization-chart text-skin-primary-9"></i>
                        {{ __('product::admin.variations') }}
                    </div>
                </template>
                <template #content>
                    <ProductVariations
                        :product-id="props.product?.id ?? null"
                        :all-attributes="allAttributes"
                    />
                </template>
            </AppCard>

            <!-- Bundle Items card -->
            <AppCard v-if="productStore.product.type === 'bundle'">
                <template #title>
                    <div class="flex items-center gap-2">
                        <i class="ri-gift-line text-skin-primary-9"></i>
                        {{ __('product::admin.bundle_items') }}
                    </div>
                </template>
                <template #content>
                    <ProductBundleItems
                        :product-id="props.product?.id ?? null"
                        :is-new="isCreate"
                        :all-products="allProducts"
                    />
                </template>
            </AppCard>
        </div>

        <!-- Right sidebar -->
        <div class="space-y-5">

            <!-- Publish / Status card -->
            <AppCard>
                <template #title>
                    <div class="flex items-center gap-2">
                        <i class="ri-settings-3-line text-skin-primary-9"></i>
                        {{ __('product::admin.product_settings') }}
                    </div>
                </template>
                <template #content>
                    <div class="mb-4">
                        <AppLabel for="type">{{ __('product::admin.product_type') }}</AppLabel>
                        <select
                            id="type"
                            v-model="productStore.product.type"
                            class="mt-1 block w-full rounded-md border-0 bg-skin-neutral-1 px-3 py-2 text-skin-neutral-12 placeholder-skin-neutral-9 shadow-xs ring-1 ring-inset ring-skin-neutral-7 focus:ring-2 focus:ring-inset focus:ring-skin-neutral-7 sm:text-sm sm:leading-6"
                        >
                            <option value="simple">{{ __('product::admin.simple_product') }}</option>
                            <option value="variable">{{ __('product::admin.variable_product') }}</option>
                            <option value="bundle">{{ __('product::admin.bundle_product') }}</option>
                        </select>
                    </div>
                    <ProductCategory :categories="categories" />
                    <ProductBrand :brands="brands" />
                    <ProductTags :tags="tags" />
                </template>
            </AppCard>

            <!-- Featured image card -->
            <AppCard>
                <template #title>
                    <div class="flex items-center gap-2">
                        <i class="ri-image-line text-skin-primary-9"></i>
                        {{ __('common.field.featured_image') }}
                    </div>
                </template>
                <template #content>
                    <p class="mb-2 text-xs text-skin-neutral-9">{{ __('product::admin.featured_image_hint') }}</p>
                    <ProductImage />
                </template>
            </AppCard>

            <!-- Gallery card -->
            <AppCard>
                <template #title>
                    <div class="flex items-center gap-2">
                        <i class="ri-gallery-line text-skin-primary-9"></i>
                        {{ __('common.field.gallery') }}
                    </div>
                </template>
                <template #content>
                    <p class="mb-2 text-xs text-skin-neutral-9">{{ __('product::admin.gallery_hint') }}</p>
                    <ProductGallery :gallery="gallery" :product-id="props.product?.id ?? null" />
                </template>
            </AppCard>

            <!-- Downloadable Files card -->
            <AppCard v-if="productStore.product.is_downloadable">
                <template #title>
                    <div class="flex items-center gap-2">
                        <i class="ri-download-cloud-line text-skin-primary-9"></i>
                        {{ __('common.field.downloadable_files') }}
                    </div>
                </template>
                <template #content>
                    <p class="mb-2 text-xs text-skin-neutral-9">{{ __('product::admin.downloads_hint') }}</p>
                    <ProductDownloads
                        :product-files="productFiles"
                        :product-id="props.product?.id ?? null"
                        :is-new="isCreate"
                    />
                </template>
            </AppCard>

            <!-- SEO card -->
            <AppCard>
                <template #title>
                    <div class="flex items-center gap-2">
                        <i class="ri-search-line text-skin-primary-9"></i>
                        {{ __('product::admin.seo') }}
                    </div>
                </template>
                <template #content>
                    <div class="space-y-4">
                        <div>
                            <div class="flex items-center justify-between">
                                <AppLabel for="meta_tag_title">{{ __('common.field.meta_title') }}</AppLabel>
                                <span class="text-xs" :class="productStore.getRemainingChars('meta_tag_title', 60) < 10 ? 'text-red-500' : 'text-skin-neutral-9'">
                                    {{ productStore.getRemainingChars('meta_tag_title', 60) }} / 60
                                </span>
                            </div>
                            <AppInputText
                                id="meta_tag_title"
                                v-model="productStore.product.meta_tag_title"
                                type="text"
                                maxlength="60"
                                :placeholder="__('product::admin.meta_title_placeholder')"
                                :class="{ 'input-error': errorsFields.includes('meta_tag_title') }"
                            />
                        </div>
                        <div>
                            <div class="flex items-center justify-between">
                                <AppLabel for="meta_tag_description">{{ __('common.field.meta_description') }}</AppLabel>
                                <span class="text-xs" :class="productStore.getRemainingChars('meta_tag_description', 160) < 20 ? 'text-red-500' : 'text-skin-neutral-9'">
                                    {{ productStore.getRemainingChars('meta_tag_description', 160) }} / 160
                                </span>
                            </div>
                            <AppTextArea
                                id="meta_tag_description"
                                v-model="productStore.product.meta_tag_description"
                                rows="3"
                                maxlength="160"
                                :placeholder="__('product::admin.meta_description_placeholder')"
                                :class="{ 'input-error': errorsFields.includes('meta_tag_description') }"
                            />
                        </div>
                    </div>
                </template>
            </AppCard>
        </div>
    </div>

    <div class="mt-5 flex justify-end">
        <AppButton
            v-if="can(isCreate ? 'product-create' : 'product-edit')"
            class="btn btn-primary"
            :disabled="saving"
            @click="submitForm"
        >
            <i class="ri-save-line mr-1"></i>
            {{ saving ? __('common.saving') : __('product::admin.save_product') }}
        </AppButton>
    </div>

    <AppConfirmDialog ref="confirmDialogRef"></AppConfirmDialog>
</template>

<script setup>
import { ref, watch, provide, inject } from 'vue'
import { useForm } from '@inertiajs/vue3'
import useAuthCan from '@/Composables/useAuthCan'
import { onUnmounted } from 'vue'
import useTitle from '@/Composables/useTitle'
import useFormContext from '@/Composables/useFormContext'
import useFormErrors from '@/Composables/useFormErrors'
import ProductImage from './Components/ProductImage.vue'
import ProductCategory from './Components/ProductCategory.vue'
import ProductBrand from './Components/ProductBrand.vue'
import ProductTags from './Components/ProductTags.vue'
import ProductGallery from './Components/ProductGallery.vue'
import ProductDownloads from './Components/ProductDownloads.vue'
import ProductVariations from './Components/ProductVariations.vue'
import ProductBundleItems from './Components/ProductBundleItems.vue'
import { useProductStore } from './ProductStore'

const productStore = useProductStore()
const translate = inject('translate')
const { can } = useAuthCan()
const { errorsFields } = useFormErrors()
const saving = ref(false)
const confirmDialogRef = ref(null)

provide('confirmDelete', (callback) => {
    confirmDialogRef.value?.openCustomModal({
        title: translate('common.delete_confirmation'),
        message: translate('common.delete_message'),
        onConfirm: callback
    })
})

const props = defineProps({
    product: {
        type: Object,
        default: null
    },

    categories: {
        type: Object,
        default: () => {}
    },

    brands: {
        type: Object,
        default: () => []
    },

    tags: {
        type: Object,
        default: () => {}
    },

    gallery: {
        type: Array,
        default: () => []
    },

    productFiles: {
        type: Array,
        default: () => []
    },

    allAttributes: {
        type: Array,
        default: () => []
    },

    allProducts: {
        type: Array,
        default: () => []
    }
})

if (props.product) {
    productStore.setProduct(props.product)
}

// Re-sync store when Inertia updates props without remounting (e.g. after save redirect)
watch(
    () => props.product,
    (newProduct) => {
        if (newProduct) {
            productStore.setProduct(newProduct)
        }
    }
)

onUnmounted(() => {
    productStore.$reset()
})

const breadCrumb = [
    { label: translate('common.home'), href: route('dashboard.index') },
    { label: translate('common.menu.products'), href: route('product.index') },
    { label: translate('product::admin.product'), last: true }
]

const { title } = useTitle(translate('product::admin.product'))
const { isCreate } = useFormContext()

const getValueFromKey = (data, key) => {
    return data[key] ? data[key].value : null
}

const submitForm = () => {
    saving.value = true
    const form = useForm(productStore.product)

    const INTERNAL_FIELDS = ['slug', 'image_url', 'created_at', 'updated_at', 'deleted_at', 'created_by', 'updated_by', 'deleted_by', 'tagsHasChanged']

    const productData = (data) => {
        const cleaned = { ...data }

        // Strip DB-only / internal fields that the backend doesn't accept
        for (const key of INTERNAL_FIELDS) {
            delete cleaned[key]
        }

        // Remove image when no new file selected — let controller keep existing
        if (!cleaned.image) {
            delete cleaned.image
        }

        // Normalize combobox objects to plain ids
        cleaned.category_id = getValueFromKey(data, 'category_id')
        cleaned.brand_id = getValueFromKey(data, 'brand_id')

        // Ensure numeric fields are numbers (DB stores some as strings)
        if (cleaned.price !== '' && cleaned.price != null) cleaned.price = Number(cleaned.price)
        if (cleaned.sale_price !== '' && cleaned.sale_price != null) cleaned.sale_price = Number(cleaned.sale_price)
        if (cleaned.quantity !== '' && cleaned.quantity != null) cleaned.quantity = Number(cleaned.quantity)
        if (cleaned.min_order !== '' && cleaned.min_order != null) cleaned.min_order = Number(cleaned.min_order)

        return isCreate.value ? cleaned : { ...cleaned, _method: 'PUT' }
    }

    const onFinish = () => { saving.value = false }

    if (isCreate.value) {
        form.transform(productData).post(route('product.store'), { onFinish })
    } else {
        form.transform(productData).post(route('product.update', props.product.id), { onFinish })
    }
}
</script>
