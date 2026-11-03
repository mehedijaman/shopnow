<?php

namespace App\Console\Commands;

use App\Jobs\SendSms;
use Illuminate\Console\Command;
use Modules\Customer\Models\PhoneVerification;
use Modules\Settings\Models\SmsMessage;

class MaintainSms extends Command
{
    protected $signature = 'sms:maintain';

    protected $description = 'Recover pending SMS work, expire stale sends and prune sensitive records';

    public function handle(): int
    {
        SmsMessage::query()->where('status', 'processing')->where('started_at', '<', now()->subMinutes(2))
            ->update(['status' => 'unknown', 'error' => 'worker_interrupted', 'body' => null, 'finished_at' => now()]);
        SmsMessage::query()->where('status', 'pending')->where('expires_at', '<=', now())
            ->update(['status' => 'expired', 'body' => null, 'finished_at' => now()]);
        SmsMessage::query()->where('status', 'pending')->where('created_at', '<', now()->subMinute())
            ->where(fn ($query) => $query->whereNull('queued_at')->orWhere('queued_at', '<', now()->subMinutes(15)))
            ->orderBy('id')->limit(500)->get()->each(function (SmsMessage $message): void {
                try {
                    SendSms::dispatch($message->id)->onQueue(config($message->purpose === 'otp' ? 'sms.otp_queue' : 'sms.queue'));
                    $message->update(['queued_at' => now()]);
                } catch (\Throwable) {
                    logger()->warning('SMS recovery queue unavailable.', ['sms_message_id' => $message->id]);
                }
            });
        SmsMessage::query()->where('created_at', '<', now()->subDays((int) config('sms.retention_days', 30)))->delete();
        PhoneVerification::query()->where('expires_at', '<', now()->subDay())->delete();

        return self::SUCCESS;
    }
}
