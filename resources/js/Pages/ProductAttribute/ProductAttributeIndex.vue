<template>
    <AppSectionHeader title="Product Attributes" :bread-crumb="breadCrumb">
        <template #right>
            <AppButton
                v-if="can('product-edit')"
                class="btn btn-primary"
                @click="$inertia.visit(route('productAttribute.create'))"
            >
                <i class="ri-add-fill mr-1"></i>
                Create Attribute
            </AppButton>
        </template>
    </AppSectionHeader>

    <div v-if="attributes.length">
        <AppDataTable :headers="headers">
            <template #TableBody>
                <tbody>
                    <AppDataTableRow v-for="item in attributes" :key="item.id">
                        <AppDataTableData class="font-medium">
                            {{ item.name }}
                        </AppDataTableData>

                        <AppDataTableData>
                            <span class="rounded-sm bg-skin-neutral-3 px-2 py-0.5 text-xs font-medium text-skin-neutral-11">
                                {{ item.input_type }}
                            </span>
                        </AppDataTableData>

                        <AppDataTableData>
                            <div class="flex flex-wrap gap-1">
                                <span
                                    v-for="val in item.values"
                                    :key="val.id"
                                    class="inline-flex items-center gap-1 rounded-sm bg-skin-neutral-3 px-2 py-0.5 text-xs"
                                >
                                    <span
                                        v-if="val.swatch && item.input_type === 'color'"
                                        class="inline-block h-3 w-3 rounded-full border border-skin-neutral-6"
                                        :style="{ backgroundColor: val.swatch }"
                                    ></span>
                                    <span
                                        v-else-if="val.swatch && item.input_type === 'image'"
                                        class="inline-block h-3 w-3 rounded bg-cover bg-center"
                                        :style="{ backgroundImage: `url(${val.swatch})` }"
                                    ></span>
                                    {{ val.value }}
                                </span>
                                <span v-if="!item.values.length" class="text-xs text-skin-neutral-9 italic">
                                    No values
                                </span>
                            </div>
                        </AppDataTableData>

                        <AppDataTableData class="text-right">
                            <AppTooltip
                                v-if="can('product-edit')"
                                text="Edit Attribute"
                                class="mr-3"
                            >
                                <AppButton
                                    class="btn btn-icon btn-primary"
                                    @click="$inertia.visit(route('productAttribute.edit', item.id))"
                                >
                                    <i class="ri-edit-line"></i>
                                </AppButton>
                            </AppTooltip>

                            <AppTooltip
                                v-if="can('product-delete')"
                                text="Delete Attribute"
                            >
                                <AppButton
                                    class="btn btn-icon btn-destructive"
                                    @click="confirmDelete(route('productAttribute.destroy', item.id))"
                                >
                                    <i class="ri-delete-bin-line"></i>
                                </AppButton>
                            </AppTooltip>
                        </AppDataTableData>
                    </AppDataTableRow>
                </tbody>
            </template>
        </AppDataTable>
    </div>

    <AppAlert v-if="!attributes.length" class="mt-4">
        No product attributes found. Create attributes (e.g. Color, Size) to enable variable products.
    </AppAlert>

    <AppConfirmDialog ref="confirmDialogRef"></AppConfirmDialog>
</template>

<script setup>
import { ref } from 'vue'
import useAuthCan from '@/Composables/useAuthCan'

defineProps({
    attributes: {
        type: Array,
        default: () => []
    }
})

const breadCrumb = [
    { label: 'Home', href: route('dashboard.index') },
    { label: 'Product Attributes', last: true }
]

const headers = ['Name', 'Input Type', 'Values', 'Actions']

const confirmDialogRef = ref(null)
const confirmDelete = (deleteRoute) => {
    confirmDialogRef.value.openModal(deleteRoute)
}

const { can } = useAuthCan()
</script>
