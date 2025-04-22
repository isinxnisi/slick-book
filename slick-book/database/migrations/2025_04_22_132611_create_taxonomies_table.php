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
        Schema::create('taxonomies', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // 管理UI用の表示名
            $table->string('slug')->unique(); // URLや識別子用
            $table->string('type')->nullable(); // 'series', 'collection', 'location'など
            $table->string('purpose')->nullable(); // 'content', 'nav', 'seo', 'filter'など
            $table->boolean('is_hierarchical')->default(false); // カテゴリ型かタグ型か
            $table->unsignedBigInteger('site_id')->nullable(); // マルチサイト用
            $table->boolean('is_public')->default(true); // 分類軸自体の公開・非公開
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('taxonomies');
    }
};
