<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique()->nullable(); // 任意のため nullable を追加
            $table->text('body');
            $table->text('html_body');
            $table->text('toc')->nullable();
            $table->enum('status', ['draft', 'published'])->default('draft');
            
            $table->foreignId('created_user')->constrained('users')->onDelete('cascade');
            $table->timestamp('created')->useCurrent();
            $table->foreignId('updated_user')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('updated')->nullable()->useCurrentOnUpdate();
            $table->foreignId('deleted_user')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('deleted')->nullable();
            $table->boolean('is_deleted')->default(false);
            
            $table->timestamps();
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
