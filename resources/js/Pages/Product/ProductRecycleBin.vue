<template>
    <Head :title="__('common.recycle_bin')"></Head>
    <AppSectionHeader :title="__('common.menu.products') + ' — ' + __('common.recycle_bin')" :bread-crumb="breadCrumb">
        <template #right>
            <div class="flex gap-2">
                <AppButton class="btn btn-secondary" @click="$inertia.visit(route('product.index'))">
                    <i class="ri-arrow-left-line mr-1"></i> {{ __('common.back') }}
                </AppButton>
                <AppButton v-if="can('product-recycle-bin-restore') && products.data.length"
                    class="btn btn-secondary" @click="restoreAll">
                    <i class="ri-arrow-go-back-line mr-1"></i> {{ __('common.restore_all') }}
                </AppButton>
                <AppButton v-if="can('product-recycle-bin-delete') && products.data.length"
                    class="btn btn-destructive" @click="emptyBin">
                    <i class="ri-delete-bin-line mr-1"></i> {{ __('common.empty_bin') }}
                </AppButton>
            </div>
        </template>
    </AppSectionHeader>

    <AppDataTable v-if="products.data.length" :headers="headers">
        <template #TableBody>
            <tbody>
                <AppDataTableRow v-for="(item, index) in products.data" :key="item.id">
                    <AppDataTableData class="w-16">
                        {{ products.from + index }}
                    </AppDataTableData>
                    <AppDataTableData class="font-medium text-skin-neutral-12">
                        {{ item.name }}
                    </AppDataTableData>
                    <AppDataTableData class="w-40 text-right">
                        <AppTooltip v-if="can('product-recycle-bin-restore')" :text="__('common.restore')" class="mr-3">
                            <AppButton class="btn btn-icon btn-primary"
                                @click="router.get(route('product.recycleBin.restore', item.id))">
                                <i class="ri-arrow-go-back-line"></i>
                            </AppButton>
                        </AppTooltip>
                        <AppTooltip v-if="can('product-recycle-bin-delete')" :text="__('common.delete_permanently')">
                            <AppButton class="btn btn-icon btn-destructive"
                                @click="confirmDelete(route('product.recycleBin.destroyForce', item.id))">
                                <i class="ri-delete-bin-line"></i>
                            </AppButton>
                        </AppTooltip>
                    </AppDataTableData>
                </AppDataTableRow>
            </tbody>
        </template>
    </AppDataTable>

    <AppPaginator v-if="products.data.length" :links="products.links" :from="products.from || 0"
        :to="products.to || 0" :total="products.total || 0" class="mt-4 justify-center" />

    <AppAlert v-if="!products.data.length" class="mt-4">
        {{ __('common.recycle_bin_empty') }}
    </AppAlert>

    <AppConfirmDialog ref="confirmDialogRef" />
</template>

<script setup>
import { ref, computed, inject } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import useAuthCan from '@/Composables/useAuthCan'

const props = defineProps({
    products: { type: Object, default: () => ({}) },
})

const translate = inject('translate')

const breadCrumb = [
    { label: translate('common.home'), href: route('dashboard.index') },
    { label: translate('common.menu.products'), href: route('product.index') },
    { label: translate('common.recycle_bin'), last: true },
]

const headers = computed(() => [
    translate('common.header.sl'),
    translate('common.header.name'),
    translate('common.header.actions'),
])

const confirmDialogRef = ref(null)
const confirmDelete = (deleteRoute) => {
    confirmDialogRef.value.openModal(deleteRoute)
}

const restoreAll = () => router.get(route('product.recycleBin.restoreAll'))
const emptyBin = () => confirmDialogRef.value.openModal(route('product.recycleBin.empty'))

const { can } = useAuthCan()
</script>
