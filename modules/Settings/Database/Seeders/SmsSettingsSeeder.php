<?php

namespace Modules\Settings\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Settings\Models\Setting;
use Modules\Settings\Services\SmsSettings;

class SmsSettingsSeeder extends Seeder
{
    public function run(): void
    {
        foreach (app(SmsSettings::class)->defaults() as $key => $value) {
            Setting::query()->firstOrCreate(['group' => 'sms', 'key' => $key], [
                'value' => is_array($value) ? json_encode($value, JSON_THROW_ON_ERROR) : $value,
                'type' => is_bool($value) ? 'boolean' : (is_array($value) ? 'json' : 'text'),
                'is_public' => false,
            ]);
        }
    }
}
