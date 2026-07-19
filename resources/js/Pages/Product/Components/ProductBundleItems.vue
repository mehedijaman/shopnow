<template>
    <div>
        <AppFormErrors />

        <!-- Bundle Pricing Config -->
        <AppCard class="mb-4">
            <template #title>
                <div class="flex items-center gap-2">
                    <i class="ri-price-tag-3-line text-skin-primary-9"></i>
                    Bundle Pricing
                </div>
            </template>
            <template #content>
                <div class="space-y-4">
                    <div>
                        <AppLabel for="pricing_type">Pricing Type</AppLabel>
                        <select
                            id="pricing_type"
                            v-model="pricingType"
                            class="mt-1 block w-full rounded-md border-0 bg-skin-neutral-1 px-3 py-2 text-skin-neutral-12 placeholder-skin-neutral-9 shadow-xs ring-1 ring-inset ring-skin-neutral-7 focus:ring-2 focus:ring-inset focus:ring-skin-neutral-7 sm:text-sm sm:leading-6"
                        >
                            <option value="calculated">Calculated (sum of children)</option>
                            <option value="fixed">Fixed Price</option>
                        </select>
                    </div>

                    <div v-if="pricingType === 'calculated'">
                        <div>
                            <AppLabel for="discount_type">Discount Type</AppLabel>
                            <select
                                id="discount_type"
                                v-model="discountType"
                                class="mt-1 block w-full rounded-md border-0 bg-skin-neutral-1 px-3 py-2 text-skin-neutral-12 placeholder-skin-neutral-9 shadow-xs ring-1 ring-inset ring-skin-neutral-7 focus:ring-2 focus:ring-inset focus:ring-skin-neutral-7 sm:text-sm sm:leading-6"
                            >
                                <option value="none">No Discount</option>
                                <option value="percentage">Percentage (%)</option>
                                <option value="fixed_amount">Fixed Amount</option>
                            </select>
                        </div>
                        <div v-if="discountType !== 'none'" class="mt-3">
                            <AppLabel for="discount_value">
                                {{ discountType === 'percentage' ? 'Discount %' : 'Discount Amount' }}
                            </AppLabel>
                            <AppInputText
                                id="discount_value"
                                v-model="discountValue"
                                type="number"
                                step="0.01"
                                placeholder="0"
                            />
                        </div>
                    </div>

                    <div v-if="pricingType === 'fixed'">
                        <AppLabel for="fixed_price">Fixed Bundle Price</AppLabel>
                        <AppInputText
                            id="fixed_price"
                            v-model="fixedPrice"
                            type="number"
                            step="0.01"
                            placeholder="0.00"
                        />
                    </div>

                    <div class="flex items-center gap-2">
                        <AppButton size="sm" @click="saveConfig">Save Pricing Config</AppButton>
                        <span v-if="pricePreview !== null" class="text-sm text-skin-neutral-9">
                            Preview: {{ currency(pricePreview) }}
                        </span>
                    </div>
                </div>
            </template>
        </AppCard>

        <!-- Bundle Items List -->
        <AppCard>
            <template #title>
                <div class="flex items-center gap-2">
                    <i class="ri-gift-line text-skin-primary-9"></i>
                    Bundle Items
                </div>
            </template>
            <template #content>
                <p class="mb-3 text-xs text-skin-neutral-9">
                    Products included in this bundle. Drag to reorder. Optional items can be skipped by the customer.
                </p>

                <div v-if="savingItem" class="mb-4 flex items-center gap-3 rounded-lg border border-skin-neutral-6 bg-skin-neutral-2 p-3">
                    <div class="min-w-0 flex-1">
                        <select
                            v-model="newChildId"
                            class="mb-2 block w-full rounded-md border-0 bg-skin-neutral-1 px-3 py-2 text-skin-neutral-12 shadow-xs ring-1 ring-inset ring-skin-neutral-7 focus:ring-2 focus:ring-inset focus:ring-skin-neutral-7 sm:text-sm sm:leading-6"
                        >
                            <option :value="null">-- Select Product --</option>
                            <option v-for="p in allProducts" :key="p.id" :value="p.id">
                                {{ p.name }} ({{ currency(p.sale_price ?? p.price) }}, stock: {{ p.quantity }})
                            </option>
                        </select>
                        <div class="flex flex-wrap gap-2">
                            <div>
                                <AppLabel class="text-xs">Qty</AppLabel>
                                <AppInputText v-model="newQty" type="number" min="1" class="w-20" />
                            </div>
                            <label class="flex cursor-pointer items-center gap-1 text-xs">
                                <AppCheckbox v-model="newOptional" :value="true" />
                                Optional
                            </label>
                            <div>
                                <AppLabel class="text-xs">Price Override</AppLabel>
                                <AppInputText v-model="newPriceOverride" type="number" step="0.01" placeholder="auto" class="w-24" />
                            </div>
                        </div>
                    </div>
                    <div class="flex flex-col gap-1">
                        <button class="btn btn-sm btn-primary" :disabled="!newChildId" @click="addItem">Add</button>
                        <button class="btn btn-sm btn-ghost" @click="savingItem = false">Cancel</button>
                    </div>
                </div>

                <AppButton v-else size="sm" @click="savingItem = true">
                    <i class="ri-add-line mr-1"></i> Add Bundle Item
                </AppButton>

                <div v-if="items.length === 0" class="py-6 text-center text-sm text-skin-neutral-9">
                    No items added yet.
                </div>

                <draggable
                    v-else
                    v-model="orderedItems"
                    tag="div"
                    class="mt-3 space-y-2"
                    handle=".drag-handle"
                    item-key="id"
                    :on-end="onReorder"
                >
                    <template #item="{ element, index }">
                        <div class="flex items-center gap-3 rounded-lg border border-skin-neutral-6 bg-skin-neutral-2 p-3">
                            <i class="ri-draggable drag-handle cursor-grab text-skin-neutral-9"></i>
                            <div class="min-w-0 flex-1">
                                <div class="text-sm font-medium">{{ element.child_product_name }}</div>
                                <div class="text-xs text-skin-neutral-9">
                                    Qty: {{ element.quantity }} |
                                    Price: {{ currency(element.price_override ?? element.child_product_price) }}
                                    <span v-if="element.is_optional" class="ml-1 text-skin-info-9">(optional)</span>
                                </div>
                            </div>
                            <button class="btn btn-sm btn-ghost text-red-500" @click="removeItem(element.id)">
                                <i class="ri-delete-bin-line"></i>
                            </button>
                        </div>
                    </template>
                </draggable>
            </template>
        </AppCard>
    </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import axios from 'axios'
