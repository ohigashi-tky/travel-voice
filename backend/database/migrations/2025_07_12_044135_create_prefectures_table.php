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
        Schema::create('prefectures', function (Blueprint $table) {
            $table->id();
            $table->string('name', 10)->comment('都道府県名');
            $table->string('name_kana', 20)->comment('都道府県名（カナ）');
            $table->string('region', 10)->comment('地域名');
            $table->integer('display_order')->comment('表示順（1-47）');
            $table->boolean('is_available')->default(false)->comment('観光地データ有無');
            $table->timestamps();
            
            // インデックス
            $table->unique('name');
            $table->unique('display_order');
            $table->index('region');
            $table->index('is_available');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prefectures');
    }
};
