<template>
    <li class="block">
        <!-- Menu Link Item -->

        <Link v-if="menuItem.label && menuItem.link && (!menuItem.permission || can(menuItem.permission))"
            :href="menuItem.link"
            class="group flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-all duration-200 cursor-pointer"
            :class="[
                isActive(menuItem.link)
                    ? (depth > 0
                        ? 'bg-red-500 text-white shadow-md shadow-blue-500/20'
                        : 'bg-linear-to-r from-red-600 to-red-500 text-white shadow-lg shadow-red-500/25')
                    : 'text-slate-400 hover:text-white',
                depth > 0 ? 'py-2 text-[13px]' : ''
            ]">
            <!-- Icon -->
            <i :class="menuItem.icon || 'ri-circle-line'" class="text-lg transition-colors duration-200"
                :style="{ fontSize: depth > 0 ? '14px' : '18px' }"></i>

            <span class="truncate flex-1">{{ __(menuItem.label) }}</span>

            <!-- Badge -->
            <span v-if="menuItem.badge"
                class="flex h-5 min-w-5 items-center justify-center rounded-full px-1.5 text-[10px] font-bold" :class="isActive(menuItem.link)
                    ? 'bg-white/20 text-white'
                    : 'bg-blue-500/20 text-blue-400'">
                {{ menuItem.badge }}
            </span>

        </Link>

        <!-- Nested children -->
        <ul v-if="menuItem.children && menuItem.children.length > 0" class="mt-0.5 space-y-0.5">
            <app-menu-item v-for="(childItem, childIndex) in menuItem.children" :key="childIndex" :menu-item="childItem"
                :depth="depth + 1" />
        </ul>

        <slot />
    </li>
</template>

<script setup>
import { usePage, Link } from '@inertiajs/vue3'
import useAuthCan from '@/Composables/useAuthCan'

const props = defineProps({
    menuItem: {
        type: Object,
        default: () => { }
    },
    depth: {
        type: Number,
        default: 0
    }
})

const { can } = useAuthCan()
const page = usePage()

// Check if current route matches the menu item link
function isActive(link) {
    if (!link) return false
    const currentUrl = page.url

    // Extract pathname from link (which may be a full URL from route())
    let linkPath = link
    try {
        // If link is a full URL, extract just the pathname
        if (link.startsWith('http://') || link.startsWith('https://')) {
            linkPath = new URL(link).pathname
        }
    } catch (e) {
        // If URL parsing fails, use the link as-is
        linkPath = link
    }

    // Remove query strings for comparison
    const cleanUrl = currentUrl.split('?')[0]
    const cleanLink = linkPath.split('?')[0]

    // Exact match only
    return cleanUrl === cleanLink
}
</script>