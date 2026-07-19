<?php

namespace Modules\Customer\Models;

use Devfaysal\BangladeshGeocode\Models\District;
use Devfaysal\BangladeshGeocode\Models\Division;
use Devfaysal\BangladeshGeocode\Models\Union;
use Devfaysal\BangladeshGeocode\Models\Upazila;
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

    protected $fillable = [
        'customer_id', 'division_id', 'district_id', 'upazilla_id', 'union_id', 'address', 'country', 'default',
    ];

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

    public function division()
    {
        return $this->belongsTo(Division::class, 'division_id');
    }

    public function district()
    {
        return $this->belongsTo(District::class, 'district_id');
    }

    public function upazila()
    {
        return $this->belongsTo(Upazila::class, 'upazilla_id');
    }

    public function union()
    {
        return $this->belongsTo(Union::class, 'union_id');
    }
}
