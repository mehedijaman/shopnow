import { defineStore } from 'pinia'

export const useProductStore = defineStore('ProductStore', {
    state: () => {
        return {
            product: {
                category_id: null,
                brand_id: null,
                tags: [],

                name: '',
                price: '',
                sale_price: '',
                quantity: '',
                unit: '',
                min_order: '',
                active: true,
                featured: false,
                type: 'simple',
                is_virtual: false,
                is_downloadable: false,
                image: null,

                gallery_images: [],

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
            // Normalize category_id and brand_id to combobox objects so
            // ProductCategory / ProductBrand bind correctly and
            // getValueFromKey() extracts the id on submit.
            const normalizeOption = (value) => {
                if (value && typeof value === 'object' && 'value' in value) return value
                if (value != null) return { value: Number(value), label: '' }
                return null
            }

            this.product = {
                ...product,
                category_id: normalizeOption(product.category_id),
                brand_id: normalizeOption(product.brand_id),
                type: String(product.type ?? 'simple'),
                price: Number(product.price) || '',
                sale_price: product.sale_price ? Number(product.sale_price) : '',
                quantity: product.quantity != null ? Number(product.quantity) : '',
                min_order: product.min_order ? Number(product.min_order) : '',
                image: null,
                gallery_images: [],
            }
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
                    state.product.name.length > 1 &&
                    state.product.description.length > 2
                ) {
                    return false
                }

                return true
            }
        }
    }
})
