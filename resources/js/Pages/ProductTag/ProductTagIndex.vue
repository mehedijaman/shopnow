<template>
    <AppSectionHeader :title="__('common.menu.product_tags')" :bread-crumb="breadCrumb">
        <template #right>
            <div class="flex gap-2">
                <AppButton
                    v-if="can('product-tag-recycle-bin-list')"
                    class="btn btn-secondary"
                    @click="$inertia.visit(route('productTag.recycleBin.index'))"
                >
                    <i class="ri-delete-bin-2-line mr-1"></i> {{ __('common.recycle_bin') }}
                </AppButton>
                <AppButton
                    v-if="can('product-tag-create')"
                    class="btn btn-primary"
                    @click="$inertia.visit(route('productTag.create'))"
                >
                    {{ __('product::admin.create_tag') }}
                </AppButton>
            </div>
        </template>
    </AppSectionHeader>

    <AppDataSearch
        v-if="tags.data.length || route().params.searchTerm"
        :url="route('productTag.index')"
        fields-to-search="name"
    ></AppDataSearch>

    <AppDataTable v-if="tags.data.length" :headers="headers">
        <template #TableBody>
            <tbody>
                <AppDataTableRow v-for="item in tags.data" :key="item.id">
                    <AppDataTableData>
                        {{ item.name }}
                    </AppDataTableData>

                    <AppDataTableData>
                        {{ item.products_count }}
                    </AppDataTableData>

                    <AppDataTableData>
                        <!-- edit tag -->
                        <AppTooltip
                            v-if="can('product-tag-edit')"
                            :text="__('product::admin.edit_tag')"
                            class="mr-3"
                        >
                            <AppButton
                                class="btn btn-icon btn-primary"
                                @click="
                                    $inertia.visit(
                                        route('productTag.edit', item.id)
                                    )
                                "
                            >
                                <i class="ri-edit-line"></i>
                            </AppButton>
                        </AppTooltip>

                        <!-- delete tag -->
                        <AppTooltip
                            v-if="can('product-tag-delete')"
                            :text="__('product::admin.delete_tag')"
                        >
                            <AppButton
                                class="btn btn-icon btn-destructive"
                                @click="
                                    confirmDelete(
                                        route('productTag.destroy', item.id)
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
        :links="tags.links"
        :from="tags.from || 0"
        :to="tags.to || 0"
        :total="tags.total || 0"
        class="mt-4 justify-center"
    ></AppPaginator>

    <AppAlert v-if="!tags.data.length" class="mt-4"> {{ __('product::admin.tags_empty') }} </AppAlert>

    <AppConfirmDialog ref="confirmDialogRef"></AppConfirmDialog>
</template>

<script setup>
import { ref, computed, inject } from 'vue'
import useAuthCan from '@/Composables/useAuthCan'

const props = defineProps({
    tags: {
        type: Object,
        default: () => {}
    }
})

const translate = inject('translate')

const breadCrumb = [
    { label: translate('common.home'), href: route('dashboard.index') },
    { label: translate('common.menu.product_tags'), last: true }
]

const headers = computed(() => [
    translate('common.field.name'),
    translate('common.header.products'),
    translate('common.header.actions'),
])

const confirmDialogRef = ref(null)
const confirmDelete = (deleteRoute) => {
    confirmDialogRef.value.openModal(deleteRoute)
}

const { can } = useAuthCan()
</script>
