<?php

namespace Modules\Settings\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Inertia\Response;
use Modules\Settings\Http\Requests\SettingsGroupValidate;
use Modules\Settings\Services\SettingService;
use Modules\Support\Http\Controllers\BackendController;
use Modules\Support\Traits\UploadFile;

class SettingsController extends BackendController
{
    use UploadFile;

    private const GROUPS = ['general', 'branding', 'contact', 'social', 'seo', 'mail', 'shipping', 'homepage', 'pixel', 'downloads', 'analytics'];

    /** @var array<string, string[]> */
    private const IMAGE_FIELDS = [
        'branding' => ['logo', 'favicon', 'dark_logo'],
        'seo' => ['og_image'],
    ];

    public function redirect(): RedirectResponse
    {
        return redirect()->route('settings.show', ['group' => 'general']);
    }

    public function show(string $group, SettingService $service): Response
    {
        abort_unless(in_array($group, self::GROUPS, true), 404);
        $this->authorizeGroupAccess($group);

        $groups = self::GROUPS;
        $user = Auth::guard('user')->user();
        if (! $user || ! $user->can('pixel-settings-edit')) {
            $groups = array_values(array_filter($groups, static fn ($item) => $item !== 'pixel'));
        }
        if (! $user || ! $user->can('analytics-settings-edit')) {
            $groups = array_values(array_filter($groups, static fn ($item) => $item !== 'analytics'));
        }

        return inertia('Settings/SettingsForm', [
            'group' => $group,
            'settings' => $service->getGroup($group),
            'groups' => $groups,
        ]);
    }

    public function update(string $group, SettingsGroupValidate $request, SettingService $service): RedirectResponse
    {
        abort_unless(in_array($group, self::GROUPS, true), 404);
        $this->authorizeGroupAccess($group);

        $imageFields = self::IMAGE_FIELDS[$group] ?? [];
        $settingsToUpdate = [];

        foreach ($request->validated() as $key => $value) {
            if (in_array($key, $imageFields, true) || str_starts_with($key, 'remove_previous_')) {
                continue;
            }
            $settingsToUpdate[$key] = $value;
        }

        foreach ($imageFields as $field) {
            if ($request->hasFile($field)) {
                $service->deleteImage($service->getCurrentImageFilename($group, $field));
                $uploaded = $this->uploadFile($field, 'settings', 'originalUUID', 'public');
                $settingsToUpdate[$field] = $uploaded[$field] ?? null;
            } elseif ($request->boolean("remove_previous_{$field}")) {
                $service->deleteImage($service->getCurrentImageFilename($group, $field));
                $settingsToUpdate[$field] = null;
            }
            // No file and no remove flag: leave the existing image unchanged.
        }

        $service->updateGroup($group, $settingsToUpdate);
        $service->clearCache();

        return back()->with('success', 'Settings updated.');
    }

    public function sendTestEmail(Request $request): JsonResponse
    {
        $request->validate([
            'recipient' => 'required|email',
            'message' => 'required|string',
            'enable_smtp' => 'nullable|boolean',
            'from_name' => 'nullable|string|max:100',
            'from_address' => 'nullable|email|max:255',
            'host' => 'nullable|string|max:255',
            'port' => 'nullable|integer|min:1|max:65535',
            'username' => 'nullable|string|max:255',
            'password' => 'nullable|string|max:255',
            'encryption' => 'nullable|string|in:tls,ssl,starttls',
        ]);

        try {
            if ($request->boolean('enable_smtp')) {
                config([
                    'mail.mailers.smtp.host' => $request->input('host'),
                    'mail.mailers.smtp.port' => $request->input('port') ?? 587,
                    'mail.mailers.smtp.username' => $request->input('username') ?? null,
                    'mail.mailers.smtp.password' => $request->input('password') ?? null,
                    'mail.mailers.smtp.encryption' => $request->input('encryption') ?? 'tls',
                ]);
                if ($request->filled('from_address')) {
                    config([
                        'mail.from.address' => $request->input('from_address'),
                        'mail.from.name' => $request->input('from_name') ?? config('mail.from.name'),
                    ]);
                }
            } else {
                config([
                    'mail.mailers.smtp.host' => env('MAIL_HOST', '127.0.0.1'),
                    'mail.mailers.smtp.port' => env('MAIL_PORT', 2525),
                    'mail.mailers.smtp.username' => env('MAIL_USERNAME'),
                    'mail.mailers.smtp.password' => env('MAIL_PASSWORD'),
                    'mail.mailers.smtp.encryption' => env('MAIL_ENCRYPTION', 'tls'),
                    'mail.from.address' => env('MAIL_FROM_ADDRESS', 'hello@example.com'),
                    'mail.from.name' => env('MAIL_FROM_NAME', config('app.name')),
                ]);
            }

            if (! app()->runningUnitTests()) {
                Mail::purge();
            }

            $siteName = setting('branding.site_name', config('app.name'));
            $recipient = $request->input('recipient');
            $messageBody = $request->input('message');

            Mail::raw($messageBody, function ($message) use ($recipient, $siteName) {
                $message->to($recipient)
                    ->subject("Test Email from {$siteName}");
            });

            return response()->json([
                'success' => true,
                'message' => 'Test email sent successfully!',
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send test email: '.$e->getMessage(),
            ], 500);
        }
    }

    private function authorizeGroupAccess(string $group): void
    {
        if ($group === 'pixel') {
            $user = Auth::guard('user')->user();
            abort_unless($user && $user->can('pixel-settings-edit'), 403);
        }

        if ($group === 'analytics') {
            $user = Auth::guard('user')->user();
            abort_unless($user && $user->can('analytics-settings-edit'), 403);
        }
    }
}
