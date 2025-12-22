<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('backlink_articles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('url_backlink_id')
                  ->constrained('url_backlinks')
                  ->cascadeOnDelete();

            $table->string('article_slug');
            $table->string('article_domain');
            $table->timestamps();

            // mencegah duplikasi artikel + backlink
            $table->unique(
                ['article_slug', 'article_domain', 'url_backlink_id'],
                'uniq_article_backlink'
            );

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('backlink_articles');
    }
};
