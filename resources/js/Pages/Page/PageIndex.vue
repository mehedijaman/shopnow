<template>
    <Head :title="title"></Head>
    <AppSectionHeader :title="title" :bread-crumb="breadCrumb">
        <template #right>
            <AppButton v-if="can('page-create')" class="btn btn-primary" @click="$inertia.visit(route('page.create'))">
                <i class="ri-add-fill mr-1"></i> New Page
            </AppButton>
        </template>
    </AppSectionHeader>

    <AppDataTable v-if="pages.data.length" :headers="headers" class="mt-5 shadow-sm">
        <template #TableBody>
            <tbody>
                <AppDataTableRow v-for="item in pages.data" :key="item.id">
                    <AppDataTableData>
                        <div class="flex items-center gap-2">
                            <span class="font-medium text-skin-neutral-12">{{ item.title }}</span>
                            <span v-if="item.is_system" class="rounded-full bg-blue-100 px-2 py-0.5 text-xs font-medium text-blue-700">System</span>
                        </div>
                        <p class="text-xs text-skin-neutral-7">/{{ item.slug }}</p>
                    </AppDataTableData>
                    <AppDataTableData>
                        <span class="rounded-full px-2.5 py-0.5 text-xs font-medium" :class="item.status === 'Published' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700'">
                            {{ item.status }}
                        </span>
                    </AppDataTableData>
                    <AppDataTableData class="text-xs text-skin-neutral-8">{{ item.published_at ?? '—' }}</AppDataTableData>
                    <AppDataTableData>
                        <div class="flex gap-1.5">
                            <AppTooltip v-if="can('page-edit')" text="Edit">
                                <AppButton class="btn btn-icon btn-primary" @click="$inertia.visit(route('page.edit', item.id))">
                                    <i class="ri-edit-line"></i>
                                </AppButton>
                            </AppTooltip>
                            <AppTooltip v-if="can('page-delete') && !item.is_system" text="Delete">
                                <AppButton class="btn btn-icon btn-destructive" @click="confirmDelete(route('page.destroy', item.id))">
                                    <i class="ri-delete-bin-line"></i>
                                </AppButton>
                            </AppTooltip>
                        </div>
                    </AppDataTableData>
                </AppDataTableRow>
            </tbody>
        </template>
    </AppDataTable>

    <AppPaginator :links="pages.links" :from="pages.from ?? 0" :to="pages.to ?? 0" :total="pages.total ?? 0" class="mt-4 justify-center"></AppPaginator>
    <AppAlert v-if="!pages.data.length" class="mt-5">No pages found.</AppAlert>
    <AppConfirmDialog ref="confirmDialogRef"></AppConfirmDialog>
</template>

<script setup>
import { ref } from 'vue'
import { Head } from '@inertiajs/vue3'
import useTitle from '@/Composables/useTitle'
import useAuthCan from '@/Composables/useAuthCan'

const { title } = useTitle('Pages')
const { can } = useAuthCan()

defineProps({
    pages: { type: Object, default: () => ({}) },
})

const breadCrumb = [
    { label: 'Home', href: route('dashboard.index') },
    { label: 'Pages', last: true },
]

const headers = ['Title', 'Status', 'Published', 'Actions']

const confirmDialogRef = ref(null)
const confirmDelete = (deleteRoute) => {
    confirmDialogRef.value.openModal(deleteRoute)
}
</script>
