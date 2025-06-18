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
        Schema::create('audio_guides', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guide_id')->constrained()->onDelete('cascade');
            $table->string('audio_file_path');
            $table->string('audio_file_url')->nullable();
            $table->integer('duration_seconds');
            $table->string('format')->default('mp3');
            $table->integer('file_size')->nullable();
            $table->string('voice_actor')->nullable();
            $table->string('language')->default('ja');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audio_guides');
    }
};
