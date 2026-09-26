<template>
    <div v-if="parsedSliders.length" class="mx-auto max-w-7xl ">
        <div class="group relative w-full overflow-hidden rounded-sm bg-gray-900 h-[180px] sm:h-[260px] md:h-[340px] lg:h-[420px] xl:h-[460px] shadow-sm"
            @mouseenter="pauseTimer" @mouseleave="startTimer">
            <!-- Slides -->
            <div class="flex h-full transition-transform duration-700 ease-out"
                :style="{ transform: `translateX(-${currentIndex * 100}%)` }">
                <div v-for="(slide, index) in parsedSliders" :key="index"
                    class="relative flex h-full w-full shrink-0 items-center justify-center overflow-hidden"
                    :style="{ backgroundColor: slide.bg_color || '#111827' }">
                    <!-- Image (object-cover fills the full container on mobile and desktop without blank space) -->
                    <img v-if="slide.image_url" :src="slide.image_url" :alt="slide.title ?? ''"
                        class="h-full w-full object-cover object-center transition-all duration-700" />
                    <div v-else class="h-full w-full" :style="{ backgroundColor: slide.bg_color || '#1e3a5f' }"></div>

                    <!-- Slide Link Overlay (when no explicit button text) -->
                    <a v-if="slide.url && !slide.button_text" :href="slide.url" class="absolute inset-0 z-10"
                        :aria-label="slide.title || 'Slide link'"></a>

                    <!-- Caption Container -->
                    <div v-if="slide.title || slide.description || (slide.url && slide.button_text)"
                        class="pointer-events-none absolute inset-0 z-20 flex items-center justify-center p-4 text-center text-white sm:p-8 md:p-12">
                        <div class="mx-auto max-w-3xl transform transition-all duration-700 delay-100"
                            :class="index === currentIndex ? 'translate-y-0 opacity-100' : 'translate-y-6 opacity-0'">
                            <!-- Slide Title -->
                            <h2 v-if="slide.title"
                                class="mb-2 text-xl font-extrabold tracking-tight text-white drop-shadow-md sm:mb-3 sm:text-3xl md:text-4xl lg:text-5xl">
                                {{ slide.title }}
                            </h2>
                            <!-- Slide Description -->
                            <p v-if="slide.description"
                                class="mx-auto mb-4 max-w-2xl text-xs text-gray-200 drop-shadow-sm sm:mb-6 sm:text-sm md:text-base lg:text-lg">
                                {{ slide.description }}
                            </p>
                            <!-- Action Button -->
                            <a v-if="slide.url && slide.button_text" :href="slide.url"
                                class="pointer-events-auto inline-flex items-center justify-center rounded-full bg-white px-5 py-2 text-xs font-bold text-gray-900 shadow-lg transition-all duration-300 hover:scale-105 hover:bg-gray-100 hover:shadow-xl sm:px-7 sm:py-2.5 sm:text-sm">
                                {{ slide.button_text }}
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                                    class="ml-1.5 h-4 w-4 transition-transform duration-300 group-hover:translate-x-1">
                                    <path fill-rule="evenodd"
                                        d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z"
                                        clip-rule="evenodd" />
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Prev button -->
            <button v-if="parsedSliders.length > 1"
                class="absolute left-3 top-1/2 z-30 flex h-9 w-9 -translate-y-1/2 items-center justify-center rounded-full bg-black/30 text-white opacity-0 backdrop-blur-md transition-all duration-300 hover:bg-black/60 group-hover:opacity-100 focus:outline-none sm:left-4 sm:h-11 sm:w-11"
                aria-label="Previous slide" @click="prev">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 sm:h-6 sm:w-6" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg>
            </button>

            <!-- Next button -->
            <button v-if="parsedSliders.length > 1"
                class="absolute right-3 top-1/2 z-30 flex h-9 w-9 -translate-y-1/2 items-center justify-center rounded-full bg-black/30 text-white opacity-0 backdrop-blur-md transition-all duration-300 hover:bg-black/60 group-hover:opacity-100 focus:outline-none sm:right-4 sm:h-11 sm:w-11"
                aria-label="Next slide" @click="next">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 sm:h-6 sm:w-6" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                </svg>
            </button>

            <!-- Dot indicators -->
            <div v-if="parsedSliders.length > 1"
                class="absolute bottom-3 left-1/2 z-30 flex -translate-x-1/2 gap-1.5 sm:bottom-4 sm:gap-2">
                <button v-for="(_, index) in parsedSliders" :key="index"
                    class="h-1.5 rounded-full transition-all duration-300 focus:outline-none sm:h-2"
                    :class="index === currentIndex ? 'w-6 bg-white sm:w-8' : 'w-1.5 bg-white/40 hover:bg-white/70 sm:w-2'"
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
