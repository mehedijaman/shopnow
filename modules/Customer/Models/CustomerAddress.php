<?php

namespace Modules\Customer\Models;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Customer\Database\Factories\CustomerAddressFactory;
use Modules\Support\Models\BaseModel;
use Modules\Support\Traits\ActivityLog;
use Modules\Support\Traits\Searchable;

class CustomerAddress extends BaseModel
{
    use ActivityLog, HasFactory, Searchable, SoftDeletes;

    protected $table = 'customer_addresses';

    protected $casts = [
        'default' => 'boolean',
    ];

    protected static function newFactory(): Factory
    {
        return CustomerAddressFactory::new();
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
