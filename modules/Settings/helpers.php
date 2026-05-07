<?php

use Illuminate\Support\Facades\Cache;
use Modules\Settings\Services\SettingService;

if (! function_exists('setting')) {
    /**
     * Retrieve a setting value from cache.
     *
     * Supports dot notation: setting('branding.site_name')
     * Bare keys default to the 'general' group: setting('site_name')
     */
    function setting(string $key, mixed $default = null): mixed
    {
        try {
            $all = Cache::rememberForever('settings', fn () => app(SettingService::class)->getAll());

            if (! str_contains($key, '.')) {
                $key = 'general.'.$key;
            }

            return $all[$key] ?? $default;
        } catch (Throwable) {
            return $default;
        }
    }
}

if (! function_exists('settings_group')) {
    /**
     * Retrieve all settings for a group as a flat key-value map.
     *
     * @return array<string, mixed>
     */
    function settings_group(string $group): array
    {
        try {
            $all = Cache::rememberForever('settings', fn () => app(SettingService::class)->getAll());

            $prefix = $group.'.';
            $result = [];

            foreach ($all as $key => $value) {
                if (str_starts_with($key, $prefix)) {
                    $result[substr($key, strlen($prefix))] = $value;
                }
            }

            return $result;
        } catch (Throwable) {
            return [];
        }
    }
}
