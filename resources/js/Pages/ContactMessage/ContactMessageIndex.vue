<template>
    <Head :title="title"></Head>
    <AppSectionHeader :title="title" :bread-crumb="breadCrumb">
        <template #right>
            <AppButton
                class="btn btn-primary"
                @click="$inertia.visit(route('contactMessage.create'))"
            >
                <i class="ri-add-fill mr-1"></i>
                Create ContactMessage
            </AppButton>
        </template>
    </AppSectionHeader>

    <AppDataSearch
        v-if="contactMessages.data.length || route().params.searchTerm"
        :url="route('contactMessage.index')"
        fields-to-search="id"
    ></AppDataSearch>

    <AppDataTable v-if="contactMessages.data.length" :headers="headers">
        <template #TableBody>
            <tbody>
                <AppDataTableRow
                    v-for="(item, index) in contactMessages.data"
                    :key="item.id"
                >
                    <AppDataTableData>
                        {{ item.id }}
                    </AppDataTableData>

                    <!-- <AppDataTableData>
                        {{ item.name }}
                    </AppDataTableData> -->

                    <AppDataTableData>
                        <!-- Edit contactMessage -->
                        <AppTooltip text="Edit ContactMessage" class="mr-2">
                            <AppButton
                                class="btn btn-icon btn-primary"
                                @click="
                                    $inertia.visit(
                                        route(
                                            'contactMessage.edit',
                                            item.id
                                        )
                                    )
                                "
                            >
                                <i class="ri-edit-line"></i>
                            </AppButton>
                        </AppTooltip>

                        <!-- Delete contactMessage -->
                        <AppTooltip text="Delete ContactMessage">
                            <AppButton
                                class="btn btn-icon btn-destructive"
                                @click="
                                    confirmDelete(
                                        route(
                                            'contactMessage.destroy',
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
        :links="contactMessages.links"
        :from="contactMessages.from"
        :to="contactMessages.to"
        :total="contactMessages.total"
        class="mt-4 justify-center"
    ></AppPaginator>

    <AppAlert v-if="!contactMessages.data.length" class="mt-4">
        No contactMessages found.
    </AppAlert>

    <AppConfirmDialog ref="confirmDialogRef"></AppConfirmDialog>
</template>

<script setup>
import { ref } from 'vue'
import { Head } from '@inertiajs/vue3'
import useTitle from '@/Composables/useTitle'
import useAuthCan from '@/Composables/useAuthCan'

const { title } = useTitle('ContactMessage')
const { can } = useAuthCan()

const props = defineProps({
  contactMessages: {
    type: Object,
    default: () => {}
  }
})

const breadCrumb = [
  { label: 'Home', href: route('dashboard.index') },
  { label: 'ContactMessages', last: true }
]

const headers = ['ID', 'Actions']

const confirmDialogRef = ref(null)
const confirmDelete = (deleteRoute) => {
    confirmDialogRef.value.openModal(deleteRoute)
}
</script>
