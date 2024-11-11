<?php

namespace Modules\ContactMessage\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Support\Models\BaseModel;
use Modules\Support\Traits\ActivityLog;
use Modules\Support\Traits\Searchable;

class ContactMessage extends BaseModel
{
    use ActivityLog, Searchable, SoftDeletes;

    protected $table = 'contact_messages';
}