import useFormErrors from '@/Composables/useFormErrors'
import draggable from 'vuedraggable'

const { errorsFields } = useFormErrors()

const currency = (val) => {
  if (val == null) return '৳0.00'
  return `৳${Number(val).toFixed(2)}`
}

const props = defineProps({
    productId: { type: Number, default: null },
    isNew: { type: Boolean, default: false },
    allProducts: { type: Array, default: () => [] },
})

const pricingType = ref('calculated')
const discountType = ref('none')
const discountValue = ref(null)
const fixedPrice = ref(null)
const pricePreview = ref(null)
const items = ref([])
const allProducts = ref([])
const savingItem = ref(false)
const newChildId = ref(null)
const newQty = ref(1)
const newOptional = ref(false)
const newPriceOverride = ref(null)

const orderedItems = computed({
    get: () => items.value,
    set: (val) => { items.value = val },
})

const saveConfig = async () => {
    if (!props.productId) return
    const { data } = await axios.post(route('product.bundle.saveConfig', props.productId), {
        pricing_type: pricingType.value,
        discount_type: discountType.value,
        discount_value: discountType.value !== 'none' ? discountValue.value : null,
        fixed_price: pricingType.value === 'fixed' ? fixedPrice.value : null,
    })
    pricePreview.value = data.price_preview
}

const addItem = async () => {
    if (!newChildId.value || !props.productId) return
    const { data } = await axios.post(route('product.bundle.items.add', props.productId), {
        child_product_id: newChildId.value,
        quantity: newQty.value,
        is_optional: newOptional.value,
        price_override: newPriceOverride.value || null,
    })
    items.value.push(data.item)
    pricePreview.value = data.price_preview
    newChildId.value = null
    newQty.value = 1
    newOptional.value = false
    newPriceOverride.value = null
    savingItem.value = false
}

const removeItem = async (itemId) => {
    const { data } = await axios.delete(route('product.bundle.items.remove', [props.productId, itemId]))
    items.value = items.value.filter((i) => i.id !== itemId)
    pricePreview.value = data.price_preview
}

const onReorder = async () => {
    await axios.post(route('product.bundle.reorder', props.productId), {
        item_ids: orderedItems.value.map((i) => i.id),
    })
}

const loadBundle = async () => {
    if (!props.productId || props.isNew) return
    const { data } = await axios.get(route('product.bundle.config', props.productId))
    if (data.config) {
        pricingType.value = data.config.pricing_type
        discountType.value = data.config.discount_type
        discountValue.value = data.config.discount_value
        fixedPrice.value = data.config.fixed_price
        pricePreview.value = data.price_preview
    }
    items.value = data.items
    allProducts.value = data.allProducts
}

onMounted(() => {
    allProducts.value = props.allProducts
    loadBundle()
})
</script>
