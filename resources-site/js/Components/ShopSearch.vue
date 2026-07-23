<template>
    <div class="relative flex w-full items-center">
        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-gray-400">
            <i class="ri-search-2-line text-[18px]"></i>
        </div>
        <input v-model="searchText" type="text" placeholder="Search for products, brands and more..."
            class="block w-full rounded-full border border-gray-200 bg-gray-50/50 py-3 pl-11 pr-28 text-[15px] font-medium text-gray-900 shadow-sm transition-all focus:border-primary-500 focus:bg-white focus:ring-4 focus:ring-primary-500/10 dark:border-gray-700 dark:bg-gray-800 dark:text-white dark:focus:border-primary-500"
            @keyup.enter="search" />
        <button @click="search"
            class="absolute bottom-1.5 right-1.5 top-1.5 rounded-full bg-primary-600 px-6 text-[13px] font-bold uppercase tracking-wider text-white shadow-sm transition-all hover:bg-primary-700 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
            Search
        </button>
    </div>
</template>
<script setup>
import { ref } from 'vue'

const searchText = ref('')

const search = () => {
    if (searchText.value) {
        const url = `/shop/search/${encodeURIComponent(searchText.value)}`
        window.location.href = url
    }
}
</script>
