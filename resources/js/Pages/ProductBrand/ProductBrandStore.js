import { defineStore } from 'pinia'
import slug from '@resources/js/Utils/slug.js'

export const useProductBrandStore = defineStore('ProductBrandStore', {
    state: () => {
        return {
            brand: {
                name: '',
                description: '',
                image: null,

                active: true,
                featured: false,

                meta_tag_title: '',
                meta_tag_description: '',

                remove_previous_image: false
            }
        }
    },

    actions: {
        setBrand(brand) {
            this.brand = brand
        },
        initSeoTags() {
            this.brand.meta_tag_title = this.brand.name.substring(0, 60)
            this.brand.meta_tag_description = this.brand.description.replace(
                /<\/?[^>]+(>|$)/g,
                ''
            )
        }
    },

    getters: {
        getRemainingChars: (state) => {
            return (key, max) => {
                if (!state.brand[key]) return max

                return max - state.brand[key].length
            }
        },

        showSeoAlert: (state) => {
            return () => {
                if (
                    state.brand.meta_tag_title &&
                    state.brand.meta_tag_title.length
                ) {
                    return false
                }

                if (
                    state.brand.name.length > 1 &&
                    state.brand.description.length > 2
                ) {
                    return false
                }

                return true
            }
        },

        getSlug: (state) => {
            return () => {
                if (!state.brand.name) return ''

                return slug(state.brand.name)
            }
        }
    }
})
