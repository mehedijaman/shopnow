<template>
    <div
        class="mx-auto my-6 flex h-10 rounded-full border border-blue-300 border-transparent bg-gray-100 focus-within:border-blue-400 focus-within:bg-transparent lg:w-2/4"
    >
        <i class="ri-search-2-line mx-4 mt-1 text-xl"></i>
        <input
            v-model="searchText"
            type="text"
            placeholder="Search..."
            class="w-full border-none text-[15px] font-semibold text-gray-600 focus:border-none"
            @keyup.enter="search"
        />
        <button
            @click="search"
            class="rounded-r-full bg-skin-primary-9 px-4 pl-4 text-gray-100 hover:bg-skin-primary-10 hover:text-gray-200"
        >
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
