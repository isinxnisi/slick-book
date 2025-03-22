<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('site_tag_group', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('site_id');
            $table->unsignedBigInteger('tag_group_id');
            $table->timestamps();

            $table->foreign('site_id')->references('id')->on('sites')->onDelete('cascade');
            $table->foreign('tag_group_id')->references('id')->on('tag_groups')->onDelete('cascade');

            $table->unique(['site_id', 'tag_group_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('site_tag_group');
    }
};
