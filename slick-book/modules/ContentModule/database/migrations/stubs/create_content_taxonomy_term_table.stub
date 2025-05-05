<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContentTaxonomyTermTable extends Migration
{
    public function up(): void
    {
        Schema::create('content_taxonomy_term', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('content_id');
            $table->unsignedBigInteger('taxonomy_term_id');
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->foreign('content_id')
                  ->references('id')
                  ->on('contents')
                  ->onDelete('cascade');

            $table->foreign('taxonomy_term_id')
                  ->references('id')
                  ->on('taxonomy_terms')
                  ->onDelete('cascade');

            $table->unique(
                ['content_id', 'taxonomy_term_id'],
                'content_term_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('content_taxonomy_term');
    }
}
