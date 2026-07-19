<template>
  <div class="space-y-3">
    <div v-if="!isNew && sortedFiles.length" class="divide-y divide-skin-neutral-3">
      <draggable
        v-model="sortedFiles"
        tag="div"
        handle=".drag-handle"
        item-key="id"
        :on-end="onReorder"
      >
        <template #item="{ element: file, index }">
          <div class="flex items-center gap-3 py-2 first:pt-0 last:pb-0">
            <i class="ri-draggable drag-handle shrink-0 cursor-grab text-skin-neutral-8"></i>
            <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-md bg-skin-primary-10 text-sm font-bold text-skin-neutral-1">
              {{ index + 1 }}
            </span>
            <div class="min-w-0 flex-1">
              <input
                v-model="file.name"
                type="text"
                class="block w-full border-0 bg-transparent px-0 py-0.5 text-sm font-medium text-skin-neutral-12 focus:outline-none focus:ring-0"
                @blur="updateFileName(file)"
              />
              <p class="truncate text-xs text-skin-neutral-9">
                {{ file.file_name ?? '—' }}
                <span v-if="file.file_size" class="ml-1">({{ file.file_size }})</span>
              </p>
            </div>
            <button
              type="button"
              class="shrink-0 rounded-md p-1.5 text-skin-neutral-8 transition-colors hover:bg-red-50 hover:text-red-600"
              title="Remove file"
              @click="removeFile(file.id)"
            >
              <i class="ri-delete-bin-line text-base"></i>
            </button>
          </div>
        </template>
      </draggable>
    </div>

    <div v-if="isNew" class="rounded-md border-2 border-dashed border-skin-neutral-5 p-4 text-center text-sm text-skin-neutral-9">
      Save the product first to add downloadable files.
    </div>

    <div v-else class="rounded-md border-2 border-dashed border-skin-neutral-5 p-4">
      <form @submit.prevent="uploadFile" class="space-y-3">
        <div>
          <label class="mb-1 block text-xs font-medium text-skin-neutral-9">Display Name</label>
          <input
            ref="fileInput"
            v-model="newFileName"
            type="text"
            placeholder="e.g. User Manual PDF"
            class="block w-full rounded-md border-0 bg-skin-neutral-1 px-3 py-2 text-sm text-skin-neutral-12 placeholder-skin-neutral-9 shadow-xs ring-1 ring-inset ring-skin-neutral-7 focus:ring-2 focus:ring-inset focus:ring-skin-primary-7"
            required
          />
        </div>
        <div>
          <label class="mb-1 block text-xs font-medium text-skin-neutral-9">File</label>
          <input
            ref="fileUploadInput"
            type="file"
            class="block w-full text-sm text-skin-neutral-9 file:mr-3 file:cursor-pointer file:rounded-md file:border-0 file:bg-skin-primary-10 file:px-3 file:py-1.5 file:text-xs file:font-semibold file:text-skin-neutral-1 hover:file:bg-skin-primary-9"
            required
          />
        </div>
        <p class="text-xs text-skin-neutral-9">Accepted: PDF, ZIP, DOC, DOCX, XLS, XLSX, MP3, MP4, JPG, PNG (max 50MB)</p>
        <AppButton type="submit" class="btn btn-primary btn-sm" :loading="uploading">
          <i class="ri-upload-cloud-line mr-1"></i>
          Upload
        </AppButton>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useForm } from '@inertiajs/vue3'
import axios from 'axios'
import draggable from 'vuedraggable'

const props = defineProps({
  productFiles: { type: Array, default: () => [] },
  productId: { type: Number, default: null },
  isNew: { type: Boolean, default: false },
})

const fileUploadInput = ref(null)
const uploading = ref(false)
const newFileName = ref('')

const sortedFiles = ref([...props.productFiles])

const uploadFile = () => {
  const file = fileUploadInput.value?.files?.[0]
  if (!file || !newFileName.value) return

  uploading.value = true
  const form = useForm({
    name: newFileName.value,
    file: file,
  })

  form.post(route('product.downloads.store', { product: props.productId }), {
    preserveScroll: true,
    onFinish: () => {
      uploading.value = false
      newFileName.value = ''
      if (fileUploadInput.value) fileUploadInput.value.value = ''
    },
  })
}

const removeFile = (fileId) => {
  if (!confirm('Remove this downloadable file?')) return

  const form = useForm({})
  form.delete(route('product.downloads.destroy', { product: props.productId, file: fileId }), {
    preserveScroll: true,
  })
}

const updateFileName = async (file) => {
  if (!props.productId) return
  try {
    await axios.put(route('product.downloads.store', { product: props.productId }), {
      name: file.name,
    })
  } catch {
    // silently fail
  }
}

const onReorder = async () => {
  if (!props.productId) return
  try {
    await axios.post(route('product.downloads.store', { product: props.productId }), {
      sort_order: sortedFiles.value.map((f) => f.id),
    })
  } catch {
    // silently fail
  }
}
</script>
