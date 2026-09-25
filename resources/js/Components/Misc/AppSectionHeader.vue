<template>
    <header class="mb-4 sm:mb-6 mt-2 sm:mt-4">
        <nav aria-label="Top">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 sm:gap-4 py-2 sm:py-4">
                <div class="min-w-0 flex-1 space-y-1">
                    <h2 class="text-xl sm:text-2xl font-black text-skin-neutral-12 tracking-tight truncate">{{ title }}</h2>
                    <AppBreadCrumb :items="breadCrumb"></AppBreadCrumb>
                </div>
                <div v-if="$slots.right" class="flex flex-wrap items-center gap-2 sm:gap-3 w-full sm:w-auto shrink-0 justify-start sm:justify-end">
                    <slot name="right" />
                </div>
            </div>
        </nav>
    </header>
</template>

<script setup>
defineProps({
    title: {
        type: String,
        default: '-'
    },
    breadCrumb: {
        type: Array,
        default: () => []
    }
})
</script>
