<template>
    <div v-if="parsedBrands.length">
        <div class="mb-6 flex flex-col gap-3 sm:mb-8 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center justify-center gap-3">
                <span class="h-1.5 w-8 rounded-full bg-primary-600"></span>
                <h2 class="text-xl font-extrabold tracking-tight text-gray-900 dark:text-white sm:text-2xl">
                    Our Brands
                </h2>
                <span class="h-1.5 w-8 rounded-full bg-primary-600"></span>
            </div>
            <a :href="brandsUrl"
                class="inline-flex w-full items-center justify-center gap-1 rounded-lg bg-primary-600 px-4 py-2 text-sm font-medium text-white hover:bg-primary-700 dark:bg-primary-600 dark:hover:bg-primary-700 sm:w-auto">
                View All
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-4 w-4">
                    <path fill-rule="evenodd"
                        d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z"
                        clip-rule="evenodd" />
                </svg>
            </a>
        </div>

        <div ref="container" class="relative select-none overflow-hidden">
            <div ref="track" class="flex transition-transform duration-300 ease-out"
                :style="{ transform: `translateX(-${currentPage * 100}%)` }" @mousedown="onDragStart"
                @mousemove="onDragMove" @mouseup="onDragEnd" @mouseleave="onDragEnd" @touchstart.prevent="onTouchStart"
                @touchmove.prevent="onTouchMove" @touchend="onTouchEnd">
                <div v-for="page in pages" :key="page" class="grid w-full shrink-0 grid-cols-2 gap-4 lg:grid-cols-4">
                    <a v-for="brand in pageItems(page)" :key="brand.id" :href="`/brand/${brand.id}/${brand.slug}`"
                        class="block">
                        <img v-if="brand.image_url" :src="brand.image_url" :alt="brand.name"
                            class="w-full transition-transform duration-300 hover:scale-105" />
                        <span v-else
                            class="flex aspect-[3/2] items-center justify-center text-lg font-semibold text-gray-500 dark:text-gray-400">{{
                                brand.name }}</span>
                    </a>
                </div>
            </div>

            <button v-if="totalPages > 1"
                class="absolute left-1 top-1/2 z-10 flex h-8 w-8 -translate-y-1/2 items-center justify-center rounded-full bg-white/90 shadow-md transition hover:bg-white focus:outline-none dark:bg-gray-700/90 dark:hover:bg-gray-700 sm:-left-4 sm:h-10 sm:w-10"
                aria-label="Previous brands" @click="prevPage">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-600 dark:text-gray-300 sm:h-5 sm:w-5"
                    viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd"
                        d="M12.79 5.23a.75.75 0 01-.02 1.06L8.832 10l3.938 3.71a.75.75 0 11-1.04 1.08l-4.5-4.25a.75.75 0 010-1.08l4.5-4.25a.75.75 0 011.06.02z"
                        clip-rule="evenodd" />
                </svg>
            </button>

            <button v-if="totalPages > 1"
                class="absolute right-1 top-1/2 z-10 flex h-8 w-8 -translate-y-1/2 items-center justify-center rounded-full bg-white/90 shadow-md transition hover:bg-white focus:outline-none dark:bg-gray-700/90 dark:hover:bg-gray-700 sm:-right-4 sm:h-10 sm:w-10"
                aria-label="Next brands" @click="nextPage">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-600 dark:text-gray-300 sm:h-5 sm:w-5"
                    viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd"
                        d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z"
                        clip-rule="evenodd" />
                </svg>
            </button>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue'

const props = defineProps({
    brands: {
        type: [String, Array],
        default: () => [],
    },
})

const brandsUrl = '/brands'

const parsedBrands = computed(() => {
    if (Array.isArray(props.brands)) {
        return props.brands
    }
    try {
        return JSON.parse(props.brands)
    } catch {
        return []
    }
})

const pageSize = 4
const currentPage = ref(0)

const totalPages = computed(() => Math.ceil(parsedBrands.value.length / pageSize))

const pages = computed(() => Array.from({ length: totalPages.value }, (_, i) => i))

const pageItems = (page) => {
    const start = page * pageSize
    return parsedBrands.value.slice(start, start + pageSize)
}

const nextPage = () => {
    currentPage.value = (currentPage.value + 1) % totalPages.value
}

const prevPage = () => {
    currentPage.value = (currentPage.value - 1 + totalPages.value) % totalPages.value
}

const container = ref(null)
const track = ref(null)

let isDragging = false
let startX = 0
let currentX = 0
let isSwiping = false

const onDragStart = (e) => {
    isDragging = true
    startX = e.clientX
    currentX = startX
    track.value.style.transition = 'none'
}

const onDragMove = (e) => {
    if (!isDragging) return
    currentX = e.clientX
    const diff = currentX - startX
    if (Math.abs(diff) > 10) {
        isSwiping = true
    }
}

const onDragEnd = () => {
    if (!isDragging) return
    isDragging = false
    track.value.style.transition = ''

    if (isSwiping) {
        const diff = currentX - startX
        if (Math.abs(diff) > 50) {
            if (diff < 0) {
                nextPage()
            } else {
                prevPage()
            }
        }
        isSwiping = false
    }
}

const onTouchStart = (e) => {
    isDragging = true
    startX = e.touches[0].clientX
    currentX = startX
    track.value.style.transition = 'none'
}

const onTouchMove = (e) => {
    if (!isDragging) return
    currentX = e.touches[0].clientX
    const diff = currentX - startX
    if (Math.abs(diff) > 10) {
        isSwiping = true
    }
}

const onTouchEnd = () => {
    if (!isDragging) return
    isDragging = false
    track.value.style.transition = ''

    if (isSwiping) {
        const diff = currentX - startX
        if (Math.abs(diff) > 50) {
            if (diff < 0) {
                nextPage()
            } else {
                prevPage()
            }
        }
        isSwiping = false
    }
}
</script>
