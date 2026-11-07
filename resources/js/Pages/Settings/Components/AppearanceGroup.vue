<template>
    <div class="space-y-6">
        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label
                    for="appearance-preset"
                    class="mb-1 block text-sm font-medium"
                    >{{ __('settings::appearance.preset') }}</label
                >
                <select
                    id="appearance-preset"
                    :value="form.palette.preset"
                    class="form-select w-full border-skin-neutral-7 bg-skin-neutral-1 text-skin-neutral-12"
                    @change="applyPreset($event.target.value)"
                >
                    <option
                        v-for="name in Object.keys(meta.presets)"
                        :key="name"
                        :value="name"
                    >
                        {{ __(`settings::appearance.presets.${name}`) }}
                    </option>
                    <option value="custom">
                        {{ __('settings::appearance.presets.custom') }}
                    </option>
                </select>
            </div>
            <div>
                <label
                    for="appearance-neutral"
                    class="mb-1 block text-sm font-medium"
                    >{{ __('settings::appearance.neutral') }}</label
                >
                <select
                    id="appearance-neutral"
                    v-model="form.palette.neutral"
                    class="form-select w-full border-skin-neutral-7 bg-skin-neutral-1 text-skin-neutral-12"
                    @change="markCustom"
                >
                    <option
                        v-for="name in ['slate', 'gray', 'stone', 'zinc']"
                        :key="name"
                        :value="name"
                    >
                        {{ __(`settings::appearance.neutrals.${name}`) }}
                    </option>
                </select>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <button
                v-for="name in ['light', 'dark']"
                :key="name"
                type="button"
                :aria-pressed="mode === name"
                class="rounded-lg border px-4 py-2 text-sm"
                :class="
                    mode === name
                        ? 'border-skin-primary-9 bg-skin-primary-3'
                        : 'border-skin-neutral-6'
                "
                @click="mode = name"
            >
                {{ __(`settings::appearance.${name}`) }}
            </button>
            <span v-if="form.isDirty" class="text-sm text-skin-warning">{{
                __('settings::appearance.unsaved')
            }}</span>
        </div>

        <div class="grid gap-6 xl:grid-cols-2">
            <div class="space-y-4">
                <div v-for="field in ['primary', 'accent']" :key="field">
                    <label
                        :for="'appearance-' + mode + '-' + field"
                        class="mb-1 block text-sm font-medium"
                        >{{ __(`settings::appearance.colors.${field}`) }}</label
                    >
                    <div class="flex gap-2">
                        <input
                            type="color"
                            :value="colorValue(field)"
                            :aria-label="
                                __(`settings::appearance.colors.${field}`)
                            "
                            class="h-10 w-12 cursor-pointer rounded border border-skin-neutral-6"
                            @input="setColor(field, $event.target.value)"
                        />
                        <input
                            :id="'appearance-' + mode + '-' + field"
                            v-model="form.palette[mode][field]"
                            type="text"
                            maxlength="7"
                            spellcheck="false"
                            class="form-input min-w-0 flex-1 border-skin-neutral-7 bg-skin-neutral-1 font-mono text-skin-neutral-12"
                            :aria-invalid="Boolean(fieldError(field))"
                            @input="markCustom"
                        />
                    </div>
                    <p
                        v-if="fieldError(field)"
                        class="mt-1 text-sm text-red-600"
                    >
                        {{ fieldError(field) }}
                    </p>
                </div>

                <details class="rounded-lg border border-skin-neutral-5 p-4">
                    <summary class="cursor-pointer text-sm font-semibold">
                        {{ __('settings::appearance.advanced') }}
                    </summary>
                    <div class="mt-4 space-y-4">
                        <div
                            v-for="field in meta.fields.filter(
                                (name) => !['primary', 'accent'].includes(name)
                            )"
                            :key="field"
                        >
                            <label
                                :for="'appearance-' + mode + '-' + field"
                                class="mb-1 block text-sm font-medium"
                                >{{
                                    __(`settings::appearance.colors.${field}`)
                                }}</label
                            >
                            <div class="flex gap-2">
                                <input
                                    type="color"
                                    :value="colorValue(field)"
                                    :aria-label="
                                        __(
                                            `settings::appearance.colors.${field}`
                                        )
                                    "
                                    class="h-10 w-12 cursor-pointer rounded border border-skin-neutral-6"
                                    @input="
                                        setColor(field, $event.target.value)
                                    "
                                />
                                <input
                                    :id="'appearance-' + mode + '-' + field"
                                    v-model="form.palette[mode][field]"
                                    type="text"
                                    maxlength="7"
                                    spellcheck="false"
                                    class="form-input min-w-0 flex-1 border-skin-neutral-7 bg-skin-neutral-1 font-mono text-skin-neutral-12"
                                    :aria-invalid="Boolean(fieldError(field))"
                                    @input="markCustom"
                                />
                            </div>
                            <p
                                v-if="fieldError(field)"
                                class="mt-1 text-sm text-red-600"
                            >
                                {{ fieldError(field) }}
                            </p>
                        </div>
                    </div>
                </details>
                <p class="text-xs text-skin-neutral-11">
                    {{ __('settings::appearance.contrast_hint') }}
                </p>
                <p
                    v-for="(error, key) in allErrors"
                    :key="key"
                    class="text-sm text-red-600"
                >
                    {{ error }}
                </p>
            </div>

            <div class="space-y-3">
                <div
                    class="flex items-center justify-between text-sm font-semibold"
                >
                    <span>{{ __('settings::appearance.preview') }}</span>
                    <span
                        v-if="previewing"
                        role="status"
                        class="text-xs font-normal text-skin-neutral-11"
                        >{{ __('settings::appearance.updating') }}</span
                    >
                </div>
                <div
                    v-if="variables[mode]"
                    class="site-preview overflow-hidden rounded-xl border"
                    :style="variables[mode]"
                    :aria-busy="previewing"
                >
                    <div class="preview-topbar px-4 py-2 text-xs">
                        {{ __('settings::appearance.preview_topbar') }}
                    </div>
                    <div
                        class="preview-header flex items-center justify-between gap-3 p-4"
                    >
                        <strong>{{
                            __('settings::appearance.preview_shop')
                        }}</strong>
                        <span>{{
                            __('settings::appearance.preview_navigation')
                        }}</span>
                    </div>
                    <div class="space-y-4 p-4 sm:p-6">
                        <h3 class="text-lg font-bold">
                            {{ __('settings::appearance.preview_heading') }}
                        </h3>
                        <div
                            class="preview-card space-y-3 rounded-xl border p-4"
                        >
                            <div
                                class="preview-image flex h-24 items-center justify-center rounded-lg"
                                aria-hidden="true"
                            >
                                <i class="ri-shopping-bag-3-line text-4xl"></i>
                            </div>
                            <h4 class="font-semibold">
                                {{ __('settings::appearance.preview_product') }}
                            </h4>
                            <p class="preview-muted text-sm">
                                {{ __('settings::appearance.preview_details') }}
                            </p>
                            <div class="flex items-center justify-between">
                                <strong>৳1,250</strong
                                ><span
                                    class="preview-accent rounded px-2 py-1 text-xs"
                                    >{{
                                        __('settings::appearance.preview_new')
                                    }}</span
                                >
                            </div>
                            <button
                                type="button"
                                class="preview-button w-full rounded-lg px-4 py-2 text-sm font-semibold"
                            >
                                {{ __('settings::appearance.preview_cart') }}
                            </button>
                        </div>
                        <label
                            class="block text-sm font-semibold"
                            for="appearance-preview-email"
                            >{{
                                __('settings::appearance.preview_email')
                            }}</label
                        >
                        <input
                            id="appearance-preview-email"
                            type="email"
                            autocomplete="off"
                            class="preview-input w-full rounded-lg border px-3 py-2 text-sm"
                            :placeholder="
                                __(
                                    'settings::appearance.preview_email_placeholder'
                                )
                            "
                        />
                        <a
                            href="#appearance-preview"
                            class="preview-link text-sm"
                            @click.prevent
                            >{{
                                __('settings::appearance.preview_checkout')
                            }}</a
                        >
                    </div>
                    <div class="preview-footer p-4 text-sm">
                        {{ __('settings::appearance.preview_footer') }}
                    </div>
                </div>
                <p
                    v-if="previewFailure"
                    role="alert"
                    class="text-sm text-red-600"
                >
                    {{ previewFailure }}
                </p>
            </div>
        </div>

        <div class="flex flex-wrap gap-3">
            <button
                type="button"
                class="btn btn-secondary"
                :disabled="form.processing"
                @click="applyPreset('default')"
            >
                {{ __('settings::appearance.restore') }}
            </button>
            <button
                type="button"
                class="btn btn-secondary"
                :disabled="!form.isDirty || form.processing"
                @click="discard"
            >
                {{ __('settings::appearance.discard') }}
            </button>
        </div>
    </div>
