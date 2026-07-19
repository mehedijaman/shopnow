<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('order_products', function (Blueprint $table) {
            $table->foreignId('product_variation_id')
                ->nullable()
                ->after('product_id')
                ->constrained('product_variations')
                ->nullOnDelete();

            $table->string('variation_label')->nullable()->after('product_variation_id');
        });
    }

    public function down(): void
    {
        Schema::table('order_products', function (Blueprint $table) {
            $table->dropConstrainedForeignId('product_variation_id');
            $table->dropColumn('variation_label');
        });
    }
};
