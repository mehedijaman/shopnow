<?php

namespace Modules\Product\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Modules\Product\Models\DownloadPermission;
use Modules\Support\Http\Controllers\BackendController;

class ProductPermissionController extends BackendController
{
    public function toggle(DownloadPermission $id): JsonResponse
    {
        $id->update([
            'active' => ! $id->active,
        ]);

        return response()->json([
            'success' => true,
            'active' => $id->fresh()->active,
            'message' => 'Download permission '.($id->fresh()->active ? 'activated' : 'revoked').'.',
        ]);
    }
}
