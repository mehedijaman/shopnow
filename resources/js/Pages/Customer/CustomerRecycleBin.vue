<template>
    <Head :title="title"></Head>
    <AppSectionHeader :title="title" :bread-crumb="breadCrumb">
        <template #right>
            <div class="flex gap-2">
                <AppButton
                    v-if="can('customer-list')"
                    class="btn btn-secondary"
                    @click="$inertia.visit(route('customer.index'))"
                >
                    <i class="ri-arrow-left-s-line mr-1"></i>
                    {{ __('common.back_to_list') }}
                </AppButton>

                <AppButton
                    v-if="can('customer-recycle-bin-restore')"
                    class="btn btn-primary"
                    @click="
                        $inertia.visit(route('customer.recycleBin.restoreAll'))
                    "
                >
                    <i class="ri-recycle-fill mr-1"></i>
                    {{ __('customer::admin.restore_recycle_bin') }}
                </AppButton>

                <AppButton
                    v-if="can('customer-recycle-bin-delete')"
                    class="btn btn-destructive"
                    @click="confirmDelete(route('customer.recycleBin.empty'))"
                >
                    <i class="ri-delete-bin-7-line mr-1"></i>
                    {{ __('customer::admin.empty_recycle_bin') }}
                </AppButton>
            </div>
        </template>
    </AppSectionHeader>

    <AppDataSearch
        v-if="customers.data.length || route().params.searchTerm"
        :url="route('customer.recycleBin.index')"
        fields-to-search="id"
    ></AppDataSearch>

    <AppDataTable v-if="customers.data.length" :headers="headers">
        <template #TableBody>
            <tbody>
                <AppDataTableRow
                    v-for="(item, index) in customers.data"
                    :key="item.id"
                >
                    <AppDataTableData>
                        {{
                            (customers.current_page - 1) * customers.per_page +
                            (index + 1)
                        }}
                    </AppDataTableData>

                    <AppDataTableData>
                        {{ item.name }}
                    </AppDataTableData>

                    <AppDataTableData>
                        {{ item.phone }}
                    </AppDataTableData>

                    <AppDataTableData>
                        {{ item.email }}
                    </AppDataTableData>

                    <AppDataTableData>
                        <!-- Restore -->
                        <AppTooltip
                            v-if="can('customer-recycle-bin-restore')"
                            :text="__('common.restore')"
                            class="mr-2"
                        >
                            <AppButton
                                class="btn btn-icon btn-primary"
                                @click="
                                    $inertia.visit(
                                        route(
                                            'customer.recycleBin.restore',
                                            item.id
                                        )
                                    )
                                "
                            >
                                <i class="ri-recycle-fill"></i>
                            </AppButton>
                        </AppTooltip>

                        <!-- Delete -->
                        <AppTooltip
                            v-if="can('customer-recycle-bin-delete')"
                            :text="__('common.delete_permanently')"
                        >
                            <AppButton
                                class="btn btn-icon btn-destructive"
                                @click="
                                    confirmDelete(
                                        route(
                                            'customer.recycleBin.destroyForce',
                                            item.id
                                        )
                                    )
                                "
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
        :links="customers.links"
        :from="customers.from ?? 0"
        :to="customers.to ?? 0"
        :total="customers.total ?? 0"
        class="mt-4 justify-center"
    ></AppPaginator>

    <AppAlert v-if="!customers.data.length" class="mt-4">
        {{ __('customer::admin.no_data_found') }}
    </AppAlert>

    <AppConfirmDialog ref="confirmDialogRef"></AppConfirmDialog>
</template>

<script setup>
import { computed, inject, ref } from 'vue'
import { Head } from '@inertiajs/vue3'
import useTitle from '@/Composables/useTitle'
import useAuthCan from '@/Composables/useAuthCan'

const { can } = useAuthCan()
const translate = inject('translate')

const { title } = useTitle(translate('customer::admin.customer_recycle_bin'))

const props = defineProps({
    customers: {
        type: Object,
        default: () => {}
    }
})

const breadCrumb = [
    { label: translate('common.home'), href: route('customer.index') },
    { label: translate('customer::admin.customers'), href: route('customer.index') },
    { label: title, last: true }
]

const headers = computed(() => [
    translate('common.header.sl'),
    translate('common.header.name'),
    translate('common.header.phone'),
    translate('customer::admin.email'),
    translate('common.field.status'),
    translate('common.header.actions'),
])

const confirmDialogRef = ref(null)
const confirmDelete = (deleteRoute) => {
    confirmDialogRef.value.openModal(deleteRoute)
}
</script>
