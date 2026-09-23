<template>
    <div>
        <div
            v-if="isOpen"
            class="fixed inset-0 z-[200] flex items-start justify-center pt-[10vh]"
            @click.self="close"
        >
            <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" @click.self="close"></div>
            <div class="relative z-10 w-full max-w-2xl mx-4">
                <div class="relative flex items-center gap-2 rounded-2xl bg-white p-2 shadow-2xl">
                    <i class="ri-search-2-line text-xl text-gray-400 pl-3"></i>
                    <input
                        ref="searchInput"
                        v-model="searchText"
                        type="text"
                        placeholder="Search for products, brands and more..."
                        class="block w-full rounded-xl border-none bg-transparent py-4 pl-3 pr-24 text-[16px] font-medium text-gray-900 focus:outline-none"
                        @keyup.enter="search"
                        @focus="isOpen = true"
                    />
                    <button
                        type="button"
                        @click="close"
                        class="absolute right-2 top-1/2 -translate-y-1/2 flex h-10 w-10 items-center justify-center rounded-full hover:bg-gray-100"
                        aria-label="Close search"
                    >
                        <i class="ri-close-line text-lg text-gray-400"></i>
                    </button>
                    <button
                        type="button"
                        @click="search"
                        class="rounded-xl bg-primary-600 px-6 py-2.5 text-[13px] font-bold uppercase tracking-wider text-white shadow-sm transition-all hover:bg-primary-700"
                    >
                        Search
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'

const isOpen = ref(false)
const searchText = ref('')
const searchInput = ref(null)

function search() {
    if (searchText.value) {
        const url = `/shop/search/${encodeURIComponent(searchText.value)}`
        window.location.href = url
    }
    close()
}

function close() {
    isOpen.value = false
    searchText.value = ''
}

function toggle() {
    isOpen.value = !isOpen.value
    if (isOpen.value) {
        setTimeout(() => searchInput.value?.focus(), 100)
    }
}

onMounted(() => {
    window.toggleSearchModal = toggle
})
</script>
