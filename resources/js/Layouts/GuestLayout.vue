<template>
    <Head title="Modular"></Head>
    <main class="min-h-screen bg-skin-neutral-3">
        <article>
            <AppFlashMessage />
            <slot></slot>
        </article>
    </main>
</template>

<script setup>
import { Head } from '@inertiajs/vue3'
</script>
