<?php

namespace Modules\Settings\Services;

use App\Jobs\SendSms;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Modules\Settings\Models\SmsMessage;

class SmsMessages
{
    /** @param array<string, mixed> $attributes */
    public function create(string $eventKey, string $purpose, string $phone, string $body, array $attributes = []): SmsMessage
    {
        $message = SmsMessage::query()->firstOrCreate(['event_key' => $eventKey], array_merge([
            'reference' => (string) Str::uuid(), 'purpose' => $purpose,
            'phone' => $phone, 'body' => $body, 'status' => 'pending', 'expires_at' => now()->addDay(),
        ], $attributes));
        if ($message->wasRecentlyCreated) {
            $id = $message->id;
            DB::afterCommit(static function () use ($id, $purpose): void {
                try {
                    SendSms::dispatch($id)->onQueue(config($purpose === 'otp' ? 'sms.otp_queue' : 'sms.queue'));
                    SmsMessage::query()->whereKey($id)->where('status', 'pending')->update(['queued_at' => now()]);
                } catch (\Throwable) {
                    logger()->warning('SMS queue unavailable; pending message retained.', ['sms_message_id' => $id]);
                }
            });
        }

        return $message;
    }
}
