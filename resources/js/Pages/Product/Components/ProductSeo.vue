<template>
    <div class="mt-10">
        <h4 class="flex items-end justify-between text-xl">
            <span>{{ __('common.seo.preview_heading') }}</span>

            <a
                href="#"
                class="text-sm text-skin-primary-9 hover:underline"
                @click.prevent="toggleSeoForm"
            >
                {{ __('common.seo.edit') }}
            </a>
        </h4>

        <small
            v-show="productStore.showSeoAlert()"
            class="block text-sm text-skin-neutral-9"
        >
            {{ __('common.seo.fill_hint') }}
        </small>

        <template v-if="showSeoForm">
            <div>
                <div class="mb-2 mt-2 flex items-center">
                    <div
                        class="mr-4 flex h-10 w-10 rounded-full bg-linear-to-bl from-skin-neutral-3 to-skin-neutral-6"
                    ></div>

                    <div class="flex flex-col items-start">
                        <p class="text-sm">{{ __('common.seo.site_name') }}</p>
                        <p class="-mt-1 text-sm text-skin-neutral-10">
                            https://your-domain.com/product/{{
                                productStore.getSlug()
                            }}
                        </p>
                    </div>
                </div>

                <div>
                    <p class="text-2xl text-skin-primary-11">
                        {{ productStore.product.meta_tag_title }}
                    </p>

                    <p class="">
                        {{ productStore.product.meta_tag_description }}
                    </p>
                </div>
            </div>

            <div class="mt-5 border-t border-dashed pt-5">
                <AppLabel for="meta_tag_title">{{ __('common.seo.meta_tag_title') }}</AppLabel>
                <AppInputText
                    id="meta_tag_title"
                    v-model="productStore.product.meta_tag_title"
                    type="text"
                    maxlength="60"
                    :class="{
                        'input-error': errorsFields.includes('meta_tag_title')
                    }"
                />
                <small class="block text-right text-skin-neutral-9">
                    {{ __('common.seo.of_limit', { remaining: productStore.getRemainingChars('meta_tag_title', 60), limit: 60 }) }}
                </small>
            </div>

            <div class="mt-5">
                <AppLabel for="meta_tag_description"
                    >{{ __('common.seo.meta_tag_description') }}</AppLabel
                >
                <AppTextArea
                    id="meta_tag_description"
                    v-model="productStore.product.meta_tag_description"
                    class="h-24"
                    maxlength="160"
                    :class="{
                        'input-error': errorsFields.includes(
                            'meta_tag_description'
                        )
                    }"
                />
                <small class="block text-right text-skin-neutral-9">
                    {{ __('common.seo.of_limit', { remaining: productStore.getRemainingChars('meta_tag_description', 160), limit: 160 }) }}
                </small>
            </div>
        </template>
    </div>
</template>

<script setup>
import useFormErrors from '@/Composables/useFormErrors'
import useFormContext from '@/Composables/useFormContext'
import { useProductStore } from '../ProductStore'
import { ref, onMounted } from 'vue'
const productStore = useProductStore()
const { errorsFields } = useFormErrors()

const { isCreate } = useFormContext()

const showSeoForm = ref(false)

onMounted(() => {
    if (!isCreate.value) {
        showSeoForm.value = true
    }
})

const toggleSeoForm = () => {
    if (
        !showSeoForm.value &&
        isCreate.value &&
        !productStore.product.meta_tag_title.length &&
        !productStore.product.meta_tag_description.length
    ) {
        productStore.initSeoTags()
    }

    showSeoForm.value = !showSeoForm.value
}
</script>
