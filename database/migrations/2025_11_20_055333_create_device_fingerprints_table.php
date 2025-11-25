<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('device_fingerprints', function (Blueprint $table) {
            $table->id();

            // Konsisten: fingerprint_id, bukan fp_id
            $table->string('fingerprint_id', 128)->nullable()->index();

            // IP address hanya untuk pengecekan manual
            $table->string('ip_address', 45)->nullable()->index();

            // Raw fingerprint JSON
            $table->json('fingerprint_data');

            // Berapa kali device yang sama muncul
            $table->integer('scan_count')->default(1);

            // Persentase similarity
            $table->decimal('similarity_score', 5, 3)->nullable();

            // created_at = first seen, updated_at = last seen
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('device_fingerprints');
    }
};
