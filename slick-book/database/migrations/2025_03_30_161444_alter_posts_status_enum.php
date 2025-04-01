<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE posts DROP CONSTRAINT IF EXISTS posts_status_check");
        DB::statement("ALTER TABLE posts ADD CONSTRAINT posts_status_check CHECK (status IN ('temp', 'draft', 'published'))");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE posts DROP CONSTRAINT IF EXISTS posts_status_check");
        DB::statement("ALTER TABLE posts ADD CONSTRAINT posts_status_check CHECK (status IN ('draft', 'published'))");
    }
};
