<template>
    <a
        v-if="whatsappNumber"
        :href="whatsappUrl"
        target="_blank"
        rel="noopener noreferrer"
        class="fixed bottom-6 right-6 z-50 flex h-14 w-14 items-center justify-center rounded-full bg-[#25D366] text-white shadow-lg transition-transform hover:scale-110 hover:shadow-xl focus:outline-none focus:ring-4 focus:ring-green-300 dark:focus:ring-green-800"
        aria-label="Chat on WhatsApp"
        title="Chat on WhatsApp"
    >
        <i class="ri-whatsapp-line text-3xl"></i>
    </a>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    number: {
        type: String,
        required: false,
        default: null,
    },
});

const whatsappNumber = computed(() => {
    if (!props.number) return null;
    return props.number.replace(/[^0-9+]/g, '');
});

const whatsappUrl = computed(() => {
    return `https://wa.me/${whatsappNumber.value}`;
});
</script>

<style scoped>
@keyframes pulse-soft {
    0% {
        box-shadow: 0 0 0 0 rgba(37, 211, 102, 0.4);
    }
    70% {
        box-shadow: 0 0 0 15px rgba(37, 211, 102, 0);
    }
    100% {
        box-shadow: 0 0 0 0 rgba(37, 211, 102, 0);
    }
}
a {
    animation: pulse-soft 2s infinite;
}
a:hover {
    animation: none;
}
</style>
