<script setup>
import { computed, inject, ref } from 'vue'
import { Head, Link, router, usePage } from '@inertiajs/vue3'

const props = defineProps({
    messages: { type: Object, required: true },
    providers: { type: Object, required: true },
    canEditSettings: { type: Boolean, default: false }
})
const t = inject('translate')
const page = usePage()
const refreshing = ref(false)
const title = computed(() => t('settings::sms.history'))
const breadCrumb = computed(() => [
    { label: t('common.home'), href: route('dashboard.index') },
    ...(props.canEditSettings
        ? [
              {
                  label: t('settings::admin.settings'),
                  href: route('settings.show', { group: 'sms' })
              }
          ]
        : []),
    { label: title.value, last: true }
])
const headers = computed(() =>
    [
        'created_at',
        'recipient',
        'purpose',
        'status',
        'provider',
        'order',
        'attempts',
        'error'
    ].map((key) => t(`settings::sms.${key}`))
)

function refresh() {
    if (refreshing.value) return
    router.reload({
        only: ['messages'],
        onStart: () => {
            refreshing.value = true
        },
        onFinish: () => {
            refreshing.value = false
        }
    })
}

function formatDate(value) {
    return new Date(value).toLocaleString(page.props.locale ?? 'en')
}
</script>

<template>
    <div>
        <Head :title="title" />
        <AppSectionHeader :title="title" :bread-crumb="breadCrumb">
            <template #right>
                <div class="flex flex-wrap gap-2">
                    <Link
                        v-if="canEditSettings"
                        :href="route('settings.show', { group: 'sms' })"
                        class="btn btn-secondary"
                    >
                        <i
                            class="ri-settings-2-line mr-1"
                            aria-hidden="true"
                        ></i>
                        {{ t('settings::sms.back_to_settings') }}
                    </Link>
                    <AppButton
                        type="button"
                        class="btn btn-primary"
                        :loading="refreshing"
                        :disabled="refreshing"
                        @click="refresh"
                    >
                        <i class="ri-refresh-line mr-1" aria-hidden="true"></i>
                        {{ t('settings::sms.refresh') }}
                    </AppButton>
                </div>
            </template>
        </AppSectionHeader>

        <div
            class="mt-6 rounded-lg bg-skin-neutral-1 p-4 shadow-sm ring-1 ring-skin-neutral-4 sm:p-6"
        >
            <p class="mb-5 text-sm text-skin-neutral-9">
                {{ t('settings::sms.history_description') }}
            </p>
            <AppDataTable v-if="messages.data.length" :headers="headers">
                <template #TableBody>
                    <tbody>
                        <AppDataTableRow
                            v-for="message in messages.data"
                            :key="message.id"
                        >
                            <AppDataTableData class="whitespace-nowrap text-sm">
                                <time :datetime="message.created_at">{{
                                    formatDate(message.created_at)
                                }}</time>
                            </AppDataTableData>
                            <AppDataTableData class="font-mono text-sm">{{
                                message.recipient
                            }}</AppDataTableData>
                            <AppDataTableData
                                class="whitespace-nowrap text-sm"
                                >{{
                                    t(`settings::sms.${message.purpose}_label`)
                                }}</AppDataTableData
                            >
                            <AppDataTableData
                                class="whitespace-nowrap text-sm"
                                >{{
                                    t(`settings::sms.${message.status}`)
                                }}</AppDataTableData
                            >
                            <AppDataTableData
                                class="whitespace-nowrap text-sm"
                                >{{
                                    providers[message.provider] ??
                                    message.provider ??
                                    '—'
                                }}</AppDataTableData
                            >
                            <AppDataTableData class="text-sm">{{
                                message.order_id ? `#${message.order_id}` : '—'
                            }}</AppDataTableData>
                            <AppDataTableData class="text-sm">{{
                                message.attempts
                            }}</AppDataTableData>
                            <AppDataTableData class="min-w-48 text-sm">{{
                                message.error
                                    ? t(`settings::sms.${message.error}`)
                                    : '—'
                            }}</AppDataTableData>
                        </AppDataTableRow>
                    </tbody>
                </template>
            </AppDataTable>
            <AppPaginator
                v-if="messages.data.length"
                :links="messages.links"
                :from="messages.from || 0"
                :to="messages.to || 0"
                :total="messages.total || 0"
                class="mt-5 justify-center"
            />
            <AppAlert v-else>{{ t('settings::sms.empty') }}</AppAlert>
        </div>
    </div>
</template>
