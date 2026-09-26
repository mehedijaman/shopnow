<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->decimal('discount', 10, 2)->default(0)->after('tax');
            $table->string('coupon_code', 50)->nullable()->after('shipping_method');
            $table->index('coupon_code');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['coupon_code']);
            $table->dropColumn(['discount', 'coupon_code']);
        });
    }
};
