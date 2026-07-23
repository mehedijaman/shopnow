<template>
    <AppLink href="#">
        <img
            :src="logoSrc"
            :alt="branding.site_name"
            class="h-20 w-auto max-w-full object-contain mx-auto"
            @error="onImageError"
        />
    </AppLink>
</template>

<script setup>
import { computed, ref, watch } from 'vue'
import { usePage } from '@inertiajs/vue3'

const page = usePage()
const branding = computed(() => page.props.branding ?? { site_name: 'ShopNow', logo_url: null })

const imageError = ref(false)

watch(() => branding.value.logo_url, () => {
    imageError.value = false
})

const logoSrc = computed(() => {
    if (imageError.value || !branding.value.logo_url) {
        return '/logo.png'
    }
    return branding.value.logo_url
})

const onImageError = () => {
    imageError.value = true
}
</script>
