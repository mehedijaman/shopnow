<template>
    <Head :title="title"></Head>
    <AppSectionHeader :title="title" :bread-crumb="breadCrumb">
        <template #right>
            <div class="flex gap-2">
                <AppButton
                    class="btn btn-secondary"
                    @click="$inertia.visit(route('contactMessage.index'))"
                >
                    <i class="ri-arrow-left-s-line mr-1"></i>
                    {{ __('common.back_to_list') }}
                </AppButton>

                <AppButton
                    class="btn btn-primary"
                    @click="
                        $inertia.visit(
                            route('contactMessage.recycleBin.restoreAll')
                        )
                    "
                >
                    <i class="ri-recycle-fill mr-1"></i>
                    {{ __('contactMessage::admin.restore_recycle_bin') }}
                </AppButton>

                <AppButton
                    class="btn btn-destructive"
                    @click="
                        confirmDelete(route('contactMessage.recycleBin.empty'))
                    "
                >
                    <i class="ri-delete-bin-7-line mr-1"></i>
                    {{ __('contactMessage::admin.empty_recycle_bin') }}
                </AppButton>
            </div>
        </template>
    </AppSectionHeader>

    <AppDataSearch
        v-if="messages.data.length || route().params.searchTerm"
        :url="route('contactMessage.recycleBin.index')"
        fields-to-search="id"
    ></AppDataSearch>

    <AppDataTable v-if="messages.data.length" :headers="headers">
        <template #TableBody>
            <tbody>
                <AppDataTableRow v-for="item in messages.data" :key="item.id">
                    <AppDataTableData>
                        {{ item.name }}
                    </AppDataTableData>

                    <AppDataTableData>
                        {{ item.phone }}
                    </AppDataTableData>

                    <AppDataTableData>
                        {{ item.email }}
                    </AppDataTableData>

                    <AppDataTableData>
                        {{ item.subject }}
                    </AppDataTableData>

                    <AppDataTableData>
                        {{ item.message }}
                    </AppDataTableData>

                    <AppDataTableData>
                        <!-- Edit -->
                        <AppTooltip :text="__('common.restore')" class="mr-2">
                            <AppButton
                                class="btn btn-icon btn-primary"
                                @click="
                                    $inertia.visit(
                                        route(
                                            'contactMessage.recycleBin.restore',
                                            item.id
                                        )
                                    )
                                "
                            >
                                <i class="ri-recycle-fill"></i>
                            </AppButton>
                        </AppTooltip>

                        <!-- Delete -->
                        <AppTooltip :text="__('common.delete_permanently')">
                            <AppButton
                                class="btn btn-icon btn-destructive"
                                @click="
                                    confirmDelete(
                                        route(
                                            'contactMessage.recycleBin.destroyForce',
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
        v-if="messages.data.length"
        :links="messages.links"
        :from="messages.from || 0"
        :to="messages.to || 0"
        :total="messages.total || 0"
        class="mt-4 justify-center"
    ></AppPaginator>

    <AppAlert v-if="!messages.data.length" class="mt-4">
        {{ __('contactMessage::admin.no_data_found') }}
    </AppAlert>

    <AppConfirmDialog ref="confirmDialogRef"></AppConfirmDialog>
</template>

<script setup>
import { inject, ref } from 'vue'
import { Head } from '@inertiajs/vue3'
import useTitle from '@/Composables/useTitle'
const translate = inject('translate')

const { title } = useTitle(translate('contactMessage::admin.contact_message_recycle_bin'))

const props = defineProps({
    messages: {
        type: Object,
        default: () => {}
    }
})

const breadCrumb = [
    { label: translate('common.home'), href: route('dashboard.index') },
    { label: translate('contactMessage::admin.contact_messages'), href: route('contactMessage.index') },
    { label: translate('common.recycle_bin'), last: true }
]

const headers = [
    translate('common.header.name'),
    translate('common.header.phone'),
    translate('contactMessage::admin.email'),
    translate('contactMessage::admin.subject'),
    translate('contactMessage::admin.message'),
    translate('common.header.actions'),
]

const confirmDialogRef = ref(null)
const confirmDelete = (deleteRoute) => {
    confirmDialogRef.value.openModal(deleteRoute)
}
</script>
