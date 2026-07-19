<?php

namespace Modules\Blog\Services\Site;

use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Modules\Blog\Models\Post;

class GetPostsFromArchive
{
    public function get(string $archiveDate): LengthAwarePaginator
    {
        $archiveDateCarbon = Carbon::createFromFormat('m-Y', $archiveDate);
        $startOfMonth = $archiveDateCarbon->startOfMonth()->toDateString();
        $endOfMonth = $archiveDateCarbon->endOfMonth()->toDateString();

        $posts = Post::with(['tags', 'author'])
            ->whereDate('published_at', '>=', $startOfMonth)
            ->whereDate('published_at', '<=', $endOfMonth)
            ->latest()
            ->paginate(6);

        return $posts;
    }
}
