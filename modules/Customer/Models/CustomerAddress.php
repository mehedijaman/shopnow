<?php

namespace Modules\Customer\Models;

use Modules\Support\Models\BaseModel;
use Modules\Support\Traits\Searchable;
use Modules\Support\Traits\ActivityLog;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Customer\Database\Factories\CustomerAddressFactory;

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
