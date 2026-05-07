<template>
    <Head :title="__('Settings')"></Head>

    <AppSectionHeader :title="__('Settings')" :bread-crumb="breadCrumb" />

    <div class="mt-6 flex gap-6">
        <!-- Group sidebar navigation -->
        <aside class="w-44 shrink-0">
            <nav class="flex flex-col gap-1">
                <Link
                    v-for="g in groups"
                    :key="g"
                    :href="route('settings.show', { group: g })"
                    :class="[
                        'rounded-md px-4 py-2 text-sm font-medium capitalize transition-colors',
                        g === group
                            ? 'bg-skin-primary-10 text-skin-neutral-1'
                            : 'text-skin-neutral-11 hover:bg-skin-neutral-3',
                    ]"
                >
                    {{ groupLabel(g) }}
                </Link>
            </nav>
        </aside>

        <!-- Active group form -->
        <div class="min-w-0 flex-1">
            <div class="rounded-lg bg-skin-neutral-1 p-6 shadow-sm ring-1 ring-skin-neutral-4">
                <form @submit.prevent="submit">
                    <component
                        :is="currentGroupComponent"
                        :errors-fields="errorsFields"
                    />

                    <div class="mt-8 flex justify-end border-t border-skin-neutral-4 pt-6">
                        <AppButton
                            type="submit"
                            class="btn btn-primary"
                            :loading="form.processing"
                        >
                            {{ __('Save Settings') }}
                        </AppButton>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed, markRaw, onUnmounted, provide } from 'vue'
import { Head, useForm } from '@inertiajs/vue3'
import useFormErrors from '@/Composables/useFormErrors'
import GeneralGroup from './Components/GeneralGroup.vue'
import BrandingGroup from './Components/BrandingGroup.vue'
import ContactGroup from './Components/ContactGroup.vue'
import SocialGroup from './Components/SocialGroup.vue'
import SeoGroup from './Components/SeoGroup.vue'
import MailGroup from './Components/MailGroup.vue'

const props = defineProps({
    group: { type: String, required: true },
    settings: { type: Object, required: true },
    groups: { type: Array, required: true },
})

const { errorsFields } = useFormErrors()

const groupComponents = {
    general: markRaw(GeneralGroup),
    branding: markRaw(BrandingGroup),
    contact: markRaw(ContactGroup),
    social: markRaw(SocialGroup),
    seo: markRaw(SeoGroup),
    mail: markRaw(MailGroup),
}

const currentGroupComponent = computed(() => groupComponents[props.group])

// Build submittable form fields, excluding display-only _url keys.
const buildFormFields = () => {
    const fields = {}
    for (const [key, value] of Object.entries(props.settings)) {
        if (!key.endsWith('_url')) {
            fields[key] = value
        }
    }

    // Add remove_previous_* flags for each image field detected via a _url companion.
    for (const key of Object.keys(props.settings)) {
        if (key.endsWith('_url')) {
            const baseKey = key.slice(0, -4)
            fields[`remove_previous_${baseKey}`] = false
        }
    }

    return fields
}

const form = useForm(buildFormFields())

// Provide the Inertia form and display URLs to group child components.
provide('settingsForm', form)
provide('settingsUrls', Object.fromEntries(
    Object.entries(props.settings).filter(([k]) => k.endsWith('_url')),
))

const submit = () => {
    form.post(route('settings.update', { group: props.group }), {
        preserveScroll: true,
    })
}

const groupLabel = (g) => {
    const labels = {
        general: 'General',
        branding: 'Branding',
        contact: 'Contact',
        social: 'Social',
        seo: 'SEO',
        mail: 'Mail',
    }
    return labels[g] ?? g
}

const breadCrumb = [{ label: 'Settings' }]

onUnmounted(() => form.reset())
</script>

