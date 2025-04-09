<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->foreignId('site_id')->nullable()->constrained()->onDelete('cascade');
        });

        // 仮に1を入れる（nullを避けるため）
        DB::table('posts')->update(['site_id' => 1]);

        // その後、非NULL制約をかける
        Schema::table('posts', function (Blueprint $table) {
            $table->foreignId('site_id')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn('site_id');
        });
    }
};