</template>

<script setup>
import { computed, inject, onBeforeUnmount, ref, watch } from 'vue'
import { router } from '@inertiajs/vue3'
import axios from 'axios'

defineProps({ errorsFields: { type: Array, default: () => [] } })
const form = inject('settingsForm')
const meta = inject('appearanceMeta')
const translate = inject('translate')
const mode = ref('light')
const variables = ref({})
const previewErrors = ref({})
const previewing = ref(false)
const previewFailure = ref('')
let timer
let controller
let requestNumber = 0

const allErrors = computed(() => ({ ...form.errors, ...previewErrors.value }))
const fieldError = (field) => allErrors.value[`palette.${mode.value}.${field}`]
const colorValue = (field) =>
    /^#[0-9a-f]{6}$/i.test(form.palette[mode.value][field])
        ? form.palette[mode.value][field]
        : '#000000'
const markCustom = () => {
    form.palette.preset = 'custom'
}
const setColor = (field, value) => {
    form.palette[mode.value][field] = value.toUpperCase()
    markCustom()
}
const applyPreset = (name) => {
    if (name === 'custom') {
        markCustom()
        return
    }
    if (!meta.value.presets[name]) return
    form.palette = JSON.parse(JSON.stringify(meta.value.presets[name]))
    form.clearErrors()
}
const discard = () => {
    form.reset()
    form.clearErrors()
}

