<template>
    <AppSectionHeader title="Contact Messages" :bread-crumb="breadCrumb">
        <template #right>
            <AppButton
                v-if="can('recycle-bin-list')"
                class="btn btn-secondary"
                @click="
                    $inertia.visit(route('contactMessage.recycleBin.index'))
                "
            >
                <i class="ri-recycle-line mr-1"></i>
                Recycle Bin
            </AppButton>
        </template>
    </AppSectionHeader>

    <AppDataSearch
        v-if="messages.data.length || route().params.searchTerm"
        :url="route('contactMessage.index')"
        fields-to-search="name"
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
                        <!-- <AppTooltip
                            v-if="can('contact-message-edit')"
                            text="Edit Post"
                            class="mr-3"
                        >
                            <AppButton
                                class="btn btn-icon btn-primary"
                                @click="
                                    $inertia.visit(
                                        route('contactMessage.edit', item.id)
                                    )
                                "
                            >
                                <i class="ri-edit-line"></i>
                            </AppButton>
                        </AppTooltip> -->

                        <!-- Delete -->
                        <AppTooltip
                            v-if="can('contact-message-delete')"
                            text="Delete Post"
                        >
                            <AppButton
                                class="btn btn-icon btn-destructive"
                                @click="
                                    confirmDelete(
                                        route('contactMessage.destroy', item.id)
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
        No messages found.
    </AppAlert>

    <AppConfirmDialog ref="confirmDialogRef"></AppConfirmDialog>
</template>

<script setup>
import { ref } from 'vue'
import useAuthCan from '@/Composables/useAuthCan'

const props = defineProps({
    messages: {
        type: Object,
        default: () => {}
    }
})

const breadCrumb = [
    { label: 'Home', href: route('dashboard.index') },
    { label: 'Contact Messages', last: true }
]

const headers = ['Name', 'Phone', 'Email', 'Subject', 'Message', 'Actions']

const confirmDialogRef = ref(null)
const confirmDelete = (deleteRoute) => {
    confirmDialogRef.value.openModal(deleteRoute)
}

const { can } = useAuthCan()
</script>

<style scoped></style>
