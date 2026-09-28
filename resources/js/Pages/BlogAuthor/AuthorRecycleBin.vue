<template>
    <Head :title="__('blog::admin.authors_recycle_bin')"></Head>
    <AppSectionHeader :title="__('blog::admin.authors_recycle_bin')" :bread-crumb="breadCrumb">
        <template #right>
            <div class="flex gap-2">
                <AppButton class="btn btn-secondary" @click="$inertia.visit(route('blogAuthor.index'))">
                    <i class="ri-arrow-left-line mr-1"></i> {{ __('common.back') }}
                </AppButton>
                <AppButton v-if="can('Blog: Author - Recycle Bin Restore') && authors.data.length"
                    class="btn btn-secondary" @click="restoreAll">
                    <i class="ri-arrow-go-back-line mr-1"></i> {{ __('common.restore_all') }}
                </AppButton>
                <AppButton v-if="can('Blog: Author - Recycle Bin Delete') && authors.data.length"
                    class="btn btn-destructive" @click="emptyBin">
                    <i class="ri-delete-bin-line mr-1"></i> {{ __('common.empty_bin') }}
                </AppButton>
            </div>
        </template>
    </AppSectionHeader>

    <AppDataTable v-if="authors.data.length" :headers="headers">
        <template #TableBody>
            <tbody>
                <AppDataTableRow v-for="(item, index) in authors.data" :key="item.id">
                    <AppDataTableData class="w-16">
                        {{ authors.from + index }}
                    </AppDataTableData>
                    <AppDataTableData class="w-20">
                        <img
                            v-if="item.image_url"
                            :src="item.image_url"
                            class="h-10 w-10 rounded-sm object-cover"
                        />
                        <AppImageNotAvailable v-else />
                    </AppDataTableData>
                    <AppDataTableData class="font-medium text-skin-neutral-12">
                        {{ item.name }}<br />
                        <small class="text-skin-neutral-9 text-sm">{{ item.email }}</small>
                    </AppDataTableData>
                    <AppDataTableData>
                        <small class="text-skin-neutral-9 text-sm">
                            <i v-if="item.github_handle" class="ri-github-fill mr-0 h-5 w-5"></i>
                            {{ item.github_handle }}<br />
                            <i v-if="item.twitter_handle" class="ri-twitter-x-line mr-1 h-5 w-5"></i>
                            {{ item.twitter_handle }}
                        </small>
                    </AppDataTableData>
                    <AppDataTableData class="w-40 text-right">
                        <AppTooltip v-if="can('Blog: Author - Recycle Bin Restore')" :text="__('common.restore')" class="mr-3">
                            <AppButton class="btn btn-icon btn-primary"
                                @click="router.get(route('blogAuthor.recycleBin.restore', item.id))">
                                <i class="ri-arrow-go-back-line"></i>
                            </AppButton>
                        </AppTooltip>
                        <AppTooltip v-if="can('Blog: Author - Recycle Bin Delete')" :text="__('common.delete_permanently')">
                            <AppButton class="btn btn-icon btn-destructive"
                                @click="confirmDelete(route('blogAuthor.recycleBin.destroyForce', item.id))">
                                <i class="ri-delete-bin-line"></i>
                            </AppButton>
                        </AppTooltip>
                    </AppDataTableData>
                </AppDataTableRow>
            </tbody>
        </template>
    </AppDataTable>

    <AppPaginator v-if="authors.data.length" :links="authors.links" :from="authors.from || 0"
        :to="authors.to || 0" :total="authors.total || 0" class="mt-4 justify-center" />

    <AppAlert v-if="!authors.data.length" class="mt-4">
        {{ __('common.recycle_bin_empty') }}
    </AppAlert>

    <AppConfirmDialog ref="confirmDialogRef" />
</template>

<script setup>
import { inject, ref } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import useAuthCan from '@/Composables/useAuthCan'
import AppImageNotAvailable from '@/Components/Modules/Blog/AppImageNotAvailable.vue'

const props = defineProps({
    authors: { type: Object, default: () => ({}) },
})

const translate = inject('translate')

const breadCrumb = [
    { label: translate('common.home'), href: route('dashboard.index') },
    { label: translate('blog::admin.authors'), href: route('blogAuthor.index') },
    { label: translate('common.recycle_bin'), last: true },
]

const headers = [
    translate('common.header.sl'),
    translate('blog::admin.image'),
    translate('blog::admin.name_email'),
    translate('blog::admin.social'),
    translate('common.header.actions'),
]

const confirmDialogRef = ref(null)
const confirmDelete = (deleteRoute) => {
    confirmDialogRef.value.openModal(deleteRoute)
}

const restoreAll = () => router.get(route('blogAuthor.recycleBin.restoreAll'))
const emptyBin = () => confirmDialogRef.value.openModal(route('blogAuthor.recycleBin.empty'))

const { can } = useAuthCan()
</script>
