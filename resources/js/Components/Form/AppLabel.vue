<template>
    <label class="block text-sm font-medium text-skin-neutral-12" :class="$attrs.class">{{ value }}<slot></slot></label>
</template>

<script setup>
defineProps({
    value: { type: String, default: '' },
})
</script>
