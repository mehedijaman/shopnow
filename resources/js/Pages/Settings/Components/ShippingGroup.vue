<template>
    <div class="space-y-6">
        <!-- Shipping Options -->
        <div>
            <AppLabel :value="__('settings::admin.shipping_options')" />
            <p class="mb-3 text-xs text-skin-neutral-9">{{ __('settings::admin.shipping_options_hint') }}</p>

            <div class="space-y-3">
                <div
                    v-for="(option, index) in form.options"
                    :key="index"
                    class="flex items-center gap-3 rounded-lg border border-skin-neutral-4 bg-skin-neutral-2/40 p-3"
                >
                    <input
                        v-model="option.name"
                        type="text"
                        :placeholder="__('settings::admin.option_name_placeholder')"
                        class="flex-1 rounded-lg border border-skin-neutral-4 bg-white px-3 py-2 text-sm text-skin-neutral-12 focus:border-skin-primary-7 focus:outline-none focus:ring-1 focus:ring-skin-primary-7"
                    />
                    <div class="relative w-28">
                        <input
                            v-model.number="option.price"
                            type="number"
                            min="0"
                            :placeholder="__('settings::admin.price')"
                            class="w-full rounded-lg border border-skin-neutral-4 bg-white px-3 py-2 pr-7 text-sm text-skin-neutral-12 focus:border-skin-primary-7 focus:outline-none focus:ring-1 focus:ring-skin-primary-7"
                        />
                        <span class="pointer-events-none absolute right-2 top-1/2 -translate-y-1/2 text-xs text-skin-neutral-8">{{ __('settings::admin.tk') }}</span>
                    </div>
                    <label class="flex items-center gap-1.5 text-xs text-skin-neutral-9" :title="option.enabled ? 'Enabled' : 'Disabled'">
                        <input
                            v-model="option.enabled"
                            type="checkbox"
                            class="h-4 w-4 rounded border-skin-neutral-4 text-skin-primary-7 focus:ring-skin-primary-7"
                        />
                        <span class="hidden sm:inline">{{ __('settings::admin.on') }}</span>
                    </label>
                    <button
                        type="button"
                        @click="removeOption(index)"
                        class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg text-skin-neutral-9 transition-colors hover:bg-red-50 hover:text-red-500"
                        :title="__('settings::admin.remove_option')"
                    >
                        <i class="ri-close-line text-base"></i>
                    </button>
                </div>
            </div>

            <button
                type="button"
                @click="addOption"
                class="mt-3 inline-flex items-center gap-1.5 rounded-lg border border-dashed border-skin-neutral-5 px-3 py-2 text-xs font-semibold text-skin-primary-7 transition-colors hover:border-skin-primary-7 hover:bg-skin-primary-2"
            >
                <i class="ri-add-line"></i>
                {{ __('settings::admin.add_option') }}
            </button>
        </div>

        <!-- Free Shipping Threshold -->
        <div>
            <AppLabel for="free_shipping_threshold" :value="__('settings::admin.free_shipping_threshold')" />
            <p class="mb-1 text-xs text-skin-neutral-9">{{ __('settings::admin.free_shipping_hint') }}</p>
            <AppInputText
                id="free_shipping_threshold"
                type="number"
                min="0"
                v-model="form.free_shipping_threshold"
                :placeholder="__('settings::admin.free_shipping_placeholder')"
                :class="{ 'input-error': errorsFields.includes('free_shipping_threshold') }"
            />
            <p v-if="errorsFields.includes('free_shipping_threshold')" class="mt-1 text-sm text-red-500">
                {{ errors.free_shipping_threshold }}
            </p>
        </div>
    </div>
</template>

<script setup>
import { inject } from 'vue'
import useFormErrors from '@/Composables/useFormErrors'

defineProps({
    errorsFields: { type: Array, default: () => [] },
})

const form = inject('settingsForm')
const { errors } = useFormErrors()

function addOption() {
    if (!Array.isArray(form.options)) {
        form.options = []
    }
    form.options.push({ id: '', name: '', price: 0, enabled: true })
}

function removeOption(index) {
    form.options.splice(index, 1)
}
</script>
