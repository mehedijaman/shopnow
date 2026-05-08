<template>
    <Head :title="pageTitle"></Head>
    <AppSectionHeader :title="pageTitle" :bread-crumb="breadCrumb">
        <template #right>
            <AppButton class="btn btn-primary" @click="submitForm">
                <i class="ri-save-line mr-1"></i> Save
            </AppButton>
        </template>
    </AppSectionHeader>

    <div class="flex flex-col xl:flex-row">
        <!-- Main content -->
        <AppCard class="w-full xl:w-8/12">
            <template #title>
                <div class="flex items-center gap-2">
                    Page Content
                    <span v-if="page?.is_system" class="rounded-full bg-blue-100 px-2.5 py-0.5 text-xs font-medium text-blue-700">System Page</span>
                </div>
            </template>
            <template #content>
                <AppFormErrors class="mb-4" />

                <div>
                    <AppLabel for="title">Title</AppLabel>
                    <AppInputText
                        id="title"
                        v-model="form.title"
                        type="text"
                        :class="{ 'input-error': form.errors.title }"
                    />
                    <p v-if="form.errors.title" class="mt-1 text-xs text-red-500">{{ form.errors.title }}</p>
                </div>

                <div class="mt-5">
                    <AppLabel for="content">Content</AppLabel>
                    <AppTipTapEditor
                        v-model="form.content"
                        editor-id="content"
                        :class="{ 'app-tip-tap-error': form.errors.content }"
                        :file-upload-url="route('page.uploadEditorImage')"
                    />
                    <p v-if="form.errors.content" class="mt-1 text-xs text-red-500">{{ form.errors.content }}</p>
                </div>

                <!-- SEO -->
                <div class="mt-8 border-t border-dashed border-skin-neutral-4 pt-6">
                    <h4 class="mb-4 text-sm font-semibold text-skin-neutral-10">SEO Settings</h4>

                    <div>
                        <AppLabel for="meta_tag_title">Meta Tag Title <span class="text-skin-neutral-7 font-normal">(max 60 chars)</span></AppLabel>
                        <AppInputText id="meta_tag_title" v-model="form.meta_tag_title" type="text" maxlength="60" />
                        <small class="block text-right text-xs text-skin-neutral-7">{{ (form.meta_tag_title ?? '').length }}/60</small>
                    </div>

                    <div class="mt-4">
                        <AppLabel for="meta_tag_description">Meta Tag Description <span class="text-skin-neutral-7 font-normal">(max 160 chars)</span></AppLabel>
                        <AppTextArea id="meta_tag_description" v-model="form.meta_tag_description" class="h-24" maxlength="160" />
                        <small class="block text-right text-xs text-skin-neutral-7">{{ (form.meta_tag_description ?? '').length }}/160</small>
                    </div>
                </div>
            </template>
        </AppCard>

        <!-- Sidebar -->
        <AppCard class="mt-4 w-full xl:ml-5 xl:mt-0 xl:w-4/12">
            <template #title>Page Settings</template>
            <template #content>
                <div>
                    <AppLabel for="published_at">Publish Date</AppLabel>
                    <AppInputText id="published_at" v-model="form.published_at" type="date" />
                    <small class="block text-xs text-skin-neutral-7">Leave empty to save as draft.</small>
                </div>

                <div class="mt-5">
                    <AppLabel>Featured Image</AppLabel>
                    <AppInputFile
                        v-model="form.image"
                        :image-preview-url="imagePreviewUrl"
                        @remove-file="form.remove_previous_image = true"
                    />
                </div>
            </template>
        </AppCard>
    </div>

    <AppButton class="btn btn-primary mt-6" @click="submitForm">
        <i class="ri-save-line mr-1"></i> Save
    </AppButton>
</template>

<script setup>
import { computed } from 'vue'
import { Head, useForm } from '@inertiajs/vue3'
import useTitle from '@/Composables/useTitle'
import useFormContext from '@/Composables/useFormContext'

const { title: pageTitle } = useTitle('Page')
const { isCreate } = useFormContext()

const props = defineProps({
    page: { type: Object, default: null },
})

const breadCrumb = [
    { label: 'Home', href: route('dashboard.index') },
    { label: 'Pages', href: route('page.index') },
    { label: isCreate.value ? 'New Page' : 'Edit Page', last: true },
]

const form = useForm({
    title: props.page?.title ?? '',
    content: props.page?.content ?? '',
    image: null,
    remove_previous_image: false,
    meta_tag_title: props.page?.meta_tag_title ?? '',
    meta_tag_description: props.page?.meta_tag_description ?? '',
    published_at: props.page?.published_at ?? '',
})

const imagePreviewUrl = computed(() => (!isCreate.value ? props.page?.image_url ?? null : null))

function submitForm() {
    if (isCreate.value) {
        form.post(route('page.store'))
    } else {
        form.transform((data) => ({ ...data, _method: 'PUT' })).post(route('page.update', props.page.id))
    }
}
</script>
