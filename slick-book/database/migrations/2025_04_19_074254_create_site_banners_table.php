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
        Schema::create('site_banners', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('site_id')->index();  // 対象サイト
            $table->string('device_type', 20);               // 'pc_tablet' or 'mobile'
            $table->string('section', 20);                   // 例: 'rside', 'center', etc.
            $table->tinyInteger('slot_no');                  // スロット番号 (例: 1, 2, ...)

            $table->string('title')->nullable();             // 管理用タイトル
            $table->text('html')->nullable();                // 表示HTML（空で非表示扱いも可能）
            $table->boolean('enabled')->default(true);       // 表示ON/OFF

            $table->timestamp('start_at')->nullable();       // 開始日時（null なら常時）
            $table->timestamp('end_at')->nullable();         // 終了日時（null なら常時）

            $table->timestamps();

            // 複合インデックス
            $table->index(['device_type', 'section', 'slot_no']);
            $table->index(['enabled', 'start_at', 'end_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('site_banners');
    }
};
