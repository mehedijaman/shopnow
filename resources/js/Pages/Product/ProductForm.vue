<template>
    <AppSectionHeader title="Products" :bread-crumb="breadCrumb">
        <AppButton
            v-if="can(isCreate ? 'product-create' : 'product-edit')"
            class="btn btn-primary mt-6"
            :disabled="saving"
            @click="submitForm"
        >
            <i class="ri-save-line mr-1"></i>
            {{ saving ? 'Saving…' : 'Save Product' }}
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
                        Basic Information
                    </div>
                </template>
                <template #content>
                    <AppFormErrors class="mb-4" />
                    <div class="space-y-4">
                        <div>
                            <AppLabel for="name">Product Name <span class="text-red-500">*</span></AppLabel>
                            <AppInputText
                                id="name"
                                v-model="productStore.product.name"
                                type="text"
                                placeholder="e.g. Wireless Headphones Pro"
                                :class="{ 'input-error': errorsFields.includes('name') }"
                            />
                        </div>

                        <div>
                            <AppLabel for="summary">Short Summary</AppLabel>
                            <AppInputText
                                id="summary"
                                v-model="productStore.product.summary"
                                type="text"
                                placeholder="One-line product description shown on listing cards"
                                :class="{ 'input-error': errorsFields.includes('summary') }"
                            />
                        </div>

                        <div class="grid grid-cols-3 gap-4">
                            <div>
                                <AppLabel for="price">Price <span class="text-red-500">*</span></AppLabel>
                                <AppInputText
                                    id="price"
                                    v-model="productStore.product.price"
                                    type="number"
                                    placeholder="0.00"
                                    :class="{ 'input-error': errorsFields.includes('price') }"
                                />
                            </div>
                            <div>
                                <AppLabel for="sale_price">Sale Price</AppLabel>
                                <AppInputText
                                    id="sale_price"
                                    v-model="productStore.product.sale_price"
                                    type="number"
                                    placeholder="0.00"
                                    :class="{ 'input-error': errorsFields.includes('sale_price') }"
                                />
                            </div>
                            <div>
                                <AppLabel for="quantity">Stock Qty <span class="text-red-500">*</span></AppLabel>
                                <AppInputText
                                    id="quantity"
                                    v-model="productStore.product.quantity"
                                    type="number"
                                    placeholder="0"
                                    :class="{ 'input-error': errorsFields.includes('quantity') }"
                                />
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <AppLabel for="unit">Unit</AppLabel>
                                <AppInputText
                                    id="unit"
                                    v-model="productStore.product.unit"
                                    type="text"
                                    placeholder="e.g. pcs, kg, pack"
                                    :class="{ 'input-error': errorsFields.includes('unit') }"
                                />
                            </div>
                            <div>
                                <AppLabel for="min_order">Min Order</AppLabel>
                                <AppInputText
                                    id="min_order"
                                    v-model="productStore.product.min_order"
                                    type="number"
                                    placeholder="1"
                                    :class="{ 'input-error': errorsFields.includes('min_order') }"
                                />
                            </div>
                        </div>

                        <div class="flex items-center gap-6 pt-1">
                            <label class="flex cursor-pointer items-center gap-2">
                                <AppCheckbox
                                    id="active"
                                    v-model="productStore.product.active"
                                    name="active"
                                    :value="true"
                                />
                                <span class="text-sm font-medium">Active</span>
                            </label>
                            <label class="flex cursor-pointer items-center gap-2">
                                <AppCheckbox
                                    id="featured"
                                    v-model="productStore.product.featured"
                                    name="featured"
                                    :value="true"
                                />
                                <span class="text-sm font-medium">Featured</span>
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
                        Description
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

            <!-- SEO card -->
            <AppCard>
                <template #title>
                    <div class="flex items-center gap-2">
                        <i class="ri-search-line text-skin-primary-9"></i>
                        SEO
                    </div>
                </template>
                <template #content>
                    <div class="space-y-4">
                        <div>
                            <div class="flex items-center justify-between">
                                <AppLabel for="meta_tag_title">Meta Title</AppLabel>
                                <span class="text-xs" :class="productStore.getRemainingChars('meta_tag_title', 60) < 10 ? 'text-red-500' : 'text-skin-neutral-9'">
                                    {{ productStore.getRemainingChars('meta_tag_title', 60) }} / 60
                                </span>
                            </div>
                            <AppInputText
                                id="meta_tag_title"
                                v-model="productStore.product.meta_tag_title"
                                type="text"
                                maxlength="60"
                                placeholder="Leave blank to use product name"
                                :class="{ 'input-error': errorsFields.includes('meta_tag_title') }"
                            />
                        </div>
                        <div>
                            <div class="flex items-center justify-between">
                                <AppLabel for="meta_tag_description">Meta Description</AppLabel>
                                <span class="text-xs" :class="productStore.getRemainingChars('meta_tag_description', 160) < 20 ? 'text-red-500' : 'text-skin-neutral-9'">
                                    {{ productStore.getRemainingChars('meta_tag_description', 160) }} / 160
                                </span>
                            </div>
                            <AppTextArea
                                id="meta_tag_description"
                                v-model="productStore.product.meta_tag_description"
                                rows="3"
                                maxlength="160"
                                placeholder="Leave blank to auto-generate from description"
                                :class="{ 'input-error': errorsFields.includes('meta_tag_description') }"
                            />
                        </div>
                    </div>
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
                        Product Settings
                    </div>
                </template>
                <template #content>
                    <ProductCategory :categories="categories" />
                    <ProductTags :tags="tags" />
                </template>
            </AppCard>

            <!-- Featured image card -->
            <AppCard>
                <template #title>
                    <div class="flex items-center gap-2">
                        <i class="ri-image-line text-skin-primary-9"></i>
                        Featured Image
                    </div>
                </template>
                <template #content>
                    <p class="mb-2 text-xs text-skin-neutral-9">Used as thumbnail on listing pages</p>
                    <ProductImage />
                </template>
            </AppCard>

            <!-- Gallery card -->
            <AppCard>
                <template #title>
                    <div class="flex items-center gap-2">
                        <i class="ri-gallery-line text-skin-primary-9"></i>
                        Gallery
                    </div>
                </template>
                <template #content>
                    <p class="mb-2 text-xs text-skin-neutral-9">Additional images shown on the product detail page</p>
                    <ProductGallery :gallery="gallery" :product-id="props.product?.id ?? null" />
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
            {{ saving ? 'Saving…' : 'Save Product' }}
        </AppButton>
    </div>
