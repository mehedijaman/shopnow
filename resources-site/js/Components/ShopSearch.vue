<template>
    <app-modal
        :is-modal-open="isOpen"
        placement="top"
        backdrop-classes="bg-slate-950/60 backdrop-blur-sm fixed inset-0 z-[200]"
        panel-classes="w-[calc(100%-1.5rem)] max-w-2xl rounded-2xl border border-gray-200 bg-white p-0 text-gray-900 shadow-2xl dark:border-gray-700 dark:bg-gray-900 dark:text-white sm:w-full"
        @modal:toggle="close"
    >
        <template #body>
            <div class="p-4 sm:p-5">
                <div class="mb-4 flex items-start justify-between gap-4">
                    <div>
                        <p class="text-[11px] font-bold uppercase tracking-wider text-primary-600 dark:text-primary-400">
                            Search
                        </p>
                        <h2 class="mt-0.5 text-base font-bold text-gray-900 dark:text-white sm:text-lg">
                            Find products
                        </h2>
                    </div>
                    <button
                        type="button"
                        class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl text-gray-500 transition-colors hover:bg-gray-100 hover:text-gray-800 dark:text-gray-400 dark:hover:bg-gray-800 dark:hover:text-white"
                        aria-label="Close search"
                        @click="close"
                    >
                        <i class="ri-close-line text-xl"></i>
                    </button>
                </div>

                <form role="search" @submit.prevent="search">
                    <div class="relative">
                        <i
                            class="ri-search-2-line pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-lg text-gray-400"
                            aria-hidden="true"
                        ></i>

                        <input
                            ref="searchInput"
                            v-model="searchText"
                            type="search"
                            name="q"
                            autocomplete="off"
                            enterkeyhint="search"
                            placeholder="Search for products, brands and more..."
                            aria-label="Search products"
                            class="block w-full rounded-2xl border border-gray-200 bg-gray-50 py-3.5 pl-12 pr-14 text-[15px] font-medium text-gray-900 placeholder:font-normal placeholder:text-gray-400 focus:border-primary-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-primary-500/10 dark:border-gray-700 dark:bg-gray-800 dark:text-white dark:placeholder:text-gray-500 dark:focus:border-primary-500 dark:focus:bg-gray-900 sm:pr-36"
                            @keydown.esc.prevent="close"
                        />

                        <div
                            class="absolute right-2 top-1/2 flex -translate-y-1/2 items-center gap-1"
                        >
                            <button
                                v-if="searchText"
                                type="button"
                                class="flex h-8 w-8 items-center justify-center rounded-full text-gray-400 transition-colors hover:bg-gray-200 hover:text-gray-700 dark:hover:bg-gray-700 dark:hover:text-white"
                                aria-label="Clear search"
                                @click="clearQuery"
                            >
                                <i class="ri-close-circle-fill text-lg"></i>
                            </button>

                            <button
                                type="submit"
                                class="flex h-9 items-center justify-center gap-1.5 rounded-xl bg-primary-600 px-3 text-xs font-bold uppercase tracking-wide text-white shadow-sm transition-all hover:bg-primary-700 focus:outline-none focus:ring-4 focus:ring-primary-500/20 active:scale-[0.98] sm:px-4 sm:text-[13px]"
                            >
                                <i class="ri-search-line sm:hidden"></i>
                                <span class="hidden sm:inline">Search</span>
                                <span class="sr-only sm:hidden">Search</span>
                            </button>
                        </div>
                    </div>
                </form>

                <div
                    class="mt-3 flex flex-col gap-2 text-xs text-gray-500 dark:text-gray-400 sm:flex-row sm:items-center sm:justify-between"
                >
                    <!-- <p>
                        Try
                        <button
                            type="button"
                            class="font-semibold text-primary-600 transition-colors hover:text-primary-700 dark:text-primary-400"
                            @click="useExample('shirt')"
                        >
                            shirt</button>,
                        <button
                            type="button"
                            class="font-semibold text-primary-600 transition-colors hover:text-primary-700 dark:text-primary-400"
                            @click="useExample('wireless')"
                        >
                            wireless</button>, or a brand name
                    </p> -->

                    <p class="hidden items-center gap-3 sm:flex">
                        <span class="inline-flex items-center gap-1.5">
                            <kbd
                                class="rounded-md border border-gray-200 bg-gray-100 px-1.5 py-0.5 font-sans text-[10px] font-semibold text-gray-600 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300"
                            >
                                Enter
                            </kbd>
                            to search
                        </span>
                        <span class="inline-flex items-center gap-1.5">
                            <kbd
                                class="rounded-md border border-gray-200 bg-gray-100 px-1.5 py-0.5 font-sans text-[10px] font-semibold text-gray-600 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300"
                            >
                                Esc
                            </kbd>
                            to close
                        </span>
                    </p>
                </div>
            </div>
        </template>
    </app-modal>
</template>

<script setup>
import { onBeforeUnmount, onMounted, ref, watch } from 'vue'
// import AppModal from '@resources/js/Components/Overlay/AppModal.vue'

const isOpen = ref(false)
const searchText = ref('')
const searchInput = ref(null)

function search() {
    const query = searchText.value.trim()

    if (!query) {
        searchInput.value?.focus()
        return
    }

    window.location.href = `/shop/search/${encodeURIComponent(query)}`
}

function clearQuery() {
    searchText.value = ''
    searchInput.value?.focus()
}

function useExample(term) {
    searchText.value = term
    searchInput.value?.focus()
}

function close() {
    isOpen.value = false
    searchText.value = ''
}

function open() {
    isOpen.value = true
    setTimeout(() => searchInput.value?.focus(), 80)
}

function toggle() {
    if (isOpen.value) {
        close()
    } else {
        open()
    }
}

function onKeydown(event) {
    if (event.key === 'Escape' && isOpen.value) {
        close()
    }
}

watch(isOpen, (value) => {
    document.body.style.overflow = value ? 'hidden' : ''
})

onMounted(() => {
    window.toggleSearchModal = toggle
    window.addEventListener('keydown', onKeydown)
})

onBeforeUnmount(() => {
    window.removeEventListener('keydown', onKeydown)
    document.body.style.overflow = ''
})
</script>
