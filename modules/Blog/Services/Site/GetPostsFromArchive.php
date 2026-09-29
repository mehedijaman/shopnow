<?php

namespace Modules\Blog\Services\Site;

use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Modules\Blog\Models\Post;

class GetPostsFromArchive
{
    public function get(string $archiveDate): LengthAwarePaginator
    {
        // The `!` prefix resets unfilled fields to epoch values, so the missing
        // day defaults to 1 instead of today's date (which overflows in short
        // months, e.g. Feb on the 29th).
        $archiveDateCarbon = Carbon::createFromFormat('!m-Y', $archiveDate);
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
