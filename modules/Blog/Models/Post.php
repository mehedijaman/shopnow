<?php

namespace Modules\Blog\Models;

use Carbon\Carbon;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Blog\Database\Factories\BlogPostFactory;
use Modules\Support\Models\BaseModel;
use Modules\Support\Traits\ActivityLog;
use Modules\Support\Traits\Searchable;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Post extends BaseModel implements HasMedia
{
    use ActivityLog, HasFactory, InteractsWithMedia, Searchable, Sluggable, SoftDeletes;

    protected $table = 'blog_posts';

    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];

    protected $appends = ['image_url'];

    protected $casts = [
        'published_at' => 'datetime:Y-m-d',
    ];

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'blog_posts_tags', 'blog_post_id', 'blog_tag_id');
    }

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'title',
            ],
        ];
    }

    public function getStatusAttribute(): string
    {
        if ($this->published_at and Carbon::now()->greaterThan($this->published_at)) {
            return 'Published';
        }

        return 'Draft';
    }

    public function getImageUrlAttribute(): ?string
    {
        return $this->getFirstMediaUrl('image') ?: null;
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('image')->singleFile();
    }

    protected static function newFactory(): Factory
    {
        return BlogPostFactory::new();
    }

    public function author()
    {
        return $this->belongsTo(Author::class, 'blog_author_id');
    }
}
