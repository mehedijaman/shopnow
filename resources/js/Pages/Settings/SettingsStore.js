import { defineStore } from 'pinia'

export const useSettingsStore = defineStore('settings', {
    state: () => ({
        /** @type {Record<string, any>} */
        settings: {},
        group: '',
    }),

    actions: {
        /**
         * Hydrate the store with the current group and its settings.
         *
         * @param {string} group
         * @param {Record<string, any>} settings
         */
        setSettings(group, settings) {
            this.group = group
            this.settings = { ...settings }
        },

        /**
         * Add an empty entry to a repeater field array.
         *
         * @param {string} field
         */
        addRepeaterItem(field) {
            if (!Array.isArray(this.settings[field])) {
                this.settings[field] = []
            }
            this.settings[field] = [...this.settings[field], '']
        },

        /**
         * Remove an entry from a repeater field array by index.
         *
         * @param {string} field
         * @param {number} index
         */
        removeRepeaterItem(field, index) {
            this.settings[field] = this.settings[field].filter((_, i) => i !== index)
        },
    },
})
