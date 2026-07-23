<template>
  <div class="space-y-4">
    <div v-for="attr in attributes" :key="attr.id" class="space-y-2">

      <label class="block text-sm font-medium text-gray-700">{{ attr.name }}

      </label>

      <!-- Color swatches -->
      <div v-if="attr.input_type === 'color'" class="flex flex-wrap gap-2">
        <button v-for="val in attr.values" :key="val.id" type="button" :title="val.value" :class="[
          'h-8 w-8 rounded-full border-2 transition-all',
          selectedIds.includes(val.id)
            ? 'border-gray-900 ring-2 ring-gray-900 ring-offset-2'
            : 'border-gray-200 hover:border-gray-400',
          !isAvailable(val.id) ? 'opacity-30 cursor-not-allowed' : 'cursor-pointer',
        ]" :style="{ backgroundColor: val.swatch || '#ccc' }" :disabled="!isAvailable(val.id)"
          @click="selectValue(attr.id, val.id)"></button>
      </div>

      <!-- Image swatches -->
      <div v-else-if="attr.input_type === 'image'" class="flex flex-wrap gap-2">
        <button v-for="val in attr.values" :key="val.id" type="button" :title="val.value" :class="[
          'h-12 w-12 overflow-hidden rounded-lg border-2 bg-cover bg-center transition-all',
          selectedIds.includes(val.id)
            ? 'border-gray-900 ring-2 ring-gray-900 ring-offset-1'
            : 'border-gray-200 hover:border-gray-400',
          !isAvailable(val.id) ? 'opacity-30 cursor-not-allowed' : 'cursor-pointer',
        ]" :style="val.swatch ? { backgroundImage: `url(${val.swatch})` } : {}" :disabled="!isAvailable(val.id)"
          @click="selectValue(attr.id, val.id)">
          <span v-if="!val.swatch" class="flex h-full w-full items-center justify-center text-xs text-gray-400">{{
            val.value.charAt(0) }}</span>
        </button>
      </div>

      <!-- Select dropdown -->
      <div v-else>
        <select :value="selectedValueForAttr(attr.id)"
          class="block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
          @change="selectValue(attr.id, Number($event.target.value))">
          <option value="">Select {{ attr.name }}</option>
          <option v-for="val in attr.values" :key="val.id" :value="val.id" :disabled="!isAvailable(val.id)">
            {{ val.value }}
          </option>
        </select>
      </div>
    </div>

    <!-- Selected state display -->
    <div v-if="variation">
      <div class="flex items-center gap-3">
        <div class="text-lg font-bold text-gray-900">
          <template v-if="variation.sale_price && variation.sale_price < variation.price">
            <span class="text-gray-400 line-through">৳{{ variation.price }}</span>
            <span class="ml-2">৳{{ variation.sale_price }}</span>
          </template>
          <template v-else>
            ৳{{ variation.price }}
          </template>
        </div>
        <span v-if="variation.quantity <= 0"
          class="rounded-full bg-red-50 px-2.5 py-0.5 text-xs font-medium text-red-600">Out
          of Stock</span>
        <span v-else-if="variation.quantity < 10"
          class="rounded-full bg-orange-50 px-2.5 py-0.5 text-xs font-medium text-orange-600">Only {{ variation.quantity
          }}
          left</span>
        <span v-else class="rounded-full bg-green-50 px-2.5 py-0.5 text-xs font-medium text-green-600">In Stock</span>
      </div>
      <p v-if="variation.sku" class="mt-1 text-xs text-gray-400">SKU: {{ variation.sku }}</p>
    </div>

    <div v-else-if="Object.keys(allAttributes).length > 0" class="text-sm text-gray-500">
      Select all options to see pricing and availability.
    </div>
  </div>
</template>

<script setup>
import { computed, ref, watch } from 'vue'

const props = defineProps({
  allAttributes: { type: Object, default: () => ({}) },
  variations: { type: Array, default: () => [] },
  parentProduct: { type: Object, default: () => ({}) },
})

const emit = defineEmits(['variation-change'])

const selected = ref({})

const attributes = computed(() => {
  return Object.values(props.allAttributes).filter((a) => a.values?.length)
})

const selectedIds = computed(() => Object.values(selected.value).filter(Boolean))

const isAvailable = (valueId) => {
  // Check ALL variations (active and inactive) so attribute values are always selectable.
  // If the matched variation is inactive, canAddToCart will handle the stock check.
  return props.variations.some((v) => {
    if (!v.attribute_value_ids?.includes(valueId)) return false
    return true
  })
}

const matchedVariation = computed(() => {
  const ids = Object.values(selected.value).filter(Boolean).sort()
  if (ids.length === 0 || ids.length !== attributes.value.length) return null

  return props.variations.find((v) => {
    const vIds = [...(v.attribute_value_ids || [])].sort()
    return JSON.stringify(vIds) === JSON.stringify(ids)
  }) || null
})

const variation = computed(() => {
  return matchedVariation.value
})

watch(variation, (v) => {
  emit('variation-change', v)
})

const selectValue = (attrId, valId) => {
  if (selected.value[attrId] === valId) {
    selected.value[attrId] = null
  } else {
    selected.value[attrId] = valId
  }
}

const selectedValueForAttr = (attrId) => {
  return selected.value[attrId] || ''
}

const reset = () => {
  selected.value = {}
}

defineExpose({ reset })
</script>
