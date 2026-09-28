<template>
    <Head :title="title"></Head>
    <AppSectionHeader :title="title" :bread-crumb="breadCrumb">
        <template #right>
            <AppButton
                class="btn btn-primary"
                @click="$inertia.visit(route('settings.create'))"
            >
                <i class="ri-add-fill mr-1"></i>
                {{ __('settings::admin.create_settings') }}
            </AppButton>
        </template>
    </AppSectionHeader>

    <AppDataSearch
        v-if="settings.data.length || route().params.searchTerm"
        :url="route('settings.index')"
        fields-to-search="id"
    ></AppDataSearch>

    <AppDataTable v-if="settings.data.length" :headers="headers">
        <template #TableBody>
            <tbody>
                <AppDataTableRow
                    v-for="(item, index) in settings.data"
                    :key="item.id"
                >
                    <AppDataTableData>
                        {{ item.id }}
                    </AppDataTableData>

                    <!-- <AppDataTableData>
                        {{ item.name }}
                    </AppDataTableData> -->

                    <AppDataTableData>
                        <!-- Edit settings -->
                        <AppTooltip :text="__('settings::admin.edit_settings')" class="mr-2">
                            <AppButton
                                class="btn btn-icon btn-primary"
                                @click="
                                    $inertia.visit(
                                        route(
                                            'settings.edit',
                                            item.id
                                        )
                                    )
                                "
                            >
                                <i class="ri-edit-line"></i>
                            </AppButton>
                        </AppTooltip>

                        <!-- Delete settings -->
                        <AppTooltip :text="__('settings::admin.delete_settings')">
                            <AppButton
                                class="btn btn-icon btn-destructive"
                                @click="
                                    confirmDelete(
                                        route(
                                            'settings.destroy',
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
        :links="settings.links"
        :from="settings.from || 0"
        :to="settings.to || 0"
        :total="settings.total || 0"
        class="mt-4 justify-center"
    ></AppPaginator>

    <AppAlert v-if="!settings.data.length" class="mt-4">
        {{ __('settings::admin.no_settings_found') }}
    </AppAlert>

    <AppConfirmDialog ref="confirmDialogRef"></AppConfirmDialog>
</template>

<script setup>
import { inject, ref } from 'vue'
import { Head } from '@inertiajs/vue3'
import useTitle from '@/Composables/useTitle'
import useAuthCan from '@/Composables/useAuthCan'

const translate = inject('translate')

const { title } = useTitle(translate('settings::admin.settings'))
const { can } = useAuthCan()

const props = defineProps({
  settings: {
    type: Object,
    default: () => {}
  }
})

const breadCrumb = [
  { label: translate('common.home'), href: route('dashboard.index') },
  { label: translate('settings::admin.settings'), last: true }
]

const headers = ['ID', 'Actions']

const confirmDialogRef = ref(null)
const confirmDelete = (deleteRoute) => {
    confirmDialogRef.value.openModal(deleteRoute)
}
</script>
