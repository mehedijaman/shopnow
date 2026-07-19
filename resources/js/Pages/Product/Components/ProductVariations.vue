<template>
  <div class="space-y-6">
    <p class="text-sm text-skin-neutral-9">Define attributes and generate variations for this product.</p>

    <!-- Empty state: no attributes exist at all -->
    <div v-if="localAttributes.length === 0 && !showCreateForm" class="rounded-lg border border-dashed border-skin-neutral-5 bg-skin-neutral-2 p-6 text-center">
      <i class="ri-settings-3-line mb-2 text-3xl text-skin-neutral-8"></i>
      <p class="mb-1 text-sm font-medium text-skin-neutral-12">No product attributes found</p>
      <p class="mb-3 text-xs text-skin-neutral-9">Create attributes like <strong>Color</strong> or <strong>Size</strong> to define product variations.</p>
      <AppButton size="sm" @click="showCreateForm = true">
        <i class="ri-add-line mr-1"></i> Create First Attribute
      </AppButton>
    </div>

    <!-- Inline Create Attribute Form -->
    <div v-if="showCreateForm" class="rounded-lg border border-skin-primary-6 bg-skin-primary-1 p-4">
      <div class="mb-3 flex items-center justify-between">
        <h4 class="text-sm font-semibold text-skin-neutral-12">Create New Attribute</h4>
        <button type="button" class="text-skin-neutral-9 hover:text-skin-neutral-12" @click="resetCreateForm">
          <i class="ri-close-line text-lg"></i>
        </button>
      </div>
      <div class="space-y-3">
        <div class="flex gap-3">
          <div class="flex-1">
            <AppLabel class="text-xs">Name <span class="text-red-500">*</span></AppLabel>
            <AppInputText v-model="newAttr.name" type="text" placeholder="e.g. Color, Size" />
          </div>
          <div class="w-36">
            <AppLabel class="text-xs">Type</AppLabel>
            <select
              v-model="newAttr.input_type"
              class="mt-1 block w-full rounded-md border-0 bg-skin-neutral-1 px-3 py-2 text-sm text-skin-neutral-12 shadow-xs ring-1 ring-inset ring-skin-neutral-7 focus:ring-2 focus:ring-inset focus:ring-skin-neutral-7"
            >
              <option value="select">Select</option>
              <option value="color">Color</option>
              <option value="image">Image</option>
            </select>
          </div>
        </div>

        <!-- Values -->
        <div>
          <AppLabel class="text-xs">Values</AppLabel>
          <div class="space-y-2">
            <div v-for="(val, i) in newAttr.values" :key="i" class="flex items-center gap-2">
              <input
                v-model="val.value"
                type="text"
                class="flex-1 rounded-md border border-skin-neutral-5 bg-skin-neutral-1 px-3 py-1.5 text-xs"
                :placeholder="newAttr.input_type === 'color' ? 'e.g. Red' : 'e.g. Small'"
              />
              <input
                v-if="newAttr.input_type === 'color'"
                v-model="val.swatch"
                type="color"
                class="h-8 w-8 cursor-pointer rounded border border-skin-neutral-5"
              />
              <button
                type="button"
                class="text-skin-neutral-9 hover:text-red-600"
                @click="newAttr.values.splice(i, 1)"
              >
                <i class="ri-close-line"></i>
              </button>
            </div>
          </div>
          <button
            type="button"
            class="mt-2 flex items-center gap-1 text-xs text-skin-primary-9 hover:underline"
            @click="newAttr.values.push({ value: '', swatch: '' })"
          >
            <i class="ri-add-line"></i> Add value
          </button>
        </div>

        <div class="flex gap-2">
          <AppButton size="sm" :loading="creatingAttribute" @click="createAttribute">
            <i class="ri-check-line mr-1"></i> Create & Use
          </AppButton>
          <AppButton size="sm" variant="ghost" @click="resetCreateForm">Cancel</AppButton>
        </div>
      </div>
    </div>

    <!-- Price Range Preview -->
    <div v-if="variations.length > 0 && priceRange" class="flex items-center gap-2 rounded-lg border border-skin-neutral-5 bg-skin-neutral-2 px-4 py-2.5">
      <i class="ri-money-dollar-circle-line text-skin-primary-9"></i>
      <span class="text-sm font-semibold text-skin-neutral-12">{{ priceRange }}</span>
      <span class="text-xs text-skin-neutral-9">price range (active variations)</span>
    </div>

    <!-- Attribute value selectors -->
    <div v-if="localAttributes.length > 0" class="flex flex-wrap gap-4">
      <div v-for="attr in localAttributes" :key="attr.id" class="min-w-48">
        <label class="mb-1 block text-xs font-medium text-skin-neutral-9">{{ attr.name }}</label>
        <div class="flex flex-wrap gap-2">
          <label
            v-for="val in attr.values"
            :key="val.id"
            class="flex cursor-pointer items-center gap-1.5 rounded-md border px-3 py-1.5 text-xs font-medium transition-colors"
            :class="isValueSelected(attr.id, val.id) ? 'border-skin-primary-7 bg-skin-primary-10 text-skin-neutral-1' : 'border-skin-neutral-6 bg-skin-neutral-2 text-skin-neutral-11 hover:border-skin-neutral-7'"
          >
            <input
              type="checkbox"
              :checked="isValueSelected(attr.id, val.id)"
              class="sr-only"
              @change="toggleValue(attr.id, val.id)"
            />
            <span v-if="val.swatch && attr.input_type === 'color'" class="inline-block h-3 w-3 rounded-full" :style="{ backgroundColor: val.swatch }"></span>
            <span v-if="val.swatch && attr.input_type === 'image'" class="inline-block h-4 w-4 rounded bg-cover bg-center" :style="{ backgroundImage: `url(${val.swatch})` }"></span>
            {{ val.value }}
          </label>
        </div>
      </div>
    </div>

    <!-- Generate button -->
    <div v-if="Object.keys(selected).length > 0" class="space-y-2">
      <div v-if="!props.productId" class="rounded-md bg-skin-warning-light px-3 py-2 text-xs text-skin-warning-dark">
        <i class="ri-information-line mr-1"></i>
        Save the product first, then manage variations from the edit page.
      </div>
      <div class="flex items-center gap-3">
        <AppButton class="btn btn-primary btn-sm" :loading="generating" :disabled="!props.productId" @click="generateVariations">
          <i class="ri-refresh-line mr-1"></i>
          Generate Variations
        </AppButton>
        <span v-if="generatedCount > 0" class="text-xs text-skin-neutral-9">{{ generatedCount }} variation(s) generated</span>
        <button
          v-if="localAttributes.length > 0"
          type="button"
          class="text-xs text-skin-primary-9 hover:underline"
          @click="showCreateForm = true"
        >
          + Add another attribute
        </button>
      </div>
    </div>

    <!-- Variations DataTable -->
    <div v-if="variations.length > 0" class="overflow-hidden rounded-lg border border-skin-neutral-4">
      <table class="w-full text-sm">
        <thead>
          <tr class="border-b border-skin-neutral-4 bg-skin-neutral-2 text-left text-xs font-semibold uppercase tracking-wider text-skin-neutral-8">
            <th class="px-3 py-2">#</th>
            <th class="px-3 py-2">Attributes</th>
            <th class="px-3 py-2">SKU</th>
            <th class="px-3 py-2 text-right">Price</th>
            <th class="px-3 py-2 text-right">Sale</th>
            <th class="px-3 py-2 text-center">Qty</th>
            <th class="px-3 py-2 text-center">Active</th>
            <th class="px-3 py-2 text-center">Image</th>
            <th class="px-3 py-2 text-right">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-skin-neutral-3">
          <tr v-for="(v, i) in variations" :key="v.id" class="transition-colors hover:bg-skin-neutral-2">
            <td class="px-3 py-2 text-xs text-skin-neutral-9">{{ i + 1 }}</td>
            <td class="px-3 py-2 text-xs text-skin-neutral-11">{{ variationLabel(v) }}</td>
            <td class="px-3 py-2 text-xs">
              <input
                v-model="v.sku"
                type="text"
                class="w-24 rounded border border-skin-neutral-5 bg-skin-neutral-1 px-2 py-1 text-xs"
                @change="updateVariation(v)"
              />
            </td>
            <td class="px-3 py-2">
              <input
                v-model.number="v.price"
                type="number"
                step="0.01"
                min="0"
                class="w-20 rounded border border-skin-neutral-5 bg-skin-neutral-1 px-2 py-1 text-right text-xs"
                @change="updateVariation(v)"
              />
            </td>
            <td class="px-3 py-2">
              <input
                v-model.number="v.sale_price"
                type="number"
                step="0.01"
                min="0"
                class="w-20 rounded border border-skin-neutral-5 bg-skin-neutral-1 px-2 py-1 text-right text-xs"
                @change="updateVariation(v)"
              />
            </td>
            <td class="px-3 py-2 text-center">
              <input
                v-model.number="v.quantity"
                type="number"
                min="0"
                class="w-16 rounded border border-skin-neutral-5 bg-skin-neutral-1 px-2 py-1 text-center text-xs"
                @change="updateVariation(v)"
              />
            </td>
            <td class="px-3 py-2 text-center">
              <input
                type="checkbox"
                :checked="v.active"
                class="h-4 w-4 rounded border-skin-neutral-6"
                @change="v.active = !v.active; updateVariation(v)"
              />
            </td>
            <td class="px-3 py-2 text-center">
              <div class="flex items-center justify-center gap-2">
                <!-- Upload / Edit Button -->
                <label
                  class="flex h-7 w-7 cursor-pointer items-center justify-center rounded-full bg-skin-neutral-2 text-skin-neutral-10 shadow-xs ring-1 ring-skin-neutral-5 hover:bg-skin-primary-1 hover:text-skin-primary-9 transition-colors"
                  title="Upload / Change Image"
                >
                  <input type="file" accept="image/*" class="sr-only" @change="uploadVariationImage(v, $event)" />
                  <i v-if="v.image_url" class="ri-edit-line text-sm"></i>
                  <i v-else class="ri-image-add-line text-sm"></i>
                </label>
                
                <!-- Preview Button -->
                <a
                  v-if="v.image_url"
                  :href="v.image_url"
                  target="_blank"
                  class="flex h-7 w-7 items-center justify-center rounded-full bg-skin-neutral-2 text-skin-neutral-10 shadow-xs ring-1 ring-skin-neutral-5 hover:bg-skin-info-light hover:text-skin-info-dark transition-colors"
                  title="Preview Image"
                >
                  <i class="ri-eye-line text-sm"></i>
                </a>

                <!-- Remove Button -->
                <button
                  v-if="v.image_url"
                  type="button"
                  class="flex h-7 w-7 items-center justify-center rounded-full bg-skin-neutral-2 text-skin-neutral-10 shadow-xs ring-1 ring-skin-neutral-5 hover:bg-red-50 hover:text-red-600 transition-colors"
                  title="Remove Image"
                  @click="removeVariationImage(v)"
                >
                  <i class="ri-delete-bin-line text-sm"></i>
                </button>
              </div>
            </td>
            <td class="px-3 py-2 text-right">
              <div class="flex items-center justify-end gap-1">
                <button
                  type="button"
                  class="rounded p-1 text-skin-neutral-8 transition-colors hover:bg-red-50 hover:text-red-600"
                  title="Remove variation"
                  @click="removeVariation(v)"
                >
                  <i class="ri-delete-bin-line text-xs"></i>
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed, watch, inject } from 'vue'
import axios from 'axios'

