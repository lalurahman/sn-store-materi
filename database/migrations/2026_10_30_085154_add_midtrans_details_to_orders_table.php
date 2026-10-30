<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->text('snap_token')->nullable();
            $table->text('payment_url')->nullable();
            $table->string('gateway_transaction_id')->nullable();
            $table->string('gateway_status', 30)->nullable();
            $table->timestamp('gateway_transaction_time')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'snap_token',
                'payment_url',
                'gateway_transaction_id',
                'gateway_status',
                'gateway_transaction_time',
            ]);
        });
    }
};
