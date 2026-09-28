<template>
    <Head :title="title"></Head>
    <AppSectionHeader :title="title" :bread-crumb="breadCrumb">
        <template #right>
            <div class="flex gap-2">
                <AppButton
                    v-if="can('page-recycle-bin-list')"
                    class="btn btn-secondary"
                    @click="$inertia.visit(route('page.recycleBin.index'))"
                >
                    <i class="ri-delete-bin-line mr-1"></i> {{ __('common.recycle_bin') }}
                </AppButton>
                <AppButton v-if="can('page-create')" class="btn btn-primary" @click="$inertia.visit(route('page.create'))">
                    <i class="ri-add-fill mr-1"></i> {{ __('page::admin.new_page') }}
                </AppButton>
            </div>
        </template>
    </AppSectionHeader>

    <AppDataTable v-if="pages.data.length" :headers="headers" class="mt-5 shadow-sm">
        <template #TableBody>
            <tbody>
                <AppDataTableRow v-for="item in pages.data" :key="item.id">
                    <AppDataTableData>
                        <div class="flex items-center gap-2">
                            <span class="font-medium text-skin-neutral-12">{{ item.title }}</span>
                            <span v-if="item.is_system" class="rounded-full bg-blue-100 px-2 py-0.5 text-xs font-medium text-blue-700">{{ __('page::admin.system') }}</span>
                        </div>
                        <p class="text-xs text-skin-neutral-7">/{{ item.slug }}</p>
                    </AppDataTableData>
                    <AppDataTableData>
                        <span class="rounded-full px-2.5 py-0.5 text-xs font-medium" :class="item.status === 'Published' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700'">
                            {{ pageStatusText(item.status) }}
                        </span>
                    </AppDataTableData>
                    <AppDataTableData class="text-xs text-skin-neutral-8">{{ item.published_at ?? '—' }}</AppDataTableData>
                    <AppDataTableData>
                        <div class="flex gap-1.5">
                            <AppTooltip v-if="can('page-edit')" :text="__('common.edit')">
                                <AppButton class="btn btn-icon btn-primary" @click="$inertia.visit(route('page.edit', item.id))">
                                    <i class="ri-edit-line"></i>
                                </AppButton>
                            </AppTooltip>
                            <AppTooltip v-if="can('page-delete') && !item.is_system" :text="__('common.delete')">
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
    <AppAlert v-if="!pages.data.length" class="mt-5">{{ __('page::admin.no_pages_found') }}</AppAlert>
    <AppConfirmDialog ref="confirmDialogRef"></AppConfirmDialog>
</template>

<script setup>
import { inject, ref } from 'vue'
import { Head } from '@inertiajs/vue3'
import useTitle from '@/Composables/useTitle'
import useAuthCan from '@/Composables/useAuthCan'

const translate = inject('translate')

const { title } = useTitle(translate('page::admin.pages'))
const { can } = useAuthCan()

defineProps({
    pages: { type: Object, default: () => ({}) },
})

const breadCrumb = [
    { label: translate('common.home'), href: route('dashboard.index') },
    { label: translate('page::admin.pages'), last: true },
]

const headers = [
    translate('page::admin.title'),
    translate('common.field.status'),
    translate('page::admin.published'),
    translate('common.header.actions'),
]

const pageStatusText = (status) => {
    if (status === 'Published') return translate('page::admin.published')
    if (status === 'Draft') return translate('page::admin.draft')
    return status
}

const confirmDialogRef = ref(null)
const confirmDelete = (deleteRoute) => {
    confirmDialogRef.value.openModal(deleteRoute)
}
</script>
