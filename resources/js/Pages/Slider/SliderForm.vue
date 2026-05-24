<template>
    <AppSectionHeader title="Sliders" :bread-crumb="breadCrumb"></AppSectionHeader>

    <AppCard class="w-full md:w-3/4 xl:w-1/2">
        <template #title>{{ title }}</template>
        <template #content>
            <AppFormErrors class="mb-4" />
            <form class="space-y-5 pt-4">
                <div>
                    <AppLabel for="title">Title</AppLabel>
                    <AppInputText
                        id="title"
                        v-model="form.title"
                        type="text"
                        :class="{ 'input-error': errorsFields.includes('title') }"
                        autocomplete="off"
                    />
                </div>

                <div>
                    <AppLabel for="description">Description</AppLabel>
                    <AppTextArea
                        id="description"
                        v-model="form.description"
                        rows="3"
                        :class="{ 'input-error': errorsFields.includes('description') }"
                    />
                </div>

                <div>
                    <AppInputFile
                        v-model="form.image"
                        :image-preview-url="getImagePreviewURL()"
                        @remove-file="form.remove_previous_image = true"
                    ></AppInputFile>
                </div>

                <div>
                    <AppLabel for="bg_color">Background Color <span class="text-xs font-normal text-skin-neutral-7">(used when no image)</span></AppLabel>
                    <div class="mt-1 flex items-center gap-3">
                        <input
                            id="bg_color"
                            v-model="form.bg_color"
                            type="color"
                            class="h-9 w-12 cursor-pointer rounded border border-skin-neutral-4 bg-transparent p-0.5"
                        />
                        <AppInputText
                            v-model="form.bg_color"
                            type="text"
                            placeholder="#1e3a5f"
                            maxlength="7"
                            class="w-32 font-mono"
                            :class="{ 'input-error': errorsFields.includes('bg_color') }"
                            autocomplete="off"
                        />
                        <button
                            v-if="form.bg_color"
                            type="button"
                            class="text-xs text-skin-neutral-7 hover:text-skin-neutral-11"
                            @click="form.bg_color = ''"
                        >
                            Clear
                        </button>
                    </div>
                </div>

                <div>
                    <AppLabel for="url">Link URL</AppLabel>
                    <AppInputText
                        id="url"
                        v-model="form.url"
                        type="text"
                        placeholder="https://example.com/page"
                        :class="{ 'input-error': errorsFields.includes('url') }"
                        autocomplete="off"
                    />
                </div>

                <div>
                    <AppLabel for="button_text">Button Text</AppLabel>
                    <AppInputText
                        id="button_text"
                        v-model="form.button_text"
                        type="text"
                        placeholder="e.g. Shop Now"
                        :class="{ 'input-error': errorsFields.includes('button_text') }"
                        autocomplete="off"
                    />
                </div>

                <div>
                    <AppLabel for="order">Display Order</AppLabel>
                    <AppInputText
                        id="order"
                        v-model="form.order"
                        type="number"
                        placeholder="1"
                        :class="{ 'input-error': errorsFields.includes('order') }"
                        autocomplete="off"
                    />
                </div>

                <div>
                    <label class="flex cursor-pointer items-center gap-2">
                        <AppCheckbox
                            id="active"
                            v-model="form.active"
                            name="active"
                            :value="true"
                        />
                        <span class="text-sm font-medium">Active</span>
                    </label>
                </div>
            </form>
        </template>
        <template #footer>
            <AppButton
                v-if="can(isCreate ? 'slider-create' : 'slider-edit')"
                class="btn btn-primary"
                @click="submitForm"
            >
                {{ __('Save') }}
            </AppButton>
        </template>
    </AppCard>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3'
import useAuthCan from '@/Composables/useAuthCan'
import useTitle from '@/Composables/useTitle'
import useFormContext from '@/Composables/useFormContext'
import useFormErrors from '@/Composables/useFormErrors'

const props = defineProps({
    slider: { type: Object, default: null },
})

const breadCrumb = [
    { label: 'Home', href: route('dashboard.index') },
    { label: 'Sliders', href: route('slider.index') },
    { label: 'Slider', last: true },
]

const { title } = useTitle('Slider')
const { isCreate } = useFormContext()
const { can } = useAuthCan()

const form = useForm({
    title: props.slider?.title ?? '',
    description: props.slider?.description ?? '',
    bg_color: props.slider?.bg_color ?? '',
    url: props.slider?.url ?? '',
    button_text: props.slider?.button_text ?? '',
    order: props.slider?.order ?? '',
    active: props.slider ? Boolean(props.slider.active) : true,
    image: null,
    image_url: props.slider?.image_url ?? null,
    remove_previous_image: false,
})

const getImagePreviewURL = () => {
    if (!isCreate.value && form.image_url) {
        return form.image_url
    }

    return null
}

const submitForm = () => {
    if (isCreate.value) {
        form.post(route('slider.store'))
    } else {
        form.transform((data) => ({
            ...data,
            _method: 'PUT',
        })).post(route('slider.update', props.slider.id))
    }
}

const { errorsFields } = useFormErrors()
</script>
