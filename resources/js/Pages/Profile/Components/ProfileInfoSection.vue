<template>
    <form class="space-y-6" @submit.prevent>

        <!-- Avatar -->
        <div>
            <AppLabel value="Profile Photo" />
            <p class="mb-3 text-xs text-skin-neutral-9">Upload a photo to personalise your account. JPG, PNG or GIF — max 2 MB.</p>

            <div class="flex items-center gap-5">
                <!-- Preview -->
                <div class="shrink-0">
                    <img
                        v-if="previewUrl"
                        :src="previewUrl"
                        alt="Avatar preview"
                        class="h-20 w-20 rounded-full object-cover ring-2 ring-skin-primary-9 ring-offset-2"
                    />
                    <div
                        v-else
                        class="flex h-20 w-20 items-center justify-center rounded-full bg-skin-primary-9 text-2xl font-bold text-skin-primary-1 ring-2 ring-skin-primary-9 ring-offset-2"
                    >
                        {{ initials }}
                    </div>
                </div>

                <!-- Upload control -->
                <div class="flex-1">
                    <AppInputFile
                        v-model="form.avatar"
                        :image-preview-url="null"
                        :show-remove-file-button="false"
                        @update:model-value="onAvatarChange"
                    />
                    <p v-if="form.errors.avatar" class="mt-1 text-sm text-skin-error">{{ form.errors.avatar }}</p>
                </div>
            </div>
        </div>

        <!-- Name -->
        <div>
            <AppLabel for="name" value="Full Name" />
            <p class="mb-1 text-xs text-skin-neutral-9">Your display name across the admin panel.</p>
            <AppInputText
                id="name"
                v-model="form.name"
                type="text"
                placeholder="e.g. John Doe"
                :class="{ 'input-error': form.errors.name }"
            />
            <p v-if="form.errors.name" class="mt-1 text-sm text-skin-error">{{ form.errors.name }}</p>
        </div>

    </form>
</template>

<script setup>
import { ref, computed, inject } from 'vue'

const props = defineProps({
    profileUser: { type: Object, required: true },
    errorsFields: { type: Array, default: () => [] },
})

const form = inject('profileForm')

const filePreview = ref(null)

const previewUrl = computed(() => filePreview.value ?? props.profileUser.avatar_url ?? null)

const initials = computed(() => {
    return props.profileUser.name
        ? props.profileUser.name.split(' ').slice(0, 2).map((w) => w[0]?.toUpperCase() ?? '').join('')
        : '?'
})

function onAvatarChange(file) {
    if (file) {
        const reader = new FileReader()
        reader.onload = (e) => { filePreview.value = e.target.result }
        reader.readAsDataURL(file)
    } else {
        filePreview.value = null
    }
}
</script>
