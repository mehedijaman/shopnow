<template>
    <div v-if="parsedSliders.length" class="relative w-full overflow-hidden bg-gray-900" style="height: 480px;">
        <!-- Slides -->
        <div
            class="flex h-full transition-transform duration-500 ease-in-out"
            :style="{ transform: `translateX(-${currentIndex * 100}%)` }"
        >
            <div
                v-for="(slide, index) in parsedSliders"
                :key="index"
                class="relative h-full w-full shrink-0"
            >
                <!-- Background: image or color fallback -->
                <img
                    v-if="slide.image_url"
                    :src="slide.image_url"
                    :alt="slide.title ?? ''"
                    class="h-full w-full object-cover"
                />
                <div
                    v-else
                    class="h-full w-full"
                    :style="{ backgroundColor: slide.bg_color || '#1e3a5f' }"
                ></div>

                <!-- Overlay -->
                <div
                    class="absolute inset-0"
                    :class="slide.image_url ? 'bg-gradient-to-t from-black/60 via-black/10 to-transparent' : 'bg-black/20'"
                ></div>

                <!-- Caption -->
                <div v-if="slide.title || slide.description" class="absolute inset-0 flex items-end p-6 text-white md:p-10" :class="{ 'items-center justify-center text-center': !slide.image_url }">
                    <div class="mx-auto max-w-3xl">
                        <h2 v-if="slide.title" class="mb-2 text-2xl font-bold drop-shadow md:text-4xl">
                            {{ slide.title }}
                        </h2>
                        <p v-if="slide.description" class="text-sm text-gray-200 drop-shadow md:text-base">
                            {{ slide.description }}
                        </p>
                        <a
                            v-if="slide.url"
                            :href="slide.url"
                            class="mt-4 inline-block rounded-lg bg-white px-5 py-2 text-sm font-semibold text-gray-900 hover:bg-gray-100"
                        >
                            {{ slide.button_text || 'Shop Now' }}
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Prev button -->
        <button
            v-if="parsedSliders.length > 1"
            class="absolute left-3 top-1/2 flex h-10 w-10 -translate-y-1/2 items-center justify-center rounded-full bg-white/30 text-white backdrop-blur-sm transition hover:bg-white/50 focus:outline-none"
            aria-label="Previous slide"
            @click="prev"
        >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M12.79 5.23a.75.75 0 01-.02 1.06L8.832 10l3.938 3.71a.75.75 0 11-1.04 1.08l-4.5-4.25a.75.75 0 010-1.08l4.5-4.25a.75.75 0 011.06.02z" clip-rule="evenodd" />
            </svg>
        </button>

        <!-- Next button -->
        <button
            v-if="parsedSliders.length > 1"
            class="absolute right-3 top-1/2 flex h-10 w-10 -translate-y-1/2 items-center justify-center rounded-full bg-white/30 text-white backdrop-blur-sm transition hover:bg-white/50 focus:outline-none"
            aria-label="Next slide"
            @click="next"
        >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd" />
            </svg>
        </button>

        <!-- Dot indicators -->
        <div v-if="parsedSliders.length > 1" class="absolute bottom-4 left-1/2 flex -translate-x-1/2 gap-2">
            <button
                v-for="(_, index) in parsedSliders"
                :key="index"
                class="h-2 rounded-full transition-all duration-300 focus:outline-none"
                :class="index === currentIndex ? 'w-6 bg-white' : 'w-2 bg-white/50 hover:bg-white/80'"
                :aria-label="`Go to slide ${index + 1}`"
                @click="goTo(index)"
            ></button>
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
    currentIndex.value = (currentIndex.value + 1) % parsedSliders.value.length
    resetTimer()
}

const prev = () => {
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
        }, 5000)
    }
}

const resetTimer = () => {
    clearInterval(timer)
    startTimer()
}

onMounted(() => {
    startTimer()
})

onUnmounted(() => {
    clearInterval(timer)
})
</script>
