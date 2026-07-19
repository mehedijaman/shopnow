<?php

namespace Modules\Product\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Product\Models\DownloadPermission;
use Modules\Product\Services\IncrementDownloadUsage;
use Modules\Product\Services\ValidateDownloadRequest;
use Modules\Support\Http\Controllers\SiteController;

class DownloadController extends SiteController
{
    public function show(
        string $token,
        ValidateDownloadRequest $validateDownloadRequest,
        IncrementDownloadUsage $incrementDownloadUsage,
    ) {
        $permission = $validateDownloadRequest->run($token);

        $incrementDownloadUsage->run($permission);

        $media = $permission->productFile->media;

        if ($media?->getDiskDriverName() === 's3') {
            return redirect()->away($media->getTemporaryUrl(now()->addMinutes(5)));
        }

        return $media?->toResponse(request());
    }

    public function index(Request $request)
    {
        $customer = Auth::guard('customer')->user();

        $permissions = DownloadPermission::with(['product', 'productFile'])
            ->where('customer_id', $customer->id)
            ->orderByDesc('created_at')
            ->get()
            ->map(fn ($p) => [
                'id' => $p->id,
                'product_name' => $p->product?->name ?? 'Product #'.$p->product_id,
                'file_name' => $p->productFile?->name ?? 'File #'.$p->product_file_id,
                'download_count' => $p->download_count,
                'download_limit' => $p->download_limit,
                'expires_at' => $p->expires_at?->format('d M Y'),
                'active' => $p->active,
                'created_at' => $p->created_at->format('d M Y'),
                'download_url' => $p->active ? route('product.download', $p->download_token) : null,
            ]);

        return view('product::account-downloads', [
            'permissions' => $permissions,
            'customer' => $customer,
        ]);
    }
}
