<template>
    <Head :title="__('Settings')"></Head>

    <AppSectionHeader :title="__('Settings')" :bread-crumb="breadCrumb" />

    <div class="mt-6 flex flex-col gap-4 sm:flex-row sm:gap-6">
        <!-- Group sidebar navigation -->
        <aside class="w-full sm:w-52 sm:shrink-0">
            <nav class="flex flex-row gap-1 overflow-x-auto pb-1 sm:flex-col sm:overflow-x-visible sm:pb-0">
                <Link
                    v-for="g in groups"
                    :key="g"
                    :href="route('settings.show', { group: g })"
                    :class="[
                        'flex shrink-0 items-center gap-2.5 rounded-md px-3 py-2.5 text-sm font-medium transition-colors sm:w-full',
                        g === group
                            ? 'bg-skin-primary-10 text-skin-neutral-1'
                            : 'text-skin-neutral-11 hover:bg-skin-neutral-3',
                    ]"
                >
                    <i :class="[groupIcon(g), 'text-base leading-none']"></i>
                    <span class="capitalize">{{ groupLabel(g) }}</span>
                </Link>
            </nav>
        </aside>

        <!-- Active group form -->
        <div class="min-w-0 flex-1">
            <div class="rounded-lg bg-skin-neutral-1 p-4 shadow-xs ring-1 ring-skin-neutral-4 sm:p-6">
                <div class="mb-6 border-b border-skin-neutral-4 pb-5">
                    <div class="flex items-center gap-2">
                        <span class="flex h-8 w-8 items-center justify-center rounded-md bg-skin-primary-10 text-skin-neutral-1">
                            <i :class="[groupIcon(group), 'text-base']"></i>
                        </span>
                        <h2 class="text-base font-semibold capitalize text-skin-neutral-12">
                            {{ groupLabel(group) }} {{ __('Settings') }}
                        </h2>
                    </div>
                    <p class="mt-2 text-sm text-skin-neutral-9">{{ groupDescription(group) }}</p>
                </div>
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
import ShippingGroup from './Components/ShippingGroup.vue'
import HomepageGroup from './Components/HomepageGroup.vue'
import PixelGroup from './Components/PixelGroup.vue'

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
    shipping: markRaw(ShippingGroup),
    homepage: markRaw(HomepageGroup),
    pixel: markRaw(PixelGroup),
}

const currentGroupComponent = computed(() => groupComponents[props.group])

// Build submittable form fields, excluding display-only _url keys.
const buildFormFields = () => {
    const fields = {}

    // Image fields are identified by having a companion _url key.
    // They must start as null so stored filename strings don't fail
    // Laravel's `image` validation rule when no new file is selected.
    const imageBaseKeys = new Set(
        Object.keys(props.settings)
            .filter((k) => k.endsWith('_url'))
            .map((k) => k.slice(0, -4)),
    )

    for (const [key, value] of Object.entries(props.settings)) {
        if (key.endsWith('_url')) continue
        fields[key] = imageBaseKeys.has(key) ? null : value
    }

    // Add remove_previous_* flags for each image field.
    for (const baseKey of imageBaseKeys) {
        fields[`remove_previous_${baseKey}`] = false
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
        shipping: 'Shipping',
        homepage: 'Homepage',
        pixel: 'Pixel',
    }
    return labels[g] ?? g
}

const groupIcon = (g) => {
    const icons = {
        general: 'ri-settings-2-line',
        branding: 'ri-palette-line',
        contact: 'ri-contacts-line',
        social: 'ri-share-line',
        seo: 'ri-search-2-line',
        mail: 'ri-mail-line',
        shipping: 'ri-truck-line',
        homepage: 'ri-home-line',
        pixel: 'ri-radar-line',
    }
    return icons[g] ?? 'ri-settings-line'
}

const groupDescription = (g) => {
    const descriptions = {
        general: 'Configure your site description and admin notification email.',
        branding: 'Upload your logo, favicon, and other brand assets.',
        contact: 'Manage public contact details like phone, email, and address.',
        social: 'Link your social media profiles to be shown on the site.',
        seo: 'Control meta title, description, and keywords for search engine visibility.',
        mail: 'Configure the SMTP server used to send outgoing emails.',
        shipping: 'Set flat rate shipping charge and free shipping threshold.',
        homepage: 'Control which sections are visible on the homepage.',
        pixel: 'Configure Meta Pixel and Conversions API tracking settings.',
    }
    return descriptions[g] ?? ''
}

const breadCrumb = [{ label: 'Settings' }]

onUnmounted(() => form.reset())
</script>

