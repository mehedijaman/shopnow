<template>
    <nav aria-label="Breadcrumb">
        <ol class="flex items-center text-sm">
            <!-- Home -->
            <li>

                <Link :href="homeHref || route('dashboard.index')"
                    class="text-slate-400 hover:text-blue-600 dark:text-slate-500 dark:hover:text-blue-400 transition-colors">
                    <i class="ri-home-5-line"></i>

                </Link>
            </li>

            <template v-for="(item, index) in items" :key="item.label">
                <!-- Separator -->
                <li class="mx-2 text-slate-300 dark:text-slate-600">/</li>

                <!-- Item -->
                <li>

                    <Link v-if="item.href && index !== items.length - 1" :href="item.href"
                        class="text-slate-500 hover:text-blue-600 dark:text-slate-400 dark:hover:text-blue-400 transition-colors">
                        {{ __(item.label) }}

                    </Link>
                    <span v-else class="font-medium text-slate-900 dark:text-white">
                        {{ __(item.label) }}
                    </span>
                </li>
            </template>
        </ol>
    </nav>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'

defineProps({
    items: {
        type: Array,
        default: () => []
    },
    homeHref: {
        type: String,
        default: null
    }
})
</script>