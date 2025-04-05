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
        Schema::create('soal', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_ujian')->constrained('ujian');
            $table->string('gambar_soal')->nullable();
            $table->text('pertanyaan');
            $table->enum('tipe_pertanyaan', ['pilihan_ganda', 'opsi_jawaban', 'benar_salah'])->default('pilihan_ganda');
            $table->integer('bobot');
            $table->enum('tipe_jawaban', ['text', 'gambar'])->default('text');
            $table->text('pilihan_a')->nullable();
            $table->text('pilihan_b')->nullable();
            $table->text('pilihan_c')->nullable();
            $table->text('pilihan_d')->nullable();
            $table->text('pilihan_e')->nullable();
            $table->string('pilihan_a_gambar')->nullable();
            $table->string('pilihan_b_gambar')->nullable();
            $table->string('pilihan_c_gambar')->nullable();
            $table->string('pilihan_d_gambar')->nullable();
            $table->string('pilihan_e_gambar')->nullable();
            $table->string('jawaban');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('soal');
    }
};
