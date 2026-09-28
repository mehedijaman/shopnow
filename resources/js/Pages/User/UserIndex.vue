<template>
    <AppSectionHeader :title="__('user::admin.users')" :bread-crumb="breadCrumb">
        <template #right>
            <div class="flex gap-2">
                <AppButton
                    v-if="can('Acl: User - Recycle Bin')"
                    class="btn btn-secondary"
                    @click="$inertia.visit(route('user.recycleBin.index'))"
                >
                    <i class="ri-delete-bin-line mr-1"></i>
                    {{ __('common.recycle_bin') }}
                </AppButton>
                <AppButton
                    v-if="can('Acl: User - Create')"
                    class="btn btn-primary"
                    @click="$inertia.visit(route('user.create'))"
                >
                    {{ __('user::admin.create_user') }}
                </AppButton>
            </div>
        </template>
    </AppSectionHeader>

    <AppDataSearch
        v-if="users.data.length || route().params.searchTerm"
        :url="route('user.index')"
        fields-to-search="name"
    ></AppDataSearch>

    <AppDataTable v-if="users.data.length" :headers="headers">
        <template #TableBody>
            <tbody>
                <AppDataTableRow v-for="item in users.data" :key="item.id">
                    <AppDataTableData>
                        {{ item.id }}
                    </AppDataTableData>

                    <AppDataTableData>
                        {{ item.name }}
                    </AppDataTableData>

                    <AppDataTableData>
                        {{ item.email }}
                    </AppDataTableData>

                    <AppDataTableData>
                        <!-- edit user roles -->
                        <AppTooltip
                            v-if="can('Acl: User: Role - Edit')"
                            :text="__('user::admin.user_roles')"
                            class="mr-2"
                        >
                            <AppButton
                                class="btn btn-icon btn-primary"
                                @click="
                                    $inertia.visit(
                                        route('aclUserRole.edit', item.id)
                                    )
                                "
                            >
                                <i class="ri-account-box-line"></i>
                            </AppButton>
                        </AppTooltip>

                        <!-- edit user permissions -->
                        <AppTooltip
                            v-if="can('Acl: User: Permission - Edit')"
                            :text="__('user::admin.user_permissions')"
                            class="mr-2"
                        >
                            <AppButton
                                class="btn btn-icon btn-primary"
                                @click="
                                    $inertia.visit(
                                        route('aclUserPermission.edit', item.id)
                                    )
                                "
                            >
                                <i class="ri-shield-keyhole-line"></i>
                            </AppButton>
                        </AppTooltip>

                        <!-- edit user -->
                        <AppTooltip
                            v-if="can('Acl: User - Edit')"
                            :text="__('user::admin.edit_user')"
                            class="mr-2"
                        >
                            <AppButton
                                class="btn btn-icon btn-primary"
                                @click="
                                    $inertia.visit(route('user.edit', item.id))
                                "
                            >
                                <i class="ri-edit-line"></i>
                            </AppButton>
                        </AppTooltip>

                        <!-- delete user -->
                        <AppTooltip
                            v-if="can('Acl: User - Delete')"
                            :text="__('user::admin.delete_user')"
                        >
                            <AppButton
                                class="btn btn-icon btn-destructive"
                                @click="
                                    confirmDelete(
                                        route('user.destroy', item.id)
                                    )
                                "
                            >
                                <i class="ri-delete-bin-line"></i>
                            </AppButton>
                        </AppTooltip>
                    </AppDataTableData>
                </AppDataTableRow>
            </tbody>
        </template>
    </AppDataTable>

    <AppPaginator
        :links="users.links"
        :from="users.from || 0"
        :to="users.to || 0"
        :total="users.total || 0"
        class="mt-4 justify-center"
    ></AppPaginator>

    <AppAlert v-if="!users.data.length" class="mt-4">
        {{ __('user::admin.no_users_found') }}
    </AppAlert>

    <AppConfirmDialog ref="confirmDialogRef"></AppConfirmDialog>
</template>

<script setup>
import { inject, ref } from 'vue'
import useAuthCan from '@/Composables/useAuthCan'

const { can } = useAuthCan()

defineProps({
    users: {
        type: Object,
        default: () => {}
    }
})

const translate = inject('translate')

const breadCrumb = [
    { label: translate('common.home'), href: route('dashboard.index') },
    { label: translate('user::admin.users'), last: true }
]

const headers = [
    'ID',
    translate('common.header.name'),
    translate('user::admin.email'),
    translate('common.header.actions'),
]

const confirmDialogRef = ref(null)
const confirmDelete = (deleteRoute) => {
    confirmDialogRef.value.openModal(deleteRoute)
}
</script>
