<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\ContentModule\Enums\ContentType;
use Modules\ContentModule\Enums\ContentKind;

class CreateContentsTable extends Migration
{
    public function up(): void
    {
        Schema::create('contents', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('scope_key')->nullable();

            $table->string('title');
            $table->string('slug')->unique();

            // UI 表示軸
            $table->enum(
                'content_type',
                array_map(fn(ContentType $c) => $c->value, ContentType::cases())
            )->default(ContentType::Slot->value);

            // 意味構造軸
            $table->enum(
                'content_kind',
                array_map(fn(ContentKind $k) => $k->value, ContentKind::cases())
            )->default(ContentKind::Article->value);

            // 本文・メタ情報
            $table->text('body')->nullable();
            $table->json('meta')->nullable();

            // 公開制御
            $table->enum('status', ['draft', 'published', 'scheduled'])
                  ->default('draft');
            $table->timestamp('published_at')->nullable();

            // 作成／更新者
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            $table->timestamps();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contents');
    }
}
