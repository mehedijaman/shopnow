<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('permissions')->updateOrInsert(
            ['name' => 'courier-settings-edit', 'guard_name' => 'user'],
            ['updated_at' => now(), 'created_at' => now()]
        );

        $defaults = [
            ['key' => 'default_courier', 'value' => 'steadfast', 'type' => 'text', 'label' => 'Default Courier', 'description' => 'Courier provider used for shipment booking.', 'sort_order' => 1],
            ['key' => 'default_weight_kg', 'value' => '1', 'type' => 'text', 'label' => 'Default Parcel Weight (kg)', 'description' => 'Weight sent to couriers when booking. Products do not store weight.', 'sort_order' => 2],

            ['key' => 'pathao_enabled', 'value' => '0', 'type' => 'boolean', 'label' => 'Enable Pathao', 'description' => null, 'sort_order' => 3],
            ['key' => 'pathao_sandbox', 'value' => '1', 'type' => 'boolean', 'label' => 'Pathao Sandbox Mode', 'description' => 'Use the Pathao staging API.', 'sort_order' => 4],
            ['key' => 'pathao_client_id', 'value' => null, 'type' => 'text', 'label' => 'Pathao Client ID', 'description' => null, 'sort_order' => 5],
            ['key' => 'pathao_client_secret', 'value' => null, 'type' => 'text', 'label' => 'Pathao Client Secret', 'description' => null, 'sort_order' => 6],
            ['key' => 'pathao_username', 'value' => null, 'type' => 'text', 'label' => 'Pathao Username', 'description' => null, 'sort_order' => 7],
            ['key' => 'pathao_password', 'value' => null, 'type' => 'text', 'label' => 'Pathao Password', 'description' => null, 'sort_order' => 8],
            ['key' => 'pathao_store_id', 'value' => null, 'type' => 'text', 'label' => 'Pathao Store ID', 'description' => null, 'sort_order' => 9],
            ['key' => 'pathao_webhook_secret', 'value' => null, 'type' => 'text', 'label' => 'Pathao Webhook Secret', 'description' => 'HMAC secret used to verify incoming webhooks.', 'sort_order' => 10],
            ['key' => 'pathao_tracking_url', 'value' => null, 'type' => 'text', 'label' => 'Pathao Tracking URL', 'description' => 'Tracking link template containing {tracking}.', 'sort_order' => 11],

            ['key' => 'steadfast_enabled', 'value' => '0', 'type' => 'boolean', 'label' => 'Enable Steadfast', 'description' => null, 'sort_order' => 12],
            ['key' => 'steadfast_api_key', 'value' => null, 'type' => 'text', 'label' => 'Steadfast API Key', 'description' => null, 'sort_order' => 13],
            ['key' => 'steadfast_secret_key', 'value' => null, 'type' => 'text', 'label' => 'Steadfast Secret Key', 'description' => null, 'sort_order' => 14],
            ['key' => 'steadfast_webhook_secret', 'value' => null, 'type' => 'text', 'label' => 'Steadfast Webhook Secret', 'description' => 'HMAC secret used to verify incoming webhooks.', 'sort_order' => 15],
            ['key' => 'steadfast_tracking_url', 'value' => null, 'type' => 'text', 'label' => 'Steadfast Tracking URL', 'description' => 'Tracking link template containing {tracking}.', 'sort_order' => 16],

            ['key' => 'redx_enabled', 'value' => '0', 'type' => 'boolean', 'label' => 'Enable RedX', 'description' => null, 'sort_order' => 17],
            ['key' => 'redx_sandbox', 'value' => '1', 'type' => 'boolean', 'label' => 'RedX Sandbox Mode', 'description' => 'Use the RedX sandbox API.', 'sort_order' => 18],
            ['key' => 'redx_access_token', 'value' => null, 'type' => 'text', 'label' => 'RedX Access Token', 'description' => null, 'sort_order' => 19],
            ['key' => 'redx_webhook_secret', 'value' => null, 'type' => 'text', 'label' => 'RedX Webhook Secret', 'description' => 'HMAC secret used to verify incoming webhooks.', 'sort_order' => 20],
            ['key' => 'redx_tracking_url', 'value' => null, 'type' => 'text', 'label' => 'RedX Tracking URL', 'description' => 'Tracking link template containing {tracking}.', 'sort_order' => 21],

            ['key' => 'ecourier_enabled', 'value' => '0', 'type' => 'boolean', 'label' => 'Enable eCourier', 'description' => null, 'sort_order' => 22],
            ['key' => 'ecourier_sandbox', 'value' => '1', 'type' => 'boolean', 'label' => 'eCourier Sandbox Mode', 'description' => 'Use the eCourier staging API.', 'sort_order' => 23],
            ['key' => 'ecourier_api_key', 'value' => null, 'type' => 'text', 'label' => 'eCourier API Key', 'description' => null, 'sort_order' => 24],
            ['key' => 'ecourier_api_secret', 'value' => null, 'type' => 'text', 'label' => 'eCourier API Secret', 'description' => null, 'sort_order' => 25],
            ['key' => 'ecourier_user_id', 'value' => null, 'type' => 'text', 'label' => 'eCourier User ID', 'description' => null, 'sort_order' => 26],
            ['key' => 'ecourier_webhook_secret', 'value' => null, 'type' => 'text', 'label' => 'eCourier Webhook Secret', 'description' => 'HMAC secret used to verify incoming webhooks.', 'sort_order' => 27],
            ['key' => 'ecourier_tracking_url', 'value' => null, 'type' => 'text', 'label' => 'eCourier Tracking URL', 'description' => 'Tracking link template containing {tracking}.', 'sort_order' => 28],

            ['key' => 'paperfly_enabled', 'value' => '0', 'type' => 'boolean', 'label' => 'Enable Paperfly', 'description' => null, 'sort_order' => 29],
            ['key' => 'paperfly_username', 'value' => null, 'type' => 'text', 'label' => 'Paperfly Username', 'description' => null, 'sort_order' => 30],
            ['key' => 'paperfly_password', 'value' => null, 'type' => 'text', 'label' => 'Paperfly Password', 'description' => null, 'sort_order' => 31],
            ['key' => 'paperfly_api_key', 'value' => null, 'type' => 'text', 'label' => 'Paperfly API Key', 'description' => null, 'sort_order' => 32],
            ['key' => 'paperfly_webhook_secret', 'value' => null, 'type' => 'text', 'label' => 'Paperfly Webhook Secret', 'description' => 'HMAC secret used to verify incoming webhooks.', 'sort_order' => 33],
            ['key' => 'paperfly_tracking_url', 'value' => null, 'type' => 'text', 'label' => 'Paperfly Tracking URL', 'description' => 'Tracking link template containing {tracking}.', 'sort_order' => 34],

            ['key' => 'fraud_enabled', 'value' => '0', 'type' => 'boolean', 'label' => 'Enable Fraud Checks', 'description' => 'Check COD customer cancel history across couriers after order placement.', 'sort_order' => 35],
            ['key' => 'fraud_min_deliveries', 'value' => '5', 'type' => 'text', 'label' => 'Minimum Deliveries Before Flagging', 'description' => 'Customers below this delivery count are never flagged.', 'sort_order' => 36],
            ['key' => 'fraud_cancel_ratio_threshold', 'value' => '40', 'type' => 'text', 'label' => 'Cancel Ratio Threshold (%)', 'description' => 'Flag as high risk at or above this cancel ratio.', 'sort_order' => 37],
            ['key' => 'fraud_steadfast_user', 'value' => null, 'type' => 'text', 'label' => 'Fraud: Steadfast Portal Email', 'description' => 'Login credentials for the fraud checker, not the API key.', 'sort_order' => 38],
            ['key' => 'fraud_steadfast_password', 'value' => null, 'type' => 'text', 'label' => 'Fraud: Steadfast Portal Password', 'description' => null, 'sort_order' => 39],
            ['key' => 'fraud_pathao_user', 'value' => null, 'type' => 'text', 'label' => 'Fraud: Pathao Portal Email', 'description' => null, 'sort_order' => 40],
            ['key' => 'fraud_pathao_password', 'value' => null, 'type' => 'text', 'label' => 'Fraud: Pathao Portal Password', 'description' => null, 'sort_order' => 41],
            ['key' => 'fraud_redx_phone', 'value' => null, 'type' => 'text', 'label' => 'Fraud: RedX Portal Phone', 'description' => null, 'sort_order' => 42],
            ['key' => 'fraud_redx_password', 'value' => null, 'type' => 'text', 'label' => 'Fraud: RedX Portal Password', 'description' => null, 'sort_order' => 43],
            ['key' => 'fraud_paperfly_user', 'value' => null, 'type' => 'text', 'label' => 'Fraud: Paperfly Username', 'description' => null, 'sort_order' => 44],
            ['key' => 'fraud_paperfly_password', 'value' => null, 'type' => 'text', 'label' => 'Fraud: Paperfly Password', 'description' => null, 'sort_order' => 45],
            ['key' => 'fraud_carrybee_phone', 'value' => null, 'type' => 'text', 'label' => 'Fraud: Carrybee Phone', 'description' => null, 'sort_order' => 46],
            ['key' => 'fraud_carrybee_password', 'value' => null, 'type' => 'text', 'label' => 'Fraud: Carrybee Password', 'description' => null, 'sort_order' => 47],
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
        DB::table('settings')->where('group', 'courier')->delete();
        DB::table('permissions')
            ->where('name', 'courier-settings-edit')
            ->where('guard_name', 'user')
            ->delete();
    }
};
