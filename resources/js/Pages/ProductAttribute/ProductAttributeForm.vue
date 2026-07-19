<template>
    <AppSectionHeader title="Product Attributes" :bread-crumb="breadCrumb">
        <template #right>
            <AppButton
                v-if="can('product-edit') || can('product-create')"
                class="btn btn-primary mt-6"
                :disabled="saving"
                @click="submitForm"
            >
                <i class="ri-save-line mr-1"></i>
                {{ saving ? 'Saving…' : 'Save Attribute' }}
            </AppButton>
        </template>
    </AppSectionHeader>

    <div class="grid grid-cols-1 gap-5 xl:grid-cols-3">
        <!-- Left: Main form -->
        <div class="xl:col-span-2 space-y-5">
            <AppCard>
                <template #title>
                    <div class="flex items-center gap-2">
                        <i class="ri-settings-3-line text-skin-primary-9"></i>
                        {{ isEdit ? 'Edit Attribute' : 'New Attribute' }}
                    </div>
                </template>
                <template #content>
                    <AppFormErrors class="mb-4" />
                    <div class="space-y-4">
                        <div>
                            <AppLabel for="name">Attribute Name <span class="text-red-500">*</span></AppLabel>
                            <AppInputText
                                id="name"
                                v-model="form.name"
                                type="text"
                                placeholder="e.g. Color, Size, Material"
                                :class="{ 'input-error': errorsFields.includes('name') }"
                            />
                        </div>

                        <div>
                            <AppLabel for="input_type">Input Type <span class="text-red-500">*</span></AppLabel>
                            <select
                                id="input_type"
                                v-model="form.input_type"
                                class="mt-1 block w-full rounded-md border-0 bg-skin-neutral-1 px-3 py-2 text-skin-neutral-12 shadow-xs ring-1 ring-inset ring-skin-neutral-7 focus:ring-2 focus:ring-inset focus:ring-skin-neutral-7 sm:text-sm sm:leading-6"
                            >
                                <option value="select">Dropdown Select</option>
                                <option value="color">Color Swatch</option>
                                <option value="image">Image Swatch</option>
                            </select>
                        </div>
                    </div>
                </template>
            </AppCard>

            <!-- Values -->
            <AppCard>
                <template #title>
                    <div class="flex items-center gap-2">
                        <i class="ri-list-unordered text-skin-primary-9"></i>
                        Attribute Values
                    </div>
                </template>
                <template #content>
                    <p class="mb-3 text-xs text-skin-neutral-9">
                        Define the possible values for this attribute (e.g. Red, Blue, Green for Color).
                    </p>

                    <div class="space-y-3">
                        <div
                            v-for="(val, index) in form.values"
                            :key="index"
                            class="flex items-start gap-3 rounded-lg border border-skin-neutral-5 bg-skin-neutral-2 p-3"
                        >
                            <span class="mt-2 flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-skin-neutral-4 text-xs font-semibold text-skin-neutral-11">
                                {{ index + 1 }}
                            </span>

                            <div class="min-w-0 flex-1 space-y-2">
                                <div class="flex gap-2">
                                    <div class="flex-1">
                                        <AppLabel class="text-xs">Value Name <span class="text-red-500">*</span></AppLabel>
                                        <AppInputText
                                            v-model="val.value"
                                            type="text"
                                            :placeholder="form.input_type === 'color' ? 'e.g. Red' : form.input_type === 'image' ? 'e.g. Pattern A' : 'e.g. Small'"
                                        />
                                    </div>

                                    <!-- Swatch input (color or image) -->
                                    <div v-if="form.input_type === 'color'" class="w-24">
                                        <AppLabel class="text-xs">Color</AppLabel>
                                        <input
                                            v-model="val.swatch"
                                            type="color"
                                            class="mt-1 h-9 w-full cursor-pointer rounded border border-skin-neutral-5 bg-transparent"
                                        />
                                    </div>

                                    <div v-if="form.input_type === 'image'" class="w-32">
                                        <AppLabel class="text-xs">Swatch Image URL</AppLabel>
                                        <AppInputText
                                            v-model="val.swatch"
                                            type="text"
                                            placeholder="https://..."
                                        />
                                    </div>
                                </div>
                            </div>

                            <button
                                type="button"
                                class="mt-6 shrink-0 rounded p-1.5 text-skin-neutral-8 transition-colors hover:bg-red-50 hover:text-red-600"
                                title="Remove value"
                                @click="removeValue(index)"
                            >
                                <i class="ri-close-circle-line text-lg"></i>
                            </button>
                        </div>
                    </div>

                    <button
                        type="button"
                        class="mt-3 flex items-center gap-1.5 rounded-md border border-dashed border-skin-neutral-6 px-3 py-2 text-sm text-skin-neutral-11 transition hover:border-skin-primary-9 hover:text-skin-primary-10"
                        @click="addValue"
                    >
                        <i class="ri-add-line"></i>
                        Add Value
                    </button>
                </template>
            </AppCard>
        </div>

        <!-- Right sidebar: preview -->
        <div class="space-y-5">
            <AppCard>
                <template #title>
                    <div class="flex items-center gap-2">
                        <i class="ri-eye-line text-skin-primary-9"></i>
                        Preview
                    </div>
                </template>
                <template #content>
                    <div v-if="form.name" class="space-y-3">
                        <p class="text-sm font-medium text-skin-neutral-12">{{ form.name }}</p>
                        <p class="text-xs text-skin-neutral-9">Type: {{ form.input_type }}</p>

                        <div v-if="form.values.length" class="flex flex-wrap gap-2">
                            <span
                                v-for="(val, i) in form.values"
                                :key="i"
                                class="inline-flex items-center gap-1.5 rounded-md border border-skin-neutral-6 bg-skin-neutral-2 px-3 py-1.5 text-xs font-medium text-skin-neutral-11"
                            >
                                <span
                                    v-if="val.swatch && form.input_type === 'color'"
                                    class="inline-block h-3 w-3 rounded-full"
                                    :style="{ backgroundColor: val.swatch }"
                                ></span>
                                <span
                                    v-else-if="val.swatch && form.input_type === 'image'"
                                    class="inline-block h-4 w-4 rounded bg-cover bg-center"
                                    :style="{ backgroundImage: `url(${val.swatch})` }"
                                ></span>
                                {{ val.value || '—' }}
                            </span>
                        </div>
                        <p v-else class="text-xs text-skin-neutral-9 italic">No values added yet</p>
                    </div>
                    <p v-else class="text-xs text-skin-neutral-9 italic">Enter an attribute name to preview</p>
                </template>
            </AppCard>
        </div>
    </div>

    <div class="mt-5 flex justify-end">
        <AppButton
            v-if="can('product-edit') || can('product-create')"
            class="btn btn-primary"
            :disabled="saving"
            @click="submitForm"
        >
            <i class="ri-save-line mr-1"></i>
            {{ saving ? 'Saving…' : 'Save Attribute' }}
        </AppButton>
    </div>
