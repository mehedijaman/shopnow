<?php

namespace Modules\Settings\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Modules\Settings\Models\Setting;

class SettingService
{
    private const CACHE_KEY = 'settings';

    /**
     * Get all settings for a group as a typed key-value map.
     * Image-type settings include an additional {key}_url entry.
     *
     * @return array<string, mixed>
     */
    public function getGroup(string $group): array
    {
        $rows = Setting::where('group', $group)->orderBy('sort_order')->get();
        $result = [];

        foreach ($rows as $row) {
            $result[$row->key] = $row->value;

            if ($row->getRawOriginal('type') === 'image') {
                $result[$row->key.'_url'] = $row->value_url;
            }
        }

        return $result;
    }

    /**
     * Persist a key-value map for the given group.
     * Arrays are JSON-encoded automatically.
     *
     * @param  array<string, mixed>  $data
     */
    public function updateGroup(string $group, array $data): void
    {
        foreach ($data as $key => $value) {
            Setting::where('group', $group)
                ->where('key', $key)
                ->update([
                    'value' => is_array($value) ? json_encode($value) : $value,
                ]);
        }
    }

    /**
     * Retrieve the raw stored filename for an image setting.
     */
    public function getCurrentImageFilename(string $group, string $key): ?string
    {
        return Setting::where('group', $group)
            ->where('key', $key)
            ->value('value');
    }

    /**
     * Delete an image file from the settings storage disk.
     */
    public function deleteImage(?string $filename): void
    {
        if ($filename) {
            Storage::disk('public')->delete("settings/{$filename}");
        }
    }

    /**
     * Build a flat ['group.key' => value] map of all settings for the cache.
     *
     * @return array<string, mixed>
     */
    public function getAll(): array
    {
        $result = [];

        foreach (Setting::all() as $setting) {
            $result[$setting->group.'.'.$setting->key] = $setting->value;

            if ($setting->getRawOriginal('type') === 'image') {
                $result[$setting->group.'.'.$setting->key.'_url'] = $setting->value_url;
            }
        }

        return $result;
    }

    /**
     * Remove the settings cache so it is rebuilt on the next request.
     */
    public function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    /**
     * Return all settings from cache, rebuilding if necessary.
     *
     * @return array<string, mixed>
     */
    public function getFromCache(): array
    {
        return Cache::rememberForever(self::CACHE_KEY, fn () => $this->getAll());
    }
}
