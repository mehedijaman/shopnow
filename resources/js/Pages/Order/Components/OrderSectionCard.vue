<template>
    <section class="overflow-hidden rounded-2xl bg-skin-neutral-1 border border-skin-neutral-4/80 shadow-xs transition-shadow hover:shadow-md duration-200">
        <div v-if="title || $slots.header || $slots.badge" class="flex items-center justify-between gap-3 border-b border-skin-neutral-4/80 bg-skin-neutral-2/40 px-5 py-4 sm:px-6">
            <div class="flex items-center gap-3.5 min-w-0">
                <span v-if="icon" class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl shadow-2xs" :class="iconClass">
                    <i :class="icon" class="text-lg"></i>
                </span>
                <div class="min-w-0 flex-1">
                    <h3 class="truncate text-sm font-bold uppercase tracking-wider text-skin-neutral-12">{{ title }}</h3>
                    <p v-if="description" class="truncate text-xs text-skin-neutral-9 font-medium mt-0.5">{{ description }}</p>
                </div>
            </div>
            <div v-if="$slots.badge" class="shrink-0">
                <slot name="badge" />
            </div>
        </div>

        <div v-if="$slots.default" :class="flush ? '' : 'p-5 sm:p-6'">
            <slot />
        </div>

        <div v-if="$slots.footer" class="border-t border-skin-neutral-4/80 bg-skin-neutral-2/30 px-5 py-4 sm:px-6">
            <slot name="footer" />
        </div>
    </section>
</template>

<script setup>
defineProps({
    title: {
        type: String,
        required: true,
    },
    description: {
        type: String,
        default: '',
    },
    icon: {
        type: String,
        default: '',
    },
    iconClass: {
        type: String,
        default: 'bg-skin-neutral-3 text-skin-neutral-11',
    },
    flush: {
        type: Boolean,
        default: false,
    },
})
</script>
