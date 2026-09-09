# ShopNow — GTM/GA4 Integration Architecture & Implementation Plan

> **Generated:** 2026-09-09
> **Scope:** Google Tag Manager + GA4 integration into existing Laravel e-commerce system
> **Constraint:** Preserve existing Meta Pixel implementation; no breaking changes

---

## Table of Contents

1. [Executive Summary](#1-executive-summary)
2. [Current Architecture](#2-current-architecture)
3. [Existing Analytics Implementation](#3-existing-analytics-implementation)
4. [Existing Meta Pixel Architecture](#4-existing-meta-pixel-architecture)
5. [E-commerce Event Flow](#5-e-commerce-event-flow)
6. [Database/Data Model Analysis](#6-databasedata-model-analysis)
7. [Frontend Analysis](#7-frontend-analysis)
8. [Current dataLayer Analysis](#8-current-datalayer-analysis)
9. [Recommended GTM Architecture](#9-recommended-gtm-architecture)
10. [Recommended GA4 Architecture](#10-recommended-ga4-architecture)
11. [GA4 Ecommerce Event Specification](#11-ga4-ecommerce-event-specification)
12. [Recommended dataLayer Contract](#12-recommended-datalayer-contract)
13. [Meta Pixel Compatibility Strategy](#13-meta-pixel-compatibility-strategy)
14. [GTM Container Design](#14-gtm-container-design)
15. [Files Requiring Changes](#15-files-requiring-changes)
16. [Files That Should Remain Unchanged](#16-files-that-should-remain-unchanged)
17. [Duplicate Tracking Risks](#17-duplicate-tracking-risks)
18. [Edge Cases](#18-edge-cases)
19. [Consent Considerations](#19-consent-considerations)
20. [Security/Privacy Considerations](#20-securityprivacy-considerations)
21. [Implementation Sequence](#21-implementation-sequence)
22. [Testing Strategy](#22-testing-strategy)
23. [Deployment Strategy](#23-deployment-strategy)
24. [Rollback Strategy](#24-rollback-strategy)
25. [Future Extensibility](#25-future-extensibility)
26. [Final Recommendations](#26-final-recommendations)

---

## 1. Executive Summary

### Current State

ShopNow already has a working dual-tracking system:

- **Meta Pixel**: Client-side (`fbevents.js`) + Server-side (Conversions API) — fully implemented with consent management
- **GA4**: Client-side (`gtag.js`) — basic implementation via `window.ShopNowTracking.trackGa()`
- **GTM**: Not implemented
- **dataLayer**: Created by gtag.js but unused for ecommerce events

### Key Decision: Keep Meta Pixel Independent

**Recommendation:** The Meta Pixel implementation should remain completely independent of GTM. Reasons:

1. Meta Pixel already works correctly with CAPI deduplication
2. Moving Meta into GTM would require reworking the consent flow
3. Meta's CAPI server-side events are tightly coupled with PHP controllers
4. The risk of breaking working Meta tracking outweighs any benefits

GTM will manage **only GA4 and future marketing tags**. Meta stays as-is.

---

## 2. Current Architecture

### Technology Stack

| Layer | Technology |
|---|---|
| Backend | Laravel 13.16 / PHP 8.4 |
| Admin SPA | Inertia.js + Vue 3 + Pinia |
| Public site | Blade templates + Vue 3 components |
| Bundler | Vite 8 |
| CSS | Tailwind CSS 4 |
| Auth | Two guards: `user` (admin), `customer` (shopper) |
| Modules | `daniel-cintra/modular` (15 modules) |
| Currency | BDT (Bangladesh Taka) |
| Geography | Bangladesh-specific (Division > District > Upazila > Union) |

### Dual Frontend

```
Admin Panel (Inertia SPA)
    resources/js/app.js          → Inertia + Ziggy routes
    resources/views/app.blade.php → Root template (@inertia)

Public Site (Blade + Vue components)
    resources-site/js/index-app.js → Vue components (cart, checkout, etc.)
    resources-site/views/site-layout.blade.php → Master layout
    modules/*/views/*.blade.php   → Page views extending site-layout
```

### Module Structure

```
modules/
├── AdminAuth/          # Admin login (user guard)
├── CustomerAuth/       # Customer login (customer guard)
├── Product/            # Products, categories, brands, attributes, variations, bundles
├── Cart/               # Cart + cart items + middleware
├── Order/              # Orders, order products, payments, shipments
├── Customer/           # Customer profiles, addresses
├── Settings/           # All settings (pixel, analytics, general, etc.)
├── ContactMessage/     # Contact form + Lead tracking
├── Blog/               # Blog posts
├── Index/              # Homepage
├── Page/               # Static pages
├── Slider/             # Homepage sliders
├── Dashboard/          # Admin dashboard
├── User/               # Admin users
├── Acl/                # Roles + permissions
└── Profile/            # User profile
```

---

## 3. Existing Analytics Implementation

### Tracking Architecture

All tracking is centralized through `window.ShopNowTracking`, defined in `site-layout.blade.php`:

```
window.ShopNowTracking = {
    hasConsent()           → checks consent state
    setConsent(granted)    → persists consent, initializes trackers
    track(name, payload)  → Meta Pixel fbq('track', ...)
    trackCustom(name, p)  → Meta Pixel fbq('trackCustom', ...)
    trackGa(name, payload)→ GA4 gtag('event', ...)
}
```

### Event Inventory

#### Client-Side Events (via ShopNowTracking)

| Event | Meta Pixel | GA4 | Source File |
|---|---|---|---|
| PageView | `fbq('track', 'PageView')` | — | `site-layout.blade.php:179` |
| ViewContent | `fbq('track', 'ViewContent', ...)` | `gtag('event', 'view_item', ...)` | `product-show.blade.php:13,21` |
| AddToCart | `fbq('track', 'AddToCart', ...)` | `gtag('event', 'add_to_cart', ...)` | `AddToCartButton.vue:126,134` |
| remove_from_cart | — | `gtag('event', 'remove_from_cart', ...)` | `CartStore.js:53` |
| view_cart | — | `gtag('event', 'view_cart', ...)` | `ShoppingCart.vue:243` |
| InitiateCheckout | `fbq('track', 'InitiateCheckout', ...)` | `gtag('event', 'begin_checkout', ...)` | `CheckoutForm.vue:459,466` |
| Purchase | `fbq('track', 'Purchase', ...)` | — | `CheckoutForm.vue:739` |
| purchase (GA4) | — | `gtag('event', 'purchase', ...)` | `order-confirm.blade.php:27` |
| Search | `fbq('track', 'Search', ...)` | — | `shop.blade.php:159` |

#### Server-Side Events (via MetaConversionApiService)

| Event | Controller | File |
|---|---|---|
| Purchase | `SiteOrderController::trackPurchaseEvent()` | `SiteOrderController.php:226` |
| CompleteRegistration | `AuthenticatedSessionController::signup()` | `AuthenticatedSessionController.php:61` |
| Lead | `SiteContactMessageController::store()` | `SiteContactMessageController.php:24` |

### Settings (Database-Driven)

**Pixel group** (`pixel.*`):
- `enabled`, `meta_pixel_id`, `require_consent`, `enable_non_production`
- `capi_enabled`, `capi_access_token`, `api_version`, `test_event_code`

**Analytics group** (`analytics.*`):
- `enabled`, `ga_measurement_id`

All accessed via `setting('pixel.enabled')` / `setting('analytics.ga_measurement_id')` helper. No `.env` variables or config files.

---

## 4. Existing Meta Pixel Architecture

### Browser-Side Flow

```
site-layout.blade.php <head>
    ↓
Reads pixel.* settings from DB
    ↓
Computes $pixelCanLoad (enabled + env + pixelId)
    ↓
If $trackingCanLoad: defines ShopNowTracking IIFE
    ↓
If consent granted: initPixel() loads fbevents.js
    ↓
fbq('init', pixelId) + fbq('track', 'PageView')
    ↓
Child components call ShopNowTracking.track()
    ↓
fbq('track', eventName, payload, options)
```

### Server-Side CAPI Flow

```
SiteOrderController::store()
    ↓
Creates order in DB transaction
    ↓
trackPurchaseEvent() called
    ↓
MetaConversionApiService::track('Purchase', customData, context)
    ↓
isEnabled() checks: pixel.enabled, capi_enabled, consent, environment
    ↓
buildUserData() hashes PII (SHA-256)
    ↓
Http::post("https://graph.facebook.com/{version}/{pixelId}/events", payload)
```

### Deduplication

Browser Purchase: `eventID: 'purchase_' + order_id`
Server Purchase: `event_id: 'purchase_' + order_id`
→ Meta deduplicates these automatically.

### Consent Flow

```
First visit → banner shown → Accept/Decline
    ↓
Accept → consent = 'granted' → initPixel() + initGa() → banner hidden
Decline → consent = 'denied' → no tracking → banner hidden
    ↓
Subsequent visits → consent read from localStorage/cookie → no banner
```

---

## 5. E-commerce Event Flow

### Product View

```
User visits /shop/product/{id}/{slug}
    ↓
SiteProductController::show() → loads Product with category, variations, bundleItems
    ↓
Renders product-show.blade.php (extends site-layout)
    ↓
@section('bodyEndScripts') fires:
    ShopNowTracking.track('ViewContent', {...})     → Meta Pixel
    ShopNowTracking.trackGa('view_item', {...})     → GA4
```

**Available data:** product.id, product.name, product.price, product.sale_price, product.category.name, product.sku, product.variations, product.brand.name

### Add to Cart

```
User clicks Add to Cart button
    ↓
AddToCartButton.vue → addToCart() function
    ↓
cartStore.addItem(item, quantity) → POST /cart/items
    ↓
Server creates CartItem → returns updated cart
    ↓
ShopNowTracking.track('AddToCart', {...})     → Meta Pixel
    ShopNowTracking.trackGa('add_to_cart', {...}) → GA4
```

**Available data:** product.id, product.name, effectivePrice, quantity, variation_label, cartStore.subtotal

### View Cart

```
User visits /cart
    ↓
ShoppingCart.vue mounts → cartStore fetches items
    ↓
trackViewCart() fires:
    ShopNowTracking.trackGa('view_cart', {...}) → GA4
```

**Available data:** cartStore.items (array), cartStore.subtotal

### Remove from Cart

```
User clicks remove button in cart
    ↓
CartStore.js → removeItem()
    ↓
DELETE /cart/items/{id}
    ↓
ShopNowTracking.trackGa('remove_from_cart', {...}) → GA4
```

**Available data:** item.id, item.name, item.price, item.quantity, item.variation_label

### Checkout

```
User visits /checkout
    ↓
CheckoutForm.vue mounts
    ↓
onMounted fires:
    ShopNowTracking.track('InitiateCheckout', {...})  → Meta Pixel
    ShopNowTracking.trackGa('begin_checkout', {...})  → GA4
```

**Available data:** cartStore.items, orderTotal, shippingCharge, cartStore.subtotal

### Order Submission (Purchase)

```
User clicks "Place Order" in CheckoutForm
    ↓
submitForm() → axios.post('/site-order-store', payload)
    ↓
SiteOrderController::store()
    ├→ Creates Order + OrderProducts in DB transaction
    ├→ Decrements stock
    ├→ Creates OrderShipment (if shipping required)
    ├→ Queues confirmation emails
    ├→ trackPurchaseEvent() → Meta CAPI Purchase event
    └→ Returns JSON { order_id }
    ↓
CheckoutForm receives response
    ↓
ShopNowTracking.track('Purchase', {...}, {eventID}) → Meta Pixel
    ↓
cartStore.clearCart()
    ↓
window.location.href = '/order-confirm/' + order_id
    ↓
order-confirm.blade.php renders
    ↓
@section('bodyEndScripts') fires:
    ShopNowTracking.trackGa('purchase', {...}) → GA4
```

**Available data:** order.id, order.total, order.orderProducts (with product names, prices, quantities, variation_labels)

---

## 6. Database/Data Model Analysis

### GA4 Ecommerce Field Mapping

#### Products

| Database Field | Application Access | GA4 Field |
|---|---|---|
| `products.id` | `$product->id` | `item_id` |
| `products.name` | `$product->name` | `item_name` |
| `products.price` | `$product->price` | `price` (fallback) |
| `products.sale_price` | `$product->sale_price` | `price` (primary) |
| `products.sku` | `$product->sku` | `item_id` (if SKU-based) |
| `product_categories.name` | `$product->category->name` | `item_category` |
| `product_brands.name` | `$product->brand->name` | `item_brand` |
| `product_variations.price` | `$variation->price` | `price` (variant) |
| `product_variations.sale_price` | `$variation->sale_price` | `price` (variant primary) |
| `product_variations.sku` | `$variation->sku` | `item_id` (variant) |

#### Orders

| Database Field | Application Access | GA4 Field |
|---|---|---|
| `orders.id` | `$order->id` | `transaction_id` |
| `orders.total` | `$order->total` | `value` |
| `orders.subtotal` | `$order->subtotal` | `value` (before shipping/tax) |
| `orders.tax` | `$order->tax` | `tax` |
| `orders.shipping` | `$order->shipping` | `shipping` |
| `orders.payment_method` | `$order->payment_method` | `payment_type` |
| `orders.created_at` | `$order->created_at` | (timestamp) |

#### Order Products

| Database Field | Application Access | GA4 Field |
|---|---|---|
| `order_products.product_id` | `$op->product_id` | `item_id` |
| `order_products.unit_price` | `$op->unit_price` | `price` |
| `order_products.quantity` | `$op->quantity` | `quantity` |
| `order_products.variation_label` | `$op->variation_label` | `item_variant` |
| `order_products.discount` | `$op->discount` | (discount handling) |

---

## 7. Frontend Analysis

### Entry Points

```
Admin:  resources/js/app.js           → Inertia SPA (no tracking needed)
Site:   resources-site/js/index-app.js → Vue components (tracking lives here)
Blog:   resources-site/js/blog-app.js  → Blog SPA (no tracking)
```

### Vue Components with Tracking

| Component | Events Fired | Type |
|---|---|---|
| `AddToCartButton.vue` | AddToCart, add_to_cart | Meta + GA4 |
| `CheckoutForm.vue` | InitiateCheckout, begin_checkout, Purchase | Meta + GA4 |
| `ShoppingCart.vue` | view_cart | GA4 only |
| `CartStore.js` | remove_from_cart | GA4 only |

### Blade Views with Tracking

| View | Events Fired | Type |
|---|---|---|
| `site-layout.blade.php` | PageView, consent system | Meta + GA4 init |
| `product-show.blade.php` | ViewContent, view_item | Meta + GA4 |
| `order-confirm.blade.php` | purchase | GA4 only |
| `shop.blade.php` | Search | Meta only |

### State Management

- **CartStore** (Pinia): Manages cart items, subtotal, tax. Located at `resources-site/js/Stores/CartStore.js`
- **No dedicated analytics store**: Tracking calls are scattered across components

---

## 8. Current dataLayer Analysis

### What Exists

The `dataLayer` is created by `gtag.js` during GA4 initialization:

```javascript
// In initGa() function (site-layout.blade.php:194-198)
window.dataLayer = window.dataLayer || []
function gtag(){ dataLayer.push(arguments); }
window.gtag = gtag
gtag('js', new Date())
gtag('config', gaConfig.gaId)
```

### What's Missing

**No explicit `dataLayer.push()` calls for ecommerce events.** All ecommerce tracking goes through `gtag('event', ...)` directly:

```javascript
// Current approach (scattered across components)
window.ShopNowTracking.trackGa('view_item', { currency, value, items })

// This internally calls:
window.gtag('event', 'view_item', { currency, value, items })
```

### Implications for GTM

Since GTM reads events from `dataLayer`, the current approach of calling `gtag()` directly means **GTM cannot see ecommerce events**. To integrate GTM, we need to:

1. Push events to `dataLayer` instead of (or in addition to) calling `gtag()` directly
2. Let GTM's GA4 tags pick up these events from `dataLayer`
3. OR use GTM's built-in GA4 event tags with custom triggers

---

## 9. Recommended GTM Architecture

### Architecture Diagram

```
Laravel Application (Blade + Vue)
    │
    ├── dataLayer.push() for ecommerce events
    │
    ▼
Google Tag Manager Container
    │
    ├── GA4 Configuration Tag (fires on all pages)
    │
    ├── GA4 Event Tags (triggered by custom events)
    │   ├── view_item
    │   ├── add_to_cart
    │   ├── remove_from_cart
    │   ├── view_cart
    │   ├── begin_checkout
    │   ├── add_shipping_info
    │   ├── add_payment_info
    │   └── purchase
    │
    ├── Google Ads (future)
    └── Other marketing tags (future)

Meta Pixel (INDEPENDENT — not managed by GTM)
    │
    ├── Browser: fbevents.js (site-layout.blade.php)
    └── Server: MetaConversionApiService (PHP)
```

### Why Keep Meta Separate

1. **CAPI coupling**: Meta server-side events are deeply integrated with PHP controllers. Moving browser tracking into GTM would create a split architecture (browser in GTM, server in PHP) that's harder to maintain.
2. **Deduplication**: Meta's event_id deduplication between browser and CAPI is working correctly. Changing the browser tracking mechanism could break this.
3. **Consent flow**: The existing consent system initializes Meta Pixel directly. GTM has its own consent mechanisms that would need separate configuration.
4. **Risk vs reward**: Meta tracking works. The marginal benefit of consolidating it into GTM doesn't justify the risk of breaking it.

### What GTM Manages

- GA4 configuration and event tags
- Future Google Ads tags
- Future conversion tracking tags
- Future remarketing tags
- A/B testing tools (Optimizely, VWO, etc.)
- Heatmap tools (Hotjar, etc.)

---

## 10. Recommended GA4 Architecture

### Approach: dataLayer-First with GTM

Instead of calling `gtag('event', ...)` directly, push structured events to `dataLayer`. GTM reads these and fires the appropriate GA4 tags.

### Migration Path

**Phase 1:** Add dataLayer pushes alongside existing gtag calls (no breaking changes)
**Phase 2:** Configure GTM to read dataLayer events and fire GA4 tags
**Phase 3:** Remove direct gtag.js loading (GTM loads GA4 via its container)
**Phase 4:** Remove `ShopNowTracking.trackGa()` calls (replaced by dataLayer pushes)

**Recommended: Phase 1 + 2 only.** Keep the existing gtag.js as a fallback and add dataLayer pushes for GTM. This provides redundancy and allows gradual migration.

---

## 11. GA4 Ecommerce Event Specification

### view_item

| Property | Value |
|---|---|
| **Event name** | `view_item` |
| **Trigger** | Product page load |
| **Source file** | `modules/Product/views/product-show.blade.php` |
| **dataLayer structure** | See Section 12 |
| **GA4 parameters** | `currency`, `value`, `items[]` |
| **Duplicate risk** | Low — fires once per page load |

### view_item_list

| Property | Value |
|---|---|
| **Event name** | `view_item_list` |
| **Trigger** | Shop/category page load with products |
| **Source file** | `modules/Product/views/shop.blade.php` |
| **GA4 parameters** | `item_list_name`, `items[]` |
| **Duplicate risk** | Low — fires once per page load |
| **Note** | Currently NOT tracked. Recommend adding. |

### select_item

| Property | Value |
|---|---|
| **Event name** | `select_item` |
| **Trigger** | User clicks a product card |
| **Source file** | `modules/Product/views/shop.blade.php` or `product-card.blade.php` |
| **GA4 parameters** | `item_list_name`, `items[]` |
| **Duplicate risk** | Low |
| **Note** | Currently NOT tracked. Recommend adding. |

### add_to_cart

| Property | Value |
|---|---|
| **Event name** | `add_to_cart` |
| **Trigger** | Add to cart button click |
| **Source file** | `resources-site/js/Components/AddToCartButton.vue` |
| **GA4 parameters** | `currency`, `value`, `items[]` |
| **Duplicate risk** | Low — user-initiated action |

### remove_from_cart

| Property | Value |
|---|---|
| **Event name** | `remove_from_cart` |
| **Trigger** | Remove button click in cart |
| **Source file** | `resources-site/js/Stores/CartStore.js` |
| **GA4 parameters** | `currency`, `value`, `items[]` |
| **Duplicate risk** | Low — user-initiated action |

### view_cart

| Property | Value |
|---|---|
| **Event name** | `view_cart` |
| **Trigger** | Cart page load |
| **Source file** | `resources-site/js/Components/ShoppingCart.vue` |
| **GA4 parameters** | `currency`, `value`, `items[]` |
| **Duplicate risk** | Low — fires once per page load |

### begin_checkout

| Property | Value |
|---|---|
| **Event name** | `begin_checkout` |
| **Trigger** | Checkout page load |
| **Source file** | `resources-site/js/Components/CheckoutForm.vue` |
| **GA4 parameters** | `currency`, `value`, `coupon`, `items[]` |
| **Duplicate risk** | Low — fires once per page load |

### add_shipping_info

| Property | Value |
|---|---|
| **Event name** | `add_shipping_info` |
| **Trigger** | User selects/enters shipping address |
| **Source file** | `resources-site/js/Components/CheckoutForm.vue` |
| **GA4 parameters** | `currency`, `value`, `shipping_tier`, `items[]` |
| **Duplicate risk** | Medium — user may change address multiple times |
| **Note** | Currently NOT tracked. Recommend adding. |

### add_payment_info

| Property | Value |
|---|---|
| **Event name** | `add_payment_info` |
| **Trigger** | User selects payment method |
| **Source file** | `resources-site/js/Components/CheckoutForm.vue` |
| **GA4 parameters** | `currency`, `value`, `payment_type`, `items[]` |
| **Duplicate risk** | Medium — user may change payment method |
| **Note** | Currently NOT tracked. Recommend adding. |

### purchase

| Property | Value |
|---|---|
| **Event name** | `purchase` |
| **Trigger** | Order confirmation page load |
| **Source file** | `modules/Order/views/order-confirm.blade.php` |
| **GA4 parameters** | `transaction_id`, `currency`, `value`, `tax`, `shipping`, `items[]` |
| **Duplicate risk** | HIGH — page refresh re-fires event |
| **Mitigation** | Use `sessionStorage` to deduplicate |

### refund

| Property | Value |
|---|---|
| **Event name** | `refund` |
| **Trigger** | Order refunded |
| **Source file** | N/A — no refund system exists |
| **Note** | Not applicable — no refund functionality in current system |

---

## 12. Recommended dataLayer Contract

### Base Structure

Every ecommerce event push should follow this contract:

```javascript
window.dataLayer = window.dataLayer || []
window.dataLayer.push({
    event: '<event_name>',

    ecommerce: {
        currency: 'BDT',
        value: <number>,

        items: [
            {
                item_id: '<product_id_or_sku>',
                item_name: '<product_name>',
                affiliation: '<store_name>',
                coupon: '<coupon_code_or_null>',
                discount: <discount_amount>,
                item_brand: '<brand_name_or_null>',
                item_category: '<category_name>',
                item_category2: '<sub_category_or_null>',
                item_category3: '<sub_sub_category_or_null>',
                item_category4: null,
                item_category5: null,
                item_list_id: '<list_id_or_null>',
                item_list_name: '<list_name_or_null>',
                item_variant: '<variant_label_or_null>',
                location_id: null,
                price: <unit_price>,
                quantity: <quantity>
            }
        ]
    }
})
```

### Event-Specific Contracts

#### view_item

```javascript
window.dataLayer.push({
    event: 'view_item',
    ecommerce: {
        currency: 'BDT',
        value: <sale_price_or_price>,
        items: [{
            item_id: String(product.id),
            item_name: product.name,
            item_category: product.category?.name,
            item_brand: product.brand?.name,
            item_variant: null,
            price: Number(product.sale_price || product.price),
            quantity: 1
        }]
    }
})
```

#### add_to_cart

```javascript
window.dataLayer.push({
    event: 'add_to_cart',
    ecommerce: {
        currency: 'BDT',
        value: <unit_price * quantity>,
        items: [{
            item_id: String(product.id),
            item_name: product.name,
            item_category: product.category?.name,
            item_brand: product.brand?.name,
            item_variant: variation_label || null,
            price: Number(unit_price),
            quantity: Number(quantity)
        }]
    }
})
```

#### begin_checkout

```javascript
window.dataLayer.push({
    event: 'begin_checkout',
    ecommerce: {
        currency: 'BDT',
        value: <order_total>,
        items: cartStore.items.map(item => ({
            item_id: String(item.item.id),
            item_name: item.item.name,
            item_category: item.item.category?.name,
            item_brand: item.item.brand?.name,
            item_variant: item.variation_label || null,
            price: Number(item.unit_price || item.item.price),
            quantity: Number(item.quantity)
        }))
    }
})
```

#### purchase

```javascript
window.dataLayer.push({
    event: 'purchase',
    ecommerce: {
        transaction_id: String(order.id),
        value: Number(order.total),
        tax: Number(order.tax),
        shipping: Number(order.shipping),
        currency: 'BDT',
        items: order.orderProducts.map(op => ({
            item_id: String(op.product_id),
            item_name: op.product?.name || 'Product #' + op.product_id,
            item_variant: op.variation_label || null,
            price: Number(op.unit_price),
            quantity: Number(op.quantity)
        }))
    }
})
```

---

## 13. Meta Pixel Compatibility Strategy

### Coexistence Model

```
site-layout.blade.php
    │
    ├── Meta Pixel (INDEPENDENT)
    │   ├── Loads fbevents.js directly
    │   ├── Initializes fbq('init', pixelId)
    │   ├── Fires fbq('track', 'PageView')
    │   └── Child components use ShopNowTracking.track()
    │
    ├── GA4 via GTM (NEW)
    │   ├── GTM container script loads in <head>
    │   ├── GTM loads GA4 configuration tag
    │   ├── dataLayer pushes trigger GA4 event tags
    │   └── GTM manages all GA4 logic
    │
    └── Consent Management (SHARED)
        ├── Gates both Meta Pixel and GTM
        ├── GTM uses consent initialization API
        └── Meta continues using ShopNowTracking.hasConsent()
```

### What Stays the Same

- `ShopNowTracking.track()` — Meta Pixel events (unchanged)
- `ShopNowTracking.trackCustom()` — Meta custom events (unchanged)
- `MetaConversionApiService` — Server-side CAPI (unchanged)
- All Meta event_id deduplication (unchanged)
- All Meta consent checks (unchanged)

### What Changes

- `ShopNowTracking.trackGa()` — Eventually replaced by dataLayer pushes
- GTM container script added to `<head>`
- dataLayer initialization moved to GTM (instead of gtag.js)

### Risk Mitigation

1. **Keep gtag.js as fallback**: Don't remove direct GA4 loading until GTM is verified working
2. **Test in GTM Preview mode**: Verify all events fire correctly before publishing
3. **Monitor GA4 DebugView**: Verify event parameters match expected structure
4. **A/B test**: Run both systems in parallel for 1-2 weeks before removing old code

---

## 14. GTM Container Design

### Variables

| Variable Name | Type | Source |
|---|---|---|
| GA4 Measurement ID | Constant | `analytics.ga_measurement_id` from settings |
| Ecommerce Items | Data Layer Variable | `ecommerce.items` |
| Transaction ID | Data Layer Variable | `ecommerce.transaction_id` |
| Currency | Data Layer Variable | `ecommerce.currency` |
| Value | Data Layer Variable | `ecommerce.value` |
| Tax | Data Layer Variable | `ecommerce.tax` |
| Shipping | Data Layer Variable | `ecommerce.shipping` |
| Item ID | Data Layer Variable | `ecommerce.items.*.item_id` |
| Item Name | Data Layer Variable | `ecommerce.items.*.item_name` |
| Item Category | Data Layer Variable | `ecommerce.items.*.item_category` |
| Item Price | Data Layer Variable | `ecommerce.items.*.price` |
| Item Quantity | Data Layer Variable | `ecommerce.items.*.quantity` |
| Item Variant | Data Layer Variable | `ecommerce.items.*.item_variant` |

### Triggers

| Trigger Name | Type | Configuration |
|---|---|---|
| Custom Event - view_item | Custom Event | Event Name: `view_item` |
| Custom Event - view_item_list | Custom Event | Event Name: `view_item_list` |
| Custom Event - select_item | Custom Event | Event Name: `select_item` |
| Custom Event - add_to_cart | Custom Event | Event Name: `add_to_cart` |
| Custom Event - remove_from_cart | Custom Event | Event Name: `remove_from_cart` |
| Custom Event - view_cart | Custom Event | Event Name: `view_cart` |
| Custom Event - begin_checkout | Custom Event | Event Name: `begin_checkout` |
| Custom Event - add_shipping_info | Custom Event | Event Name: `add_shipping_info` |
| Custom Event - add_payment_info | Custom Event | Event Name: `add_payment_info` |
| Custom Event - purchase | Custom Event | Event Name: `purchase` |
| All Pages | Page View | Fires on every page |

### Tags

| Tag Name | Type | Configuration | Trigger |
|---|---|---|---|
| GA4 Configuration | GA4 Configuration | Measurement ID: {{GA4 Measurement ID}} | All Pages |
| GA4 - view_item | GA4 Event | Event Name: view_item | Custom Event - view_item |
| GA4 - view_item_list | GA4 Event | Event Name: view_item_list | Custom Event - view_item_list |
| GA4 - select_item | GA4 Event | Event Name: select_item | Custom Event - select_item |
| GA4 - add_to_cart | GA4 Event | Event Name: add_to_cart | Custom Event - add_to_cart |
| GA4 - remove_from_cart | GA4 Event | Event Name: remove_from_cart | Custom Event - remove_from_cart |
| GA4 - view_cart | GA4 Event | Event Name: view_cart | Custom Event - view_cart |
| GA4 - begin_checkout | GA4 Event | Event Name: begin_checkout | Custom Event - begin_checkout |
| GA4 - add_shipping_info | GA4 Event | Event Name: add_shipping_info | Custom Event - add_shipping_info |
| GA4 - add_payment_info | GA4 Event | Event Name: add_payment_info | Custom Event - add_payment_info |
| GA4 - purchase | GA4 Event | Event Name: purchase | Custom Event - purchase |

---

## 15. Files Requiring Changes

### Must Change

| File | Purpose | Current Responsibility | Required Change | Risk Level |
|---|---|---|---|---|
| `resources-site/views/site-layout.blade.php` | Master layout | Loads GA4 gtag.js directly, defines ShopNowTracking | Add GTM container script, initialize dataLayer for GTM, keep gtag.js as fallback | **High** — affects all pages |
| `resources-site/js/analytics/datalayer.js` | Centralized analytics | N/A (does not exist) | **Create** — reusable dataLayer push utility | **Low** — new file |
| `modules/Product/views/product-show.blade.php` | Product page | Fires ViewContent + view_item via ShopNowTracking | Add dataLayer.push for view_item alongside existing calls | **Low** — isolated to one page |
| `resources-site/js/Components/AddToCartButton.vue` | Add to cart | Fires AddToCart + add_to_cart via ShopNowTracking | Add dataLayer.push for add_to_cart alongside existing calls | **Low** — isolated to one component |
| `resources-site/js/Stores/CartStore.js` | Cart state | Fires remove_from_cart via ShopNowTracking | Add dataLayer.push for remove_from_cart alongside existing calls | **Low** — isolated to one store |
| `resources-site/js/Components/ShoppingCart.vue` | Cart page | Fires view_cart via ShopNowTracking | Add dataLayer.push for view_cart alongside existing calls | **Low** — isolated to one component |
| `resources-site/js/Components/CheckoutForm.vue` | Checkout | Fires InitiateCheckout + begin_checkout + Purchase via ShopNowTracking | Add dataLayer.push for begin_checkout, remove GA4 purchase (keep only Meta) | **Medium** — critical purchase flow |
| `modules/Order/views/order-confirm.blade.php` | Order confirmation | Fires purchase GA4 via ShopNowTracking | Add dataLayer.push for purchase with sessionStorage deduplication | **Medium** — critical purchase flow |
| `modules/Product/views/shop.blade.php` | Shop page | Fires Search via ShopNowTracking | Add dataLayer.push for view_item_list | **Low** — isolated to one page |
| `modules/Settings/Database/Migrations/...` | Settings | N/A (does not exist) | **Create** — add `analytics.gtm_container_id` setting | **Low** — new migration |
| `resources/js/Pages/Settings/Components/AnalyticsGroup.vue` | Admin UI | Shows GA4 toggle + measurement ID input | Add GTM Container ID input field | **Low** — admin-only |
| `modules/Settings/Http/Requests/SettingsGroupValidate.php` | Validation | Validates analytics settings | Add GTM container ID validation rule | **Low** — isolated change |

### Should NOT Change

| File | Reason |
|---|---|
| `modules/Settings/Services/MetaConversionApiService.php` | Meta CAPI — independent system |
| `modules/Order/Http/Controllers/SiteOrderController.php` | Meta CAPI Purchase event — do not touch |
| `modules/CustomerAuth/Http/Controllers/AuthenticatedSessionController.php` | Meta CAPI CompleteRegistration — do not touch |
| `modules/ContactMessage/Http/Controllers/SiteContactMessageController.php` | Meta CAPI Lead — do not touch |
| Any file with `fbq()` calls | Meta Pixel — independent system |
| `modules/Settings/Services/SettingService.php` | Core settings — no changes needed |
| Cart/Order business logic | Tracking should not affect business logic |

---

## 16. Duplicate Tracking Risks

### Purchase Event Duplication

**Risk Level: HIGH**

Current flow:
1. CheckoutForm.vue fires Meta `Purchase` (with eventID)
2. order-confirm.blade.php fires GA4 `purchase`

With GTM:
1. CheckoutForm.vue fires Meta `Purchase` (unchanged)
2. CheckoutForm.vue pushes `purchase` to dataLayer → GTM fires GA4 `purchase`
3. order-confirm.blade.php pushes `purchase` to dataLayer → GTM fires GA4 `purchase` AGAIN

**Problem:** GA4 `purchase` fires twice.

**Mitigation:**
- Option A: Remove GA4 purchase from CheckoutForm, keep only on order-confirm page (recommended)
- Option B: Use `sessionStorage` to deduplicate
- Option C: Use GA4's `page_location` parameter to differentiate

**Recommended:** Option A — GA4 purchase should fire ONLY on the order-confirm page.

### Page Refresh on Order Confirm

**Risk Level: MEDIUM**

If user refreshes `/order-confirm/{id}`, the GA4 `purchase` event fires again.

**Mitigation:** Use `sessionStorage`:

```javascript
if (!sessionStorage.getItem('purchase_' + transaction_id)) {
    window.dataLayer.push({ event: 'purchase', ... })
    sessionStorage.setItem('purchase_' + transaction_id, '1')
}
```

### GTM + gtag.js Coexistence

**Risk Level: MEDIUM**

If both GTM and direct gtag.js are loaded simultaneously, GA4 events could fire twice.

**Mitigation:**
- During transition: keep gtag.js but stop calling `gtag('event', ...)` directly. Only push to `dataLayer`.
- After verification: remove gtag.js loading from site-layout.blade.php.

---

## 17. Edge Cases

### Failed Payments

- CheckoutForm.vue only fires Purchase after `axios.post` succeeds
- If order creation fails (500 response), no Purchase event fires
- **Safe:** Failed payments do not generate purchase events

### COD Orders

- COD orders follow the same flow as online payments
- Order is created with `payment_method: 'cod'`, `payment_status: 'unpaid'`
- Purchase event fires on order creation (not payment confirmation)
- **GA4 recommendation:** Track as purchase with `payment_type: 'COD'`

### Online Payments

- Currently only COD is implemented
- If online payment gateway is added later, Purchase should fire after payment confirmation
- **Current behavior:** Purchase fires on order creation regardless of payment method

### Abandoned Checkouts

- `begin_checkout` fires on checkout page load
- If user abandons, no `purchase` fires
- **GA4:** Use GA4's built-in checkout abandonment reporting

### Multiple Quantities

- Cart items store `quantity` field
- GA4 `items[].quantity` should reflect the actual quantity
- `value` should be `unit_price * quantity` for add_to_cart/remove_from_cart
- **Already handled correctly in existing code**

### Product Variants

- Variations stored in `product_variations` table
- `variation_label` stored on `order_products`
- GA4 `item_variant` should use `variation_label`
- **Already handled correctly in existing code**

### Discounts

- `order_products.discount` column exists
- GA4 supports `coupon` and `discount` parameters
- **Currently not tracked in GA4 events**
- **Recommendation:** Add `discount` parameter to purchase/checkout events

### Shipping

- `orders.shipping` column exists
- GA4 `shipping` parameter on purchase event
- **Currently tracked in order-confirm.blade.php**
- `add_shipping_info` event not yet tracked — recommend adding

### Taxes

- `orders.tax` column exists (currently hardcoded to 0 in cart totals)
- GA4 `tax` parameter on purchase event
- **Currently tracked in order-confirm.blade.php** (value is 0)

### Refunds

- No refund system exists in the current codebase
- **Not applicable** — no refund tracking needed

### Ad Blockers

- GTM container script may be blocked by ad blockers
- gtag.js may be blocked by ad blockers
- Meta fbevents.js may be blocked by ad blockers
- **Mitigation:** Server-side GA4 (via Measurement Protocol) could be added in future
- **Current state:** No mitigation — accepted limitation

### SPA Navigation

- The public site uses Blade (full page loads), not SPA navigation
- Each navigation is a fresh page load, so `dataLayer` is cleared naturally
- No risk of duplicate pageview events from SPA routing

---

## 18. Consent Considerations

### Current Consent System

```
Consent stored in: localStorage + cookie (tracking_consent)
Values: 'granted' | 'denied' | null (not decided)
Persistence: 1 year
Banner: shown when tracking enabled but consent not decided
```

### GTM Consent Integration

GTM has its own consent management system. Two approaches:

**Option A: Use GTM Consent Mode (Recommended)**
- Set `analytics_storage: 'granted'` when user accepts
- Set `analytics_storage: 'denied'` when user declines
- GTM tags respect these consent states
- GA4 runs in "consent mode" when denied (sends cookieless pings)

**Option B: Gate GTM loading on consent**
- Only load GTM container script after consent is granted
- Simpler but loses consent mode benefits

**Recommended:** Option A — integrate with GTM's consent initialization API.

### Implementation

In `site-layout.blade.php`, after consent is determined:

```javascript
// After consent is granted
window.dataLayer.push({
    event: 'consent_update',
    analytics_storage: 'granted'
})

// After consent is denied
window.dataLayer.push({
    event: 'consent_update',
    analytics_storage: 'denied'
})
```

In GTM, configure the GA4 Configuration tag to respect `analytics_storage`.

---

## 19. Security/Privacy Considerations

### PII Handling

- GA4 events should NOT contain PII (email, phone, name, address)
- Current GA4 events only contain product/order data — no PII
- Meta CAPI events contain hashed PII — this is Meta-specific and acceptable

### Cookie Consent

- Consent banner already implemented
- Consent is stored in localStorage + cookie
- GTM consent mode integration recommended (see Section 18)

### Data Retention

- GA4 default data retention: 2 months (configurable up to 14 months)
- No special handling needed for ShopNow

---

## 20. Implementation Sequence

### Phase 1: Foundation (No Breaking Changes)

1. **Create `resources-site/js/analytics/datalayer.js`**
   - Centralized `pushEvent(eventName, ecommerceData)` function
   - Consent-aware (checks `ShopNowTracking.hasConsent()`)
   - Falls back gracefully if dataLayer not available

2. **Add GTM Container ID setting**
   - Create migration for `analytics.gtm_container_id`
   - Update `AnalyticsGroup.vue` to include GTM input
   - Update `SettingsGroupValidate` with GTM validation rule

3. **Add GTM container to site-layout.blade.php**
   - Load GTM script in `<head>` (after existing tracking code)
   - Initialize `dataLayer` array for GTM
   - Keep existing gtag.js as fallback

### Phase 2: Event Migration (Parallel Running)

4. **Add dataLayer pushes to existing events**
   - `product-show.blade.php`: push `view_item`
   - `AddToCartButton.vue`: push `add_to_cart`
   - `CartStore.js`: push `remove_from_cart`
   - `ShoppingCart.vue`: push `view_cart`
   - `CheckoutForm.vue`: push `begin_checkout`
   - `order-confirm.blade.php`: push `purchase` with deduplication

5. **Add new events**
   - `shop.blade.php`: push `view_item_list`
   - `CheckoutForm.vue`: push `add_shipping_info`
   - `CheckoutForm.vue`: push `add_payment_info`

### Phase 3: GTM Configuration (External)

6. **Configure GTM container**
   - Create GA4 Configuration tag
   - Create all event tags with dataLayer triggers
   - Configure variables for ecommerce data
   - Test in GTM Preview mode

### Phase 4: Cleanup (After Verification)

7. **Remove direct gtag.js calls**
   - Remove `initGa()` from site-layout.blade.php
   - Remove `ShopNowTracking.trackGa()` calls from components
   - Keep `ShopNowTracking.track()` for Meta Pixel (unchanged)

---

## 21. Testing Strategy

### GTM Preview Mode

| Test | Expected Result |
|---|---|
| Visit homepage | GTM container loads, no ecommerce events |
| Visit product page | `view_item` event fires with correct product data |
| Add product to cart | `add_to_cart` event fires with correct item data |
| Visit cart page | `view_cart` event fires with all cart items |
| Remove item from cart | `remove_from_cart` event fires with removed item |
| Visit checkout | `begin_checkout` event fires with all items + total |
| Complete order (COD) | `purchase` event fires on order-confirm with transaction_id |
| Refresh order-confirm page | `purchase` does NOT fire again (deduplication) |

### GA4 DebugView

| Test | Expected Result |
|---|---|
| Enable GA4 DebugView | Events appear in real-time |
| `view_item` parameters | `currency: 'BDT'`, `value: <number>`, `items: [...]` |
| `add_to_cart` parameters | `currency: 'BDT'`, `value: <number>`, `items: [{...}]` |
| `purchase` parameters | `transaction_id: '<id>'`, `currency: 'BDT'`, `value: <total>`, `tax: <tax>`, `shipping: <shipping>` |

### Meta Pixel Verification

| Test | Expected Result |
|---|---|
| Meta Pixel Helper extension | Pixel fires on all pages |
| Events Manager | Purchase events appear with correct event_id |
| CAPI events | Server-side Purchase events match browser events |
| Deduplication | No duplicate Purchase events in Events Manager |

### Cross-Browser

| Test | Expected Result |
|---|---|
| Chrome desktop | All events fire correctly |
| Safari desktop | All events fire correctly (ITP may affect cookies) |
| Mobile Chrome | All events fire correctly |
| Mobile Safari | All events fire correctly |

### User States

| Test | Expected Result |
|---|---|
| Guest user (no consent) | No events fire |
| Guest user (consent granted) | All events fire correctly |
| Logged-in user (consent granted) | All events fire correctly |
| User declines consent | No events fire, GTM respects `analytics_storage: 'denied'` |

---

## 22. Deployment Strategy

### Pre-Deployment

1. Verify GTM container works in Preview mode
2. Verify all events fire correctly in GA4 DebugView
3. Verify Meta Pixel still works (no regression)
4. Run full test suite (`php artisan test --compact`)
5. Run `vendor/bin/pint --test`
6. Run `npm run build`

### Deployment

1. Deploy code changes (GTM container script + dataLayer pushes)
2. Publish GTM container (if ready)
3. Monitor GA4 Realtime for events
4. Monitor Meta Pixel Helper for regressions

### Post-Deployment

1. Check GA4 reports for data accuracy
2. Check Meta Events Manager for deduplication
3. Monitor for 48 hours before removing fallback code

---

## 23. Rollback Strategy

### If GTM Breaks GA4

1. Remove GTM container script from site-layout.blade.php
2. Re-enable direct gtag.js loading
3. Restore `ShopNowTracking.trackGa()` calls
4. Redeploy

### If Meta Pixel Breaks

1. Meta Pixel is independent — GTM changes should not affect it
2. If affected, remove GTM container script
3. Verify Meta Pixel works
4. Investigate GTM conflict

### Quick Rollback

The implementation is designed for easy rollback:
- All GTM changes are additive (dataLayer pushes alongside existing gtag calls)
- Removing GTM script restores previous behavior
- No existing code is deleted until Phase 4 (cleanup)

---

## 24. Future Extensibility

### Google Ads

- Add Google Ads conversion tag in GTM
- Use `purchase` event as trigger
- Map GA4 ecommerce data to Google Ads conversion parameters

### Remarketing

- Add Google Ads remarketing tag in GTM
- Use ecommerce data for dynamic remarketing audiences

### Server-Side GA4

- Implement GA4 Measurement Protocol for server-side events
- Useful for COD orders where payment confirmation is server-side
- Can be added alongside existing Meta CAPI in controllers

### Multi-Currency

- If ShopNow expands beyond BDT, GA4 supports multi-currency
- Update `currency` parameter in dataLayer pushes

### Enhanced Ecommerce

- GA4 supports additional parameters: `affiliation`, `coupon`, `promotion_id`, `promotion_name`
- Can be added incrementally as needed

---

## 25. Final Recommendations

### Priority Order

1. **Create dataLayer abstraction** (`analytics/datalayer.js`) — centralized, reusable
2. **Add GTM container to site-layout.blade.php** — foundation for all GTM tracking
3. **Migrate existing GA4 events to dataLayer pushes** — incremental, no breaking changes
4. **Add missing GA4 events** (view_item_list, add_shipping_info, add_payment_info)
5. **Configure GTM container** (external — GA4 tags, triggers, variables)
6. **Add purchase deduplication** (sessionStorage-based)
7. **Clean up old gtag.js code** (only after GTM verified working)

### Key Principles

- **Keep Meta Pixel independent** — do not move into GTM
- **Additive changes only** — dataLayer pushes alongside existing gtag calls
- **Consent-first** — all events gated by consent system
- **Centralized analytics** — one abstraction, not scattered calls
- **Test thoroughly** — GTM Preview + GA4 DebugView before production

### Estimated Effort

| Phase | Effort | Risk |
|---|---|---|
| Phase 1: Foundation | 2-3 hours | Low |
| Phase 2: Event Migration | 3-4 hours | Low |
| Phase 3: GTM Configuration | 2-3 hours (external) | Low |
| Phase 4: Cleanup | 1-2 hours | Medium |
| **Total** | **8-12 hours** | **Low overall** |

---

## Recommended Next Step

**Start with Phase 1:** Create the `resources-site/js/analytics/datalayer.js` abstraction and add the GTM container script to `site-layout.blade.php`.

First files to touch:
1. `resources-site/js/analytics/datalayer.js` (create)
2. `resources-site/views/site-layout.blade.php` (modify — add GTM script)
3. `modules/Settings/Database/Migrations/...` (create — add GTM container ID setting)
4. `resources/js/Pages/Settings/Components/AnalyticsGroup.vue` (modify — add GTM input)
5. `modules/Settings/Http/Requests/SettingsGroupValidate.php` (modify — add GTM validation)
