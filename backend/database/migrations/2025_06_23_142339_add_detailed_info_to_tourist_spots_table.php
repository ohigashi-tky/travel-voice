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
            $table->text('overview')->nullable()->after('description');
            $table->json('highlights')->nullable()->after('overview');
            $table->text('history')->nullable()->after('highlights');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tourist_spots', function (Blueprint $table) {
            $table->dropColumn(['overview', 'highlights', 'history']);
        });
    }
};
