<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sms_messages', function (Blueprint $table): void {
            $table->id();
            $table->uuid('reference')->unique();
            $table->string('event_key', 150)->unique();
            $table->string('purpose', 30);
            $table->string('order_status', 30)->nullable();
            $table->string('status', 20)->default('pending');
            $table->foreignId('order_id')->nullable()->constrained('orders')->nullOnDelete();
            $table->foreignId('customer_id')->nullable()->constrained('customers')->nullOnDelete();
            $table->unsignedBigInteger('verification_id')->nullable()->index();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->text('phone');
            $table->text('body')->nullable();
            $table->string('provider', 30)->nullable();
            $table->string('provider_id', 100)->nullable();
            $table->unsignedSmallInteger('attempts')->default(0);
            $table->string('error', 50)->nullable();
            $table->timestamp('expires_at');
            $table->timestamp('queued_at')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->timestamps();
            $table->index(['status', 'created_at']);
            $table->index(['order_id', 'id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sms_messages');
    }
};
