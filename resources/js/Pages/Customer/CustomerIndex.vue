<template>
    <Head :title="title"></Head>
    <AppSectionHeader :title="title" :bread-crumb="breadCrumb">
        <template #right>
            <AppButton
                class="btn btn-primary"
                @click="$inertia.visit(route('customer.create'))"
            >
                <i class="ri-add-fill mr-1"></i>
                Create Customer
            </AppButton>
        </template>
    </AppSectionHeader>

    <AppDataSearch
        v-if="customers.data.length || route().params.searchTerm"
        :url="route('customer.index')"
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
                        {{ item.id }}
                    </AppDataTableData>

                    <!-- <AppDataTableData>
                        {{ item.name }}
                    </AppDataTableData> -->

                    <AppDataTableData>
                        <!-- Edit customer -->
                        <AppTooltip text="Edit Customer" class="mr-2">
                            <AppButton
                                class="btn btn-icon btn-primary"
                                @click="
                                    $inertia.visit(
                                        route(
                                            'customer.edit',
                                            item.id
                                        )
                                    )
                                "
                            >
                                <i class="ri-edit-line"></i>
                            </AppButton>
                        </AppTooltip>

                        <!-- Delete customer -->
                        <AppTooltip text="Delete Customer">
                            <AppButton
                                class="btn btn-icon btn-destructive"
                                @click="
                                    confirmDelete(
                                        route(
                                            'customer.destroy',
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
        :from="customers.from"
        :to="customers.to"
        :total="customers.total"
        class="mt-4 justify-center"
    ></AppPaginator>

    <AppAlert v-if="!customers.data.length" class="mt-4">
        No customers found.
    </AppAlert>

    <AppConfirmDialog ref="confirmDialogRef"></AppConfirmDialog>
</template>

<script setup>
import { ref } from 'vue'
import { Head } from '@inertiajs/vue3'
import useTitle from '@/Composables/useTitle'
import useAuthCan from '@/Composables/useAuthCan'

const { title } = useTitle('Customer')
const { can } = useAuthCan()

const props = defineProps({
  customers: {
    type: Object,
    default: () => {}
  }
})

const breadCrumb = [
  { label: 'Home', href: route('dashboard.index') },
  { label: 'Customers', last: true }
]

const headers = ['ID', 'Actions']

const confirmDialogRef = ref(null)
const confirmDelete = (deleteRoute) => {
    confirmDialogRef.value.openModal(deleteRoute)
}
</script>
