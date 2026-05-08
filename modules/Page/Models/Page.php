<?php

namespace Modules\Page\Models;

use Carbon\Carbon;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Page\Database\Factories\PageFactory;
use Modules\Support\Models\BaseModel;
use Modules\Support\Traits\ActivityLog;
use Modules\Support\Traits\Searchable;

class Page extends BaseModel
{
    use ActivityLog, HasFactory, Searchable, Sluggable, SoftDeletes;

    protected $table = 'pages';

    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];

    protected $casts = [
        'published_at' => 'date',
        'is_system' => 'boolean',
    ];

    protected $appends = ['image_url'];

    public function getPublishedAtFormattedAttribute()
    {
        return $this->published_at ? $this->published_at->format('d/m/Y') : null;
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
        if ($this->image) {
            return asset("storage/page/{$this->image}");
        }

        return null;
    }

    protected static function newFactory(): Factory
    {
        return PageFactory::new();
    }

    public function parent()
    {
        return $this->belongsTo(Page::class, 'parent_id');
    }
}
