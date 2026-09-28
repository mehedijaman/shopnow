<template>
    <AppSectionHeader :title="__('blog::admin.tags')" :bread-crumb="breadCrumb">
        <template #right>
            <div class="flex gap-2">
                <AppButton
                    v-if="can('Blog: Tag - Recycle Bin List')"
                    class="btn btn-secondary"
                    @click="$inertia.visit(route('blogTag.recycleBin.index'))"
                >
                    <i class="ri-delete-bin-line mr-1"></i> {{ __('common.recycle_bin') }}
                </AppButton>
                <AppButton
                    v-if="can('Blog: Tag - Create')"
                    class="btn btn-primary"
                    @click="$inertia.visit(route('blogTag.create'))"
                >
                    {{ __('blog::admin.create_tag') }}
                </AppButton>
            </div>
        </template>
    </AppSectionHeader>

    <AppDataSearch
        v-if="tags.data.length || route().params.searchTerm"
        :url="route('blogTag.index')"
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
                        {{ item.posts_count }}
                    </AppDataTableData>

                    <AppDataTableData>
                        <!-- edit tag -->
                        <AppTooltip
                            v-if="can('Blog: Tag - Edit')"
                            :text="__('blog::admin.edit_tag')"
                            class="mr-3"
                        >
                            <AppButton
                                class="btn btn-icon btn-primary"
                                @click="
                                    $inertia.visit(
                                        route('blogTag.edit', item.id)
                                    )
                                "
                            >
                                <i class="ri-edit-line"></i>
                            </AppButton>
                        </AppTooltip>

                        <!-- delete tag -->
                        <AppTooltip
                            v-if="can('Blog: Tag - Delete')"
                            :text="__('blog::admin.delete_tag')"
                        >
                            <AppButton
                                class="btn btn-icon btn-destructive"
                                @click="
                                    confirmDelete(
                                        route('blogTag.destroy', item.id)
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

    <AppAlert v-if="!tags.data.length" class="mt-4"> {{ __('blog::admin.no_tags_found') }} </AppAlert>

    <AppConfirmDialog ref="confirmDialogRef"></AppConfirmDialog>
</template>

<script setup>
import { inject, ref } from 'vue'
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
    { label: translate('blog::admin.tags'), last: true }
]

const headers = [
    translate('common.header.name'),
    translate('blog::admin.posts'),
    translate('common.header.actions'),
]

const confirmDialogRef = ref(null)
const confirmDelete = (deleteRoute) => {
    confirmDialogRef.value.openModal(deleteRoute)
}

const { can } = useAuthCan()
</script>
