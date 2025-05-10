<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('collection_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('collection_id');
            $table->string('item_type')->comment('関連モデルのポリモーフィックタイプ');
            $table->unsignedBigInteger('item_id')->comment('関連モデルのID');
            $table->integer('order')->default(0);
            $table->timestamps();

            $table->foreign('collection_id')
                  ->references('id')
                  ->on('collections')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('collection_items');
    }
};
