<?php

namespace Modules\Customer\Models;

use Modules\Support\Models\BaseModel;
use Modules\Support\Traits\Searchable;
use Modules\Support\Traits\ActivityLog;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Modules\Customer\Database\Factories\CustomerFactory;

class Customer extends BaseModel
{
    use ActivityLog, HasFactory, Searchable, SoftDeletes, Notifiable;

    protected $table = 'customers';

    protected $casts = [
        'active' => 'boolean',
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    protected static function newFactory(): Factory
    {
        return CustomerFactory::new();
    }

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPassword($token));
    }

    public function addresses()
    {
        return $this->hasMany(CustomerAddress::class);
    }
}
