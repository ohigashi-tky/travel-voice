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
        Schema::table('tourist_spots', function (Blueprint $table) {
            $table->json('public_transport')->nullable()->after('access_info');
            $table->json('car_access')->nullable()->after('public_transport');
            $table->text('parking_info')->nullable()->after('car_access');
            $table->text('walking_info')->nullable()->after('parking_info');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tourist_spots', function (Blueprint $table) {
            $table->dropColumn(['public_transport', 'car_access', 'parking_info', 'walking_info']);
        });
    }
};
