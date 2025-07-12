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
        Schema::create('prefecture_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prefecture_id')->constrained('prefectures')->onDelete('cascade');
            $table->text('image_url')->comment('画像URL');
            $table->enum('image_type', ['icon', 'banner', 'thumbnail'])->comment('画像タイプ');
            $table->integer('display_order')->default(1)->comment('表示順');
            $table->timestamps();
            
            // インデックス
            $table->index(['prefecture_id', 'image_type']);
            $table->index(['prefecture_id', 'display_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prefecture_images');
    }
};
