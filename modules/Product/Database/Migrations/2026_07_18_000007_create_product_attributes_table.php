<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_attributes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('input_type', 20)->default('select');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_attributes');
    }
};
