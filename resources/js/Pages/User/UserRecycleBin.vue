<template>
    <Head :title="title"></Head>
    <AppSectionHeader :title="title" :bread-crumb="breadCrumb">
        <template #right>
            <div class="flex gap-2">
                <AppButton
                    class="btn btn-secondary"
                    @click="$inertia.visit(route('user.index'))"
                >
                    <i class="ri-arrow-left-s-line mr-1"></i>
                    {{ __('common.back_to_list') }}
                </AppButton>

                <AppButton
                    v-if="can('Acl: User - Recycle Bin')"
                    class="btn btn-primary"
                    @click="
                        $inertia.visit(route('user.recycleBin.restoreAll'))
                    "
                >
                    <i class="ri-recycle-fill mr-1"></i>
                    {{ __('user::admin.restore_recycle_bin') }}
                </AppButton>

                <AppButton
                    v-if="can('Acl: User - Recycle Bin')"
                    class="btn btn-destructive"
                    @click="confirmDelete(route('user.recycleBin.empty'))"
                >
                    <i class="ri-delete-bin-7-line mr-1"></i>
                    {{ __('user::admin.empty_recycle_bin') }}
                </AppButton>
            </div>
        </template>
    </AppSectionHeader>

    <AppDataSearch
        v-if="users.data.length || route().params.searchTerm"
        :url="route('user.recycleBin.index')"
        fields-to-search="name"
    ></AppDataSearch>

    <AppDataTable v-if="users.data.length" :headers="headers">
        <template #TableBody>
            <tbody>
                <AppDataTableRow
                    v-for="item in users.data"
                    :key="item.id"
                >
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
                        <!-- Restore -->
                        <AppTooltip
                            v-if="can('Acl: User - Recycle Bin')"
                            :text="__('common.restore')"
                            class="mr-2"
                        >
                            <AppButton
                                class="btn btn-icon btn-primary"
                                @click="
                                    $inertia.visit(
                                        route(
                                            'user.recycleBin.restore',
                                            item.id
                                        )
                                    )
                                "
                            >
                                <i class="ri-recycle-fill"></i>
                            </AppButton>
                        </AppTooltip>

                        <!-- Delete -->
                        <AppTooltip
                            v-if="can('Acl: User - Recycle Bin')"
                            :text="__('common.delete_permanently')"
                        >
                            <AppButton
                                class="btn btn-icon btn-destructive"
                                @click="
                                    confirmDelete(
                                        route(
                                            'user.recycleBin.destroyForce',
                                            item.id
                                        )
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
        :from="users.from ?? 0"
        :to="users.to ?? 0"
        :total="users.total ?? 0"
        class="mt-4 justify-center"
    ></AppPaginator>

    <AppAlert v-if="!users.data.length" class="mt-4">
        {{ __('user::admin.no_users_recycle_bin') }}
    </AppAlert>

    <AppConfirmDialog ref="confirmDialogRef"></AppConfirmDialog>
</template>

<script setup>
import { inject, ref } from 'vue'
import { Head } from '@inertiajs/vue3'
import useTitle from '@/Composables/useTitle'
import useAuthCan from '@/Composables/useAuthCan'

const { can } = useAuthCan()
const translate = inject('translate')

const { title } = useTitle(translate('user::admin.user_recycle_bin'))

const props = defineProps({
    users: {
        type: Object,
        default: () => {}
    }
})

const breadCrumb = [
    { label: translate('common.home'), href: route('dashboard.index') },
    { label: translate('user::admin.users'), href: route('user.index') },
    { label: title, last: true }
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
