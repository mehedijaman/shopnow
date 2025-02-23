<template>
    <Head :title="title"></Head>
    <AppSectionHeader :title="title" :bread-crumb="breadCrumb">
        <!-- <template #right>
            <AppButton
                class="btn btn-primary"
                @click="$inertia.visit(route('order.create'))"
            >
                <i class="ri-add-fill mr-1"></i>
                Create Order
            </AppButton>
        </template> -->
    </AppSectionHeader>

    <AppDataSearch
        v-if="orders.data.length || route().params.searchTerm"
        :url="route('order.index')"
        fields-to-search="id"
    ></AppDataSearch>

    <AppDataTable v-if="orders.data.length" :headers="headers">
        <template #TableBody>
            <tbody>
                <AppDataTableRow
                    v-for="(item, index) in orders.data"
                    :key="item.id"
                >
                    <AppDataTableData>
                        {{ item.created_at }}
                    </AppDataTableData>

                    <AppDataTableData>
                        {{ item.name }}
                    </AppDataTableData>

                    <AppDataTableData>
                        {{ item.email }}
                    </AppDataTableData>

                    <AppDataTableData>
                        {{ item.phone }}
                    </AppDataTableData>

                    <AppDataTableData>
                        {{ item.address }}
                    </AppDataTableData>

                    <AppDataTableData>
                        {{ item.status }}
                    </AppDataTableData>

                    <AppDataTableData>
                        {{ item.total }}
                    </AppDataTableData>

                    <AppDataTableData>
                        <AppTooltip text="View Order" class="mr-2">
                            <AppButton
                                class="btn btn-icon btn-primary"
                                @click="
                                    $inertia.visit(route('order.show', item.id))
                                "
                            >
                                <i class="ri-eye-line"></i>
                            </AppButton>
                        </AppTooltip>

                        <!-- <AppTooltip text="Delete Order">
                            <AppButton
                                class="btn btn-icon btn-destructive"
                                @click="
                                    confirmDelete(
                                        route('order.destroy', item.id)
                                    )
                                "
                            >
                                <i class="ri-delete-bin-line"></i>
                            </AppButton>
                        </AppTooltip> -->
                    </AppDataTableData>
                </AppDataTableRow>
            </tbody>
        </template>
    </AppDataTable>

    <AppPaginator
        :links="orders.links"
        :from="orders.from"
        :to="orders.to"
        :total="orders.total"
        class="mt-4 justify-center"
    ></AppPaginator>

    <AppAlert v-if="!orders.data.length" class="mt-4">
        No orders found.
    </AppAlert>

    <AppConfirmDialog ref="confirmDialogRef"></AppConfirmDialog>
</template>

<script setup>
import { ref } from 'vue'
import { Head } from '@inertiajs/vue3'
import useTitle from '@/Composables/useTitle'
import useAuthCan from '@/Composables/useAuthCan'

const { title } = useTitle('Order')
const { can } = useAuthCan()

const props = defineProps({
    orders: {
        type: Object,
        default: () => {}
    }
})

const breadCrumb = [
    { label: 'Home', href: route('dashboard.index') },
    { label: 'Orders', last: true }
]

const headers = [
    'Date',
    'Name',
    'Email',
    'Phone',
    'Address',
    'Status',
    'Total',
    'Actions'
]

const confirmDialogRef = ref(null)
const confirmDelete = (deleteRoute) => {
    confirmDialogRef.value.openModal(deleteRoute)
}
</script>
