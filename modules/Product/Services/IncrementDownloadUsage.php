<?php

namespace Modules\Product\Services;

use Modules\Product\Models\DownloadPermission;

class IncrementDownloadUsage
{
    public function run(DownloadPermission $permission): void
    {
        $permission->increment('download_count');

        if ($permission->first_downloaded_at === null) {
            $permission->first_downloaded_at = now();
        }

        $permission->last_downloaded_at = now();
        $permission->saveQuietly();
    }
}
