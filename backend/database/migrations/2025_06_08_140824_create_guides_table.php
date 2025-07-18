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
        Schema::create('guides', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tourist_spot_id')->constrained('travel_spots')->onDelete('cascade');
            $table->string('title');
            $table->text('content');
            $table->string('type')->default('text'); // text, audio, video
            $table->integer('duration_minutes')->nullable();
            $table->integer('order')->default(1);
            $table->json('highlights')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guides');
    }
};
