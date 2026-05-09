<template>
    <AppLink href="#">
        <img
            :src="branding.logo_url ?? '/logo.png'"
            :alt="branding.site_name"
            class="h-24 w-auto object-contain"
        />
    </AppLink>
</template>

<script setup>
import { computed } from 'vue'
import { usePage } from '@inertiajs/vue3'

const page = usePage()
const branding = computed(() => page.props.branding ?? { site_name: 'ShopNow', logo_url: null })
</script>
