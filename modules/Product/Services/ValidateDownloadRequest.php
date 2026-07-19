<?php

namespace Modules\Product\Services;

use Modules\Product\Models\DownloadPermission;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ValidateDownloadRequest
{
    public function run(string $token): DownloadPermission
    {
        $permission = DownloadPermission::with('productFile.media')
            ->where('download_token', $token)
            ->first();

        if (! $permission) {
            throw new NotFoundHttpException('Download link not found.');
        }

        if (! $permission->active) {
            throw new NotFoundHttpException('This download has been revoked.');
        }

        if ($permission->expires_at && $permission->expires_at->isPast()) {
            throw new NotFoundHttpException('This download link has expired.');
        }

        if ($permission->download_limit !== null && $permission->download_count >= $permission->download_limit) {
            throw new NotFoundHttpException('Download limit reached.');
        }

        return $permission;
    }
}
