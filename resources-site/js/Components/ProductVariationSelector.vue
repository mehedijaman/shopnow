<template>
    <div class="space-y-2 sm:space-y-2.5">
        <div v-for="attr in attributes" :key="attr.id" class="space-y-1">
            <div
                class="flex items-center justify-between text-[10px] font-semibold text-gray-700 dark:text-gray-300 sm:text-xs"
            >
                <span
                    class="text-[9px] font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400 sm:text-[10px]"
                    >{{ attr.name }}</span
                >
                <span
                    v-if="getSelectedValueName(attr.id)"
                    class="max-w-[100px] truncate text-right font-bold capitalize text-primary-600 dark:text-primary-400"
                >
                    {{ getSelectedValueName(attr.id) }}
                </span>
            </div>

            <!-- Color swatches -->
            <div
                v-if="attr.input_type === 'color'"
                class="flex flex-wrap gap-1"
            >
                <button
                    v-for="val in attr.values"
                    :key="val.id"
                    type="button"
                    :title="val.value"
                    :class="[
                        'h-5 w-5 rounded-full border transition-all duration-200 focus:outline-none sm:h-6 sm:w-6',
                        selectedIds.includes(Number(val.id))
                            ? 'scale-110 border-primary-600 shadow-sm ring-2 ring-primary-500/40 ring-offset-1 dark:border-primary-400'
                            : 'border-gray-200 hover:scale-105 hover:border-gray-400 dark:border-gray-700',
                        !isAvailable(val.id)
                            ? 'cursor-not-allowed opacity-30'
                            : 'cursor-pointer'
                    ]"
                    :style="{ backgroundColor: val.swatch || '#ccc' }"
                    :disabled="!isAvailable(val.id)"
                    @click="selectValue(attr.id, val.id)"
                ></button>
            </div>

            <!-- Image swatches -->
            <div
                v-else-if="attr.input_type === 'image'"
                class="flex flex-wrap gap-1"
            >
                <button
                    v-for="val in attr.values"
                    :key="val.id"
                    type="button"
                    :title="val.value"
                    :class="[
                        'h-8 w-8 overflow-hidden rounded-md border bg-cover bg-center transition-all duration-200 focus:outline-none sm:h-9 sm:w-9',
                        selectedIds.includes(Number(val.id))
                            ? 'scale-105 border-primary-600 shadow-sm ring-2 ring-primary-500/40 ring-offset-1 dark:border-primary-400'
                            : 'border-gray-200 hover:border-gray-400 dark:border-gray-700',
                        !isAvailable(val.id)
                            ? 'cursor-not-allowed opacity-30'
                            : 'cursor-pointer'
                    ]"
                    :style="
                        val.swatch
                            ? { backgroundImage: `url(${val.swatch})` }
                            : {}
                    "
                    :disabled="!isAvailable(val.id)"
                    @click="selectValue(attr.id, val.id)"
                >
                    <span
                        v-if="!val.swatch"
                        class="flex h-full w-full items-center justify-center text-[10px] font-bold text-gray-400"
                        >{{ val.value.charAt(0) }}</span
                    >
                </button>
            </div>

            <!-- Clickable option chips -->
            <div v-else class="flex flex-wrap gap-1">
                <button
                    v-for="val in attr.values"
                    :key="val.id"
                    type="button"
                    :class="[
                        'rounded-md border px-2 py-0.5 text-[10px] font-semibold transition-all duration-200 focus:outline-none sm:px-2.5 sm:py-1 sm:text-xs',
                        selectedIds.includes(Number(val.id))
                            ? 'border-primary-600 bg-primary-600 text-white shadow-sm ring-1 ring-primary-500/30'
                            : 'dark:hover:bg-gray-750 border-gray-200 bg-white text-gray-700 hover:border-gray-300 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200',
                        !isAvailable(val.id)
                            ? 'cursor-not-allowed opacity-30'
                            : 'cursor-pointer'
                    ]"
                    :disabled="!isAvailable(val.id)"
                    @click="selectValue(attr.id, val.id)"
                >
                    {{ val.value }}
                </button>
            </div>
        </div>

        <!-- Selected state display -->
        <div v-if="variation" class="pt-0.5">
            <div class="flex items-center gap-1.5">
                <div
                    class="text-xs font-extrabold text-gray-900 dark:text-white sm:text-sm"
                >
                    <template
                        v-if="
                            variation.sale_price &&
                            variation.sale_price < variation.price
                        "
                    >
                        <span
                            class="mr-1 text-[10px] font-normal text-gray-400 line-through sm:text-xs"
                            >৳{{ variation.price }}</span
                        >
                        <span class="text-red-600 dark:text-red-400"
                            >৳{{ variation.sale_price }}</span
                        >
                    </template>
                    <template v-else> ৳{{ variation.price }} </template>
                </div>
                <span
                    v-if="!variation.active || variation.quantity <= 0"
                    class="rounded-full bg-red-50 px-1.5 py-0.5 text-[9px] font-bold uppercase tracking-wider text-red-600 dark:bg-red-950/40 dark:text-red-400 sm:text-[10px]"
                    >{{ __('site.product.out_of_stock') }}</span
                >
                <span
                    v-else-if="variation.quantity < 10"
                    class="rounded-full bg-amber-50 px-1.5 py-0.5 text-[9px] font-bold uppercase tracking-wider text-amber-600 dark:bg-amber-950/40 dark:text-amber-400 sm:text-[10px]"
                    >{{
                        __('site.product.only_left', {
                            count: variation.quantity
                        })
                    }}</span
                >
                <span
                    v-else
                    class="rounded-full bg-emerald-50 px-1.5 py-0.5 text-[9px] font-bold uppercase tracking-wider text-emerald-600 dark:bg-emerald-950/40 dark:text-emerald-400 sm:text-[10px]"
                    >{{ __('site.product.in_stock') }}</span
                >
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed, ref, watch } from 'vue'

