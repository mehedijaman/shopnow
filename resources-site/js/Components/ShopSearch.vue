<template>
    <app-modal
        :is-modal-open="isOpen"
        placement="top"
        backdrop-classes="bg-black bg-opacity-50 fixed inset-0 z-[200]"
        @modal:toggle="close"
    >
        <template #body>
            <div class="relative flex items-center gap-2">
                <i class="ri-search-2-line text-xl text-gray-400"></i>
                <input
                    ref="searchInput"
                    v-model="searchText"
                    type="text"
                    placeholder="Search for products, brands and more..."
                    class="block w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-[15px] font-medium text-gray-900 focus:border-primary-500 focus:outline-none focus:ring-4 focus:ring-primary-500/10"
                    @keyup.enter="search"
                />
                <button
                    type="button"
                    @click="search"
                    class="rounded-xl bg-primary-600 px-6 py-3 text-[13px] font-bold uppercase tracking-wider text-white shadow-sm transition-all hover:bg-primary-700"
                >
                    Search
                </button>
            </div>
        </template>
    </app-modal>
</template>

<script setup>
import { ref, onMounted } from 'vue'
// import AppModal from '@resources/js/Components/Overlay/AppModal.vue'

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
