import { defineStore } from 'pinia'

export const useCartStore = defineStore('CartStore', {
    state: () => ({
        items: JSON.parse(localStorage.getItem('cart')) || [],
        tax: 0,
        shipping: 0
    }),

    actions: {
        addItem(item, quantity) {
            const existingItem = this.items.find(
                (cartItem) => cartItem.item.id === item.id
            )

            if (existingItem) {
                // If item is already in cart, increase its quantity
                existingItem.quantity += quantity
            } else {
                // If item is not in cart, add it as a new entry
                this.items.push({
                    item,
                    quantity
                })
            }
            this.saveCartToLocalStorage()
        },

        removeItem(item) {
            this.items = this.items.filter(
                (cartItem) => cartItem.item.id !== item.id
            )
            this.saveCartToLocalStorage()
        },

        increaseQuantity(item) {
            const existingItem = this.items.find(
                (cartItem) => cartItem.item.id === item.id
            )
            if (existingItem) {
                existingItem.quantity++
                this.saveCartToLocalStorage()
            }
        },
        decreaseQuantity(item) {
            const existingItem = this.items.find(
                (cartItem) => cartItem.item.id === item.id
            )
            if (existingItem && existingItem.quantity > 1) {
                existingItem.quantity--
                this.saveCartToLocalStorage()
            } else {
                // Remove item if quantity reaches 0 or less
                this.removeItem(item)
            }
        },
        clearCart() {
            this.items = []
            this.saveCartToLocalStorage()
        },
        saveCartToLocalStorage() {
            localStorage.setItem('cart', JSON.stringify(this.items))
        }
    },

    getters: {
        totalItems: (state) => state.items.length,
        totalQuantity: (state) =>
            state.items.reduce(
                (total, cartItem) => total + cartItem.quantity,
                0
            ),
        subtotal: (state) =>
            state.items.reduce(
                (total, cartItem) =>
                    total + cartItem.item.price * cartItem.quantity,
                0
            ),
        // calculate total = subtotal + tax + shipping
        total: (state) => state.subtotal + state.tax + state.shipping
    }
})
