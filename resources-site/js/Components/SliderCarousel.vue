<template>
    <div v-if="parsedSliders.length" class="mx-auto max-w-7xl px-6 pt-6 sm:pt-8 lg:px-6">
        <div class="group relative w-full overflow-hidden rounded-sm bg-gray-900 h-[125px] lg:h-[285px]"
            @mouseenter="pauseTimer" @mouseleave="startTimer">
            <!-- Slides -->
            <div class="flex h-full transition-transform duration-700 ease-out"
                :style="{ transform: `translateX(-${currentIndex * 100}%)` }">
                <div v-for="(slide, index) in parsedSliders" :key="index"
                    class="relative h-full w-full shrink-0 overflow-hidden">
                    <!-- Background: image or color fallback -->
                    <img v-if="slide.image_url" :src="slide.image_url" :alt="slide.title ?? ''"
                        class="h-full w-full object-cover transition-transform duration-10000 ease-linear"
                        :class="{ 'scale-105': index === currentIndex }" />
                    <div v-else class="h-full w-full" :style="{ backgroundColor: slide.bg_color || '#1e3a5f' }"></div>

                    <!-- Gradient Overlay removed by request -->

                    <!-- Caption Container (Centered horizontally and vertically) -->
                    <div class="absolute inset-0 flex items-center justify-center p-6 text-center text-white md:p-12">
                        <div class="mx-auto max-w-4xl transform transition-all duration-700 delay-100"
                            :class="index === currentIndex ? 'translate-y-0 opacity-100' : 'translate-y-8 opacity-0'">
                            <!-- Slide Title -->
                            <h2 v-if="slide.title"
                                class="mb-3 text-2xl font-extrabold tracking-tight text-white drop-shadow-md sm:text-4xl md:text-5xl lg:text-6xl">
                                {{ slide.title }}
                            </h2>
                            <!-- Slide Description -->
                            <p v-if="slide.description"
                                class="mx-auto mb-6 max-w-2xl text-xs text-gray-200 drop-shadow-sm sm:text-sm md:text-base lg:text-lg">
                                {{ slide.description }}
                            </p>
                            <!-- Action Button -->
                            <a v-if="slide.url" :href="slide.url"
                                class="inline-flex items-center justify-center rounded-full bg-white px-6 py-2.5 text-xs font-bold text-gray-900 shadow-lg transition-all duration-300 hover:scale-105 hover:bg-gray-100 hover:shadow-xl sm:px-8 sm:py-3 sm:text-sm">
                                {{ slide.button_text || 'Shop Now' }}
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                                    class="ml-2 h-4 w-4 transition-transform duration-300 group-hover:translate-x-1">
                                    <path fill-rule="evenodd"
                                        d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z"
                                        clip-rule="evenodd" />
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Prev button (hidden by default, visible on hover) -->
            <button v-if="parsedSliders.length > 1"
                class="absolute left-4 top-1/2 flex h-10 w-10 -translate-y-1/2 items-center justify-center rounded-full bg-white/20 text-white opacity-0 backdrop-blur-md transition-all duration-300 hover:bg-white/40 group-hover:opacity-100 focus:outline-none sm:h-12 sm:w-12"
                aria-label="Previous slide" @click="prev">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg>
            </button>

            <!-- Next button (hidden by default, visible on hover) -->
            <button v-if="parsedSliders.length > 1"
                class="absolute right-4 top-1/2 flex h-10 w-10 -translate-y-1/2 items-center justify-center rounded-full bg-white/20 text-white opacity-0 backdrop-blur-md transition-all duration-300 hover:bg-white/40 group-hover:opacity-100 focus:outline-none sm:h-12 sm:w-12"
                aria-label="Next slide" @click="next">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                </svg>
            </button>

            <!-- Dot indicators (styled as premium pill indicators) -->
            <div v-if="parsedSliders.length > 1" class="absolute bottom-5 left-1/2 flex -translate-x-1/2 gap-2">
                <button v-for="(_, index) in parsedSliders" :key="index"
                    class="h-2 rounded-full transition-all duration-300 focus:outline-none"
                    :class="index === currentIndex ? 'w-8 bg-white' : 'w-2 bg-white/40 hover:bg-white/70'"
                    :aria-label="`Go to slide ${index + 1}`" @click="goTo(index)"></button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'

const props = defineProps({
    sliders: {
        type: [String, Array],
        default: () => [],
    },
})

const parsedSliders = computed(() => {
    if (Array.isArray(props.sliders)) {
        return props.sliders
    }
    try {
        return JSON.parse(props.sliders)
    } catch {
        return []
    }
})

const currentIndex = ref(0)
let timer = null

const next = () => {
    if (!parsedSliders.value.length) return
    currentIndex.value = (currentIndex.value + 1) % parsedSliders.value.length
    resetTimer()
}

const prev = () => {
    if (!parsedSliders.value.length) return
    currentIndex.value = (currentIndex.value - 1 + parsedSliders.value.length) % parsedSliders.value.length
    resetTimer()
}

const goTo = (index) => {
    currentIndex.value = index
    resetTimer()
}

const startTimer = () => {
    if (parsedSliders.value.length > 1) {
        timer = setInterval(() => {
            currentIndex.value = (currentIndex.value + 1) % parsedSliders.value.length
        }, 6000)
    }
}

const pauseTimer = () => {
    if (timer) {
        clearInterval(timer)
        timer = null
    }
}

const resetTimer = () => {
    pauseTimer()
    startTimer()
}

onMounted(() => {
    startTimer()
})

onUnmounted(() => {
    pauseTimer()
})
</script>