</template>

<script setup>
import { ref, reactive, computed } from 'vue'
import { useForm } from '@inertiajs/vue3'
import useAuthCan from '@/Composables/useAuthCan'
import useTitle from '@/Composables/useTitle'
import useFormErrors from '@/Composables/useFormErrors'

const { can } = useAuthCan()
const { errorsFields } = useFormErrors()
const saving = ref(false)

const props = defineProps({
    attribute: {
        type: Object,
        default: null
    }
})

const isEdit = computed(() => !!props.attribute)

const form = reactive({
    name: props.attribute?.name || '',
    input_type: props.attribute?.input_type || 'select',
    values: (props.attribute?.values || []).map((v) => ({
        id: v.id || null,
        value: v.value || '',
        swatch: v.swatch || '',
    })),
})

const addValue = () => {
    form.values.push({ id: null, value: '', swatch: '' })
}

const removeValue = (index) => {
    form.values.splice(index, 1)
}

const breadCrumb = [
    { label: 'Home', href: route('dashboard.index') },
    { label: 'Attributes', href: route('productAttribute.index') },
    { label: isEdit.value ? 'Edit' : 'Create', last: true }
]

const { title } = useTitle(isEdit.value ? 'Edit Attribute' : 'Create Attribute')

const submitForm = () => {
    saving.value = true

    // Filter out empty values
    const filteredValues = form.values.filter((v) => v.value.trim() !== '')

    const payload = {
        name: form.name,
        input_type: form.input_type,
        values: filteredValues,
    }

    const inertiaForm = useForm(payload)

    if (isEdit.value) {
        inertiaForm.transform((data) => ({ ...data, _method: 'PUT' }))
            .post(route('productAttribute.update', props.attribute.id), {
                onFinish: () => { saving.value = false },
            })
    } else {
        inertiaForm.post(route('productAttribute.store'), {
            onFinish: () => { saving.value = false },
        })
    }
}
</script>