const props = defineProps({
  productId: { type: Number, default: null },
  allAttributes: { type: Array, default: () => [] },
})

// Local copy of attributes — can be refreshed after inline creation
const localAttributes = ref([...props.allAttributes])

const selected = reactive({})
const variations = ref([])
const generating = ref(false)
const generatedCount = ref(0)

// Inline create form state
const showCreateForm = ref(false)
const creatingAttribute = ref(false)
const newAttr = reactive({
  name: '',
  input_type: 'select',
  values: [{ value: '', swatch: '' }],
})

const resetCreateForm = () => {
  showCreateForm.value = false
  newAttr.name = ''
  newAttr.input_type = 'select'
  newAttr.values = [{ value: '', swatch: '' }]
}

const createAttribute = async () => {
  if (!newAttr.name.trim()) return
  creatingAttribute.value = true
  try {
    const response = await axios.post(route('productAttribute.storeAjax'), {
      name: newAttr.name,
      input_type: newAttr.input_type,
      values: newAttr.values.filter((v) => v.value.trim() !== ''),
    })
    // Add the new attribute to local list
    localAttributes.value.push(response.data.data)
    // Reset form
    resetCreateForm()
  } catch {
    // silently fail
  } finally {
    creatingAttribute.value = false
  }
}

const refreshAttributes = async () => {
  try {
    const response = await axios.get(route('productAttribute.indexAjax'))
    localAttributes.value = response.data.data || []
  } catch {
    // silently fail
  }
}

