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
        Schema::table('travel_spot_images', function (Blueprint $table) {
            $table->text('image_url')->change();
            $table->text('thumbnail_url')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('travel_spot_images', function (Blueprint $table) {
            $table->string('image_url')->change();
            $table->string('thumbnail_url')->nullable()->change();
        });
    }
};
