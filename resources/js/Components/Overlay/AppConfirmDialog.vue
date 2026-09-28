<template>
    <AppModal
        :is-modal-open="isModalOpen"
        size="md"
        @modal:toggle="toggleModal"
    >
        <template #header>
            <div class="flex items-center justify-between border-b px-6 py-4">
                <h3
                    :class="[
                        'text-lg font-semibold',
                        type === 'danger' ? 'text-red-600' : 'text-green-600'
                    ]"
                >
                    {{ __(modalTitle) }}
                </h3>
                <button
                    class="text-neutral-500 hover:text-neutral-700 dark:text-neutral-400 dark:hover:text-neutral-200"
                    @click="closeModal"
                >
                    <i class="ri-close-line text-xl"></i>
                </button>
            </div>
        </template>

        <!-- Modal body -->
        <template #body>
            <div class="py-4">
                <p class="text-neutral-700 dark:text-neutral-300">
                    {{ __(modalMessage) }}
                </p>

                <div
                    v-if="type === 'primary'"
                    class="mt-4 rounded-lg bg-green-50 p-4 dark:bg-green-900/20"
                >
                    <div class="flex items-start gap-3">
                        <i :class="[iconClass, 'text-xl text-green-600']"></i>
                        <div class="text-sm text-green-900 dark:text-green-100">
                            {{ __(helpText || __('common.restore_help')) }}
                        </div>
                    </div>
                </div>

                <div
                    v-if="type === 'danger'"
                    class="mt-4 rounded-lg bg-red-50 p-4 dark:bg-red-900/20"
                >
                    <div class="flex items-start gap-3">
                        <i :class="[iconClass, 'text-xl text-red-600']"></i>
                        <div class="text-sm text-red-900 dark:text-red-100">
                            {{ __(helpText || __('common.delete_help')) }}
                        </div>
                    </div>
                </div>
            </div>
        </template>

        <!-- Modal footer -->
        <template #footer>
            <div class="flex justify-end gap-2 border-t px-6 py-4">
                <AppButton class="btn btn-neutral" @click="closeModal">
                    {{ __('common.cancel') }}
                </AppButton>

                <AppButton
                    :class="[confirmButtonClass]"
                    @click="confirmAction()"
                >
                    <i
                        v-if="type === 'primary'"
                        class="ri-recycle-line mr-1"
                    ></i>
                    <i v-else class="ri-delete-bin-line mr-1"></i>
                    {{ __(confirmText) }}
                </AppButton>
            </div>
        </template>
    </AppModal>
</template>

<script setup>
import { ref, computed } from 'vue'
import { router } from '@inertiajs/vue3'

const isModalOpen = ref(false)
const actionTargetRoute = ref(null)
const modalTitle = ref('common.confirmation')
const modalMessage = ref('common.confirm_message')
const confirmButtonClass = ref('btn btn-destructive')
const confirmText = ref('common.confirm')
const confirmCallback = ref(null)
const actionMethod = ref('delete')
const type = ref('danger') // danger, primary
const helpText = ref(null)

const iconClass = computed(() => {
    if (type.value === 'danger') return 'ri-error-warning-line'
    if (type.value === 'primary') return 'ri-information-line'
    return 'ri-question-line'
})

const toggleModal = () => {
    isModalOpen.value = !isModalOpen.value
}

/**
 * Standard Delete Confirmation
 */
const openModal = (
    deleteRoute,
    title = 'common.delete_confirmation',
    message = 'common.delete_message'
) => {
    actionTargetRoute.value = deleteRoute
    modalTitle.value = title
    modalMessage.value = message
    confirmButtonClass.value = 'btn btn-destructive'
    confirmText.value = 'common.delete'
    actionMethod.value = 'delete'
    type.value = 'danger'
    helpText.value = null
    confirmCallback.value = null
    isModalOpen.value = true
}

/**
 * Restore Confirmation
 */
const confirmRestore = (
    restoreRoute,
    title = 'common.restore_confirmation',
    message = 'common.restore_message'
) => {
    actionTargetRoute.value = restoreRoute
    modalTitle.value = title
    modalMessage.value = message
    confirmButtonClass.value = 'btn btn-primary'
    confirmText.value = 'common.restore'
    actionMethod.value = 'post'
    type.value = 'primary'
    helpText.value = null
    confirmCallback.value = null
    isModalOpen.value = true
}

const openCustomModal = (options) => {
    const {
        route = null,
        title = 'common.confirmation',
        message = 'common.confirm_message',
        buttonClass = 'btn btn-destructive',
        buttonText = 'common.confirm',
        method = 'delete',
        onConfirm = null,
        modalType = 'danger',
        helpText: customHelpText = null
    } = options || {}

    actionTargetRoute.value = route
    modalTitle.value = title
    modalMessage.value = message
    confirmButtonClass.value = buttonClass
    confirmText.value = buttonText
    actionMethod.value = method
    type.value = modalType
    helpText.value = customHelpText
    confirmCallback.value = onConfirm
    isModalOpen.value = true
}

const closeModal = () => {
    isModalOpen.value = false
}

const confirmAction = () => {
    isModalOpen.value = false

    // If callback is provided, execute it
    if (confirmCallback.value) {
        confirmCallback.value()
        return
    }

    // Default action
    if (actionTargetRoute.value) {
        router.visit(actionTargetRoute.value, {
            method: actionMethod.value
        })
    }
}

defineExpose({
    openModal,
    confirmRestore,
    openCustomModal
})
</script>
