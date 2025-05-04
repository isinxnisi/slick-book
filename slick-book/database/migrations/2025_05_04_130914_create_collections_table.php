<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('galleries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('scope_key')->nullable()->comment('サイト／スコープ識別子');
            $table->string('name')->comment('ギャラリー名');
            $table->string('slug')->unique();
            $table->timestamps();

            $table->index('scope_key');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('galleries');
    }
};
