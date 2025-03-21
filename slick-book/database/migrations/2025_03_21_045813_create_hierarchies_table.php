<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hierarchies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->nullable()->constrained('hierarchies')->onDelete('cascade');
            $table->string('title');
            $table->integer('order')->default(0);
            $table->string('description')->nullable();
            $table->enum('status', ['draft', 'published'])->default('draft');

            $table->foreignId('created_user')->constrained('users');
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
        Schema::dropIfExists('hierarchies');
    }
};
