<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('carts', function (Blueprint $table) {
            $table->foreignId('customer_id')->nullable()->constrained('customers')->nullOnDelete();
            $table->string('guest_token', 100)->nullable()->unique();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('carts', function (Blueprint $table) {
            $table->dropForeign(['customer_id']);
            $table->dropColumn(['customer_id', 'guest_token', 'deleted_at']);
        });
    }
};
