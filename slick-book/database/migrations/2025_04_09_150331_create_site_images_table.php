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
        Schema::create('site_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('site_id')->constrained()->onDelete('cascade');
            $table->string('type'); // favicon, logo, og_image, etc.
            $table->string('path'); // 実ファイルパス（storage内）
            $table->string('title')->nullable(); // タイトル
            $table->text('description')->nullable(); // 管理者向けメモ
            $table->string('alt')->nullable(); // alt属性
            $table->boolean('is_public')->default(true); // 公開/非公開
            $table->integer('order')->default(0); // 並び順
            $table->string('variant')->nullable(); // 'desktop', 'mobile' など
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('site_images');
    }
};
