<template>
    <AppSectionHeader :title="__('user::admin.user_roles')" :bread-crumb="breadCrumb">
    </AppSectionHeader>

    <AppCard>
        <template #title>
            {{ __('acl::admin.user_roles_for') }}:
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
                            v-for="role in column"
                            :key="role.id"
                            class="mb-4 flex items-center"
                        >
                            <AppCheckbox
                                :id="role.name"
                                v-model="form.userRoles"
                                name="role"
                                :value="role"
                            />
                            <AppLabel :for="role.name" class="ml-2">
                                {{ role.name }}
                            </AppLabel>
                        </div>
                    </div>
                </form>
            </div>

            <AppAlert v-else class="mt-4">
                {{ __('acl::admin.no_roles_found') }}
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
    roles: {
        type: Array,
        required: true
    }
})

const translate = inject('translate')

const breadCrumb = [
    { label: translate('common.home'), href: route('dashboard.index') },
    { label: translate('user::admin.users'), href: route('user.index') },
    { label: translate('user::admin.user_roles'), last: true }
]

const form = useForm({
    userRoles: props.user.roles
})

const chunks = computed(() => {
    const itensPerColumn = props.roles.length / 3

    return chunk(props.roles, Math.ceil(itensPerColumn))
})

const submitForm = () => {
    form.put(route('aclUserRole.update', props.user.id))
}
</script>
