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
        Schema::table('backlink_articles', function (Blueprint $table) {
            $table->foreign('url_backlink_id')
                ->references('id')
                ->on('url_backlinks')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('backlink_articles', function (Blueprint $table) {
            //
        });
    }
};