const props = defineProps({
    allAttributes: { type: Object, default: () => ({}) },
    variations: { type: Array, default: () => [] },
    parentProduct: { type: Object, default: () => ({}) }
})

const emit = defineEmits(['variation-change'])

const selected = ref({})

const attributes = computed(() => {
    return Object.values(props.allAttributes).filter((a) => a.values?.length)
})

const selectedIds = computed(() =>
    Object.values(selected.value).filter(Boolean).map(Number)
)

const isAvailable = (valueId) => {
    const targetId = Number(valueId)
    return props.variations.some((v) => {
        const vIds = (v.attribute_value_ids || []).map(Number)
        return vIds.includes(targetId)
    })
}

const matchedVariation = computed(() => {
    const selectedValues = Object.values(selected.value)
        .filter(Boolean)
        .map(Number)
        .sort((a, b) => a - b)
    if (
        selectedValues.length === 0 ||
        selectedValues.length !== attributes.value.length
    )
        return null

    return (
        props.variations.find((v) => {
            const vIds = (v.attribute_value_ids || [])
                .map(Number)
                .sort((a, b) => a - b)
            return (
                vIds.length === selectedValues.length &&
                vIds.every((id, index) => id === selectedValues[index])
            )
        }) || null
    )
})

const variation = computed(() => {
    return matchedVariation.value
})

watch(variation, (v) => {
    emit('variation-change', v)
})

const selectValue = (attrId, valId) => {
    const current = selected.value[attrId]
    if (current != null && Number(current) === Number(valId)) {
        selected.value[attrId] = null
    } else {
        selected.value[attrId] = Number(valId)
    }
}

const selectedValueForAttr = (attrId) => {
    return selected.value[attrId] || ''
}

const getSelectedValueName = (attrId) => {
    const valId = selected.value[attrId]
    if (valId == null) return ''
    const attr = attributes.value.find((a) => Number(a.id) === Number(attrId))
    const val = attr?.values?.find((v) => Number(v.id) === Number(valId))
    return val?.value || ''
}

const reset = () => {
    selected.value = {}
}

defineExpose({ reset })
</script>
