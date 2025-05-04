<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContentsTable extends Migration
{
    public function up(): void
    {
        Schema::create('contents', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('scope_key')->nullable();

            $table->string('title');
            $table->string('slug', 255);

            // UI 表示軸
            $table->string('content_type', 64)
                  ->default('slot')
                  ->comment('UI 表示軸');

            // 意味構造軸
            $table->string('content_kind', 64)
                  ->default('article')
                  ->comment('意味構造軸');

            // 本文・メタ情報
            $table->text('body')->nullable();
            $table->json('meta')->nullable();

            // 公開制御ステータス
            $table->string('status', 32)
                  ->default('draft')
                  ->comment('公開ステータス');

            $table->timestamp('published_at')->nullable();

            // 作成／更新者
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            $table->timestamps();

            // 複合ユニークインデックスを追加
            $table->unique(['scope_key', 'slug'], 'contents_scope_slug_unique');
        });
    }

    public function down(): void
    {
        Schema::table('contents', function (Blueprint $table) {
            $table->dropUnique('contents_scope_slug_unique');
        });
        Schema::dropIfExists('contents');
    }
}
