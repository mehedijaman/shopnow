<template>
    <Head :title="title"></Head>
    <AppSectionHeader :title="title" :bread-crumb="breadCrumb">
        <template #right>
            <AppButton
                class="btn btn-primary"
                @click="$inertia.visit(route('cart.create'))"
            >
                <i class="ri-add-fill mr-1"></i>
                Create Cart
            </AppButton>
        </template>
    </AppSectionHeader>

    <AppDataSearch
        v-if="carts.data.length || route().params.searchTerm"
        :url="route('cart.index')"
        fields-to-search="id"
    ></AppDataSearch>

    <AppDataTable v-if="carts.data.length" :headers="headers">
        <template #TableBody>
            <tbody>
                <AppDataTableRow
                    v-for="(item, index) in carts.data"
                    :key="item.id"
                >
                    <AppDataTableData>
                        {{ item.id }}
                    </AppDataTableData>

                    <!-- <AppDataTableData>
                        {{ item.name }}
                    </AppDataTableData> -->

                    <AppDataTableData>
                        <!-- Edit cart -->
                        <AppTooltip text="Edit Cart" class="mr-2">
                            <AppButton
                                class="btn btn-icon btn-primary"
                                @click="
                                    $inertia.visit(
                                        route(
                                            'cart.edit',
                                            item.id
                                        )
                                    )
                                "
                            >
                                <i class="ri-edit-line"></i>
                            </AppButton>
                        </AppTooltip>

                        <!-- Delete cart -->
                        <AppTooltip text="Delete Cart">
                            <AppButton
                                class="btn btn-icon btn-destructive"
                                @click="
                                    confirmDelete(
                                        route(
                                            'cart.destroy',
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
        :links="carts.links"
        :from="carts.from"
        :to="carts.to"
        :total="carts.total"
        class="mt-4 justify-center"
    ></AppPaginator>

    <AppAlert v-if="!carts.data.length" class="mt-4">
        No carts found.
    </AppAlert>

    <AppConfirmDialog ref="confirmDialogRef"></AppConfirmDialog>
</template>

<script setup>
import { ref } from 'vue'
import { Head } from '@inertiajs/vue3'
import useTitle from '@/Composables/useTitle'
import useAuthCan from '@/Composables/useAuthCan'

const { title } = useTitle('Cart')
const { can } = useAuthCan()

const props = defineProps({
  carts: {
    type: Object,
    default: () => {}
  }
})

const breadCrumb = [
  { label: 'Home', href: route('dashboard.index') },
  { label: 'Carts', last: true }
]

const headers = ['ID', 'Actions']

const confirmDialogRef = ref(null)
const confirmDelete = (deleteRoute) => {
    confirmDialogRef.value.openModal(deleteRoute)
}
</script>
