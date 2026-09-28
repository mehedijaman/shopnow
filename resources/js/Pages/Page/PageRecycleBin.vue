<template>
    <Head :title="__('page::admin.page_recycle_bin')"></Head>
    <AppSectionHeader :title="__('page::admin.page_recycle_bin')" :bread-crumb="breadCrumb">
        <template #right>
            <AppButton v-if="pages.data.length && can('page-recycle-bin-delete')" class="btn btn-destructive" @click="confirmEmptyBin">
                <i class="ri-delete-bin-2-line mr-1"></i> {{ __('common.empty_bin') }}
            </AppButton>
        </template>
    </AppSectionHeader>

    <div v-if="pages.data.length" class="mt-5 overflow-x-auto rounded-xl border border-skin-neutral-4 bg-skin-neutral-2 shadow-sm">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-skin-neutral-4 text-left text-xs text-skin-neutral-9">
                    <th class="px-4 py-3 font-medium">{{ __('page::admin.title') }}</th>
                    <th class="px-4 py-3 font-medium">{{ __('common.field.status') }}</th>
                    <th class="px-4 py-3 font-medium">{{ __('page::admin.deleted_at') }}</th>
                    <th class="px-4 py-3 font-medium">{{ __('common.header.actions') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-skin-neutral-3">
                <tr v-for="item in pages.data" :key="item.id" class="hover:bg-skin-neutral-1">
                    <td class="px-4 py-3">
                        <span class="font-medium text-skin-neutral-12">{{ item.title }}</span>
                        <p class="text-xs text-skin-neutral-7">/{{ item.slug }}</p>
                    </td>
                    <td class="px-4 py-3">
                        <span class="rounded-full px-2.5 py-0.5 text-xs font-medium" :class="item.status === 'Published' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700'">
                            {{ pageStatusText(item.status) }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-xs text-skin-neutral-8">{{ item.deleted_at }}</td>
                    <td class="px-4 py-3">
                        <div class="flex gap-1.5">
                            <AppTooltip v-if="can('page-recycle-Bin-Restore')" :text="__('common.restore')">
                                <AppButton class="btn btn-icon btn-primary" @click="$inertia.visit(route('page.recycleBin.restore', item.id))">
                                    <i class="ri-arrow-go-back-line"></i>
                                </AppButton>
                            </AppTooltip>
                            <AppTooltip v-if="can('page-recycle-bin-delete')" :text="__('common.delete_permanently')">
                                <AppButton class="btn btn-icon btn-destructive" @click="confirmForceDelete(route('page.recycleBin.destroyForce', item.id))">
                                    <i class="ri-delete-bin-line"></i>
                                </AppButton>
                            </AppTooltip>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <AppPaginator :links="pages.links" :from="pages.from ?? 0" :to="pages.to ?? 0" :total="pages.total ?? 0" class="mt-4 justify-center"></AppPaginator>
    <AppAlert v-if="!pages.data.length" class="mt-5">{{ __('page::admin.recycle_bin_empty') }}</AppAlert>
    <AppConfirmDialog ref="confirmDialogRef"></AppConfirmDialog>
</template>

<script setup>
import { inject, ref } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import useAuthCan from '@/Composables/useAuthCan'

const translate = inject('translate')

const { can } = useAuthCan()

defineProps({
    pages: { type: Object, default: () => ({}) },
})

const breadCrumb = [
    { label: translate('common.home'), href: route('dashboard.index') },
    { label: translate('page::admin.pages'), href: route('page.index') },
    { label: translate('common.recycle_bin'), last: true },
]

const pageStatusText = (status) => {
    if (status === 'Published') return translate('page::admin.published')
    if (status === 'Draft') return translate('page::admin.draft')
    return status
}

const confirmDialogRef = ref(null)

const confirmForceDelete = (deleteRoute) => {
    confirmDialogRef.value.openModal(deleteRoute)
}

const confirmEmptyBin = () => {
    confirmDialogRef.value.openModal(route('page.recycleBin.empty'))
}
</script>
