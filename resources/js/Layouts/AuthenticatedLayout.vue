<template>

    <Head title="Modular"></Head>

    <AppSideBar ref="sidebarRef" :backdrop="isMobile" :body-scrolling="!isMobile" @sidebar:toggle="sidebarToggle">

        <Link :href="route('dashboard.index')" class="flex items-center">
        <img :src="branding.logo_url || '/logo.png'" :alt="branding.site_name" class="max-h-16 w-full object-contain" />

        </Link>

        <AppMenu :items="items" />
    </AppSideBar>

    <main class="flex flex-1 flex-col pb-9" :class="{ 'md:pl-64': isSideBarOpen }">
        <AppFlashMessage />
        <AppTopBar :class="{ 'ml-64': isSideBarOpen }" class="md:ml-0" @sidebar:toggle="sidebarToggle" />
        <div class="mx-8 2xl:mx-16">
            <transition name="fade" mode="out-in">
                <!-- eslint-disable-next-line vue/require-toggle-inside-transition -->
                <div :key="page.props.ziggy.location">
                    <slot></slot>
                </div>
            </transition>
        </div>
    </main>
</template>

<script setup>
import { computed, ref, onMounted } from 'vue'
import { Head, usePage } from '@inertiajs/vue3'
import useIsMobile from '@/Composables/useIsMobile'
import menu from '@/Configs/menu'

const page = usePage()

const branding = computed(() => page.props.branding ?? { site_name: 'ShopNow', logo_url: null })

const isSideBarOpen = ref(true)
const sidebarRef = ref()

const { isMobile } = useIsMobile()

onMounted(() => {
    if (isMobile.value) {
        sidebarToggle()
    }
})

const sidebarToggle = () => {
    isSideBarOpen.value = !isSideBarOpen.value
    sidebarRef.value.toggle()
}

const items = menu.items
</script>

<style scoped>
@reference "../../css/app.css";

.fade-enter-active,
.fade-leave-active {
    @apply transition-opacity duration-300 ease-out;
}

.fade-enter-from,
.fade-leave-to {
    @apply opacity-0;
}
</style>
