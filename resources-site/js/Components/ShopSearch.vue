<template>
    <app-modal
        :is-modal-open="isOpen"
        placement="top"
        backdrop-classes="bg-site-strong/60 backdrop-blur-sm fixed inset-0 z-[200]"
        panel-classes="w-[calc(100%-1.5rem)] max-w-2xl rounded-2xl border border-site-divider bg-site-surface p-0 text-site-text shadow-2xl dark:border-site-divider dark:bg-site-surface-muted dark:text-site-text sm:w-full"
        @modal:toggle="close"
    >
        <template #body>
            <div class="p-4 sm:p-5">
                <div class="mb-4 flex items-start justify-between gap-4">
                    <div>
                        <p
                            class="text-[11px] font-bold uppercase tracking-wider text-site-link dark:text-site-link"
                        >
                            {{ __('site.search.heading') }}
                        </p>
                        <h2
                            class="mt-0.5 text-base font-bold text-site-text dark:text-site-text sm:text-lg"
                        >
                            {{ __('site.search.find_products') }}
                        </h2>
                    </div>
                    <button
                        type="button"
                        class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl text-site-muted transition-colors hover:bg-site-surface-muted hover:text-site-text dark:text-site-muted dark:hover:bg-site-surface-muted dark:hover:text-site-text"
                        :aria-label="__('site.aria.close_search')"
                        @click="close"
                    >
                        <i class="ri-close-line text-xl"></i>
                    </button>
                </div>

                <form role="search" @submit.prevent="search">
                    <div class="relative">
                        <i
                            class="ri-search-2-line pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-lg text-site-muted"
                            aria-hidden="true"
                        ></i>

                        <input
                            ref="searchInput"
                            v-model="searchText"
                            type="search"
                            name="q"
                            autocomplete="off"
                            enterkeyhint="search"
                            :placeholder="__('site.search.placeholder')"
                            :aria-label="__('site.aria.search_products')"
                            class="block w-full rounded-2xl border border-site-border bg-site-surface-muted py-3.5 pl-12 pr-14 text-[15px] font-medium text-site-text placeholder:font-normal placeholder:text-site-muted focus:border-site-focus focus:bg-site-surface focus:outline-none focus:ring-4 focus:ring-site-focus/10 dark:border-site-border dark:bg-site-surface-muted dark:text-site-text dark:placeholder:text-site-muted dark:focus:border-site-focus dark:focus:bg-site-surface-muted sm:pr-36"
                            @keydown.esc.prevent="close"
                        />

                        <div
                            class="absolute right-2 top-1/2 flex -translate-y-1/2 items-center gap-1"
                        >
                            <button
                                v-if="searchText"
                                type="button"
                                class="flex h-8 w-8 items-center justify-center rounded-full text-site-muted transition-colors hover:bg-site-surface-muted hover:text-site-text dark:hover:bg-site-surface-muted dark:hover:text-site-text"
                                :aria-label="__('site.aria.clear_search')"
                                @click="clearQuery"
                            >
                                <i class="ri-close-circle-fill text-lg"></i>
                            </button>

                            <button
                                type="submit"
                                class="flex h-9 items-center justify-center gap-1.5 rounded-xl bg-site-primary px-3 text-xs font-bold uppercase tracking-wide text-site-on-primary shadow-sm transition-all hover:bg-site-primary-hover focus:outline-none focus:ring-4 focus:ring-site-focus/20 active:scale-[0.98] sm:px-4 sm:text-[13px]"
                            >
                                <i class="ri-search-line sm:hidden"></i>
                                <span class="hidden sm:inline">{{
                                    __('common.search')
                                }}</span>
                                <span class="sr-only sm:hidden">{{
                                    __('common.search')
                                }}</span>
                            </button>
                        </div>
                    </div>
                </form>

                <div
                    class="mt-3 flex flex-col gap-2 text-xs text-site-muted dark:text-site-muted sm:flex-row sm:items-center sm:justify-between"
                >
                    <!-- <p>
                        Try
                        <button
                            type="button"
                            class="font-semibold text-site-link transition-colors hover:text-site-link dark:text-site-link"
                            @click="useExample('shirt')"
                        >
                            shirt</button>,
                        <button
                            type="button"
                            class="font-semibold text-site-link transition-colors hover:text-site-link dark:text-site-link"
                            @click="useExample('wireless')"
                        >
                            wireless</button>, or a brand name
                    </p> -->

                    <p class="hidden items-center gap-3 sm:flex">
                        <span class="inline-flex items-center gap-1.5">
                            <kbd
                                class="rounded-md border border-site-divider bg-site-surface-muted px-1.5 py-0.5 font-sans text-[10px] font-semibold text-site-muted dark:border-site-divider dark:bg-site-surface-muted dark:text-site-text"
                            >
                                {{ __('site.search.key_enter') }}
                            </kbd>
                            {{ __('site.search.to_search') }}
                        </span>
                        <span class="inline-flex items-center gap-1.5">
                            <kbd
                                class="rounded-md border border-site-divider bg-site-surface-muted px-1.5 py-0.5 font-sans text-[10px] font-semibold text-site-muted dark:border-site-divider dark:bg-site-surface-muted dark:text-site-text"
                            >
                                {{ __('site.search.key_esc') }}
                            </kbd>
                            {{ __('site.search.to_close') }}
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
