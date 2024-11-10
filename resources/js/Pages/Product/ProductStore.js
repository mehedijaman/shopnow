import { defineStore } from 'pinia'
import slug from '@resources/js/Utils/slug.js'

export const useProductStore = defineStore('ProductStore', {
    state: () => {
        return {
            product: {
                category_id: null,
                // blog_author_id: null,

                tags: [],

                name: '',
                price: '',
                sale_price: '',
                quantity: '',
                unit: '',
                min_order: '',
                active: true,
                featured: false,
                image: null,

                summary: '',
                description: '',
                meta_tag_title: '',
                meta_tag_description: '',

                remove_previous_image: false,

                tagsHasChanged: false
            }
        }
    },

    actions: {
        setProduct(product) {
            this.product = product
        },
        initSeoTags() {
            this.product.meta_tag_title = this.product.name.substring(0, 60)
            this.product.meta_tag_description =
                this.product.description.replace(/<\/?[^>]+(>|$)/g, '')
        }
    },

    getters: {
        getRemainingChars: (state) => {
            return (key, max) => {
                if (!state.product[key]) return max

                return max - state.product[key].length
            }
        },

        showSeoAlert: (state) => {
            return () => {
                if (
                    state.product.meta_tag_title &&
                    state.product.meta_tag_title.length
                ) {
                    return false
                }

                if (
                    state.product.title.length > 1 &&
                    state.product.content.length > 2
                ) {
                    return false
                }

                return true
            }
        },

        getSlug: (state) => {
            return () => {
                if (!state.product.name) return ''

                return slug(state.product.name)
            }
        }
    }
})
