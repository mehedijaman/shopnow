<template>
    <AppSectionHeader :title="__('user::admin.user_permissions')" :bread-crumb="breadCrumb">
    </AppSectionHeader>

    <AppCard>
        <template #title>
            {{ __('acl::admin.user_permissions_for') }}:
            <span class="text-skin-primary-10">{{ user.name }}</span>
        </template>

        <template #content>
            <div v-if="chunks.length">
                <AppFormErrors class="mb-4" />
                <form class="mt-5 flex">
                    <div
                        v-for="(column, index) in chunks"
                        :key="index"
                        class="w-1/3"
                    >
                        <div
                            v-for="permission in column"
                            :key="permission.id"
                            class="mb-4 flex items-center"
                        >
                            <AppCheckbox
                                :id="permission.name"
                                v-model="form.userPermissions"
                                name="permission"
                                :value="permission"
                            />
                            <AppLabel :for="permission.name" class="ml-2">
                                {{ permission.name }}
                            </AppLabel>
                        </div>
                    </div>
                </form>
            </div>

            <AppAlert v-else class="mt-4">
                {{ __('acl::admin.no_permissions_found') }}
            </AppAlert>
        </template>

        <template v-if="chunks.length" #footer>
            <AppButton class="btn btn-primary" @click="submitForm">
                {{ __('common.save') }}
            </AppButton>
        </template>
    </AppCard>
</template>

<script setup>
import { computed, inject } from 'vue'
import { useForm } from '@inertiajs/vue3'
import chunk from '@/Utils/chunk'

const props = defineProps({
    user: {
        type: Object,
        required: true
    },
    userPermissions: {
        type: Array,
        required: true
    },
    permissions: {
        type: Array,
        required: true
    }
})

const translate = inject('translate')

const breadCrumb = [
    { label: translate('common.home'), href: route('dashboard.index') },
    { label: translate('user::admin.users'), href: route('user.index') },
    { label: translate('user::admin.user_permissions'), last: true }
]

const form = useForm({
    userPermissions: props.userPermissions
})

const chunks = computed(() => {
    const itensPerColumn = props.permissions.length / 3

    return chunk(props.permissions, Math.ceil(itensPerColumn))
})

const submitForm = () => {
    form.put(route('aclUserPermission.update', props.user.id))
}
</script>
