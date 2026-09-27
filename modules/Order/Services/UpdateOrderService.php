<?php

namespace Modules\Order\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Modules\Order\Enums\OrderStatus;
use Modules\Order\Enums\PaymentStatus;
use Modules\Order\Enums\ShipmentStatus;
use Modules\Order\Models\Order;
use Modules\Order\Models\OrderProduct;
use Modules\Product\Enums\ProductType;
use Modules\Product\Models\Product;
use Modules\Product\Models\ProductVariation;
use Modules\PromoCode\Services\CalculatePromoDiscount;
use Modules\PromoCode\Services\ValidatePromoCode;

class UpdateOrderService
{
    public function __construct(
        private DetermineShippingRequirement $determineShippingRequirement,
        private ValidatePromoCode $validatePromoCode,
        private CalculatePromoDiscount $calculatePromoDiscount,
    ) {}

    /**
     * Apply an admin order edit: customer/address snapshot, line items (with stock
     * deltas for variations and bundle children), coupon/discount and totals.
     *
     * @param  array<string, mixed>  $data
     *
     * @throws ValidationException
     */
    public function run(Order $order, array $data): Order
    {
        return DB::transaction(function () use ($order, $data) {
            $order = Order::query()->lockForUpdate()->findOrFail($order->id);

            $customerData = array_intersect_key($data, array_flip([
                'name', 'email', 'phone', 'division', 'district', 'upazila', 'union', 'address', 'country', 'notes',
            ]));

            $phoneChanged = array_key_exists('phone', $customerData)
                && (string) $customerData['phone'] !== (string) $order->phone;

            if ($this->itemsLocked($order)) {
                $this->assertLockedPayloadUnchanged($order, $data);
                $this->applyCustomer($order, $customerData, $phoneChanged);

                return $order->refresh();
            }

            if (! array_key_exists('items', $data) || $data['items'] === null) {
                $this->applyCustomer($order, $customerData, $phoneChanged);

                return $order->refresh();
            }

            $this->syncItems($order, $data['items']);
            $this->applyCustomer($order, $customerData, $phoneChanged);
            $this->recalculateTotals($order, $data);
            $this->syncShipment($order);

            return $order->refresh();
        });
    }

    /**
     * Items and totals are locked once a courier consignment exists or the
     * order has left the editable statuses.
     */
    public function itemsLocked(Order $order): bool
    {
        if ($order->orderShipments()->whereNotNull('consignment_id')->exists()) {
            return true;
        }

        return in_array($order->status, [
            OrderStatus::Shipped,
            OrderStatus::Delivered,
            OrderStatus::Completed,
            OrderStatus::Cancelled,
        ], true);
    }

    /**
     * While locked, the payload may echo current values (the edit form still
     * posts them) but must not change items or totals.
     *
     * @param  array<string, mixed>  $data
     */
    private function assertLockedPayloadUnchanged(Order $order, array $data): void
    {
        $errors = [];

        if (array_key_exists('items', $data) && $data['items'] !== null && ! $this->itemsMatch($order, (array) $data['items'])) {
            $errors['items'] = 'Line items cannot be changed after the order is shipped, completed or booked with a courier.';
        }

        foreach (['shipping', 'tax', 'discount', 'coupon_code'] as $field) {
            if (! array_key_exists($field, $data) || $data[$field] === null) {
                continue;
            }

            if (! $this->lockedFieldMatches($order, $field, $data[$field])) {
                $errors[$field] = ucfirst($field).' cannot be changed after the order is shipped, completed or booked with a courier.';
            }
        }

        if ($errors !== []) {
            throw ValidationException::withMessages($errors);
        }
    }

    private function lockedFieldMatches(Order $order, string $field, mixed $value): bool
    {
        if ($field === 'coupon_code') {
            return strtoupper(trim((string) $value)) === strtoupper(trim((string) ($order->coupon_code ?? '')));
        }

        return round((float) $value, 2) === round((float) $order->{$field}, 2);
    }

    /**
     * @param  array<int, array<string, mixed>>  $submitted
     */
    private function itemsMatch(Order $order, array $submitted): bool
    {
        $current = $order->orderProducts()
            ->get()
            ->map(fn (OrderProduct $p) => $this->itemSignature([
                'id' => $p->id,
                'product_id' => $p->product_id,
                'product_variation_id' => $p->product_variation_id,
                'quantity' => $p->quantity,
                'unit_price' => $p->unit_price,
                'discount' => $p->discount,
            ]))
            ->sort()
            ->values()
            ->all();

        $incoming = collect($submitted)
            ->map(fn ($row) => $this->itemSignature((array) $row))
            ->sort()
            ->values()
            ->all();

        return $current == $incoming;
    }

