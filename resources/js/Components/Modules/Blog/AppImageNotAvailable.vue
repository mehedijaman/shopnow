<script setup>
import { ref, computed } from 'vue'
import { usePage } from '@inertiajs/vue3'

const page = usePage()
const logoUrl = computed(() => page.props.branding?.logo_url || '/logo.png')
const siteName = computed(() => page.props.branding?.site_name || 'ShopNow')
const imageError = ref(false)

function onImageError() {
    imageError.value = true
}
</script>

<template>
    <div
        class="flex h-10 w-10 items-center justify-center overflow-hidden rounded-sm bg-skin-neutral-4 p-1 border border-skin-neutral-5">
        <img v-if="logoUrl && !imageError" :src="logoUrl" :alt="siteName" class="max-h-full max-w-full object-contain"
            @error="onImageError" />
        <i v-else class="ri-store-2-line text-xs text-skin-neutral-9"></i>
    </div>
</template>
