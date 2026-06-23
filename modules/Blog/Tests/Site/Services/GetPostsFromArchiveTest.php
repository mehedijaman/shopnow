<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Pagination\LengthAwarePaginator;
use Modules\Blog\Models\Post;
use Modules\Blog\Services\Site\GetPostsFromArchive;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

test('get method returns posts from archive', function () {
    $archiveDate = '02-2022';

    Post::factory()->create(['published_at' => '2022-02-01']);
    Post::factory()->create(['published_at' => '2022-01-01']);
    Post::factory()->create(['published_at' => '2021-12-01']);

    $service = new GetPostsFromArchive;
    $posts = $service->get($archiveDate);

    $this->assertInstanceOf(LengthAwarePaginator::class, $posts);
    $this->assertSame(1, $posts->count());
    $this->assertSame('2022-02-01', $posts->first()->published_at->format('Y-m-d'));
});
