<template>
    <Head :title="__('common.menu.product_categories') + ' — ' + __('common.recycle_bin')"></Head>
    <AppSectionHeader :title="__('common.menu.product_categories') + ' — ' + __('common.recycle_bin')" :bread-crumb="breadCrumb">
        <template #right>
            <div class="flex gap-2">
                <AppButton class="btn btn-secondary" @click="$inertia.visit(route('productCategory.index'))">
                    <i class="ri-arrow-left-line mr-1"></i> {{ __('common.back') }}
                </AppButton>
                <AppButton v-if="can('product-category-recycle-bin-restore') && categories.data.length"
                    class="btn btn-secondary" @click="restoreAll">
                    <i class="ri-arrow-go-back-line mr-1"></i> {{ __('common.restore_all') }}
                </AppButton>
                <AppButton v-if="can('product-category-recycle-bin-delete') && categories.data.length"
                    class="btn btn-destructive" @click="emptyBin">
                    <i class="ri-delete-bin-line mr-1"></i> {{ __('common.empty_bin') }}
                </AppButton>
            </div>
        </template>
    </AppSectionHeader>

    <AppDataTable v-if="categories.data.length" :headers="headers">
        <template #TableBody>
            <tbody>
                <AppDataTableRow v-for="(item, index) in categories.data" :key="item.id">
                    <AppDataTableData class="w-16">
                        {{ categories.from + index }}
                    </AppDataTableData>
                    <AppDataTableData>
                        <img
                            v-if="item.image_url"
                            :src="item.image_url"
                            class="h-12 w-20 rounded-sm"
                        />
                        <AppImageNotAvailable v-else class="h-12! w-20!" />
                    </AppDataTableData>
                    <AppDataTableData class="font-medium text-skin-neutral-12">
                        {{ item.name }}
                    </AppDataTableData>
                    <AppDataTableData class="w-40 text-right">
                        <AppTooltip v-if="can('product-category-recycle-bin-restore')" :text="__('common.restore')" class="mr-3">
                            <AppButton class="btn btn-icon btn-primary"
                                @click="router.get(route('productCategory.recycleBin.restore', item.id))">
                                <i class="ri-arrow-go-back-line"></i>
                            </AppButton>
                        </AppTooltip>
                        <AppTooltip v-if="can('product-category-recycle-bin-delete')" :text="__('common.delete_permanently')">
                            <AppButton class="btn btn-icon btn-destructive"
                                @click="confirmDelete(route('productCategory.recycleBin.destroyForce', item.id))">
                                <i class="ri-delete-bin-line"></i>
                            </AppButton>
                        </AppTooltip>
                    </AppDataTableData>
                </AppDataTableRow>
            </tbody>
        </template>
    </AppDataTable>

    <AppPaginator v-if="categories.data.length" :links="categories.links" :from="categories.from || 0"
        :to="categories.to || 0" :total="categories.total || 0" class="mt-4 justify-center" />

    <AppAlert v-if="!categories.data.length" class="mt-4">
        {{ __('common.recycle_bin_empty') }}
    </AppAlert>

    <AppConfirmDialog ref="confirmDialogRef" />
</template>

<script setup>
import { ref, computed, inject } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import useAuthCan from '@/Composables/useAuthCan'
import AppImageNotAvailable from '@/Components/Modules/Blog/AppImageNotAvailable.vue'

const props = defineProps({
    categories: { type: Object, default: () => ({}) },
})

const translate = inject('translate')

const breadCrumb = [
    { label: translate('common.home'), href: route('dashboard.index') },
    { label: translate('common.menu.product_categories'), href: route('productCategory.index') },
    { label: translate('common.recycle_bin'), last: true },
]

const headers = computed(() => [
    translate('common.header.sl'),
    translate('common.field.image'),
    translate('common.header.name'),
    translate('common.header.actions'),
])

const confirmDialogRef = ref(null)
const confirmDelete = (deleteRoute) => {
    confirmDialogRef.value.openModal(deleteRoute)
}

const restoreAll = () => router.get(route('productCategory.recycleBin.restoreAll'))
const emptyBin = () => confirmDialogRef.value.openModal(route('productCategory.recycleBin.empty'))

const { can } = useAuthCan()
</script>