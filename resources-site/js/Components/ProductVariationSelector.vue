<template>
  <div class="space-y-2 sm:space-y-2.5">
    <div v-for="attr in attributes" :key="attr.id" class="space-y-1">

      <div class="flex items-center justify-between text-[10px] sm:text-xs font-semibold text-gray-700 dark:text-gray-300">
        <span class="uppercase tracking-wider font-bold text-gray-500 dark:text-gray-400 text-[9px] sm:text-[10px]">{{ attr.name }}</span>
        <span v-if="getSelectedValueName(attr.id)" class="text-primary-600 dark:text-primary-400 font-bold capitalize truncate max-w-[100px] text-right">
          {{ getSelectedValueName(attr.id) }}
        </span>
      </div>

      <!-- Color swatches -->
      <div v-if="attr.input_type === 'color'" class="flex flex-wrap gap-1">
        <button v-for="val in attr.values" :key="val.id" type="button" :title="val.value" :class="[
          'h-5 w-5 sm:h-6 sm:w-6 rounded-full border transition-all duration-200 focus:outline-none',
          selectedIds.includes(val.id)
            ? 'border-primary-600 ring-2 ring-primary-500/40 ring-offset-1 scale-110 shadow-sm dark:border-primary-400'
            : 'border-gray-200 hover:border-gray-400 hover:scale-105 dark:border-gray-700',
          !isAvailable(val.id) ? 'opacity-30 cursor-not-allowed' : 'cursor-pointer',
        ]" :style="{ backgroundColor: val.swatch || '#ccc' }" :disabled="!isAvailable(val.id)"
          @click="selectValue(attr.id, val.id)"></button>
      </div>

      <!-- Image swatches -->
      <div v-else-if="attr.input_type === 'image'" class="flex flex-wrap gap-1">
        <button v-for="val in attr.values" :key="val.id" type="button" :title="val.value" :class="[
          'h-8 w-8 sm:h-9 sm:w-9 overflow-hidden rounded-md border bg-cover bg-center transition-all duration-200 focus:outline-none',
          selectedIds.includes(val.id)
            ? 'border-primary-600 ring-2 ring-primary-500/40 ring-offset-1 scale-105 shadow-sm dark:border-primary-400'
            : 'border-gray-200 hover:border-gray-400 dark:border-gray-700',
          !isAvailable(val.id) ? 'opacity-30 cursor-not-allowed' : 'cursor-pointer',
        ]" :style="val.swatch ? { backgroundImage: `url(${val.swatch})` } : {}" :disabled="!isAvailable(val.id)"
          @click="selectValue(attr.id, val.id)">
          <span v-if="!val.swatch" class="flex h-full w-full items-center justify-center text-[10px] font-bold text-gray-400">{{
            val.value.charAt(0) }}</span>
        </button>
      </div>

      <!-- Clickable option chips -->
      <div v-else class="flex flex-wrap gap-1">
        <button v-for="val in attr.values" :key="val.id" type="button" :class="[
          'rounded-md border px-2 py-0.5 text-[10px] sm:px-2.5 sm:py-1 sm:text-xs font-semibold transition-all duration-200 focus:outline-none',
          selectedIds.includes(val.id)
            ? 'border-primary-600 bg-primary-600 text-white shadow-sm ring-1 ring-primary-500/30'
            : 'border-gray-200 bg-white text-gray-700 hover:border-gray-300 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-750',
          !isAvailable(val.id) ? 'opacity-30 cursor-not-allowed' : 'cursor-pointer',
        ]" :disabled="!isAvailable(val.id)"
          @click="selectValue(attr.id, val.id)">
          {{ val.value }}
        </button>
      </div>
    </div>

    <!-- Selected state display -->
    <div v-if="variation" class="pt-0.5">
      <div class="flex items-center gap-1.5">
        <div class="text-xs sm:text-sm font-extrabold text-gray-900 dark:text-white">
          <template v-if="variation.sale_price && variation.sale_price < variation.price">
            <span class="text-[10px] sm:text-xs text-gray-400 line-through font-normal mr-1">৳{{ variation.price }}</span>
            <span class="text-red-600 dark:text-red-400">৳{{ variation.sale_price }}</span>
          </template>
          <template v-else>
            ৳{{ variation.price }}
          </template>
        </div>
        <span v-if="variation.quantity <= 0"
          class="rounded-full bg-red-50 px-1.5 py-0.5 text-[9px] sm:text-[10px] font-bold uppercase tracking-wider text-red-600 dark:bg-red-950/40 dark:text-red-400">Out of Stock</span>
        <span v-else-if="variation.quantity < 10"
          class="rounded-full bg-amber-50 px-1.5 py-0.5 text-[9px] sm:text-[10px] font-bold uppercase tracking-wider text-amber-600 dark:bg-amber-950/40 dark:text-amber-400">Only {{ variation.quantity }} left</span>
        <span v-else class="rounded-full bg-emerald-50 px-1.5 py-0.5 text-[9px] sm:text-[10px] font-bold uppercase tracking-wider text-emerald-600 dark:bg-emerald-950/40 dark:text-emerald-400">In Stock</span>
      </div>
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

const getSelectedValueName = (attrId) => {
  const valId = selected.value[attrId]
  if (!valId) return ''
  const attr = attributes.value.find((a) => a.id === attrId)
  const val = attr?.values?.find((v) => v.id === valId)
  return val?.value || ''
}

const reset = () => {
  selected.value = {}
}

defineExpose({ reset })
</script>
