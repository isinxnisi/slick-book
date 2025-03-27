<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tag_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name');               // 表示名
            $table->string('slug')->unique();     // URLスラッグ
            $table->string('purpose');            // 用途 (e.g. 'public', 'seo', 'analysis' etc.)
            $table->string('color', 20)->nullable();
            $table->string('icon')->nullable();
            $table->unsignedBigInteger('parent_id')->nullable(); // グループ階層用
            $table->integer('order')->default(0);
            $table->timestamps();
            $table->softDeletes();
        
            $table->foreign('parent_id')->references('id')->on('tag_groups')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tag_groups');
    }
};
