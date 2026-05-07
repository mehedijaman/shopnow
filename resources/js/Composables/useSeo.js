/**
 * useSeo composable — sets the document title for Inertia admin pages.
 *
 * Inertia already handles the <title> tag via the Head component when
 * titles are passed as Inertia props. This composable is a lightweight
 * helper that reads the shared page title and generates the browser-tab
 * title in a consistent format.
 *
 * Usage:
 *   import useSeo from '@/Composables/useSeo'
 *   const { pageTitle } = useSeo({ title: 'Dashboard' })
 */

import { computed } from 'vue'
import { usePage } from '@inertiajs/vue3'

/**
 * @param {{ title?: string, noSuffix?: boolean }} options
 */
export default function useSeo(options = {}) {
    const page = usePage()

    const siteName = computed(() =>
        page.props?.appName ?? document.title.split('—').pop()?.trim() ?? 'Admin',
    )

    const pageTitle = computed(() => {
        if (!options.title) {
            return siteName.value
        }
        return options.noSuffix
            ? options.title
            : `${options.title} — ${siteName.value}`
    })

    return { pageTitle }
}
