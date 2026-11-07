<template>
    <form
        role="search"
        :action="searchUrl"
        method="get"
        class="header-search border-site-border bg-site-surface focus-within:border-site-focus focus-within:ring-site-focus/30 flex min-w-0 items-center gap-1 rounded-lg border p-1 focus-within:ring-2"
        @submit="submit"
    >
        <input
            :id="inputId"
            ref="searchInput"
            v-model="searchText"
            type="search"
            name="q"
            maxlength="200"
            enterkeyhint="search"
            :placeholder="__('site.search.in_store', { name: storeName })"
            :aria-label="__('site.aria.search_products')"
            class="text-site-text placeholder:text-site-muted h-9 min-w-0 flex-1 border-0 bg-transparent px-3 text-sm focus:outline-none focus:ring-0"
        />
        <button
            type="submit"
            :aria-label="__('common.search')"
            class="bg-site-primary text-site-on-primary hover:bg-site-primary-hover focus-visible:outline-site-focus flex h-9 w-10 shrink-0 items-center justify-center rounded-md transition focus-visible:outline-2 focus-visible:outline-offset-2"
        >
            <i class="ri-search-2-line text-lg" aria-hidden="true"></i>
        </button>
    </form>
</template>

<script setup>
import { ref } from 'vue'

const props = defineProps({
    searchUrl: { type: String, required: true },
    storeName: { type: String, required: true },
    inputId: { type: String, required: true },
    initialQuery: { type: String, default: '' }
})

const searchText = ref(props.initialQuery)
const searchInput = ref(null)

function submit(event) {
    if (!searchText.value.trim()) {
        event.preventDefault()
        searchInput.value?.focus()
    }
}
</script>

<style scoped>
.header-search {
    --site-surface: var(--site-search-surface);
    --site-text: var(--site-search-text);
    --site-muted: var(--site-search-muted);
    --site-border: var(--site-search-border);
    --site-focus: var(--site-search-focus);
}
</style>
