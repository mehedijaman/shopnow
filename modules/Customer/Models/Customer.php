<?php

namespace Modules\Customer\Models;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Modules\Customer\Database\Factories\CustomerFactory;
use Modules\Support\Traits\ActivityLog;
use Modules\Support\Traits\Searchable;

class Customer extends Authenticatable
{
    use ActivityLog, HasFactory, Notifiable, Searchable, SoftDeletes;

    protected $table = 'customers';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
    ];

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
