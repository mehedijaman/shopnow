<template>
    <Head :title="title"></Head>
    <AppSectionHeader :title="title" :bread-crumb="breadCrumb">
        <template #right>
            <AppButton
                class="btn btn-primary"
                @click="router.visit(route('profile.create'))"
            >
                <i class="ri-add-fill mr-1"></i>
                Create Profile
            </AppButton>
        </template>
    </AppSectionHeader>

    <AppDataSearch
        v-if="profiles.data.length || route().params.searchTerm"
        :url="route('profile.index')"
        fields-to-search="id"
    ></AppDataSearch>

    <AppDataTable v-if="profiles.data.length" :headers="headers">
        <template #TableBody>
            <tbody>
                <AppDataTableRow
                    v-for="(item, index) in profiles.data"
                    :key="item.id"
                >
                    <AppDataTableData>
                        {{ item.id }}
                    </AppDataTableData>

                    <!-- <AppDataTableData>
                        {{ item.name }}
                    </AppDataTableData> -->

                    <AppDataTableData>
                        <!-- Edit profile -->
                        <AppTooltip text="Edit Profile" class="mr-2">
                            <AppButton
                                class="btn btn-icon btn-primary"
                                @click="
                                    router.visit(
                                        route(
                                            'profile.edit',
                                            item.id
                                        )
                                    )
                                "
                            >
                                <i class="ri-edit-line"></i>
                            </AppButton>
                        </AppTooltip>

                        <!-- Delete profile -->
                        <AppTooltip text="Delete Profile">
                            <AppButton
                                class="btn btn-icon btn-destructive"
                                @click="
                                    confirmDelete(
                                        route(
                                            'profile.destroy',
                                            item.id
                                        )
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
        v-if="profiles.data.length"
        :links="profiles.links"
        :from="profiles.from || 0"
        :to="profiles.to || 0"
        :total="profiles.total || 0"
        class="mt-4 justify-center"
    ></AppPaginator>

    <AppAlert v-if="!profiles.data.length" class="mt-4">
        No profiles found.
    </AppAlert>

    <AppConfirmDialog ref="confirmDialogRef"></AppConfirmDialog>
</template>

<script setup>
import { ref } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import useTitle from '@/Composables/useTitle'
import useAuthCan from '@/Composables/useAuthCan'

const { title } = useTitle('Profile')
const { can } = useAuthCan()

const props = defineProps({
  profiles: {
    type: Object,
    default: () => {}
  }
})

const breadCrumb = [
  { label: 'Home', href: route('dashboard.index') },
  { label: 'Profiles', last: true }
]

const headers = ['ID', 'Actions']

const confirmDialogRef = ref(null)
const confirmDelete = (deleteRoute) => {
    confirmDialogRef.value.openModal(deleteRoute)
}
</script>
