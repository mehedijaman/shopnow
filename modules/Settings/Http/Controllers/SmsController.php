<?php

namespace Modules\Settings\Http\Controllers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Inertia\Response;
use Modules\Settings\Http\Requests\TestSmsRequest;
use Modules\Settings\Models\SmsMessage;
use Modules\Settings\Services\SmsMessages;
use Modules\Settings\Services\SmsSettings;
use Modules\Support\Http\Controllers\BackendController;

class SmsController extends BackendController
{
    public function history(Request $request): Response
    {
        return inertia('Settings/SmsHistory', [
            'messages' => $this->historyQuery($request)->paginate(25)->withQueryString()
                ->through(fn (SmsMessage $message): array => $message->summary()),
            'providers' => array_map(static fn (array $provider): string => $provider['name'], SmsSettings::PROVIDERS),
            'canEditSettings' => $request->user('user')->can('sms-settings-edit'),
        ]);
    }

    public function test(TestSmsRequest $request, SmsMessages $messages, SmsSettings $settings): JsonResponse
    {
        $provider = $settings->values()['provider'];
        if (! SmsSettings::canSend($provider)) {
            throw ValidationException::withMessages(['phone' => __('settings::sms.provider_unavailable')]);
        }
        $credentials = $settings->credentials($provider);
        foreach (SmsSettings::PROVIDERS[$provider]['fields'] as $field) {
            if (blank($credentials[$field] ?? null)) {
                throw ValidationException::withMessages(['phone' => __('settings::sms.save_credentials_first')]);
            }
        }
        $data = $request->validated();
        $message = $messages->create('test:'.Str::uuid(), 'test', $data['phone'], $data['message'], [
            'created_by' => $request->user('user')->id, 'expires_at' => now()->addMinutes(10),
        ]);

        return response()->json(['message' => __('settings::sms.test_queued'), 'attempt' => $message->fresh()->summary()], 202);
    }

    public function activity(Request $request): JsonResponse
    {
        return response()->json(['messages' => $this->historyQuery($request)->limit(30)->get()
            ->map(fn (SmsMessage $message): array => $message->summary())]);
    }

    /** @return Builder<SmsMessage> */
    private function historyQuery(Request $request): Builder
    {
        $user = $request->user('user');
        abort_unless($user && ($user->can('sms-activity-view') || $user->can('sms-test-send')), 403);

        return SmsMessage::query()
            ->when(! $user->can('sms-activity-view'), fn ($query) => $query->where('purpose', 'test')->where('created_by', $user->id))
            ->latest('id');
    }
}
