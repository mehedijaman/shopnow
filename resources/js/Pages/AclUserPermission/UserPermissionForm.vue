<template>
    <Head :title="title"></Head>

    <AppSectionHeader :title="title" :bread-crumb="breadCrumb">
        <template #right>
            <div class="flex shrink-0 items-center gap-3">
                <span class="hidden text-sm text-skin-neutral-11 sm:inline">
                    {{ __('{count} selected', { count: selectedCount }) }}
                </span>
                <AppButton
                    class="btn btn-neutral-outline"
                    @click="toggleSelectAll"
                >
                    <i
                        :class="
                            allPermissionsSelected
                                ? 'ri-checkbox-indeterminate-line'
                                : 'ri-checkbox-multiple-line'
                        "
                        class="mr-1"
                    ></i>
                    {{
                        allPermissionsSelected
                            ? __('Deselect all')
                            : __('Select all')
                    }}
                </AppButton>
            </div>
        </template>
    </AppSectionHeader>

    <AppCard>
        <template #content>
            <AppFormErrors class="mb-4" />

            <div class="mb-4 flex flex-col gap-2 sm:flex-row sm:items-center sm:gap-3">
                <div class="relative w-full sm:flex-1">
                    <i
                        class="ri-search-line pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-skin-neutral-10"
                    ></i>
                    <input
                        v-model="search"
                        type="search"
                        :placeholder="__('Search permissions...')"
                        class="block w-full min-w-0 rounded-md border-0 bg-skin-neutral-1 py-2 pl-10 pr-3 text-skin-neutral-12 placeholder-skin-neutral-9 shadow-xs ring-1 ring-inset ring-skin-neutral-7 focus:ring-2 focus:ring-inset focus:ring-skin-neutral-7 sm:text-sm sm:leading-6"
                    />
                </div>
                <AppButton
                    v-if="search"
                    class="btn btn-neutral-outline w-full sm:w-auto"
                    @click="search = ''"
                >
                    {{ __('Clear Search') }}
                </AppButton>
            </div>

            <p
                v-if="search && visiblePermissionCount < totalPermissionCount"
                class="mb-4 text-sm text-skin-neutral-10"
            >
                {{
                    __(
                        'Showing {visible} of {total} permissions',
                        { visible: visiblePermissionCount, total: totalPermissionCount }
                    )
                }}
            </p>

            <template v-if="visibleGroupedPermissions.length">
                <div
                    v-for="group in visibleGroupedPermissions"
                    :key="group.key"
                    class="mb-4 overflow-hidden rounded-md border border-skin-neutral-6 bg-skin-neutral-1"
                >
                    <div
                        class="flex flex-col gap-2 border-b border-skin-neutral-6 bg-skin-neutral-3 px-4 py-2 sm:flex-row sm:items-center sm:justify-between"
                    >
                        <div class="flex min-w-0 items-center gap-2">
                            <h4
                                class="truncate text-base font-semibold tracking-tight text-skin-neutral-12"
                            >
                                {{ group.label }}
                            </h4>
                            <span
                                class="shrink-0 rounded-full bg-skin-neutral-5 px-2 py-0.5 text-xs text-skin-neutral-11"
                            >
                                {{ group.permissions.length }}
                            </span>
                        </div>
                        <AppButton
                            class="btn btn-neutral-outline w-full !py-1 !text-xs sm:w-auto"
                            @click="toggleGroup(group)"
                        >
                            {{
                                isGroupFullySelected(group)
                                    ? __('Deselect all')
                                    : __('Select all')
                            }}
                        </AppButton>
                    </div>

                    <div class="grid grid-cols-1 gap-2 p-3 sm:p-4 md:grid-cols-2 lg:grid-cols-3">
                        <label
                            v-for="permission in group.permissions"
                            :key="permission.id"
                            class="flex min-w-0 cursor-pointer items-center gap-2 rounded-md px-2 py-1.5 hover:bg-skin-neutral-3"
                        >
                            <AppCheckbox
                                :key="
                                    permission.id +
                                    '-' +
                                    (isSelected(permission.id) ? 'on' : 'off')
                                "
                                v-model="form.userPermissions"
                                :value="permission"
                            />
                            <span
                                class="min-w-0 flex-1 select-none break-all text-sm text-skin-neutral-12"
                            >
                                {{ permission.name }}
                            </span>
                        </label>
                    </div>
                </div>
            </template>

            <AppAlert v-else class="mt-4">
                {{ __('No permissions match your search.') }}
            </AppAlert>
        </template>

        <template v-if="visibleGroupedPermissions.length" #footer>
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <span class="text-sm text-skin-neutral-10">
                    {{
                        __(
                            'Showing {visible} of {total} permissions',
                            { visible: visiblePermissionCount, total: totalPermissionCount }
                        )
                    }}
                </span>
                <AppButton class="btn btn-primary w-full sm:w-auto" @click="submitForm">
                    {{ __('Save') }}
                </AppButton>
            </div>
        </template>
    </AppCard>
