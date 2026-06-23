<?php

namespace Modules\Settings\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Modules\Support\Models\BaseModel;
use Modules\Support\Traits\ActivityLog;

class Setting extends BaseModel
{
    use ActivityLog;

    protected $table = 'settings';

    protected $fillable = [
        'group', 'key', 'value', 'type', 'label', 'description', 'is_public', 'sort_order',
    ];

    protected $casts = [
        'is_public' => 'boolean',
        'sort_order' => 'integer',
    ];

    protected $appends = ['value_url'];

    /**
     * Cast the value attribute based on the setting type.
     */
    protected function value(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                return match ($this->getRawOriginal('type')) {
                    'boolean' => (bool) $value,
                    'repeater', 'json' => is_string($value)
                        ? (json_decode($value, true) ?? [])
                        : ($value ?? []),
                    default => $value,
                };
            }
        );
    }

    /**
     * Generate asset URL for image-type settings.
     */
    public function getValueUrlAttribute(): ?string
    {
        if ($this->getRawOriginal('type') === 'image' && $this->getRawOriginal('value')) {
            return asset('storage/settings/'.$this->getRawOriginal('value'));
        }

        return null;
    }
}
