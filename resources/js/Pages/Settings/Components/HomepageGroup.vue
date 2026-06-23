<template>
    <div class="divide-y divide-skin-neutral-3">
        <div class="flex items-center justify-between gap-4 py-4">
            <div>
                <p class="text-sm font-medium text-skin-neutral-12">Slider</p>
                <p class="mt-0.5 text-xs text-skin-neutral-9">Show the hero image carousel at the top of the homepage.</p>
            </div>
            <button
                type="button"
                role="switch"
                :aria-checked="form.show_slider"
                class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer items-center rounded-full transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-skin-primary-8 focus:ring-offset-2"
                :class="form.show_slider ? 'bg-skin-primary-10' : 'bg-skin-neutral-5'"
                @click="form.show_slider = !form.show_slider"
            >
                <span
                    class="inline-block h-4 w-4 transform rounded-full bg-white shadow-sm transition-transform duration-200"
                    :class="form.show_slider ? 'translate-x-6' : 'translate-x-1'"
                />
            </button>
        </div>

        <div class="flex items-center justify-between gap-4 py-4">
            <div>
                <p class="text-sm font-medium text-skin-neutral-12">Featured Categories</p>
                <p class="mt-0.5 text-xs text-skin-neutral-9">Show the featured product categories section.</p>
            </div>
            <button
                type="button"
                role="switch"
                :aria-checked="form.show_featured_categories"
                class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer items-center rounded-full transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-skin-primary-8 focus:ring-offset-2"
                :class="form.show_featured_categories ? 'bg-skin-primary-10' : 'bg-skin-neutral-5'"
                @click="form.show_featured_categories = !form.show_featured_categories"
            >
                <span
                    class="inline-block h-4 w-4 transform rounded-full bg-white shadow-sm transition-transform duration-200"
                    :class="form.show_featured_categories ? 'translate-x-6' : 'translate-x-1'"
                />
            </button>
        </div>

        <div class="flex items-center justify-between gap-4 py-4">
            <div>
                <p class="text-sm font-medium text-skin-neutral-12">Blog Section</p>
                <p class="mt-0.5 text-xs text-skin-neutral-9">Show the latest blog posts section.</p>
            </div>
            <button
                type="button"
                role="switch"
                :aria-checked="form.show_blog"
                class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer items-center rounded-full transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-skin-primary-8 focus:ring-offset-2"
                :class="form.show_blog ? 'bg-skin-primary-10' : 'bg-skin-neutral-5'"
                @click="form.show_blog = !form.show_blog"
            >
                <span
                    class="inline-block h-4 w-4 transform rounded-full bg-white shadow-sm transition-transform duration-200"
                    :class="form.show_blog ? 'translate-x-6' : 'translate-x-1'"
                />
            </button>
        </div>

        <div class="flex items-center justify-between gap-4 py-4">
            <div>
                <p class="text-sm font-medium text-skin-neutral-12">Our Brands</p>
                <p class="mt-0.5 text-xs text-skin-neutral-9">Show the brands carousel section on the homepage.</p>
            </div>
            <button
                type="button"
                role="switch"
                :aria-checked="form.show_brands"
                class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer items-center rounded-full transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-skin-primary-8 focus:ring-offset-2"
                :class="form.show_brands ? 'bg-skin-primary-10' : 'bg-skin-neutral-5'"
                @click="form.show_brands = !form.show_brands"
            >
                <span
                    class="inline-block h-4 w-4 transform rounded-full bg-white shadow-sm transition-transform duration-200"
                    :class="form.show_brands ? 'translate-x-6' : 'translate-x-1'"
                />
            </button>
        </div>
    </div>
</template>

<script setup>
import { inject } from 'vue'

defineProps({
    errorsFields: { type: Array, default: () => [] },
})

const form = inject('settingsForm')
</script>

