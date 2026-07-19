<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('type', 20)->default('simple')->after('featured');
            $table->boolean('is_virtual')->default(false)->after('type');
            $table->boolean('is_downloadable')->default(false)->after('is_virtual');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['type', 'is_virtual', 'is_downloadable']);
        });
    }
};
