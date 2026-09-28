<template>
    <div class="mt-10">
        <h4 class="flex items-end justify-between text-xl">
            <span>{{ __('common.seo.preview_heading') }}</span>

            <a
                href="#"
                class="text-skin-primary-9 text-sm hover:underline"
                @click.prevent="toggleSeoForm"
            >
                {{ __('common.seo.edit') }}
            </a>
        </h4>

        <small
            v-show="categoryStore.showSeoAlert()"
            class="text-skin-neutral-9 block text-sm"
        >
            {{ __('common.seo.fill_hint') }}
        </small>

        <template v-if="showSeoForm">
            <div>
                <div class="mb-2 mt-2 flex items-center">
                    <div
                        class="from-skin-neutral-3 to-skin-neutral-6 mr-4 flex h-10 w-10 rounded-full bg-linear-to-bl"
                    ></div>

                    <div class="flex flex-col items-start">
                        <p class="text-sm">{{ __('common.seo.site_name') }}</p>
                        <p class="text-skin-neutral-10 -mt-1 text-sm">
                            https://your-domain.com/blog/category/{{
                                categoryStore.getSlug()
                            }}
                        </p>
                    </div>
                </div>

                <div>
                    <p class="text-skin-primary-11 text-2xl">
                        {{ categoryStore.category.meta_tag_title }}
                    </p>

                    <p class="">
                        {{ categoryStore.category.meta_tag_description }}
                    </p>
                </div>
            </div>

            <div class="mt-5 border-t border-dashed pt-5">
                <AppLabel for="meta_tag_title">{{ __('common.seo.meta_tag_title') }}</AppLabel>
                <AppInputText
                    id="meta_tag_title"
                    v-model="categoryStore.category.meta_tag_title"
                    type="text"
                    maxlength="60"
                    :class="{
                        'input-error': errorsFields.includes('meta_tag_title')
                    }"
                />
                <small class="text-skin-neutral-9 block text-right">
                    {{ __('common.seo.of_limit', { remaining: categoryStore.getRemainingChars('meta_tag_title', 60), limit: 60 }) }}
                </small>
            </div>

            <div class="mt-5">
                <AppLabel for="meta_tag_description"
                    >{{ __('common.seo.meta_tag_description') }}</AppLabel
                >
                <AppTextArea
                    id="meta_tag_description"
                    v-model="categoryStore.category.meta_tag_description"
                    class="h-24"
                    maxlength="160"
                    :class="{
                        'input-error': errorsFields.includes(
                            'meta_tag_description'
                        )
                    }"
                />
                <small class="text-skin-neutral-9 block text-right">
                    {{
                        __('common.seo.of_limit', { remaining: categoryStore.getRemainingChars('meta_tag_description', 160), limit: 160 })
                    }}
                </small>
            </div>
        </template>
    </div>
</template>

<script setup>
import useFormErrors from '@/Composables/useFormErrors'
import useFormContext from '@/Composables/useFormContext'
import { useCategoryStore } from '../CategoryStore'
import { ref, onMounted } from 'vue'

const categoryStore = useCategoryStore()
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
        !categoryStore.category.meta_tag_title.length &&
        !categoryStore.category.meta_tag_description.length
    ) {
        categoryStore.initSeoTags()
    }

    showSeoForm.value = !showSeoForm.value
}
</script>
