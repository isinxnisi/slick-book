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
        Schema::create('post_seo_settings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('post_id')->unique()->index();
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->text('meta_keywords')->nullable();
            // optional fields
            $table->string('canonical_url')->nullable();
            $table->string('twitter_card_type')->nullable();
            $table->string('ogp_image_path')->nullable();
            $table->boolean('noindex')->default(false);
            $table->timestamps();

            $table->foreign('post_id')->references('id')->on('posts')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('post_seo_settings');
    }
};
