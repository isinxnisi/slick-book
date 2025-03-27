<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('tag_groups', function (Blueprint $table) {
            $table->string('purpose', 50)->nullable()->change();
        });
    }

    public function down(): void {
        Schema::table('tag_groups', function (Blueprint $table) {
            $table->string('purpose', 50)->nullable(false)->change();
        });
    }
};
