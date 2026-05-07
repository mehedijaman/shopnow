<template>
    <div>
        <AppLabel :value="label" />
        <div class="mt-2 space-y-2">
            <div
                v-for="(item, index) in modelValue"
                :key="index"
                class="flex items-center gap-2"
            >
                <AppInputText
                    :model-value="item"
                    :placeholder="placeholder"
                    class="flex-1"
                    @update:model-value="update(index, $event)"
                />
                <button
                    type="button"
                    class="flex h-9 w-9 shrink-0 items-center justify-center rounded-md text-skin-neutral-9 transition-colors hover:bg-red-50 hover:text-red-600"
                    @click="remove(index)"
                >
                    <i class="ri-close-line text-lg"></i>
                </button>
            </div>
        </div>
        <button
            type="button"
            class="mt-3 flex items-center gap-1 text-sm text-skin-primary-10 hover:text-skin-primary-11"
            @click="add"
        >
            <i class="ri-add-line"></i>
            {{ addLabel }}
        </button>
    </div>
</template>

<script setup>
const props = defineProps({
    modelValue: { type: Array, default: () => [] },
    label: { type: String, default: '' },
    placeholder: { type: String, default: '' },
    addLabel: { type: String, default: 'Add Item' },
})

const emit = defineEmits(['update:modelValue'])

const update = (index, value) => {
    const updated = [...props.modelValue]
    updated[index] = value
    emit('update:modelValue', updated)
}

const remove = (index) => {
    emit('update:modelValue', props.modelValue.filter((_, i) => i !== index))
}

const add = () => {
    emit('update:modelValue', [...props.modelValue, ''])
}
</script>