watch(
    () => form.palette,
    () => {
        clearTimeout(timer)
        controller?.abort()
        const current = ++requestNumber
        previewing.value = true
        timer = setTimeout(async () => {
            controller = new AbortController()
            try {
                const { data } = await axios.post(
                    route('settings.appearance.preview', {
                        group: 'appearance'
                    }),
                    {
                        palette: form.palette,
                        revision: form.revision
                    },
                    { signal: controller.signal }
                )
                if (current !== requestNumber) return
                variables.value = data.variables
                previewErrors.value = {}
                previewFailure.value = ''
            } catch (error) {
                if (current !== requestNumber || axios.isCancel(error)) return
                previewErrors.value = Object.fromEntries(
                    Object.entries(error.response?.data?.errors || {}).map(
                        ([key, messages]) => [key, messages[0]]
                    )
                )
                previewFailure.value =
                    error.response?.status === 422
                        ? translate('settings::appearance.preview_invalid')
                        : translate('settings::appearance.preview_failed')
            } finally {
                if (current === requestNumber) previewing.value = false
            }
        }, 300)
    },
    { deep: true, immediate: true }
)

const beforeUnload = (event) => {
    if (form.isDirty) {
        event.preventDefault()
        event.returnValue = ''
    }
}
window.addEventListener('beforeunload', beforeUnload)
const stopNavigationGuard = router.on('before', (event) => {
    if (
        form.isDirty &&
        event.detail.visit.method.toLowerCase() === 'get' &&
        !window.confirm(translate('settings::appearance.leave_unsaved'))
    )
        event.preventDefault()
})
onBeforeUnmount(() => {
    clearTimeout(timer)
    controller?.abort()
    stopNavigationGuard()
    window.removeEventListener('beforeunload', beforeUnload)
})
</script>

<style scoped>
.site-preview {
    background: var(--site-background);
    color: var(--site-text);
    border-color: var(--site-border);
}
.preview-topbar {
    background: var(--site-topbar);
    color: var(--site-topbar-text);
}
.preview-header {
    background: var(--site-header);
    color: var(--site-header-text);
}
.preview-footer {
    background: var(--site-footer);
    color: var(--site-footer-text);
}
.preview-card,
.preview-input {
    background: var(--site-surface);
    color: var(--site-text);
    border-color: var(--site-border);
}
.preview-image {
    background: var(--site-surface-muted);
    color: var(--site-link);
}
.preview-muted,
.preview-input::placeholder {
    color: var(--site-muted);
}
.preview-button {
    background: var(--site-primary);
    color: var(--site-on-primary);
}
.preview-button:hover {
    background: var(--site-primary-hover);
}
.preview-accent {
    background: var(--site-accent);
    color: var(--site-on-accent);
}
.preview-link {
    color: var(--site-link);
}
.preview-input:focus,
.preview-button:focus-visible {
    outline: 2px solid var(--site-focus);
    outline-offset: 2px;
}
</style>
