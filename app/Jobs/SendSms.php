<?php

namespace App\Jobs;

use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\Middleware\RateLimited;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Modules\Customer\Models\Customer;
use Modules\Customer\Models\PhoneVerification;
use Modules\Settings\Models\SmsMessage;
use Modules\Settings\Services\SmsGateway;
use Modules\Settings\Services\SmsSettings;
use Throwable;

class SendSms implements ShouldQueue
{
    use Queueable;

    public int $tries = 25;

    public int $timeout = 30;

    private bool $claimed = false;

    public function __construct(public int $messageId)
    {
        $this->onQueue(config('sms.queue', 'sms'));
    }

    public function middleware(): array
    {
        return [new RateLimited('sms-provider')];
    }

    public function handle(SmsSettings $settings, SmsGateway $gateway): void
    {
        $message = SmsMessage::find($this->messageId);
        if (! $message || $message->status !== 'pending') {
            return;
        }
        if ($message->purpose === 'otp') {
            $lock = Cache::lock('sms-otp-customer:'.$message->customer_id, 60);
            if (! $lock->get()) {
                $this->release(5);

                return;
            }
            try {
                $customer = Customer::query()->find($message->customer_id);
                $challenge = PhoneVerification::query()->find($message->verification_id);
                if (! $customer || ! $challenge || $challenge->consumed_at || $challenge->expires_at->isPast()
                    || $customer->phone !== $challenge->phone || $message->event_key !== 'otp:'.$challenge->nonce) {
                    $this->finish($message, 'expired');

                    return;
                }
                $this->send($message, $settings, $gateway);
            } finally {
                $lock->release();
            }

            return;
        }
        $this->send($message, $settings, $gateway);
    }

    private function send(SmsMessage $message, SmsSettings $settings, SmsGateway $gateway): void
    {
        if ($message->order_id && SmsMessage::query()->where('order_id', $message->order_id)
            ->where('id', '<', $message->id)->whereIn('status', ['pending', 'processing'])->exists()) {
            $this->release(10);

            return;
        }
        if ($message->expires_at->isPast()) {
            $this->finish($message, 'expired');

            return;
        }
        $values = $settings->values();
        $enabled = $values['enabled'] && match ($message->purpose) {
            'order_placed' => $values['order_placed'],
            'order_updated' => $values['order_updated'] && in_array($message->order_status, $values['statuses'], true),
            default => true,
        };
        if ($message->purpose !== 'test' && ! $enabled) {
            $this->finish($message, 'cancelled');

            return;
        }
        $provider = $values['provider'];
        if (! SmsSettings::canSend($provider)) {
            $this->finish($message, 'rejected', 'provider_unavailable');

            return;
        }
        try {
            $credentials = $settings->credentials($provider);
            foreach (SmsSettings::PROVIDERS[$provider]['fields'] as $field) {
                if (blank($credentials[$field] ?? null)) {
                    $this->finish($message, 'rejected', 'configuration_missing');

                    return;
                }
            }
        } catch (Throwable) {
            $this->finish($message, 'rejected', 'configuration_unavailable');

            return;
        }
        $claimed = SmsMessage::query()->whereKey($message->id)->where('status', 'pending')->update([
            'status' => 'processing', 'started_at' => now(), 'provider' => $provider,
            'attempts' => DB::raw('attempts + 1'),
        ]);
        if (! $claimed) {
            return;
        }
        $this->claimed = true;
        try {
            $result = $gateway->send($provider, $credentials, $message->phone, $message->body, $message->reference);
            $message->provider_id = $result['provider_id'];
            $this->finish($message, $result['status'], $result['error']);
        } catch (GuzzleException) {
            $this->finish($message, 'unknown', 'transport_outcome_unknown');
        } catch (Throwable) {
            $this->finish($message, 'unknown', 'provider_outcome_unknown');
        }
    }

    private function finish(SmsMessage $message, string $status, ?string $error = null): void
    {
        SmsMessage::query()->whereKey($message->id)->where('status', $this->claimed ? 'processing' : 'pending')
            ->update(['status' => $status, 'error' => $error, 'finished_at' => now(), 'body' => null, 'provider_id' => $message->provider_id]);
    }

    public function failed(?Throwable $exception): void
    {
        SmsMessage::query()->whereKey($this->messageId)->whereIn('status', ['pending', 'processing'])
            ->update(['status' => 'unknown', 'error' => 'worker_failed', 'finished_at' => now(), 'body' => null]);
    }
}