    /**
     * @param  array<string, mixed>  $row
     */
    private function itemSignature(array $row): string
    {
        return implode('|', [
            (int) ($row['id'] ?? 0),
            (int) ($row['product_id'] ?? 0),
            (int) ($row['product_variation_id'] ?? 0),
            (int) ($row['quantity'] ?? 0),
            number_format((float) ($row['unit_price'] ?? 0), 2, '.', ''),
            number_format((float) ($row['discount'] ?? 0), 2, '.', ''),
        ]);
    }

    /**
     * @param  array<int, array<string, mixed>>  $submitted
     *
     * @throws ValidationException
     */
    private function syncItems(Order $order, array $submitted): void
    {
        $existing = $order->orderProducts()->with('bundleItems')->get()->keyBy('id');

        foreach (collect($submitted)->pluck('id')->filter() as $id) {
            if (! $existing->has((int) $id)) {
                throw ValidationException::withMessages(['items' => 'One of the line items does not belong to this order.']);
            }
        }

        if ($submitted === [] && $existing->isNotEmpty()) {
            throw ValidationException::withMessages(['items' => 'An order must keep at least one item.']);
        }

        $keptIds = [];

        foreach ($submitted as $row) {
            $existingRow = ! empty($row['id']) ? $existing->get((int) $row['id']) : null;

            $this->syncLine($order, $existingRow, (array) $row);

            if ($existingRow) {
                $keptIds[] = $existingRow->id;
            }
        }

        foreach ($existing->except($keptIds) as $row) {
            $this->releaseLineStock($row);
            $row->bundleItems()->delete();
            $row->delete();
        }
    }

    /**
     * @param  array<string, mixed>  $row
     *
     * @throws ValidationException
     */
    private function syncLine(Order $order, ?OrderProduct $existingRow, array $row): void
    {
        $product = Product::with('bundleItems.childProduct')->find($row['product_id'] ?? null);

        if (! $product) {
            throw ValidationException::withMessages(['items' => 'Selected product no longer exists.']);
        }

        $variation = null;
        $variationId = $row['product_variation_id'] ?? null;

        if ($variationId) {
            if ($product->type === ProductType::Bundle) {
                throw ValidationException::withMessages(['items' => 'Bundle products cannot have a variation.']);
            }

            $variation = ProductVariation::with('attributeValues.attribute')
                ->find($variationId);

            if (! $variation
                || $variation->product_id !== $product->id
                || ! $variation->active
                || $variation->trashed()) {
                throw ValidationException::withMessages(['items' => 'Selected variation is not available for "'.$product->name.'".']);
            }
        } elseif ($product->type === ProductType::Variable) {
            throw ValidationException::withMessages(['items' => 'Please select a variation for "'.$product->name.'".']);
        }

        $quantity = (int) $row['quantity'];
        $unitPrice = round((float) ($row['unit_price'] ?? 0), 2);
        $lineDiscount = round((float) ($row['discount'] ?? 0), 2);

        $attributes = [
            'product_id' => $product->id,
            'product_variation_id' => $variation?->id,
            'variation_label' => $variation?->label(),
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'discount' => $lineDiscount,
            'total_price' => round($unitPrice * $quantity - $lineDiscount, 2),
        ];

        // Restock what the old line held, then apply the new line's stock.
        $this->releaseLineStock($existingRow);
        $this->consumeLineStock($product, $variation, $quantity);

        if ($existingRow) {
            $existingRow->update($attributes);
            $orderProduct = $existingRow;
        } else {
            $orderProduct = $order->orderProducts()->create($attributes);
        }

        if ($product->type === ProductType::Bundle) {
            $this->syncBundleSnapshots($orderProduct, $product);
        } else {
            $orderProduct->bundleItems()->delete();
        }
    }

    private function releaseLineStock(?OrderProduct $row): void
    {
        if (! $row) {
            return;
        }

        if ($row->product_variation_id) {
            ProductVariation::withTrashed()->whereKey($row->product_variation_id)
                ->increment('quantity', (int) $row->quantity);
        }

        foreach ($row->bundleItems as $snapshot) {
            if ($snapshot->product_id) {
                Product::withTrashed()->whereKey($snapshot->product_id)
                    ->increment('quantity', (int) $snapshot->quantity);
            }
        }
    }

