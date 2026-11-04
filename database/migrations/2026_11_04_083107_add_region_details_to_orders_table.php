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
            $table->string('shipping_province_code', 2)->nullable();
            $table->string('shipping_province_name')->nullable();
            $table->string('shipping_city_code', 4)->nullable();
            $table->string('shipping_city_name')->nullable();
            $table->string('shipping_district_code', 7)->nullable();
            $table->string('shipping_district_name')->nullable();
            $table->string('shipping_village_code', 10)->nullable();
            $table->string('shipping_village_name')->nullable();
            $table->string('shipping_postal_code', 5)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'shipping_province_code',
                'shipping_province_name',
                'shipping_city_code',
                'shipping_city_name',
                'shipping_district_code',
                'shipping_district_name',
                'shipping_village_code',
                'shipping_village_name',
                'shipping_postal_code',
            ]);
        });
    }
};
