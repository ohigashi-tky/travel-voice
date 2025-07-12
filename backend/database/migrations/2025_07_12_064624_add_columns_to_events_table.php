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
        Schema::table('events', function (Blueprint $table) {
            $table->text('overview')->nullable()->comment('概要')->after('description');
            $table->string('category', 50)->default('その他')->comment('カテゴリ')->after('end_date');
            $table->text('access')->nullable()->comment('アクセス情報')->after('tags');
            $table->string('url')->nullable()->comment('公式URL')->after('access');
            $table->string('organizer')->nullable()->comment('主催者')->after('url');
            $table->integer('display_order')->default(0)->comment('表示順')->after('organizer');
            
            // インデックス追加
            $table->index('category');
            $table->index('display_order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropIndex(['category']);
            $table->dropIndex(['display_order']);
            $table->dropColumn(['overview', 'category', 'access', 'url', 'organizer', 'display_order']);
        });
    }
};
