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
        Schema::table('prefectures', function (Blueprint $table) {
            // 地域内での順番（is_availableの後ろに追加）
            $table->integer('order_in_region')->nullable()->comment('地域内での順番（1が最初）')->after('is_available');
            // 主要都道府県の並び順
            $table->integer('featured_order')->nullable()->comment('主要都道府県の並び順（NULL以外は主要都道府県）')->after('order_in_region');
            // 地域の並び順
            $table->integer('region_order')->nullable()->comment('地域の並び順（北から南へ）')->after('featured_order');
            
            // インデックス
            $table->index('order_in_region');
            $table->index('featured_order');
            $table->index('region_order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('prefectures', function (Blueprint $table) {
            $table->dropColumn(['order_in_region', 'featured_order', 'region_order']);
        });
    }
};