    /**
     * @throws ValidationException
     */
    private function consumeLineStock(Product $product, ?ProductVariation $variation, int $quantity): void
    {
        if ($variation) {
            $variation->refresh();

            if ($variation->quantity < $quantity) {
                throw ValidationException::withMessages([
                    'items' => sprintf(
                        'Insufficient stock for "%s" — only %d available.',
                        trim($product->name.' '.($variation->label() ?? '')),
                        $variation->quantity,
                    ),
                ]);
            }

            $variation->decrement('quantity', $quantity);
        }

        if ($product->type !== ProductType::Bundle) {
            return;
        }

        foreach ($product->bundleItems as $bundleItem) {
            $child = $bundleItem->childProduct;

            if (! $child) {
                continue;
            }

            $amount = (int) $bundleItem->quantity * $quantity;

            if ($child->quantity < $amount) {
                throw ValidationException::withMessages([
                    'items' => sprintf('Insufficient stock for bundle child "%s" — only %d available.', $child->name, $child->quantity),
                ]);
            }

            $child->decrement('quantity', $amount);
        }
    }

    /**
     * Recreate bundle child snapshots for the line's current quantity,
     * mirroring checkout (SiteOrderController@store).
     */
    private function syncBundleSnapshots(OrderProduct $orderProduct, Product $product): void
    {
        $orderProduct->bundleItems()->delete();

        $quantity = (int) $orderProduct->quantity;

        foreach ($product->bundleItems()->with('childProduct')->get() as $bundleItem) {
            $childUnitPrice = (float) ($bundleItem->price_override
                ?? $bundleItem->childProduct?->sale_price
                ?? $bundleItem->childProduct?->price
                ?? 0);

            $orderProduct->bundleItems()->create([
                'product_id' => $bundleItem->child_product_id,
                'product_variation_id' => $bundleItem->child_product_variation_id,
                'name' => $bundleItem->childProduct?->name ?? "Product #{$bundleItem->child_product_id}",
                'sku' => $bundleItem->childProduct?->sku,
                'quantity' => $bundleItem->quantity * $quantity,
                'unit_price' => $childUnitPrice,
                'total_price' => $childUnitPrice * $bundleItem->quantity * $quantity,
            ]);
        }
    }

    /**
     * @param  array<string, mixed>  $customerData
     */
    private function applyCustomer(Order $order, array $customerData, bool $phoneChanged): void
    {
        if ($customerData !== []) {
            $order->update($customerData);
        }

        if ($phoneChanged) {
            $order->update([
                'fraud_checked_at' => null,
                'fraud_risk' => null,
                'fraud_details' => null,
            ]);
        }
    }

    /**
     * Recompute subtotal/discount/total/due from the synced lines.
     * A coupon revalidates against promo rules (usage counts exclude this order);
     * without a coupon the manual discount applies.
     *
     * @param  array<string, mixed>  $data
     *
     * @throws ValidationException
     */
    private function recalculateTotals(Order $order, array $data): void
    {
        $subtotal = round((float) $order->orderProducts()->sum('total_price'), 2);

        $couponInput = array_key_exists('coupon_code', $data)
            ? trim((string) $data['coupon_code'])
            : (string) ($order->coupon_code ?? '');

        $couponCode = null;
        $discount = 0.0;
        $shipping = round((float) ($data['shipping'] ?? $order->shipping), 2);

        if ($couponInput !== '') {
            $promoCode = $this->validatePromoCode->run(
                $couponInput,
                $subtotal,
                $order->customer_id,
                lock: true,
                excludeOrderId: $order->id,
            );

            $discount = $this->calculatePromoDiscount->run($promoCode, $subtotal);
            $couponCode = $promoCode->code;

            if ($promoCode->discount_type->isFreeShipping()) {
                $shipping = 0.0;
            }
        } else {
            $discount = min(round((float) ($data['discount'] ?? $order->discount), 2), $subtotal);
        }

        $tax = round((float) ($data['tax'] ?? $order->tax), 2);
        $requiresShipping = $this->determineShippingRequirement->run($order);

        if (! $requiresShipping) {
            $shipping = 0.0;
        }

        $total = round($subtotal + $tax + $shipping - $discount, 2);
        $paid = (float) $order->paid;
        $due = max(0, round($total - $paid, 2));

        $order->update([
            'requires_shipping' => $requiresShipping,
            'subtotal' => $subtotal,
            'shipping' => $shipping,
            'tax' => $tax,
            'discount' => $discount,
            'coupon_code' => $couponCode,
            'total' => $total,
            'due' => $due,
            'payment_status' => $paid >= $total ? PaymentStatus::Paid : PaymentStatus::Unpaid,
        ]);
    }

    /**
     * Keep the pending shipment row in sync with requires_shipping.
     * Booked consignments never reach this (items are locked).
     */
    private function syncShipment(Order $order): void
    {
        $shipments = $order->orderShipments();

        if ($order->requires_shipping) {
            if (! $shipments->exists()) {
                $shipments->create(['shopment_status' => ShipmentStatus::Pending]);
            }

            return;
        }

        $shipments->whereNull('consignment_id')->delete();
    }
}
