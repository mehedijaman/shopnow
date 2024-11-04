<?php

namespace Modules\Cart\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Support\Models\BaseModel;
use Modules\Support\Traits\ActivityLog;
use Modules\Support\Traits\Searchable;

class Cart extends BaseModel
{
    use ActivityLog, Searchable, SoftDeletes;

    protected $table = 'carts';
}
