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
        Schema::create('travel_spot_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('travel_spot_id')->constrained('travel_spots')->onDelete('cascade');
            $table->string('image_url');
            $table->string('thumbnail_url')->nullable();
            $table->integer('width')->nullable();
            $table->integer('height')->nullable();
            $table->integer('order')->default(0);
            $table->string('attribution')->nullable();
            $table->string('source')->default('google_places');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index(['travel_spot_id', 'order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('travel_spot_images');
    }
};
