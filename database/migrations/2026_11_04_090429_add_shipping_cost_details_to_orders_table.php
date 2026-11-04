<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (! Schema::hasColumn('orders', 'shipping_cost')) {
                $table->decimal('shipping_cost', 12, 2)->default(0);
            }

            if (! Schema::hasColumn('orders', 'shipping_courier')) {
                $table->string('shipping_courier', 30)->nullable();
            }

            if (! Schema::hasColumn('orders', 'shipping_service')) {
                $table->string('shipping_service', 50)->nullable();
            }

            if (! Schema::hasColumn('orders', 'shipping_etd')) {
                $table->string('shipping_etd', 50)->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['shipping_cost', 'shipping_courier', 'shipping_service', 'shipping_etd']);
        });
    }
};
