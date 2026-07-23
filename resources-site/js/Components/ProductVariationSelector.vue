<template>
  <div class="space-y-4">
    <div v-for="attr in attributes" :key="attr.id" class="space-y-2">
      <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">{{ attr.name }}</label>

      <!-- Color swatches -->
      <div v-if="attr.input_type === 'color'" class="flex flex-wrap gap-2.5">
        <button
          v-for="val in attr.values"
          :key="val.id"
          type="button"
          :title="val.value"
          :class="[
            'h-10 w-10 rounded-full border-2 shadow-sm transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2',
            selectedIds.includes(val.id)
              ? 'border-primary-600 ring-2 ring-primary-600 ring-offset-2 scale-105'
              : 'border-slate-200 hover:border-slate-400 dark:border-slate-700',
            !isAvailable(val.id) ? 'opacity-30 cursor-not-allowed' : 'cursor-pointer',
          ]"
          :style="{ backgroundColor: val.swatch || '#ccc' }"
          :disabled="!isAvailable(val.id)"
          @click="selectValue(attr.id, val.id)"
          :aria-label="val.value"
        ></button>
      </div>

      <!-- Image swatches -->
      <div v-else-if="attr.input_type === 'image'" class="flex flex-wrap gap-2.5">
        <button
          v-for="val in attr.values"
          :key="val.id"
          type="button"
          :title="val.value"
          :class="[
            'h-12 w-12 overflow-hidden rounded-xl border-2 bg-cover bg-center shadow-sm transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2',
            selectedIds.includes(val.id)
              ? 'border-primary-600 ring-2 ring-primary-600 ring-offset-1 scale-105'
              : 'border-slate-200 hover:border-slate-400 dark:border-slate-700',
            !isAvailable(val.id) ? 'opacity-30 cursor-not-allowed' : 'cursor-pointer',
          ]"
          :style="val.swatch ? { backgroundImage: `url(${val.swatch})` } : {}"
          :disabled="!isAvailable(val.id)"
          @click="selectValue(attr.id, val.id)"
          :aria-label="val.value"
        >
          <span v-if="!val.swatch" class="flex h-full w-full items-center justify-center text-xs font-bold text-slate-500 dark:text-slate-400">{{ val.value.charAt(0) }}</span>
        </button>
      </div>

      <!-- Select dropdown -->
      <div v-else>
        <select
          :value="selectedValueForAttr(attr.id)"
          class="block min-h-[44px] w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-sm font-semibold text-slate-900 shadow-sm transition hover:border-slate-400 focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20 dark:border-slate-700 dark:bg-slate-900 dark:text-white"
          @change="selectValue(attr.id, Number($event.target.value))"
        >
          <option value="">Select {{ attr.name }}</option>
          <option
            v-for="val in attr.values"
            :key="val.id"
            :value="val.id"
            :disabled="!isAvailable(val.id)"
          >
            {{ val.value }}
          </option>
        </select>
      </div>
    </div>

    <!-- Selected state display -->
    <div v-if="variation" class="rounded-2xl border border-slate-100 bg-slate-50 p-4 dark:border-slate-800 dark:bg-slate-900/50">
      <div class="flex items-center gap-3">
        <div class="text-xl font-black text-slate-900 dark:text-white">
          <template v-if="variation.sale_price && variation.sale_price < variation.price">
            <span class="text-slate-400 line-through text-sm font-normal">৳{{ variation.price }}</span>
            <span class="ml-2 text-primary-600 dark:text-primary-400">৳{{ variation.sale_price }}</span>
          </template>
          <template v-else>
            ৳{{ variation.price }}
          </template>
        </div>
        <span v-if="variation.quantity <= 0" class="inline-flex items-center rounded-full bg-rose-50 px-3 py-1 text-xs font-bold text-rose-700 ring-1 ring-inset ring-rose-600/20 dark:bg-rose-950/50 dark:text-rose-400">Out of Stock</span>
        <span v-else-if="variation.quantity < 10" class="inline-flex items-center rounded-full bg-amber-50 px-3 py-1 text-xs font-bold text-amber-700 ring-1 ring-inset ring-amber-600/20 dark:bg-amber-950/50 dark:text-amber-400">Only {{ variation.quantity }} left in stock</span>
        <span v-else class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-700 ring-1 ring-inset ring-emerald-600/20 dark:bg-emerald-950/50 dark:text-emerald-400">
          <i class="ri-checkbox-circle-line"></i>
          <span>In Stock</span>
        </span>
      </div>
      <p v-if="variation.sku" class="mt-1 text-xs font-medium text-slate-400">SKU: {{ variation.sku }}</p>
    </div>

    <div v-else-if="Object.keys(allAttributes).length > 0" class="text-xs font-medium text-slate-500 dark:text-slate-400">
      Please select all product options to view pricing and stock.
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
