<?php

namespace Modules\Settings\Models;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Settings\Database\Factories\SmsMessageFactory;

class SmsMessage extends Model
{
    use HasFactory;

    protected static function newFactory(): Factory
    {
        return SmsMessageFactory::new();
    }

    protected $guarded = ['id'];

    protected $hidden = ['phone', 'body'];

    protected function casts(): array
    {
        return [
            'phone' => 'encrypted', 'body' => 'encrypted', 'expires_at' => 'datetime',
            'queued_at' => 'datetime', 'started_at' => 'datetime', 'finished_at' => 'datetime', 'attempts' => 'integer',
        ];
    }

    /** @return array<string, mixed> */
    public function summary(): array
    {
        return [
            'id' => $this->id, 'purpose' => $this->purpose, 'status' => $this->status,
            'recipient' => substr($this->phone, 0, 3).'*****'.substr($this->phone, -3),
            'order_id' => $this->order_id, 'provider' => $this->provider,
            'attempts' => $this->attempts, 'error' => $this->error,
            'created_at' => $this->created_at->toIso8601String(),
        ];
    }
}
