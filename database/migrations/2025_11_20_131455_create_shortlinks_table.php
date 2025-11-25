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
        Schema::create('shortlinks', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->unsignedBigInteger('domain_list_id');
            $table->string('target_url');

            $table->boolean('is_active')->default(true);
            $table->integer('hit_count')->default(0);
            $table->timestamp('last_hit_at')->nullable();

            $table->timestamps();

            $table->foreign('domain_list_id')
                ->references('id')
                ->on('domain_lists')
                ->cascadeOnDelete();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shortlinks');
    }
};
