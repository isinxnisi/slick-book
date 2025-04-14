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
            $table->dropColumn('created_user');
            $table->dropColumn('created');
            $table->dropColumn('created_at');
            $table->dropColumn('updated_user');
            $table->dropColumn('updated');
            $table->dropColumn('updated_at');
            $table->dropColumn('is_deleted');
            $table->dropColumn('deleted_user');
            $table->dropColumn('deleted');


            $table->foreignId('published_user')->nullable()->constrained('users');
            $table->timestamp('published_at')->nullable();
            $table->foreignId('created_user')->nullable()->constrained('users');
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->foreignId('updated_user')->nullable()->constrained('users');
            $table->timestamp('updated_at')->nullable()->useCurrentOnUpdate();
            $table->boolean('is_deleted')->default(false);
            $table->foreignId('deleted_user')->nullable()->constrained('users');
            $table->timestamp('deleted_at')->nullable();
        });

        // nullを避ける
        DB::table('posts')->update(['created_user' => 1]);
        DB::table('posts')->update(['created_at' => '2025-01-01 00:00:00']);

        Schema::table('posts', function (Blueprint $table) {
            $table->foreignId('created_user')->nullable(false)->change();
            $table->timestamp('created_at')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn('published_user');
            $table->dropColumn('published_at');
        });

        Schema::table('posts', function (Blueprint $table) {
            $table->timestamp('created')->nullable()->useCurrent();
            $table->timestamp('updated')->nullable()->useCurrentOnUpdate();
            $table->timestamp('deleted')->nullable();
        });

        // nullを避ける
        DB::table('posts')->update(['created' => '2025-01-01 00:00:00']);

        Schema::table('posts', function (Blueprint $table) {
            $table->timestamp('created')->nullable(false)->change();
        });
    }
};