</template>

<script setup>
import { computed, ref } from 'vue'
import { Head, useForm } from '@inertiajs/vue3'
import useTitle from '@/Composables/useTitle'
import useFormErrors from '@/Composables/useFormErrors'

const { title } = useTitle('User Permissions')

const props = defineProps({
    user: {
        type: Object,
        required: true
    },
    userPermissions: {
        type: Array,
        required: true
    },
    permissions: {
        type: Array,
        required: true
    }
})

const { errorsFields } = useFormErrors()

const breadCrumb = [
    { label: 'Home', href: route('dashboard.index') },
    { label: 'Users', href: route('user.index') },
    { label: 'User Permissions', last: true }
]

const form = useForm({
    userPermissions: props.userPermissions
})

/**
 * Live search term. Filtered by simple case-insensitive substring
 * match against the permission name. Whitespace-trimmed so the user
 * can't accidentally filter everything out with a stray space.
 */
const search = ref('')

const trimmedSearch = computed(() => search.value.trim().toLowerCase())

/**
 * Total permissions on the user (before any search filtering). Used
 * by the "X of Y selected" header and the visible/total summary.
 */
const selectedCount = computed(() => form.userPermissions.length)
const totalPermissionCount = computed(() => props.permissions.length)

const allPermissionsSelected = computed(
    () =>
        props.permissions.length > 0 &&
        form.userPermissions.length === props.permissions.length
)

function isSelected(permissionId) {
    return form.userPermissions.some((p) => p.id === permissionId)
}

/**
 * Group permissions by their module prefix. Convention: every
 * permission name is `<module>[-<sub>-]<action>` in kebab-case
 * (e.g. `acl-role-list`, `website-blog-post-create`, `customer`, `booking`).
 * Splitting on the first `-` gives the module prefix; bare names
 * (no hyphen) become a group with that name.
 *
 * Groups are sorted alphabetically by their key for a stable render
 * order across renders.
 */
const groupedPermissions = computed(() => {
    const groups = new Map()

    for (const permission of props.permissions) {
        const key = permission.name.includes('-')
            ? permission.name.split('-')[0]
            : permission.name
        const label = key
            .split('-')
            .map((segment) => segment.charAt(0).toUpperCase() + segment.slice(1))
            .join(' ')

        if (!groups.has(key)) {
            groups.set(key, { key, label, permissions: [] })
        }
        groups.get(key).permissions.push(permission)
    }

    return Array.from(groups.values()).sort((a, b) =>
        a.key.localeCompare(b.key)
    )
})

/**
 * Apply the search filter to the grouped permissions. When the
 * search is empty, every group is shown in full. When the search
 * matches a group label (e.g. typing "blog" shows the whole blog
 * group), the whole group is kept. Otherwise, only matching
 * permissions inside each group are kept.
 */
const visibleGroupedPermissions = computed(() => {
    const term = trimmedSearch.value

    if (!term) {
        return groupedPermissions.value
    }

    return groupedPermissions.value
        .map((group) => {
            const labelMatches = group.label.toLowerCase().includes(term)
            if (labelMatches) {
                return group
            }
            const filtered = group.permissions.filter((p) =>
                p.name.toLowerCase().includes(term)
            )
            return filtered.length ? { ...group, permissions: filtered } : null
        })
        .filter(Boolean)
})

const visiblePermissionCount = computed(() =>
    visibleGroupedPermissions.value.reduce(
        (sum, g) => sum + g.permissions.length,
        0
    )
)

function isGroupFullySelected(group) {
    return group.permissions.every((p) => isSelected(p.id))
}

function toggleGroup(group) {
    const fullySelected = isGroupFullySelected(group)
    const groupIds = new Set(group.permissions.map((p) => p.id))

    if (fullySelected) {
        // Remove every permission in this group.
        form.userPermissions = form.userPermissions.filter(
            (p) => !groupIds.has(p.id)
        )
    } else {
        // Add the missing ones; keep the existing ones (so we don't
        // create duplicates if some are already selected).
        const existing = new Set(form.userPermissions.map((p) => p.id))
        const toAdd = group.permissions.filter((p) => !existing.has(p.id))
        form.userPermissions = [...form.userPermissions, ...toAdd]
    }
}

function toggleSelectAll() {
    if (allPermissionsSelected.value) {
        form.userPermissions = []
    } else {
        // Preserve order of the source list (already sorted server-
        // side by `Permission::orderBy('name')`).
        form.userPermissions = [...props.permissions]
    }
}

const submitForm = () => {
    form.put(route('aclUserPermission.update', props.user.id))
}
</script>