const isValueSelected = (attrId, valId) => {
  return selected[attrId]?.includes(valId)
}

const toggleValue = (attrId, valId) => {
  if (!selected[attrId]) {
    selected[attrId] = []
  }
  const idx = selected[attrId].indexOf(valId)
  if (idx === -1) {
    selected[attrId].push(valId)
  } else {
    selected[attrId].splice(idx, 1)
  }
}

const groupForGenerate = computed(() => {
  const result = {}
  for (const [attrId, values] of Object.entries(selected)) {
    if (values.length > 0) {
      result[attrId] = values
    }
  }
  return result
})

const generateVariations = async () => {
  if (!props.productId) return
  generating.value = true
  try {
    const response = await axios.post(
      route('product.variations.generate', { product: props.productId }),
      { attribute_value_ids: groupForGenerate.value }
    )
    variations.value = response.data.variations || []
    generatedCount.value = variations.value.length
  } catch {
    // silently fail
  } finally {
    generating.value = false
  }
}

const updateVariation = async (v) => {
  const formData = new FormData()
  formData.append('_method', 'PUT')
  formData.append('price', v.price)
  formData.append('sale_price', v.sale_price ?? '')
  formData.append('quantity', v.quantity)
  formData.append('sku', v.sku ?? '')
  formData.append('active', v.active ? '1' : '0')

  try {
    const response = await axios.post(
      route('product.variations.update', { product: props.productId, variation: v.id }),
      formData
    )
    if (response.data.variation) {
      Object.assign(v, response.data.variation)
    }
  } catch {
    // silently fail
  }
}

