<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('settings')->where('group', 'courier')
            ->whereIn('key', [
                'fraud_steadfast_user',
                'fraud_steadfast_password',
                'fraud_pathao_user',
                'fraud_pathao_password',
                'fraud_redx_phone',
                'fraud_redx_password',
                'fraud_paperfly_user',
                'fraud_paperfly_password',
                'fraud_carrybee_phone',
                'fraud_carrybee_password',
            ])
            ->delete();

        $defaults = [
            ['key' => 'fraud_steadfast_enabled', 'value' => '1', 'type' => 'boolean', 'label' => 'Fraud Source: SteadFast API', 'description' => 'Check COD cancel history through the SteadFast fraud score API.', 'sort_order' => 38],
            ['key' => 'fraud_bdcourier_enabled', 'value' => '0', 'type' => 'boolean', 'label' => 'Fraud Source: BD Courier API', 'description' => 'Check COD cancel history across couriers through the BD Courier API.', 'sort_order' => 39],
            ['key' => 'bdcourier_api_key', 'value' => null, 'type' => 'text', 'label' => 'BD Courier API Key', 'description' => 'Bearer key for the BD Courier fraud check endpoint.', 'sort_order' => 40],
            ['key' => 'bdcourier_endpoint', 'value' => 'https://api.bdcourier.com', 'type' => 'text', 'label' => 'BD Courier API Endpoint', 'description' => 'Base URL of the BD Courier API.', 'sort_order' => 41],
        ];

        foreach ($defaults as $setting) {
            DB::table('settings')->updateOrInsert(
                ['group' => 'courier', 'key' => $setting['key']],
                [
                    'value' => $setting['value'],
                    'type' => $setting['type'],
                    'label' => $setting['label'],
                    'description' => $setting['description'],
                    'is_public' => false,
                    'sort_order' => $setting['sort_order'],
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }
    }

    public function down(): void
    {
        DB::table('settings')->where('group', 'courier')
            ->whereIn('key', [
                'fraud_steadfast_enabled',
                'fraud_bdcourier_enabled',
                'bdcourier_api_key',
                'bdcourier_endpoint',
            ])
            ->delete();

        $portalSettings = [
            ['key' => 'fraud_steadfast_user', 'label' => 'Fraud: Steadfast Portal Email', 'description' => 'Login credentials for the fraud checker, not the API key.', 'sort_order' => 38],
            ['key' => 'fraud_steadfast_password', 'label' => 'Fraud: Steadfast Portal Password', 'description' => null, 'sort_order' => 39],
            ['key' => 'fraud_pathao_user', 'label' => 'Fraud: Pathao Portal Email', 'description' => null, 'sort_order' => 40],
            ['key' => 'fraud_pathao_password', 'label' => 'Fraud: Pathao Portal Password', 'description' => null, 'sort_order' => 41],
            ['key' => 'fraud_redx_phone', 'label' => 'Fraud: RedX Portal Phone', 'description' => null, 'sort_order' => 42],
            ['key' => 'fraud_redx_password', 'label' => 'Fraud: RedX Portal Password', 'description' => null, 'sort_order' => 43],
            ['key' => 'fraud_paperfly_user', 'label' => 'Fraud: Paperfly Username', 'description' => null, 'sort_order' => 44],
            ['key' => 'fraud_paperfly_password', 'label' => 'Fraud: Paperfly Password', 'description' => null, 'sort_order' => 45],
            ['key' => 'fraud_carrybee_phone', 'label' => 'Fraud: Carrybee Phone', 'description' => null, 'sort_order' => 46],
            ['key' => 'fraud_carrybee_password', 'label' => 'Fraud: Carrybee Password', 'description' => null, 'sort_order' => 47],
        ];

        foreach ($portalSettings as $setting) {
            DB::table('settings')->updateOrInsert(
                ['group' => 'courier', 'key' => $setting['key']],
                [
                    'value' => null,
                    'type' => 'text',
                    'label' => $setting['label'],
                    'description' => $setting['description'],
                    'is_public' => false,
                    'sort_order' => $setting['sort_order'],
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }
    }
};
