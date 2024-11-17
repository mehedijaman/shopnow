<?php

namespace Modules\Order\Http\Requests;

use Devfaysal\BangladeshGeocode\Models\District;
use Devfaysal\BangladeshGeocode\Models\Division;
use Devfaysal\BangladeshGeocode\Models\Union;
use Illuminate\Validation\Rule;
use Modules\Support\Http\Requests\Request;

class SiteOrderValidate extends Request
{
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|string|max:255',
            'phone' => 'required|string|max:255',

            'division_id' => [
                'nullable',
                'integer',
                Rule::exists(Division::class, 'id'),
            ],
            'district_id' => [
                'nullable',
                'integer',
                Rule::exists(District::class, 'id'),
            ],
            'upazila_id' => [
                'nullable',
                'integer',
                Rule::exists(District::class, 'id'),
            ],
            'union_id' => [
                'nullable',
                'integer',
                Rule::exists(Union::class, 'id'),
            ],
            'address' => 'required|string|max:255',
            'country' => 'nullable|string|max:255',
            'subtotal' => 'required|numeric',
            'tax' => 'required|numeric',
            'shipping' => 'required|numeric',
            'total' => 'required|numeric',
            'paid' => 'required|numeric',
            'due' => 'required|numeric',
            'payment_method' => [
                'required',
                Rule::in(['cod', 'sslcommerz']),
            ],
        ];
    }
}
