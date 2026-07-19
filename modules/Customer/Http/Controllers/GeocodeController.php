<?php

namespace Modules\Customer\Http\Controllers;

use Devfaysal\BangladeshGeocode\Models\District;
use Devfaysal\BangladeshGeocode\Models\Division;
use Devfaysal\BangladeshGeocode\Models\Union;
use Devfaysal\BangladeshGeocode\Models\Upazila;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Support\Http\Controllers\AppController;

class GeocodeController extends AppController
{
    public function divisions(): JsonResponse
    {
        return response()->json(Division::orderBy('name')->get());
    }

    public function districts(Request $request): JsonResponse
    {
        $divisionId = $request->query('division_id');

        $query = District::orderBy('name');
        if ($divisionId) {
            $query->where('division_id', $divisionId);
        }

        return response()->json($query->get());
    }

    public function upazilas(Request $request): JsonResponse
    {
        $districtId = $request->query('district_id');
        if (! $districtId) {
            return response()->json([]);
        }

        return response()->json(
            Upazila::where('district_id', $districtId)->orderBy('name')->get()
        );
    }

    public function unions(Request $request): JsonResponse
    {
        $upazilaId = $request->query('upazila_id');
        if (! $upazilaId) {
            return response()->json([]);
        }

        return response()->json(
            Union::where('upazila_id', $upazilaId)->orderBy('name')->get()
        );
    }
}
