<template>
    <form class="space-y-6" @submit.prevent>
        <input
            type="email"
            autocomplete="username"
            :value="profileUser.email"
            class="sr-only"
            tabindex="-1"
            aria-hidden="true"
            readonly
        />

        <!-- Current password -->
        <div>
            <AppLabel for="current_password" value="Current Password" />
            <p class="mb-1 text-xs text-skin-neutral-9">Enter your existing password to verify your identity.</p>
            <AppInputPassword
                id="current_password"
                v-model="form.current_password"
                autocomplete="current-password"
                :class="{ 'input-error': form.errors.current_password }"
            />
            <p v-if="form.errors.current_password" class="mt-1 text-sm text-skin-error">{{ form.errors.current_password }}</p>
        </div>

        <!-- New password -->
        <div>
            <div class="mb-1 flex items-center justify-between">
                <div>
                    <AppLabel for="password" value="New Password" />
                    <p class="text-xs text-skin-neutral-9">Minimum 8 characters. Choose something strong.</p>
                </div>
                <button
                    type="button"
                    class="flex items-center gap-1.5 rounded-md bg-skin-neutral-3 px-2.5 py-1.5 text-xs font-medium text-skin-neutral-11 transition-colors hover:bg-skin-neutral-4 hover:text-skin-neutral-12"
                    @click="showGenerator = !showGenerator"
                >
                    <i class="ri-magic-line text-sm"></i>
                    Generate
                </button>
            </div>

            <!-- Password generator panel -->
            <Transition
                enter-active-class="transition duration-200 ease-out"
                enter-from-class="opacity-0 -translate-y-1"
                enter-to-class="opacity-100 translate-y-0"
                leave-active-class="transition duration-150 ease-in"
                leave-from-class="opacity-100 translate-y-0"
                leave-to-class="opacity-0 -translate-y-1"
            >
                <div v-if="showGenerator" class="mb-3 rounded-lg border border-skin-neutral-4 bg-skin-neutral-2 p-4">
                    <p class="mb-3 text-xs font-semibold uppercase tracking-wide text-skin-neutral-9">Password Generator</p>

                    <!-- Generated password preview -->
                    <div class="mb-3 flex items-center gap-2">
                        <div class="flex-1 rounded-md border border-skin-neutral-5 bg-skin-neutral-1 px-3 py-2 font-mono text-sm tracking-widest text-skin-neutral-12 select-all">
                            {{ generatedPassword || '—' }}
                        </div>
                        <button
                            type="button"
                            class="flex h-9 w-9 shrink-0 items-center justify-center rounded-md border border-skin-neutral-5 bg-skin-neutral-1 text-skin-neutral-10 transition-colors hover:bg-skin-neutral-3 hover:text-skin-neutral-12"
                            :title="copied ? 'Copied!' : 'Copy password'"
                            @click="copyGenerated"
                        >
                            <i :class="copied ? 'ri-check-line text-green-600' : 'ri-clipboard-line'" class="text-base"></i>
                        </button>
                        <button
                            type="button"
                            class="flex h-9 w-9 shrink-0 items-center justify-center rounded-md border border-skin-neutral-5 bg-skin-neutral-1 text-skin-neutral-10 transition-colors hover:bg-skin-neutral-3 hover:text-skin-neutral-12"
                            title="Regenerate"
                            @click="generate"
                        >
                            <i class="ri-refresh-line text-base"></i>
                        </button>
                    </div>

                    <!-- Length slider -->
                    <div class="mb-3">
                        <div class="mb-1 flex items-center justify-between text-xs text-skin-neutral-10">
                            <span>Length</span>
                            <span class="font-semibold text-skin-neutral-12">{{ options.length }}</span>
                        </div>
                        <input
                            v-model.number="options.length"
                            type="range"
                            min="8"
                            max="64"
                            class="h-1.5 w-full cursor-pointer appearance-none rounded-full bg-skin-neutral-5 accent-skin-primary-9"
                            @input="generate"
                        />
                        <div class="mt-0.5 flex justify-between text-xs text-skin-neutral-7">
                            <span>8</span>
                            <span>64</span>
                        </div>
                    </div>

                    <!-- Options -->
                    <div class="mb-4 grid grid-cols-2 gap-2">
                        <label
                            v-for="opt in charsetOptions"
                            :key="opt.key"
                            class="flex cursor-pointer items-center gap-2 rounded-md px-2 py-1.5 text-xs text-skin-neutral-11 transition-colors hover:bg-skin-neutral-3"
                        >
                            <input
                                v-model="options[opt.key]"
                                type="checkbox"
                                class="h-3.5 w-3.5 rounded accent-skin-primary-9"
                                @change="generate"
                            />
                            <span>{{ opt.label }}</span>
                            <code class="ml-auto rounded bg-skin-neutral-4 px-1 py-0.5 font-mono text-skin-neutral-9">{{ opt.sample }}</code>
                        </label>
                    </div>

                    <!-- Use this password -->
                    <button
                        type="button"
                        class="flex w-full items-center justify-center gap-1.5 rounded-md bg-skin-primary-10 px-3 py-2 text-sm font-medium text-skin-neutral-1 transition-opacity hover:opacity-90 disabled:opacity-40"
                        :disabled="!generatedPassword"
                        @click="applyGenerated"
                    >
                        <i class="ri-check-double-line"></i>
                        Use This Password
                    </button>
                </div>
            </Transition>

            <AppInputPassword
                id="password"
                v-model="form.password"
                autocomplete="new-password"
                :class="{ 'input-error': form.errors.password }"
            />
            <p v-if="form.errors.password" class="mt-1 text-sm text-skin-error">{{ form.errors.password }}</p>

            <!-- Strength meter -->
            <div v-if="form.password" class="mt-2">
                <div class="mb-1 flex items-center justify-between text-xs">
                    <span class="text-skin-neutral-9">Strength</span>
                    <span :class="strength.color" class="font-semibold">{{ strength.label }}</span>
                </div>
                <div class="flex gap-1">
                    <div
                        v-for="i in 4"
                        :key="i"
                        class="h-1.5 flex-1 rounded-full transition-colors duration-300"
                        :class="i <= strength.score ? strength.barColor : 'bg-skin-neutral-4'"
                    ></div>
                </div>
            </div>
        </div>

        <!-- Confirm password -->
        <div>
            <AppLabel for="password_confirmation" value="Confirm New Password" />
            <p class="mb-1 text-xs text-skin-neutral-9">Re-enter your new password to confirm.</p>
            <AppInputPassword
                id="password_confirmation"
                v-model="form.password_confirmation"
                autocomplete="new-password"
                :class="{ 'input-error': form.errors.password_confirmation }"
            />
            <p v-if="form.errors.password_confirmation" class="mt-1 text-sm text-skin-error">{{ form.errors.password_confirmation }}</p>
        </div>

    </form>
