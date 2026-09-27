<template>
    <button
        type="button"
        role="switch"
        :aria-checked="modelValue"
        :disabled="disabled"
        class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer items-center rounded-full transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-skin-primary-8 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
        :class="modelValue ? 'bg-skin-primary-10' : 'bg-skin-neutral-5'"
        @click="toggle"
    >
        <span
            class="inline-block h-4 w-4 transform rounded-full bg-white shadow-sm transition-transform duration-200"
            :class="modelValue ? 'translate-x-6' : 'translate-x-1'"
        />
    </button>
</template>

<script setup>
const props = defineProps({
    modelValue: { type: Boolean, default: false },
    disabled: { type: Boolean, default: false },
})

const emit = defineEmits(['update:modelValue'])

function toggle() {
    if (props.disabled) return
    emit('update:modelValue', !props.modelValue)
}
</script>
