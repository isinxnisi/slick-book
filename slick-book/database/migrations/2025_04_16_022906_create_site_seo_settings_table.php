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
        Schema::create('site_seo_settings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('site_id')->unique()->index();
            $table->string('meta_title')->nullable(); // サイト全体タイトル
            $table->text('meta_description')->nullable(); // 概要説明
            $table->text('meta_keywords')->nullable(); // 任意キーワード
            $table->string('canonical_base')->nullable(); // カノニカルURLベース
            $table->enum('twitter_card_type', ['summary', 'summary_large_image'])->default('summary');
            $table->text('custom_head_tags')->nullable(); // GAや追加meta
            $table->timestamps();

            $table->foreign('site_id')->references('id')->on('sites')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('site_seo_settings');
    }
};
