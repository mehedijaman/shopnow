<template>
    <Head :title="title"></Head>
    <AppSectionHeader :title="title" :bread-crumb="breadCrumb">
        <template #right>
            <AppButton v-if="can('page-create')" class="btn btn-primary" @click="$inertia.visit(route('page.create'))">
                <i class="ri-add-fill mr-1"></i> New Page
            </AppButton>
        </template>
    </AppSectionHeader>

    <div v-if="pages.data.length" class="mt-5 overflow-x-auto rounded-xl border border-skin-neutral-4 bg-skin-neutral-2 shadow-sm">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-skin-neutral-4 text-left text-xs text-skin-neutral-9">
                    <th class="px-4 py-3 font-medium">Title</th>
                    <th class="px-4 py-3 font-medium">Status</th>
                    <th class="px-4 py-3 font-medium">Published</th>
                    <th class="px-4 py-3 font-medium">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-skin-neutral-3">
                <tr v-for="item in pages.data" :key="item.id" class="hover:bg-skin-neutral-1">
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-2">
                            <span class="font-medium text-skin-neutral-12">{{ item.title }}</span>
                            <span v-if="item.is_system" class="rounded-full bg-blue-100 px-2 py-0.5 text-xs font-medium text-blue-700">System</span>
                        </div>
                        <p class="text-xs text-skin-neutral-7">/{{ item.slug }}</p>
                    </td>
                    <td class="px-4 py-3">
                        <span class="rounded-full px-2.5 py-0.5 text-xs font-medium" :class="item.status === 'Published' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700'">
                            {{ item.status }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-xs text-skin-neutral-8">{{ item.published_at ?? '—' }}</td>
                    <td class="px-4 py-3">
                        <div class="flex gap-1.5">
                            <AppTooltip v-if="can('page-edit')" text="Edit">
                                <AppButton class="btn btn-icon btn-primary" @click="$inertia.visit(route('page.edit', item.id))">
                                    <i class="ri-edit-line"></i>
                                </AppButton>
                            </AppTooltip>
                            <AppTooltip v-if="can('page-delete') && !item.is_system" text="Delete">
                                <AppButton class="btn btn-icon btn-destructive" @click="confirmDelete(route('page.destroy', item.id))">
                                    <i class="ri-delete-bin-line"></i>
                                </AppButton>
                            </AppTooltip>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <AppPaginator :links="pages.links" :from="pages.from ?? 0" :to="pages.to ?? 0" :total="pages.total ?? 0" class="mt-4 justify-center"></AppPaginator>
    <AppAlert v-if="!pages.data.length" class="mt-5">No pages found.</AppAlert>
    <AppConfirmDialog ref="confirmDialogRef"></AppConfirmDialog>
</template>

<script setup>
import { ref } from 'vue'
import { Head } from '@inertiajs/vue3'
import useTitle from '@/Composables/useTitle'
import useAuthCan from '@/Composables/useAuthCan'

const { title } = useTitle('Pages')
const { can } = useAuthCan()

defineProps({
    pages: { type: Object, default: () => ({}) },
})

const breadCrumb = [
    { label: 'Home', href: route('dashboard.index') },
    { label: 'Pages', last: true },
]

const confirmDialogRef = ref(null)
const confirmDelete = (deleteRoute) => {
    confirmDialogRef.value.openModal(deleteRoute)
}
</script>
