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
                    <AppInputText
                        v-model="option.name"
                        type="text"
                        :placeholder="__('settings::admin.option_name_placeholder')"
                        class="flex-1"
                    />
                    <div class="relative w-28">
                        <AppInputText
                            v-model.number="option.price"
                            type="number"
                            min="0"
                            :placeholder="__('settings::admin.price')"
                            class="pr-7"
                        />
                        <span class="pointer-events-none absolute right-2 top-1/2 -translate-y-1/2 text-xs text-skin-neutral-8">{{ __('settings::admin.tk') }}</span>
                    </div>
                    <label class="flex items-center gap-1.5 text-xs text-skin-neutral-9" :title="option.enabled ? 'Enabled' : 'Disabled'">
                        <AppCheckbox v-model="option.enabled" />
                        <span class="hidden sm:inline">{{ __('settings::admin.on') }}</span>
                    </label>
                    <AppButton
                        class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg text-skin-neutral-9 transition-colors hover:bg-red-50 hover:text-red-500"
                        :title="__('settings::admin.remove_option')"
                        @click="removeOption(index)"
                    >
                        <i class="ri-close-line text-base"></i>
                    </AppButton>
                </div>
            </div>

            <AppButton
                class="btn btn-secondary mt-2"
                @click="addOption"
            >
                <i class="ri-add-line"></i>
                {{ __('settings::admin.add_option') }}
            </AppButton>
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
