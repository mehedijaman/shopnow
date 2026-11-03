<?php

namespace Modules\Customer\Models;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Customer\Database\Factories\PhoneVerificationFactory;

class PhoneVerification extends Model
{
    use HasFactory;

    protected static function newFactory(): Factory
    {
        return PhoneVerificationFactory::new();
    }

    protected $guarded = ['id'];

    protected $hidden = ['phone', 'digest', 'nonce'];

    protected function casts(): array
    {
        return [
            'phone' => 'encrypted', 'attempts' => 'integer', 'expires_at' => 'datetime',
            'consumed_at' => 'datetime', 'last_sent_at' => 'datetime',
        ];
    }
}
