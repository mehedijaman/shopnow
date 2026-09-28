<template>
    <Head :title="__('promo-code::admin.promo_codes_recycle_bin')"></Head>
    <AppSectionHeader :title="__('promo-code::admin.promo_codes_recycle_bin')" :bread-crumb="breadCrumb">
        <template #right>
            <div class="flex gap-2">
                <AppButton class="btn btn-secondary" @click="$inertia.visit(route('promoCode.index'))">
                    <i class="ri-arrow-left-line mr-1"></i> {{ __('common.back') }}
                </AppButton>
                <AppButton
                    v-if="can('promo-code-recycle-bin-restore') && promoCodes.data.length"
                    class="btn btn-secondary"
                    @click="restoreAll"
                >
                    <i class="ri-arrow-go-back-line mr-1"></i> {{ __('common.restore_all') }}
                </AppButton>
                <AppButton
                    v-if="can('promo-code-recycle-bin-delete') && promoCodes.data.length"
                    class="btn btn-destructive"
                    @click="emptyBin"
                >
                    <i class="ri-delete-bin-line mr-1"></i> {{ __('common.empty_bin') }}
                </AppButton>
            </div>
        </template>
    </AppSectionHeader>

    <AppDataTable v-if="promoCodes.data.length" :headers="headers">
        <template #TableBody>
            <tbody>
                <AppDataTableRow v-for="(item, index) in promoCodes.data" :key="item.id">
                    <AppDataTableData class="w-16">
                        {{ promoCodes.from + index }}
                    </AppDataTableData>
                    <AppDataTableData class="font-medium text-skin-neutral-12">
                        {{ item.code }}
                    </AppDataTableData>
                    <AppDataTableData>
                        {{ discountLabel(item) }}
                    </AppDataTableData>
                    <AppDataTableData class="w-40">
                        {{ item.deleted_at }}
                    </AppDataTableData>
                    <AppDataTableData class="w-40 text-right">
                        <AppTooltip
                            v-if="can('promo-code-recycle-bin-restore')"
                            :text="__('common.restore')"
                            class="mr-3"
                        >
                            <AppButton
                                class="btn btn-icon btn-primary"
                                @click="router.get(route('promoCode.recycleBin.restore', item.id))"
                            >
                                <i class="ri-arrow-go-back-line"></i>
                            </AppButton>
                        </AppTooltip>
                        <AppTooltip v-if="can('promo-code-recycle-bin-delete')" :text="__('common.delete_permanently')">
                            <AppButton
                                class="btn btn-icon btn-destructive"
                                @click="confirmDelete(route('promoCode.recycleBin.destroyForce', item.id))"
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
        v-if="promoCodes.data.length"
        :links="promoCodes.links"
        :from="promoCodes.from || 0"
        :to="promoCodes.to || 0"
        :total="promoCodes.total || 0"
        class="mt-4 justify-center"
    />

    <AppAlert v-if="!promoCodes.data.length" class="mt-4">
        {{ __('common.recycle_bin_empty') }}
    </AppAlert>

    <AppConfirmDialog ref="confirmDialogRef" />
</template>

<script setup>
import { inject, ref } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import useAuthCan from '@/Composables/useAuthCan'

const props = defineProps({
    promoCodes: { type: Object, default: () => ({}) },
})

const translate = inject('translate')

const breadCrumb = [
    { label: translate('common.home'), href: route('dashboard.index') },
    { label: translate('promo-code::admin.promo_codes'), href: route('promoCode.index') },
    { label: translate('common.recycle_bin'), last: true },
]

const headers = [
    translate('common.header.sl'),
    translate('promo-code::admin.code'),
    translate('promo-code::admin.discount'),
    translate('promo-code::admin.deleted_at'),
    translate('common.header.actions'),
]

const discountLabel = (item) => {
    if (item.discount_type === 'free_shipping') return translate('promo-code::admin.free_shipping')
    if (item.discount_type === 'percentage') return `${item.discount_value}%`
    return `${item.discount_value} Tk`
}

const confirmDialogRef = ref(null)
const confirmDelete = (deleteRoute) => {
    confirmDialogRef.value.openModal(deleteRoute)
}

const restoreAll = () => router.get(route('promoCode.recycleBin.restoreAll'))
const emptyBin = () => confirmDialogRef.value.openModal(route('promoCode.recycleBin.empty'))

const { can } = useAuthCan()
</script>
