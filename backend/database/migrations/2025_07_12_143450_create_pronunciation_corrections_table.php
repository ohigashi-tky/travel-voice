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
        Schema::create('pronunciation_corrections', function (Blueprint $table) {
            $table->id();
            $table->string('original_text')->comment('元のテキスト');
            $table->string('pronunciation')->comment('正しい読み方（ひらがな）');
            $table->integer('priority')->default(0)->comment('優先度（高い順に処理）');
            $table->boolean('is_active')->default(true)->comment('有効フラグ');
            $table->timestamps();
            
            // インデックス
            $table->index(['is_active', 'priority']);
            $table->unique(['original_text', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pronunciation_corrections');
    }
};
