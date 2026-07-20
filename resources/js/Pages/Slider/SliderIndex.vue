<template>
    <Head title="Sliders"></Head>
    <AppSectionHeader title="Sliders" :bread-crumb="breadCrumb">
        <template #right>
            <div class="flex gap-2">
                <AppButton
                    v-if="can('slider-recycle-bin-list')"
                    class="btn btn-secondary"
                    @click="$inertia.visit(route('slider.recycleBin.index'))"
                >
                    <i class="ri-delete-bin-2-line mr-1"></i> Recycle Bin
                </AppButton>
                <AppButton
                    v-if="can('slider-create')"
                    class="btn btn-primary"
                    @click="$inertia.visit(route('slider.create'))"
                >
                    <i class="ri-add-fill mr-1"></i> New Slider
                </AppButton>
            </div>
        </template>
    </AppSectionHeader>

    <div v-if="sliders.data.length" class="mt-5 overflow-x-auto rounded-xl border border-skin-neutral-4 bg-skin-neutral-2 shadow-sm">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-skin-neutral-4 text-left text-xs text-skin-neutral-9">
                    <th class="px-4 py-3 font-medium">Image</th>
                    <th class="px-4 py-3 font-medium">Title</th>
                    <th class="px-4 py-3 font-medium">URL</th>
                    <th class="px-4 py-3 text-center font-medium">Order</th>
                    <th class="px-4 py-3 font-medium">Status</th>
                    <th class="px-4 py-3 font-medium">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-skin-neutral-3">
                <tr v-for="item in sliders.data" :key="item.id" class="hover:bg-skin-neutral-1">
                    <td class="px-4 py-3">
                        <img
                            v-if="item.image_url"
                            :src="item.image_url"
                            :alt="item.title"
                            class="h-14 w-24 rounded-md object-cover"
                        />
                        <div
                            v-else
                            class="flex h-14 w-24 items-center justify-center rounded-md bg-skin-neutral-3 text-skin-neutral-7"
                        >
                            <i class="ri-image-line text-xl"></i>
                        </div>
                    </td>
                    <td class="px-4 py-3">
                        <p class="font-medium text-skin-neutral-12">{{ item.title ?? '—' }}</p>
                        <p v-if="item.description" class="mt-0.5 line-clamp-1 max-w-xs text-xs text-skin-neutral-7">{{ item.description }}</p>
                    </td>
                    <td class="px-4 py-3 text-xs text-skin-neutral-9">
                        <a v-if="item.url" :href="item.url" target="_blank" class="hover:underline">{{ item.url }}</a>
                        <span v-else>—</span>
                    </td>
                    <td class="px-4 py-3 text-center text-skin-neutral-9">{{ item.order ?? '—' }}</td>
                    <td class="px-4 py-3">
                        <span
                            class="rounded-full px-2.5 py-0.5 text-xs font-medium"
                            :class="item.active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-600'"
                        >
                            {{ item.active ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex gap-1.5">
                            <AppTooltip v-if="can('slider-edit')" text="Edit">
                                <AppButton
                                    class="btn btn-icon btn-primary"
                                    @click="$inertia.visit(route('slider.edit', item.id))"
                                >
                                    <i class="ri-edit-line"></i>
                                </AppButton>
                            </AppTooltip>
                            <AppTooltip v-if="can('slider-delete')" text="Delete">
                                <AppButton
                                    class="btn btn-icon btn-destructive"
                                    @click="confirmDelete(route('slider.destroy', item.id))"
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

    <AppAlert v-if="!sliders.data.length" class="mt-4">No sliders found.</AppAlert>

    <AppConfirmDialog ref="confirmDialogRef"></AppConfirmDialog>
</template>

<script setup>
import { ref } from 'vue'
import { Head } from '@inertiajs/vue3'
import useAuthCan from '@/Composables/useAuthCan'

const { can } = useAuthCan()

defineProps({
    sliders: { type: Object, default: () => ({}) },
})

const breadCrumb = [
    { label: 'Home', href: route('dashboard.index') },
    { label: 'Sliders', last: true },
]

const confirmDialogRef = ref(null)

const confirmDelete = (deleteRoute) => {
    confirmDialogRef.value.openModal(deleteRoute)
}
</script>
