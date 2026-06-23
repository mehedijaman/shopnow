<template>
    <!-- Search Box -->
    <div class="mb-4">
        <div class="relative">
            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                <i class="ri-search-line text-slate-500 text-sm"></i>
            </div>
            <input id="sidebar-search" v-model="searchTerm" type="text" :placeholder="__('Search menu...')"
                class="w-full rounded-lg bg-slate-800/50 border border-slate-700/50 pl-9 pr-8 py-2 text-sm text-slate-300 placeholder-slate-500 focus:border-blue-500/50 focus:ring-1 focus:ring-blue-500/30 focus:outline-none transition-all duration-200" />
            <button v-if="searchTerm" type="button"
                class="absolute inset-y-0 right-0 flex items-center pr-3 text-slate-500 hover:text-slate-300 transition-colors duration-200"
                @click="searchTerm = ''">
                <i class="ri-close-circle-fill"></i>
            </button>
        </div>
    </div>

    <!-- Menu Sections -->
    <nav class="space-y-1">
        <div v-for="(item, index) in filteredItems" :key="index">
            <AppMenuSection :item="item" />
        </div>
    </nav>

    <!-- No Results -->
    <div v-if="searchTerm && filteredItems.length === 0" class="text-center py-8">
        <div class="inline-flex h-10 w-10 items-center justify-center rounded-lg bg-slate-800/50 mb-3">
            <i class="ri-search-eye-line text-lg text-slate-500"></i>
        </div>
        <p class="text-sm text-slate-400">No results found</p>
        <p class="text-xs text-slate-500 mt-1">Try a different search term</p>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { usePage } from '@inertiajs/vue3'

const props = defineProps({
    items: {
        type: Array,
        default: () => []
    }
})

const searchTerm = ref('')

// Get auth data
const page = usePage()
const isSuperAdmin = computed(() => page.props.auth.isSuperAdmin)
const isTenant = computed(() => page.props.auth.isTenant)

// Computed property to filter items based on search term and user role
const filteredItems = computed(() => {
    // Recursive function to filter by role (both parent and children)
    const filterByRole = (items) => {
        return items.reduce((acc, item) => {
            // Check role-based visibility
            let shouldShow = true

            // If item is marked as superAdminOnly, only show to super admins
            if (item.superAdminOnly && !isSuperAdmin.value) {
                shouldShow = false
            }


            if (!shouldShow) {
                return acc
            }

            // If item has children, recursively filter them
            let filteredChildren = item.children
            if (item.children && item.children.length > 0) {
                filteredChildren = filterByRole(item.children)

                // If all children are filtered out, don't show the parent
                if (filteredChildren.length === 0 && item.children.length > 0) {
                    return acc
                }
            }

            acc.push({
                ...item,
                children: filteredChildren
            })

            return acc
        }, [])
    }

    // First filter by role
    let roleFilteredItems = filterByRole(props.items)

    // Then filter by search term
    if (!searchTerm.value) {
        return roleFilteredItems
    }

    const term = searchTerm.value.toLowerCase()

    // Recursive function to filter items and their children by search term
    const filterBySearch = (items) => {
        return items.reduce((acc, item) => {
            const isParentMatch = item.label.toLowerCase().includes(term)
            let filteredChildren = []

            if (item.children) {
                if (isParentMatch) {
                    // If parent matches, include all children
                    filteredChildren = item.children
                } else {
                    // Else, filter children based on search term
                    filteredChildren = filterBySearch(item.children)
                }
            }

            // Include the item if the parent matches or any of its children match
            if (
                isParentMatch ||
                (filteredChildren && filteredChildren.length)
            ) {
                acc.push({
                    ...item,
                    // If parent matches, include all children; else, include filtered children
                    children: isParentMatch ? item.children : filteredChildren
                })
            }

            return acc
        }, [])
    }

    return filterBySearch(roleFilteredItems)
})
</script>