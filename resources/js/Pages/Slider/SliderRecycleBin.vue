<template>
    <Head :title="__('slider::admin.slider_recycle_bin')"></Head>
    <AppSectionHeader :title="__('slider::admin.slider_recycle_bin')" :bread-crumb="breadCrumb">
        <template #right>
            <div class="flex gap-2">
                <AppButton
                    v-if="sliders.data.length && can('slider-recycle-Bin-Restore')"
                    class="btn btn-secondary"
                    @click="$inertia.visit(route('slider.recycleBin.restoreAll'))"
                >
                    <i class="ri-arrow-go-back-line mr-1"></i> {{ __('common.restore_all') }}
                </AppButton>
                <AppButton
                    v-if="sliders.data.length && can('slider-recycle-bin-delete')"
                    class="btn btn-destructive"
                    @click="confirmEmptyBin"
                >
                    <i class="ri-delete-bin-2-line mr-1"></i> {{ __('common.empty_bin') }}
                </AppButton>
            </div>
        </template>
    </AppSectionHeader>

    <div v-if="sliders.data.length" class="mt-5 overflow-x-auto rounded-xl border border-skin-neutral-4 bg-skin-neutral-2 shadow-sm">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-skin-neutral-4 text-left text-xs text-skin-neutral-9">
                    <th class="px-4 py-3 font-medium">{{ __('slider::admin.title') }}</th>
                    <th class="px-4 py-3 font-medium">{{ __('common.field.status') }}</th>
                    <th class="px-4 py-3 font-medium">{{ __('slider::admin.deleted_at') }}</th>
                    <th class="px-4 py-3 font-medium">{{ __('common.header.actions') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-skin-neutral-3">
                <tr v-for="item in sliders.data" :key="item.id" class="hover:bg-skin-neutral-1">
                    <td class="px-4 py-3">
                        <span class="font-medium text-skin-neutral-12">{{ item.title ?? __('slider::admin.no_title') }}</span>
                    </td>
                    <td class="px-4 py-3">
                        <span
                            class="rounded-full px-2.5 py-0.5 text-xs font-medium"
                            :class="item.active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-600'"
                        >
                            {{ item.active ? __('common.field.active') : __('common.field.inactive') }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-xs text-skin-neutral-8">{{ item.deleted_at }}</td>
                    <td class="px-4 py-3">
                        <div class="flex gap-1.5">
                            <AppTooltip v-if="can('slider-recycle-Bin-Restore')" :text="__('common.restore')">
                                <AppButton
                                    class="btn btn-icon btn-primary"
                                    @click="$inertia.visit(route('slider.recycleBin.restore', item.id))"
                                >
                                    <i class="ri-arrow-go-back-line"></i>
                                </AppButton>
                            </AppTooltip>
                            <AppTooltip v-if="can('slider-recycle-bin-delete')" :text="__('common.delete_permanently')">
                                <AppButton
                                    class="btn btn-icon btn-destructive"
                                    @click="confirmForceDelete(route('slider.recycleBin.destroyForce', item.id))"
                                >
                                    <i class="ri-delete-bin-line"></i>
                                </AppButton>
                            </AppTooltip>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <AppPaginator
        v-if="sliders.data.length"
        :links="sliders.links"
        :from="sliders.from ?? 0"
        :to="sliders.to ?? 0"
        :total="sliders.total ?? 0"
        class="mt-4 justify-center"
    ></AppPaginator>

    <AppAlert v-if="!sliders.data.length" class="mt-5">{{ __('slider::admin.recycle_bin_empty') }}</AppAlert>

    <AppConfirmDialog ref="confirmDialogRef"></AppConfirmDialog>
</template>

<script setup>
import { inject, ref } from 'vue'
import { Head } from '@inertiajs/vue3'
import useAuthCan from '@/Composables/useAuthCan'

const translate = inject('translate')

const { can } = useAuthCan()

defineProps({
    sliders: { type: Object, default: () => ({}) },
})

const breadCrumb = [
    { label: translate('common.home'), href: route('dashboard.index') },
    { label: translate('slider::admin.sliders'), href: route('slider.index') },
    { label: translate('common.recycle_bin'), last: true },
]

const confirmDialogRef = ref(null)

const confirmForceDelete = (deleteRoute) => {
    confirmDialogRef.value.openModal(deleteRoute)
}

const confirmEmptyBin = () => {
    confirmDialogRef.value.openModal(route('slider.recycleBin.empty'))
}
</script>
