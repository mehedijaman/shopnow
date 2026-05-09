<template>
    <Head title="My Profile"></Head>
    <AppSectionHeader title="My Profile" :bread-crumb="breadCrumb"></AppSectionHeader>

    <div class="mt-6 flex flex-col gap-4 sm:flex-row sm:gap-6">

        <!-- Sidebar navigation -->
        <aside class="w-full sm:w-52 sm:shrink-0">
            <nav class="flex flex-row gap-1 overflow-x-auto pb-1 sm:flex-col sm:overflow-x-visible sm:pb-0">
                <button
                    v-for="section in sections"
                    :key="section.key"
                    type="button"
                    :class="[
                        'flex shrink-0 items-center gap-2.5 rounded-md px-3 py-2.5 text-sm font-medium transition-colors sm:w-full',
                        activeSection === section.key
                            ? 'bg-skin-primary-10 text-skin-neutral-1'
                            : 'text-skin-neutral-11 hover:bg-skin-neutral-3',
                    ]"
                    @click="activeSection = section.key"
                >
                    <i :class="[section.icon, 'text-base leading-none']"></i>
                    <span>{{ section.label }}</span>
                </button>
            </nav>
        </aside>

        <!-- Content panel -->
        <div class="min-w-0 flex-1">
            <div class="rounded-lg bg-skin-neutral-1 p-4 shadow-xs ring-1 ring-skin-neutral-4 sm:p-6">

                <!-- Panel header -->
                <div class="mb-6 border-b border-skin-neutral-4 pb-5">
                    <div class="flex items-center gap-2">
                        <span class="flex h-8 w-8 items-center justify-center rounded-md bg-skin-primary-10 text-skin-neutral-1">
                            <i :class="[currentSection.icon, 'text-base']"></i>
                        </span>
                        <h2 class="text-base font-semibold text-skin-neutral-12">{{ currentSection.label }}</h2>
                    </div>
                    <p class="mt-2 text-sm text-skin-neutral-9">{{ currentSection.description }}</p>
                </div>

                <!-- Active section content -->
                <component
                    :is="currentSection.component"
                    :profile-user="profileUser"
                    :errors-fields="errorsFields"
                />

                <!-- Footer action -->
                <div class="mt-8 flex justify-end border-t border-skin-neutral-4 pt-6">
                    <AppButton
                        class="btn btn-primary"
                        :disabled="currentForm.processing"
                        @click="submitCurrent"
                    >
                        <i v-if="currentForm.processing" class="ri-loader-4-line mr-1 animate-spin"></i>
                        <i v-else :class="[currentSection.submitIcon, 'mr-1']"></i>
                        {{ currentForm.processing ? 'Saving…' : currentSection.submitLabel }}
                    </AppButton>
                </div>

            </div>
        </div>

    </div>
</template>

<script setup>
import { computed, markRaw, provide, ref } from 'vue'
import { Head, useForm } from '@inertiajs/vue3'
import useFormErrors from '@/Composables/useFormErrors'
import ProfileInfoSection from './Components/ProfileInfoSection.vue'
import PasswordSection from './Components/PasswordSection.vue'
import EmailSection from './Components/EmailSection.vue'

const props = defineProps({
    profileUser: {
        type: Object,
        required: true,
    },
})

const breadCrumb = [
    { label: 'Home', href: route('dashboard.index') },
    { label: 'My Profile', last: true },
]

const sections = [
    {
        key: 'profile',
        label: 'Profile',
        icon: 'ri-user-line',
        description: 'Update your display name and profile photo.',
        submitLabel: 'Save Profile',
        submitIcon: 'ri-save-line',
        component: markRaw(ProfileInfoSection),
    },
    {
        key: 'email',
        label: 'Email',
        icon: 'ri-mail-line',
        description: 'Change the email address associated with your account. You will need your current password to confirm.',
        submitLabel: 'Update Email',
        submitIcon: 'ri-mail-check-line',
        component: markRaw(EmailSection),
    },
    {
        key: 'password',
        label: 'Password',
        icon: 'ri-lock-password-line',
        description: 'Change your account password. You will need your current password to proceed.',
        submitLabel: 'Update Password',
        submitIcon: 'ri-lock-line',
        component: markRaw(PasswordSection),
    },
]

const activeSection = ref('profile')
const currentSection = computed(() => sections.find((s) => s.key === activeSection.value))

const profileForm = useForm({
    name: props.profileUser.name,
    avatar: null,
})

const emailForm = useForm({
    email: props.profileUser.email,
    current_password: '',
})

const passwordForm = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
})

provide('profileForm', profileForm)
provide('emailForm', emailForm)
provide('passwordForm', passwordForm)

const { errorsFields } = useFormErrors()

const currentForm = computed(() => {
    if (activeSection.value === 'profile') return profileForm
    if (activeSection.value === 'email') return emailForm
    return passwordForm
})

function submitCurrent() {
    if (activeSection.value === 'profile') {
        profileForm.post(route('profile.update'), { forceFormData: true })
    } else if (activeSection.value === 'email') {
        emailForm.put(route('profile.updateEmail'), {
            onSuccess: () => emailForm.reset('current_password'),
        })
    } else {
        passwordForm.put(route('profile.updatePassword'), {
            onSuccess: () => passwordForm.reset(),
        })
    }
}
</script>

<script>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

export default {
    layout: AuthenticatedLayout,
}
</script>
