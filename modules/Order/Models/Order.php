<?php

namespace Modules\Order\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Support\Models\BaseModel;
use Modules\Support\Traits\Searchable;
use Modules\Support\Traits\ActivityLog;

class Order extends BaseModel
{
    use ActivityLog, Searchable, SoftDeletes;
    
    protected $table = 'orders';
}
