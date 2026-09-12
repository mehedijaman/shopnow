/**
 * Centralized dataLayer abstraction for GTM/GA4 ecommerce events.
 *
 * All ecommerce events go through this module so GTM can read them
 * from dataLayer. Consent is checked before every push.
 */

function hasConsent() {
    if (typeof window.ShopNowTracking === 'object' && typeof window.ShopNowTracking.hasConsent === 'function') {
        return window.ShopNowTracking.hasConsent()
    }
    return false
}

function ensureDataLayer() {
    window.dataLayer = window.dataLayer || []
}

/**
 * Push an ecommerce event to dataLayer for GTM consumption.
 *
 * @param {string} eventName - GTM custom event name (e.g. 'view_item')
 * @param {object} ecommerceData - Ecommerce payload with currency, value, items[]
 */
export function pushEvent(eventName, ecommerceData) {
    if (!hasConsent()) return

    ensureDataLayer()

    const payload = {
        event: eventName,
        ecommerce: {
            currency: ecommerceData.currency || 'BDT',
            value: ecommerceData.value || 0,
            ...(ecommerceData.transaction_id ? { transaction_id: ecommerceData.transaction_id } : {}),
            ...(ecommerceData.tax != null ? { tax: ecommerceData.tax } : {}),
            ...(ecommerceData.shipping != null ? { shipping: ecommerceData.shipping } : {}),
            items: ecommerceData.items || [],
        },
    }

    console.log('[GTM]', eventName, payload)

    window.dataLayer.push(payload)
}

/**
 * Push view_item event.
 * @param {object} product - { id, name, price, sale_price, category?, brand? }
 */
export function pushViewItem(product) {
    const price = Number(product.sale_price || product.price || 0)

    pushEvent('view_item', {
        currency: 'BDT',
        value: price,
        items: [{
            item_id: String(product.id),
            item_name: product.name,
            price,
            item_category: product.category?.name || undefined,
            item_brand: product.brand?.name || undefined,
            quantity: 1,
        }],
    })
}

/**
 * Push view_item_list event.
 * @param {string} listName - e.g. 'shop', category name, 'search results'
 * @param {Array} products - array of product objects
 */
export function pushViewItemList(listName, products) {
    if (!products || products.length === 0) return

    pushEvent('view_item_list', {
        currency: 'BDT',
        value: products.reduce((sum, p) => sum + Number(p.sale_price || p.price || 0), 0),
        item_list_name: listName,
        items: products.map((product, index) => ({
            item_id: String(product.id),
            item_name: product.name,
            price: Number(product.sale_price || product.price || 0),
            item_category: product.category?.name || undefined,
            item_brand: product.brand?.name || undefined,
            item_list_id: listName,
            index,
            quantity: 1,
        })),
    })
}

/**
 * Push add_to_cart event.
 * @param {object} item - { id, name, price, variation_label? }
 * @param {number} quantity
 */
export function pushAddToCart(item, quantity) {
    const price = Number(item.price || 0)

    pushEvent('add_to_cart', {
        currency: 'BDT',
        value: price * Number(quantity || 1),
        items: [{
            item_id: String(item.id),
            item_name: item.name,
            price,
            item_variant: item.variation_label || undefined,
            quantity: Number(quantity || 1),
        }],
    })
}

/**
 * Push remove_from_cart event.
 * @param {object} item - { id, name, price, variation_label?, quantity? }
 */
export function pushRemoveFromCart(item) {
    const price = Number(item.price || 0)
    const quantity = Number(item.quantity || 1)

    pushEvent('remove_from_cart', {
        currency: 'BDT',
        value: price * quantity,
        items: [{
            item_id: String(item.id),
            item_name: item.name,
            price,
            item_variant: item.variation_label || undefined,
            quantity,
        }],
    })
}

/**
 * Push view_cart event.
 * @param {Array} cartItems - array of cart items with item.price, item.name, item.id, quantity, variation_label
 * @param {number} subtotal
 */
export function pushViewCart(cartItems, subtotal) {
    pushEvent('view_cart', {
        currency: 'BDT',
        value: Number(subtotal || 0),
        items: cartItems.map((cartItem) => ({
            item_id: String(cartItem.item?.id || cartItem.id),
            item_name: cartItem.item?.name || cartItem.name,
            price: Number(cartItem.item?.price || cartItem.price || 0),
            item_variant: cartItem.variation_label || undefined,
            quantity: Number(cartItem.quantity || 1),
        })),
    })
}

/**
 * Push begin_checkout event.
 * @param {Array} cartItems
 * @param {number} orderTotal
 */
export function pushBeginCheckout(cartItems, orderTotal) {
    pushEvent('begin_checkout', {
        currency: 'BDT',
        value: Number(orderTotal || 0),
        items: cartItems.map((cartItem) => ({
            item_id: String(cartItem.item?.id || cartItem.id),
            item_name: cartItem.item?.name || cartItem.name,
            price: Number(cartItem.item?.price || cartItem.price || 0),
            item_variant: cartItem.variation_label || undefined,
            quantity: Number(cartItem.quantity || 1),
        })),
    })
}

/**
 * Push purchase event (fires on order confirmation page only).
 * Uses sessionStorage to prevent duplicate fires on page refresh.
 *
 * @param {object} order - { id, total, tax, shipping }
 * @param {Array} items - array of { product_id, product_name, unit_price, quantity, variation_label? }
 */
export function pushPurchase(order, items) {
    const transactionId = String(order.id)
    const storageKey = 'purchase_' + transactionId

    try {
        if (sessionStorage.getItem(storageKey)) return
        sessionStorage.setItem(storageKey, '1')
    } catch (e) {
        // sessionStorage may be unavailable; proceed anyway
    }

    pushEvent('purchase', {
        currency: 'BDT',
        transaction_id: transactionId,
        value: Number(order.total || 0),
        tax: Number(order.tax || 0),
        shipping: Number(order.shipping || 0),
        items: items.map((op) => ({
            item_id: String(op.product_id),
            item_name: op.product_name || ('Product #' + op.product_id),
            price: Number(op.unit_price || 0),
            quantity: Number(op.quantity || 1),
            item_variant: op.variation_label || undefined,
        })),
    })
}

export default {
    pushEvent,
    pushViewItem,
    pushViewItemList,
    pushAddToCart,
    pushRemoveFromCart,
    pushViewCart,
    pushBeginCheckout,
    pushPurchase,
}
