<?php

namespace Modules\PromoCode\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Inertia\Response;
use Modules\Order\Models\Order;
use Modules\PromoCode\Http\Requests\PromoCodeValidate;
use Modules\PromoCode\Models\PromoCode;
use Modules\Support\Http\Controllers\BackendController;

class PromoCodeController extends BackendController
{
    public function index(): Response
    {
        $promoCodes = PromoCode::latest()
            ->search(request('searchContext'), request('searchTerm'))
            ->paginate(request('rowsPerPage', 10))
            ->withQueryString();

        $usageCounts = $this->usageCounts();

        $promoCodes->through(fn (PromoCode $promoCode) => $this->mapRow($promoCode, $usageCounts[$promoCode->code] ?? 0));

        return inertia('PromoCode/PromoCodeIndex', [
            'promoCodes' => $promoCodes,
        ]);
    }

    public function create(): Response
    {
        return inertia('PromoCode/PromoCodeForm');
    }

    public function store(PromoCodeValidate $request): RedirectResponse
    {
        PromoCode::create($request->validated());

        return redirect()->route('promoCode.index')
            ->with('success', 'Promo code created.');
    }

    public function edit(int $id): Response
    {
        $promoCode = PromoCode::findOrFail($id);

        return inertia('PromoCode/PromoCodeForm', [
            'promoCode' => $this->mapForm($promoCode),
        ]);
    }

    public function update(PromoCodeValidate $request, int $id): RedirectResponse
    {
        $promoCode = PromoCode::findOrFail($id);

        $promoCode->update($request->validated());

        return redirect()->route('promoCode.index')
            ->with('success', 'Promo code updated.');
    }

    public function destroy(int $id): RedirectResponse
    {
        PromoCode::findOrFail($id)->delete();

        return redirect()->route('promoCode.index')
            ->with('success', 'Promo code deleted.');
    }

    public function recycleBin(): Response
    {
        $promoCodes = PromoCode::onlyTrashed()
            ->latest('id')
            ->search(request('searchContext'), request('searchTerm'))
            ->paginate(request('rowsPerPage', 10))
            ->withQueryString()
            ->through(fn (PromoCode $promoCode) => [
                'id' => $promoCode->id,
                'code' => $promoCode->code,
                'discount_type' => $promoCode->discount_type->value,
                'discount_value' => (float) $promoCode->discount_value,
                'active' => $promoCode->active,
                'deleted_at' => $promoCode->deleted_at ? Carbon::parse($promoCode->deleted_at)->format('d/m/Y') : null,
            ]);

        return inertia('PromoCode/PromoCodeRecycleBin', [
            'promoCodes' => $promoCodes,
        ]);
    }

    public function restore(int $id): RedirectResponse
    {
        PromoCode::onlyTrashed()->findOrFail($id)->restore();

        return redirect()->route('promoCode.recycleBin.index')
            ->with('success', 'Promo code restored.');
    }

    public function destroyForce(int $id): RedirectResponse
    {
        $promoCode = PromoCode::onlyTrashed()->findOrFail($id);

        $promoCode->forceDelete();

        return redirect()->route('promoCode.recycleBin.index')
            ->with('success', 'Promo code deleted.');
    }

    public function emptyRecycleBin(): RedirectResponse
    {
        PromoCode::onlyTrashed()->each(fn (PromoCode $promoCode) => $promoCode->forceDelete());

        return redirect()->route('promoCode.recycleBin.index')
            ->with('success', 'Recycle bin emptied.');
    }

    public function restoreRecycleBin(): RedirectResponse
    {
        PromoCode::onlyTrashed()->restore();

        return redirect()->route('promoCode.recycleBin.index')
            ->with('success', 'Promo codes restored.');
    }

    /**
     * Total orders per coupon code in one query (avoids per-row COUNT queries).
     *
     * @return array<string, int>
     */
    private function usageCounts(): array
    {
        return Order::query()
            ->whereNotNull('coupon_code')
            ->selectRaw('coupon_code, COUNT(*) as usage_count')
            ->groupBy('coupon_code')
            ->pluck('usage_count', 'coupon_code')
            ->map(fn ($count) => (int) $count)
            ->all();
    }

    /**
     * @return array<string, mixed>
     */
    private function mapRow(PromoCode $promoCode, int $usedCount): array
    {
        return [
            'id' => $promoCode->id,
            'code' => $promoCode->code,
            'discount_type' => $promoCode->discount_type->value,
            'discount_value' => (float) $promoCode->discount_value,
            'minimum_order_amount' => $promoCode->minimum_order_amount !== null ? (float) $promoCode->minimum_order_amount : null,
            'maximum_discount_amount' => $promoCode->maximum_discount_amount !== null ? (float) $promoCode->maximum_discount_amount : null,
            'usage_limit' => $promoCode->usage_limit,
            'per_customer_limit' => $promoCode->per_customer_limit,
            'starts_at' => $promoCode->starts_at?->format('d M Y, h:i A'),
            'expires_at' => $promoCode->expires_at?->format('d M Y, h:i A'),
            'active' => $promoCode->active,
            'used_count' => $usedCount,
            'status' => $promoCode->status($usedCount),
        ];
    }

    /**
     * Form-friendly payload (datetime-local values for the edit form).
     *
     * @return array<string, mixed>
     */
    private function mapForm(PromoCode $promoCode): array
    {
        $usedCount = $promoCode->usedCount();

        return array_merge($this->mapRow($promoCode, $usedCount), [
            'starts_at' => $promoCode->starts_at?->format('Y-m-d\TH:i'),
            'expires_at' => $promoCode->expires_at?->format('Y-m-d\TH:i'),
        ]);
    }
}
