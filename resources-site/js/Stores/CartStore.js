import { defineStore } from 'pinia'
import axios from 'axios'
import { pushRemoveFromCart } from '../analytics/datalayer'

export const useCartStore = defineStore('CartStore', {
    state: () => ({
        items: [],
        totalItems: 0,
        totalQuantity: 0,
        subtotal: 0,
        tax: 0,
        shipping: 0,
        discount: 0,
        coupon: null,
        requiresShipping: true,
        loaded: false
    }),

    actions: {
        async fetchCart() {
            try {
                const response = await axios.get('/cart/fetch')
                this.setCartFromResponse(response.data)
            } catch {
                this.items = []
                this.totalItems = 0
                this.totalQuantity = 0
                this.subtotal = 0
                this.discount = 0
                this.coupon = null
            }
            this.loaded = true
        },

        async addItem(product, quantity) {
            try {
                const payload = {
                    product_id: product.id,
                    quantity: quantity,
                }
                if (product.product_variation_id) {
                    payload.product_variation_id = product.product_variation_id
                }
                const response = await axios.post('/cart/items', payload)
                this.setCartFromResponse(response.data)
            } catch {
                // silently fail — server is source of truth
            }
        },

        async removeItem(product) {
            const item = this.items.find(
                (cartItem) => cartItem.product_id === product.id
            )
            if (!item) return

            pushRemoveFromCart({
                id: item.item?.id || item.product_id || item.id,
                name: item.item?.name || item.name || '',
                price: Number(item.item?.price || item.price || 0),
                variation_label: item.variation_label || undefined,
                quantity: Number(item.quantity || 1),
            })

            try {
                const response = await axios.delete(`/cart/items/${item.id}`)
                this.setCartFromResponse(response.data)
            } catch {
                // silently fail
            }
        },

        async increaseQuantity(product) {
            const item = this.items.find(
                (cartItem) => cartItem.product_id === product.id
            )
            if (!item) return

            try {
                const response = await axios.put(`/cart/items/${item.id}`, {
                    product_id: product.id,
                    product_variation_id: product.product_variation_id || null,
                    quantity: item.quantity + 1
                })
                this.setCartFromResponse(response.data)
            } catch {
                // silently fail
            }
        },

        async decreaseQuantity(product) {
            const item = this.items.find(
                (cartItem) => cartItem.product_id === product.id
            )
            if (!item) return

            if (item.quantity <= 1) {
                await this.removeItem(product)
                return
            }

            try {
                const response = await axios.put(`/cart/items/${item.id}`, {
                    product_id: product.id,
                    product_variation_id: product.product_variation_id || null,
                    quantity: item.quantity - 1
                })
                this.setCartFromResponse(response.data)
            } catch {
                // silently fail
            }
        },

        async clearCart() {
            try {
                const response = await axios.delete('/cart/items')
                this.setCartFromResponse(response.data)
            } catch {
                // silently fail
            }
        },

        async applyCoupon(code) {
            const response = await axios.post('/cart/coupon', { code })
            this.setCartFromResponse(response.data)
        },

        async removeCoupon() {
            try {
                const response = await axios.delete('/cart/coupon')
                this.setCartFromResponse(response.data)
            } catch {
                // silently fail
            }
        },

        setCartFromResponse(data) {
            this.items = data.items || []
            this.totalItems = data.totalItems || 0
            this.totalQuantity = data.totalQuantity || 0
            this.subtotal = data.subtotal || 0
            this.tax = data.tax || 0
            this.discount = data.discount || 0
            this.coupon = data.coupon || null
            this.requiresShipping = data.requiresShipping ?? true
        }
    },

    getters: {
        total: (state) => state.subtotal + state.tax + state.shipping
    }
})
