<?php

namespace Modules\Profile\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Support\Models\BaseModel;
use Modules\Support\Traits\ActivityLog;
use Modules\Support\Traits\Searchable;

class Profile extends BaseModel
{
    use ActivityLog, Searchable, SoftDeletes;

    protected $table = 'profiles';

    protected $fillable = [
        'user_id', 'bio', 'phone', 'address', 'avatar', 'social_links',
    ];
}
