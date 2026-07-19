<template>
    <div v-if="parsedBrands.length" class="group/brands relative">
        <!-- Header Row -->
        <div class="mb-6 flex flex-col gap-3 sm:mb-8 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center justify-center gap-3">
                <span class="h-1.5 w-8 rounded-full bg-primary-600"></span>
                <h2 class="text-xl font-extrabold tracking-tight text-gray-900 dark:text-white sm:text-2xl">
                    Our Brands
                </h2>
                <span class="h-1.5 w-8 rounded-full bg-primary-600"></span>
            </div>
            <a
                :href="brandsUrl"
                class="inline-flex w-full items-center justify-center gap-1 rounded-lg bg-primary-600 px-4 py-2 text-sm font-medium text-white transition-all duration-300 hover:bg-primary-700 dark:bg-primary-600 dark:hover:bg-primary-700 sm:w-auto"
            >
                View All
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-4 w-4">
                    <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd" />
                </svg>
            </a>
        </div>

        <!-- Carousel Container -->
        <div
            ref="container"
            class="relative select-none overflow-hidden"
            @mouseenter="pauseTimer"
            @mouseleave="startTimer"
        >
            <div
                ref="track"
                class="flex transition-transform duration-500 ease-out"
                :style="{ transform: `translateX(-${currentPage * 100}%)` }"
                @mousedown="onDragStart"
                @mousemove="onDragMove"
                @mouseup="onDragEnd"
                @mouseleave="onDragEnd"
                @touchstart.prevent="onTouchStart"
                @touchmove.prevent="onTouchMove"
                @touchend="onTouchEnd"
            >
                <div
                    v-for="page in pages"
                    :key="page"
                    class="grid w-full shrink-0 gap-4"
                    :class="[
                        pageSize === 2 ? 'grid-cols-2' : '',
                        pageSize === 4 ? 'grid-cols-4' : '',
                        pageSize === 6 ? 'grid-cols-6' : ''
                    ]"
                >
                    <a
                        v-for="brand in pageItems(page)"
                        :key="brand.id"
                        :href="`/brand/${brand.id}/${brand.slug}`"
                        class="flex aspect-[3/2] items-center justify-center rounded-xl border border-gray-100 bg-white p-4 transition-all duration-300 hover:border-primary-500 hover:shadow-md dark:border-gray-800 dark:bg-gray-900 dark:hover:border-primary-400"
                    >
                        <img
                            v-if="brand.image_url"
                            :src="brand.image_url"
                            :alt="brand.name"
                            class="max-h-full max-w-full object-contain transition-transform duration-300 hover:scale-105"
                        />
                        <span
                            v-else
                            class="text-center text-sm font-semibold text-gray-500 dark:text-gray-400 sm:text-base"
                        >
                            {{ brand.name }}
                        </span>
                    </a>
                </div>
            </div>

            <!-- Left navigation button -->
            <button
                v-if="totalPages > 1"
                class="absolute left-2 top-1/2 z-10 flex h-9 w-9 -translate-y-1/2 items-center justify-center rounded-full bg-white/95 shadow-lg transition-all duration-300 hover:bg-white hover:scale-105 opacity-0 group-hover/brands:opacity-100 dark:bg-gray-800/95 dark:hover:bg-gray-700"
                aria-label="Previous brands"
                @click="prevPage"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-600 dark:text-gray-300" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M12.79 5.23a.75.75 0 01-.02 1.06L8.832 10l3.938 3.71a.75.75 0 11-1.04 1.08l-4.5-4.25a.75.75 0 010-1.08l4.5-4.25a.75.75 0 011.06.02z" clip-rule="evenodd" />
                </svg>
            </button>

            <!-- Right navigation button -->
            <button
                v-if="totalPages > 1"
                class="absolute right-2 top-1/2 z-10 flex h-9 w-9 -translate-y-1/2 items-center justify-center rounded-full bg-white/95 shadow-lg transition-all duration-300 hover:bg-white hover:scale-105 opacity-0 group-hover/brands:opacity-100 dark:bg-gray-800/95 dark:hover:bg-gray-700"
                aria-label="Next brands"
                @click="nextPage"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-600 dark:text-gray-300" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd" />
                </svg>
            </button>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'

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

// Dynamic page sizing based on viewport width
const pageSize = ref(6)

const updatePageSize = () => {
    if (typeof window !== 'undefined') {
        const width = window.innerWidth
        if (width < 640) {
            pageSize.value = 2
        } else if (width < 1024) {
            pageSize.value = 4
        } else {
            pageSize.value = 6
        }
    }
}

const totalPages = computed(() => {
    if (!parsedBrands.value.length) return 0
    return Math.ceil(parsedBrands.value.length / pageSize.value)
})

const currentPage = ref(0)

const pages = computed(() => {
    const total = totalPages.value
    return Array.from({ length: total }, (_, i) => i)
})

const pageItems = (page) => {
    const start = page * pageSize.value
    return parsedBrands.value.slice(start, start + pageSize.value)
}

const nextPage = () => {
    if (totalPages.value <= 1) return
    currentPage.value = (currentPage.value + 1) % totalPages.value
    resetTimer()
}

const prevPage = () => {
    if (totalPages.value <= 1) return
    currentPage.value = (currentPage.value - 1 + totalPages.value) % totalPages.value
    resetTimer()
}

// Auto-scroll functionality
let autoScrollTimer = null

const startTimer = () => {
    if (totalPages.value > 1) {
        autoScrollTimer = setInterval(() => {
            currentPage.value = (currentPage.value + 1) % totalPages.value
        }, 5000)
    }
}

const pauseTimer = () => {
    if (autoScrollTimer) {
        clearInterval(autoScrollTimer)
        autoScrollTimer = null
    }
}

const resetTimer = () => {
    pauseTimer()
    startTimer()
}

// Drag & Swipe handling
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
    if (track.value) {
        track.value.style.transition = 'none'
    }
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
    if (track.value) {
        track.value.style.transition = ''
    }

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
    if (track.value) {
        track.value.style.transition = 'none'
    }
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
    if (track.value) {
        track.value.style.transition = ''
    }

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

onMounted(() => {
    updatePageSize()
    window.addEventListener('resize', updatePageSize)
    startTimer()
})

onUnmounted(() => {
    window.removeEventListener('resize', updatePageSize)
    pauseTimer()
})
</script>
