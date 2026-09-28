<template>
    <Head :title="__('promo-code::admin.promo_codes')"></Head>
    <AppSectionHeader :title="__('promo-code::admin.promo_codes')" :bread-crumb="breadCrumb">
        <template #right>
            <div class="flex gap-2">
                <AppButton
                    v-if="can('promo-code-recycle-bin-list')"
                    class="btn btn-secondary"
                    @click="$inertia.visit(route('promoCode.recycleBin.index'))"
                >
                    <i class="ri-delete-bin-2-line mr-1"></i> {{ __('common.recycle_bin') }}
                </AppButton>
                <AppButton
                    v-if="can('promo-code-create')"
                    class="btn btn-primary"
                    @click="$inertia.visit(route('promoCode.create'))"
                >
                    <i class="ri-add-fill mr-1"></i> {{ __('promo-code::admin.new_promo_code') }}
                </AppButton>
            </div>
        </template>
    </AppSectionHeader>

    <AppDataSearch
        v-if="promoCodes.data.length || route().params.searchTerm"
        :url="route('promoCode.index')"
        fields-to-search="code"
    ></AppDataSearch>

    <AppDataTable v-if="promoCodes.data.length" :headers="headers">
        <template #TableBody>
            <tbody>
                <AppDataTableRow v-for="item in promoCodes.data" :key="item.id">
                    <AppDataTableData class="font-medium text-skin-neutral-12">
                        {{ item.code }}
                    </AppDataTableData>

                    <AppDataTableData>
                        {{ discountLabel(item) }}
                    </AppDataTableData>

                    <AppDataTableData>
                        {{ item.minimum_order_amount > 0 ? item.minimum_order_amount + ' Tk' : '—' }}
                    </AppDataTableData>

                    <AppDataTableData>
                        {{ item.used_count }} / {{ item.usage_limit ?? '∞' }}
                    </AppDataTableData>

                    <AppDataTableData class="text-xs text-skin-neutral-9">
                        {{ validityLabel(item) }}
                    </AppDataTableData>

                    <AppDataTableData>
                        <span
                            class="rounded-full px-2.5 py-0.5 text-xs font-medium"
                            :class="statusClass(item.status)"
                        >
                            {{ promoStatusText(item.status) }}
                        </span>
                    </AppDataTableData>

                    <AppDataTableData>
                        <div class="flex gap-1.5">
                            <AppTooltip v-if="can('promo-code-edit')" :text="__('common.edit')">
                                <AppButton
                                    class="btn btn-icon btn-primary"
                                    @click="$inertia.visit(route('promoCode.edit', item.id))"
                                >
                                    <i class="ri-edit-line"></i>
                                </AppButton>
                            </AppTooltip>
                            <AppTooltip v-if="can('promo-code-delete')" :text="__('common.delete')">
                                <AppButton
                                    class="btn btn-icon btn-destructive"
                                    @click="confirmDelete(route('promoCode.destroy', item.id))"
                                >
                                    <i class="ri-delete-bin-line"></i>
                                </AppButton>
                            </AppTooltip>
                        </div>
                    </AppDataTableData>
                </AppDataTableRow>
            </tbody>
        </template>
    </AppDataTable>

    <AppPaginator
        :links="promoCodes.links"
        :from="promoCodes.from || 0"
        :to="promoCodes.to || 0"
        :total="promoCodes.total || 0"
        class="mt-4 justify-center"
    ></AppPaginator>

    <AppAlert v-if="!promoCodes.data.length" class="mt-4">{{ __('promo-code::admin.no_promo_codes_found') }}</AppAlert>

    <AppConfirmDialog ref="confirmDialogRef"></AppConfirmDialog>
</template>

<script setup>
import { inject, ref } from 'vue'
import { Head } from '@inertiajs/vue3'
import useAuthCan from '@/Composables/useAuthCan'

const translate = inject('translate')

const { can } = useAuthCan()

defineProps({
    promoCodes: { type: Object, default: () => ({}) },
})

const breadCrumb = [
    { label: translate('common.home'), href: route('dashboard.index') },
    { label: translate('promo-code::admin.promo_codes'), last: true },
]

const headers = [
    translate('promo-code::admin.code'),
    translate('promo-code::admin.discount'),
    translate('promo-code::admin.min_order'),
    translate('promo-code::admin.usage'),
    translate('promo-code::admin.validity'),
    translate('common.field.status'),
    translate('common.header.actions'),
]

const discountLabel = (item) => {
    if (item.discount_type === 'free_shipping') return translate('promo-code::admin.free_shipping')
    if (item.discount_type === 'percentage') {
        const cap = item.maximum_discount_amount ? ' ' + translate('promo-code::admin.up_to', { amount: item.maximum_discount_amount }) : ''
        return `${item.discount_value}%${cap}`
    }
    return `${item.discount_value} Tk`
}

const validityLabel = (item) => {
    if (!item.starts_at && !item.expires_at) return translate('promo-code::admin.always')
    if (item.starts_at && item.expires_at) return `${item.starts_at} → ${item.expires_at}`
    if (item.starts_at) return translate('promo-code::admin.from_date', { date: item.starts_at })
    return translate('promo-code::admin.until_date', { date: item.expires_at })
}

const promoStatusText = (status) => {
    if (status === 'active') return translate('promo-code::admin.status_active')
    if (status === 'scheduled') return translate('promo-code::admin.status_scheduled')
    if (status === 'expired') return translate('promo-code::admin.status_expired')
    if (status === 'exhausted') return translate('promo-code::admin.status_exhausted')
    if (status === 'disabled') return translate('promo-code::admin.status_disabled')
    return status
}

const statusClass = (status) => ({
    active: 'bg-green-100 text-green-700',
    scheduled: 'bg-blue-100 text-blue-700',
    expired: 'bg-red-100 text-red-600',
    exhausted: 'bg-amber-100 text-amber-700',
    disabled: 'bg-skin-neutral-3 text-skin-neutral-9',
}[status] ?? 'bg-skin-neutral-3 text-skin-neutral-9')

const confirmDialogRef = ref(null)

const confirmDelete = (deleteRoute) => {
    confirmDialogRef.value.openModal(deleteRoute)
}
</script>