const uploadVariationImage = async (v, event) => {
  const file = event.target?.files?.[0]
  if (!file || !props.productId) return

  const formData = new FormData()
  formData.append('_method', 'PUT')
  formData.append('price', v.price)
  formData.append('sale_price', v.sale_price ?? '')
  formData.append('quantity', v.quantity)
  formData.append('sku', v.sku ?? '')
  formData.append('active', v.active ? '1' : '0')
  formData.append('image', file)

  try {
    const response = await axios.post(
      route('product.variations.update', { product: props.productId, variation: v.id }),
      formData
    )
    if (response.data.variation) {
      Object.assign(v, response.data.variation)
    }
  } catch {
    // silently fail
  }
}

const removeVariationImage = async (v) => {
  const formData = new FormData()
  formData.append('_method', 'PUT')
  formData.append('price', v.price)
  formData.append('sale_price', v.sale_price ?? '')
  formData.append('quantity', v.quantity)
  formData.append('sku', v.sku ?? '')
  formData.append('active', v.active ? '1' : '0')
  formData.append('remove_image', '1')

  try {
    const response = await axios.post(
      route('product.variations.update', { product: props.productId, variation: v.id }),
      formData
    )
    if (response.data.variation) {
      Object.assign(v, response.data.variation)
    }
  } catch {
    // silently fail
  }
}

const confirmDelete = inject('confirmDelete', null)

const removeVariation = (v) => {
  const doDelete = async () => {
    try {
      await axios.delete(
        route('product.variations.destroy', { product: props.productId, variation: v.id })
      )
      variations.value = variations.value.filter((item) => item.id !== v.id)
    } catch {
      // silently fail
    }
  }

  if (confirmDelete) {
    confirmDelete(doDelete)
  } else {
    if (!confirm('Remove this variation? This cannot be undone.')) return
    doDelete()
  }
}

const priceRange = computed(() => {
  const active = variations.value.filter((v) => v.active && v.price > 0)
  if (active.length === 0) return null
  const prices = active.map((v) => v.sale_price || v.price)
  const min = Math.min(...prices)
  const max = Math.max(...prices)
  if (min === max) return `৳${min.toFixed(2)}`
  return `৳${min.toFixed(2)} – ৳${max.toFixed(2)}`
})

const variationLabel = (v) => {
  if (!v.attribute_value_ids?.length) return '—'
  const names = v.attribute_value_ids.map((vid) => {
    for (const attr of localAttributes.value) {
      const found = attr.values.find((val) => val.id === vid)
      if (found) return found.value
    }
    return `#${vid}`
  })
  return names.join(' / ')
}

const loadExisting = async () => {
  if (!props.productId) return
  try {
    const response = await axios.get(
      route('product.variations.attributes', { product: props.productId })
    )
    variations.value = response.data.variations || []
  } catch {
    // silently fail
  }
}

watch(
  () => props.productId,
  (id) => { if (id) loadExisting() },
  { immediate: true }
)

// Sync local attributes when prop changes (e.g. after Inertia redirect)
watch(
  () => props.allAttributes,
  (newAttrs) => { localAttributes.value = [...newAttrs] },
)
</script>
