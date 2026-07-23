<template>
    <Head>
        <link rel="icon" :href="branding.favicon_url || '/favicon.svg'" />
    </Head>
    <section class="flex h-screen flex-col items-center justify-center">
        <slot></slot>
    </section>
</template>

<script setup>
import { computed } from 'vue'
import { Head, usePage } from '@inertiajs/vue3'

const page = usePage()
const branding = computed(() => page.props.branding ?? { favicon_url: null })
</script>
