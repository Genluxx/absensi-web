<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('koreksi_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('presensi_id')->constrained('presensi')->cascadeOnDelete();
            $table->foreignId('mandor_id')->constrained('users')->cascadeOnDelete();
            $table->enum('status_baru', ['Hadir', 'Izin', 'Sakit', 'Telat', 'Alpa'])->nullable();
            $table->string('keterangan_baru')->nullable();
            $table->text('alasan');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('catatan_admin')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('koreksi_requests');
    }
};