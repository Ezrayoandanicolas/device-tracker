<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('domain_lists', function (Blueprint $table) {
            $table->id();

            $table->string('domain')->unique();

            // domain aktif?
            $table->boolean('is_active')->default(true);

            // domain diblokir?
            $table->boolean('is_blocked')->default(false);

            // alasan blok (optional)
            $table->string('blocked_reason')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('domain_lists');
    }
};