</template>

<script setup>
import { ref } from 'vue'
import { useForm } from '@inertiajs/vue3'
import useAuthCan from '@/Composables/useAuthCan'
import { onUnmounted } from 'vue'
import useTitle from '@/Composables/useTitle'
import useFormContext from '@/Composables/useFormContext'
import useFormErrors from '@/Composables/useFormErrors'
import ProductImage from './Components/ProductImage.vue'
import ProductCategory from './Components/ProductCategory.vue'
import ProductTags from './Components/ProductTags.vue'
import ProductGallery from './Components/ProductGallery.vue'
import { useProductStore } from './ProductStore'

const productStore = useProductStore()
const { can } = useAuthCan()
const { errorsFields } = useFormErrors()
const saving = ref(false)

const props = defineProps({
    product: {
        type: Object,
        default: null
    },

    categories: {
        type: Object,
        default: () => {}
    },

    tags: {
        type: Object,
        default: () => {}
    },

    gallery: {
        type: Array,
        default: () => []
    }
})

if (props.product) {
    productStore.setProduct(props.product)
}

onUnmounted(() => {
    productStore.$reset()
})

const breadCrumb = [
    { label: 'Home', href: route('dashboard.index') },
    { label: 'Products', href: route('product.index') },
    { label: 'Product', last: true }
]

const { title } = useTitle('Product')
const { isCreate } = useFormContext()

const getValueFromKey = (data, key) => {
    return data[key] ? data[key].value : null
}

const submitForm = () => {
    saving.value = true
    const form = useForm(productStore.product)

    const productData = (data) => {
        const commonData = {
            ...data,
            category_id: getValueFromKey(data, 'category_id')
        }

        return isCreate.value ? commonData : { ...commonData, _method: 'PUT' }
    }

    const onFinish = () => { saving.value = false }

    if (isCreate.value) {
        form.transform(productData).post(route('product.store'), { onFinish })
    } else {
        form.transform(productData).post(route('product.update', props.product.id), { onFinish })
    }
}
</script>
