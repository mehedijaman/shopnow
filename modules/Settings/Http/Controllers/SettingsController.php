<?php

namespace Modules\Settings\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Response;
use Modules\Settings\Http\Requests\SettingsGroupValidate;
use Modules\Settings\Services\SettingService;
use Modules\Support\Http\Controllers\BackendController;
use Modules\Support\Traits\UploadFile;

class SettingsController extends BackendController
{
    use UploadFile;

    private const GROUPS = ['general', 'branding', 'contact', 'social', 'seo', 'mail', 'shipping', 'homepage', 'pixel', 'downloads'];

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
            $groups = array_values(array_filter(self::GROUPS, static fn ($item) => $item !== 'pixel'));
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

    private function authorizeGroupAccess(string $group): void
    {
        if ($group !== 'pixel') {
            return;
        }

        $user = Auth::guard('user')->user();
        abort_unless($user && $user->can('pixel-settings-edit'), 403);
    }
}
