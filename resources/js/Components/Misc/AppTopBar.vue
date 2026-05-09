<template>
    <div
        class="sticky top-0 z-40 flex h-16 shrink-0 justify-between bg-skin-neutral-2 py-3 pl-3 pr-9 text-skin-neutral-11 shadow-xs"
    >
        <div class="flex items-center">
            <AppButton
                class="btn btn-icon hover:bg-skin-neutral-4"
                @click="$emit('sidebar:toggle')"
            >
                <i class="ri-menu-line"></i>
            </AppButton>

            <h1 class="flex items-center">{{ title }}</h1>
        </div>

        <div class="flex items-center gap-1">
            <AppButton
                href="#"
                class="btn btn-icon hover:bg-skin-neutral-4"
                @click="toggleTheme"
            >
                <i :class="iconThemeClass"></i>
            </AppButton>

            <!-- User avatar dropdown -->
            <div ref="dropdownRef" class="relative">
                <button
                    type="button"
                    class="flex items-center gap-2 rounded-lg px-2 py-1.5 transition-colors hover:bg-skin-neutral-4"
                    @click="dropdownOpen = !dropdownOpen"
                >
                    <img
                        v-if="authUser.avatar_url"
                        :src="authUser.avatar_url"
                        :alt="authUser.name"
                        class="h-7 w-7 rounded-full object-cover ring-1 ring-skin-neutral-6"
                    />
                    <div
                        v-else
                        class="flex h-7 w-7 items-center justify-center rounded-full bg-skin-primary-9 text-xs font-bold text-skin-primary-1"
                    >
                        {{ initials }}
                    </div>
                    <span class="hidden text-sm font-medium text-skin-neutral-11 sm:block">{{ authUser.name }}</span>
                    <i
                        class="ri-arrow-down-s-line hidden text-sm text-skin-neutral-9 transition-transform duration-200 sm:block"
                        :class="{ 'rotate-180': dropdownOpen }"
                    ></i>
                </button>

                <!-- Dropdown panel -->
                <Transition
                    enter-active-class="transition duration-150 ease-out"
                    enter-from-class="opacity-0 scale-95"
                    enter-to-class="opacity-100 scale-100"
                    leave-active-class="transition duration-100 ease-in"
                    leave-from-class="opacity-100 scale-100"
                    leave-to-class="opacity-0 scale-95"
                >
                    <div
                        v-if="dropdownOpen"
                        class="absolute right-0 top-full mt-2 w-60 origin-top-right rounded-xl border border-skin-neutral-4 bg-skin-neutral-1 shadow-lg"
                    >
                        <!-- User info -->
                        <div class="flex items-center gap-3 px-4 py-3.5">
                            <img
                                v-if="authUser.avatar_url"
                                :src="authUser.avatar_url"
                                :alt="authUser.name"
                                class="h-10 w-10 rounded-full object-cover ring-2 ring-skin-primary-9 ring-offset-1"
                            />
                            <div
                                v-else
                                class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-skin-primary-9 text-sm font-bold text-skin-primary-1 ring-2 ring-skin-primary-9 ring-offset-1"
                            >
                                {{ initials }}
                            </div>
                            <div class="min-w-0">
                                <p class="truncate text-sm font-semibold text-skin-neutral-12">{{ authUser.name }}</p>
                                <p class="truncate text-xs text-skin-neutral-9">{{ authUser.email }}</p>
                            </div>
                        </div>

                        <div class="border-t border-skin-neutral-4"></div>

                        <!-- Actions -->
                        <div class="p-1.5">
                            <Link
                                :href="route('profile.show')"
                                class="flex w-full items-center gap-2.5 rounded-lg px-3 py-2 text-sm text-skin-neutral-11 transition-colors hover:bg-skin-neutral-3 hover:text-skin-neutral-12"
                                @click="dropdownOpen = false"
                            >
                                <i class="ri-user-settings-line text-base"></i>
                                Edit Profile
                            </Link>
                        </div>

                        <div class="border-t border-skin-neutral-4"></div>

                        <div class="p-1.5">
                            <Link
                                :href="route('adminAuth.logout')"
                                class="flex w-full items-center gap-2.5 rounded-lg px-3 py-2 text-sm text-red-600 transition-colors hover:bg-red-50 hover:text-red-700"
                                @click="dropdownOpen = false"
                            >
                                <i class="ri-logout-circle-r-line text-base"></i>
                                Sign Out
                            </Link>
                        </div>
                    </div>
                </Transition>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, computed } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'

const authUser = computed(() => usePage().props.auth?.user ?? {})

const initials = computed(() => {
    const name = authUser.value.name ?? ''
    return name
        .split(' ')
        .slice(0, 2)
        .map((w) => w[0]?.toUpperCase() ?? '')
        .join('')
})

defineProps({
    title: {
        type: String,
        default: ''
    }
})

defineEmits(['sidebar:toggle'])

// ── Dropdown ─────────────────────────────────────────────────────────────────

const dropdownOpen = ref(false)
const dropdownRef = ref(null)

function onClickOutside(e) {
    if (dropdownRef.value && !dropdownRef.value.contains(e.target)) {
        dropdownOpen.value = false
    }
}

onMounted(() => {
    document.addEventListener('mousedown', onClickOutside)

    if (localStorage.getItem('modular-theme') === 'dark-theme') {
        document.documentElement.classList.add('dark-theme')
        iconThemeClass.value = 'ri-sun-line'
    } else {
        document.documentElement.classList.remove('dark-theme')
        iconThemeClass.value = 'ri-moon-line'
    }
})

onUnmounted(() => {
    document.removeEventListener('mousedown', onClickOutside)
})

// ── Theme ─────────────────────────────────────────────────────────────────────

const iconThemeClass = ref('ri-sun-line')

const toggleTheme = () => {
    if (document.documentElement.classList.contains('dark-theme')) {
        document.documentElement.classList.remove('dark-theme')
        iconThemeClass.value = 'ri-moon-line'
        localStorage.removeItem('modular-theme')
    } else {
        localStorage.setItem('modular-theme', 'dark-theme')
        document.documentElement.classList.add('dark-theme')
        iconThemeClass.value = 'ri-sun-line'
    }
}
</script>
