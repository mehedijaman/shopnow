<template>
    <div ref="rootRef" class="relative">
        <button
            type="button"
            class="btn btn-icon hover:bg-skin-neutral-4"
            :aria-label="__('common.language')"
            aria-haspopup="menu"
            :aria-expanded="open"
            @click="open = !open"
        >
            <i class="ri-translate-2"></i>
        </button>

        <Transition
            enter-active-class="transition duration-150 ease-out"
            enter-from-class="opacity-0 scale-95"
            enter-to-class="opacity-100 scale-100"
            leave-active-class="transition duration-100 ease-in"
            leave-from-class="opacity-100 scale-100"
            leave-to-class="opacity-0 scale-95"
        >
            <div
                v-if="open"
                role="menu"
                class="absolute right-0 top-full z-50 mt-2 w-44 origin-top-right rounded-xl border border-skin-neutral-4 bg-skin-neutral-1 p-1 shadow-lg"
            >
                <form
                    v-for="option in options"
                    :key="option.code"
                    :action="switchUrl"
                    method="POST"
                >
                    <input type="hidden" name="_token" :value="csrfToken" />
                    <input type="hidden" name="locale" :value="option.code" />
                    <input type="hidden" name="_redirect" :value="returnTo" />

                    <button
                        type="submit"
                        role="menuitem"
                        class="flex w-full items-center justify-between gap-3 rounded-lg px-3 py-2 text-sm text-skin-neutral-11 transition-colors hover:bg-skin-neutral-3 hover:text-skin-neutral-12"
                        @click="open = false"
                    >
                        <span :lang="option.code" :hreflang="option.code">
                            {{ option.native }}
                        </span>
                        <span
                            class="flex items-center gap-2 text-xs text-skin-neutral-9"
                        >
                            {{ option.name }}
                            <i
                                v-if="option.code === current"
                                class="ri-check-line"
                            ></i>
                        </span>
                    </button>
                </form>
            </div>
        </Transition>
    </div>
</template>

<script setup>
import { computed, onMounted, onUnmounted, ref } from 'vue'
import { usePage } from '@inertiajs/vue3'

const page = usePage()

const current = computed(() => page.props.locale ?? 'en')

const options = computed(() =>
    Object.entries(page.props.locales ?? {}).map(([code, meta]) => ({
        code,
        name: meta.name,
        native: meta.native
    }))
)

const switchUrl = route('locale.switch')

const returnTo = `${window.location.pathname}${window.location.search}`

const csrfToken =
    document.querySelector('meta[name="csrf-token"]')?.content ?? ''

const open = ref(false)
const rootRef = ref(null)

function onClickOutside(event) {
    if (rootRef.value && !rootRef.value.contains(event.target)) {
        open.value = false
    }
}

onMounted(() => document.addEventListener('mousedown', onClickOutside))

onUnmounted(() => document.removeEventListener('mousedown', onClickOutside))
</script>
