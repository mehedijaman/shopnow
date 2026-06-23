<template>
    <!-- Section Header (collapsible) -->
    <div v-if="item.label && !item.link && item.children && item.children.length > 0" class="mb-0.5">
        <button type="button"
            class="w-full group flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors duration-200 cursor-pointer"
            :class="hasActiveChild ? 'text-white bg-slate-800/80' : 'text-slate-400 hover:text-white hover:bg-slate-800/50'"
            @click="toggleSection">
            <i :class="item.icon || 'ri-folder-3-line'" class="text-lg"></i>
            <span class="flex-1 text-left text-sm font-medium">
                {{ __(item.label) }}
            </span>
            <i class="text-sm transition-transform duration-200"
                :class="isExpanded ? 'ri-arrow-up-s-line' : 'ri-arrow-down-s-line'"></i>
        </button>

        <!-- Children with smooth animation -->
        <div class="overflow-hidden transition-all duration-300 ease-out"
            :style="{ maxHeight: isExpanded ? childrenHeight + 'px' : '0px', opacity: isExpanded ? 1 : 0 }">
            <ul ref="childrenRef" class="mt-0.5 space-y-0.5 ml-5 pl-2 border-l border-slate-700/50">
                <app-menu-item v-for="(childItem, childIndex) in item.children" :key="childIndex" :menu-item="childItem"
                    :depth="1" />
            </ul>
        </div>
    </div>

    <!-- Single item without children (top level link) -->
    <app-menu-item v-else-if="item.link" :menu-item="item" :depth="0" />
</template>

<script setup>
import { ref, computed, onMounted, watch, nextTick } from 'vue'
import { usePage } from '@inertiajs/vue3'

const props = defineProps({
    item: {
        type: Object,
        required: true
    }
})

const page = usePage()
const childrenRef = ref(null)
const childrenHeight = ref(500)

// Generate a unique key for localStorage based on section label
const storageKey = computed(() => {
    const label = props.item.label || 'section'
    return `sidebar_section_${label.toLowerCase().replace(/\s+/g, '_')}`
})

// Check if any child is active
const hasActiveChild = computed(() => {
    if (!props.item.children) return false

    const isLinkActive = (link) => {
        if (!link) return false
        const currentUrl = page.url.split('?')[0]

        // Extract pathname from link (which may be a full URL from route())
        let linkPath = link
        try {
            if (link.startsWith('http://') || link.startsWith('https://')) {
                linkPath = new URL(link).pathname
            }
        } catch (e) {
            linkPath = link
        }

        const cleanLink = linkPath.split('?')[0]

        // Exact match only
        return currentUrl === cleanLink
    }

    const checkActive = (items) => {
        for (const item of items) {
            if (isLinkActive(item.link)) {
                return true
            }
            if (item.children && checkActive(item.children)) {
                return true
            }
        }
        return false
    }

    return checkActive(props.item.children)
})

// Initialize from localStorage, default to collapsed (false)
const getInitialState = () => {
    // If has active child, always expand
    if (hasActiveChild.value) return true

    // Otherwise check localStorage
    const stored = localStorage.getItem(storageKey.value)
    if (stored !== null) {
        return stored === 'true'
    }
    // Default collapsed
    return false
}

const isExpanded = ref(false)

const updateHeight = () => {
    nextTick(() => {
        if (childrenRef.value) {
            childrenHeight.value = childrenRef.value.scrollHeight + 10
        }
    })
}

onMounted(() => {
    isExpanded.value = getInitialState()
    updateHeight()
})

// Watch for URL changes to auto-expand when navigating to child
watch(() => page.url, () => {
    if (hasActiveChild.value && !isExpanded.value) {
        isExpanded.value = true
        localStorage.setItem(storageKey.value, 'true')
    }
})

// Update height when children change
watch(() => props.item.children, updateHeight, { deep: true })

const toggleSection = () => {
    isExpanded.value = !isExpanded.value
    // Persist to localStorage
    localStorage.setItem(storageKey.value, isExpanded.value.toString())
    updateHeight()
}
</script>