</template>

<script setup>
import { computed, inject, reactive, ref } from 'vue'

defineProps({
    profileUser: { type: Object, required: true },
    errorsFields: { type: Array, default: () => [] },
})

const form = inject('passwordForm')

// ── Generator state ──────────────────────────────────────────────────────────

const showGenerator = ref(false)
const generatedPassword = ref('')
const copied = ref(false)

const options = reactive({
    length: 16,
    uppercase: true,
    numbers: true,
    symbols: true,
})

const charsetOptions = [
    { key: 'uppercase', label: 'Uppercase', sample: 'A–Z' },
    { key: 'numbers',   label: 'Numbers',   sample: '0–9' },
    { key: 'symbols',   label: 'Symbols',   sample: '!@#' },
]

const CHARSETS = {
    lower:     'abcdefghijklmnopqrstuvwxyz',
    uppercase: 'ABCDEFGHIJKLMNOPQRSTUVWXYZ',
    numbers:   '0123456789',
    symbols:   '!@#$%^&*()-_=+[]{}|;:,.<>?',
}

function generate() {
    let pool = CHARSETS.lower
    const required = []

    if (options.uppercase) { pool += CHARSETS.uppercase; required.push(CHARSETS.uppercase) }
    if (options.numbers)   { pool += CHARSETS.numbers;   required.push(CHARSETS.numbers) }
    if (options.symbols)   { pool += CHARSETS.symbols;   required.push(CHARSETS.symbols) }

    const array = new Uint32Array(options.length)
    crypto.getRandomValues(array)

    const chars = Array.from(array).map((n) => pool[n % pool.length])

    // Guarantee at least one character from each required charset
    required.forEach((charset, i) => {
        const pos = i % options.length
        const idx = array[pos] % charset.length
        chars[pos] = charset[idx]
    })

    // Shuffle to avoid predictable positions
    for (let i = chars.length - 1; i > 0; i--) {
        const j = array[i] % (i + 1);
        [chars[i], chars[j]] = [chars[j], chars[i]]
    }

    generatedPassword.value = chars.join('')
    copied.value = false
}

function applyGenerated() {
    form.password = generatedPassword.value
    form.password_confirmation = generatedPassword.value
    showGenerator.value = false
}

async function copyGenerated() {
    if (!generatedPassword.value) return
    await navigator.clipboard.writeText(generatedPassword.value)
    copied.value = true
    setTimeout(() => { copied.value = false }, 2000)
}

// Generate an initial password when the panel opens
function toggleGenerator() {
    showGenerator.value = !showGenerator.value
    if (showGenerator.value && !generatedPassword.value) generate()
}

// ── Strength meter ────────────────────────────────────────────────────────────

const strength = computed(() => {
    const p = form.password
    if (!p) return { score: 0, label: '', color: '', barColor: '' }

    let score = 0
    if (p.length >= 8)              score++
    if (p.length >= 14)             score++
    if (/[A-Z]/.test(p) && /[a-z]/.test(p)) score++
    if (/[0-9]/.test(p))            score++
    if (/[^A-Za-z0-9]/.test(p))    score++

    // Normalise to 1–4
    const level = Math.min(4, Math.max(1, Math.ceil(score * 4 / 5)))

    const map = {
        1: { label: 'Weak',      color: 'text-red-500',    barColor: 'bg-red-500' },
        2: { label: 'Fair',      color: 'text-orange-500', barColor: 'bg-orange-500' },
        3: { label: 'Good',      color: 'text-yellow-500', barColor: 'bg-yellow-500' },
        4: { label: 'Strong',    color: 'text-green-600',  barColor: 'bg-green-500' },
    }

    return { score: level, ...map[level] }
})
</script>
