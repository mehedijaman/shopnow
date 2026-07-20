<template>
    <Head title="Tags Recycle Bin"></Head>
    <AppSectionHeader title="Tags — Recycle Bin" :bread-crumb="breadCrumb">
        <template #right>
            <div class="flex gap-2">
                <AppButton class="btn btn-secondary" @click="$inertia.visit(route('blogTag.index'))">
                    <i class="ri-arrow-left-line mr-1"></i> Back
                </AppButton>
                <AppButton v-if="can('Blog: Tag - Recycle Bin Restore') && tags.data.length"
                    class="btn btn-secondary" @click="restoreAll">
                    <i class="ri-arrow-go-back-line mr-1"></i> Restore All
                </AppButton>
                <AppButton v-if="can('Blog: Tag - Recycle Bin Delete') && tags.data.length"
                    class="btn btn-destructive" @click="emptyBin">
                    <i class="ri-delete-bin-line mr-1"></i> Empty Bin
                </AppButton>
            </div>
        </template>
    </AppSectionHeader>

    <AppDataTable v-if="tags.data.length" :headers="headers">
        <template #TableBody>
            <tbody>
                <AppDataTableRow v-for="(item, index) in tags.data" :key="item.id">
                    <AppDataTableData class="w-16">
                        {{ tags.from + index }}
                    </AppDataTableData>
                    <AppDataTableData class="font-medium text-skin-neutral-12">
                        {{ item.name }}
                    </AppDataTableData>
                    <AppDataTableData class="w-40 text-right">
                        <AppTooltip v-if="can('Blog: Tag - Recycle Bin Restore')" text="Restore" class="mr-3">
                            <AppButton class="btn btn-icon btn-primary"
                                @click="router.get(route('blogTag.recycleBin.restore', item.id))">
                                <i class="ri-arrow-go-back-line"></i>
                            </AppButton>
                        </AppTooltip>
                        <AppTooltip v-if="can('Blog: Tag - Recycle Bin Delete')" text="Delete Permanently">
                            <AppButton class="btn btn-icon btn-destructive"
                                @click="confirmDelete(route('blogTag.recycleBin.destroyForce', item.id))">
                                <i class="ri-delete-bin-line"></i>
                            </AppButton>
                        </AppTooltip>
                    </AppDataTableData>
                </AppDataTableRow>
            </tbody>
        </template>
    </AppDataTable>

    <AppPaginator v-if="tags.data.length" :links="tags.links" :from="tags.from || 0"
        :to="tags.to || 0" :total="tags.total || 0" class="mt-4 justify-center" />

    <AppAlert v-if="!tags.data.length" class="mt-4">
        The recycle bin is empty.
    </AppAlert>

    <AppConfirmDialog ref="confirmDialogRef" />
</template>

<script setup>
import { ref } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import useAuthCan from '@/Composables/useAuthCan'

const props = defineProps({
    tags: { type: Object, default: () => ({}) },
})

const breadCrumb = [
    { label: 'Home', href: route('dashboard.index') },
    { label: 'Tags', href: route('blogTag.index') },
    { label: 'Recycle Bin', last: true },
]

const headers = ['SL', 'Name', 'Actions']

const confirmDialogRef = ref(null)
const confirmDelete = (deleteRoute) => {
    confirmDialogRef.value.openModal(deleteRoute)
}

const restoreAll = () => router.get(route('blogTag.recycleBin.restoreAll'))
const emptyBin = () => confirmDialogRef.value.openModal(route('blogTag.recycleBin.empty'))

const { can } = useAuthCan()
</script>
