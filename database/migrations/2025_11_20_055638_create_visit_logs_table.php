<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('visit_logs', function (Blueprint $table) {
            $table->bigIncrements('id');

            // Fingerprint ID (string, sama seperti di tabel device_fingerprints)
            $table->string('fingerprint_id')->index();

            // IP yang dipakai saat kunjungan
            $table->string('ip_address')->nullable();

            // Waktu kunjungan
            $table->timestamp('visited_at')->nullable();

            $table->timestamps();

            // Optional: relasi (tidak wajib untuk berjalan)
            $table->foreign('fingerprint_id')
                ->references('fingerprint_id')
                ->on('device_fingerprints')
                ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('visit_logs');
    }
};