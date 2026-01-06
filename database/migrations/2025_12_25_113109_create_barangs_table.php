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
        Schema::create('barangs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jenis_barang_id')->constrained('jenis_barangs')->restrictOnDelete();
            $table->string('merk', 150);
            $table->string('gambar');
            $table->string('register', 150);
            $table->date('tahun');
            $table->string('barcode', 100)->unique();
            $table->foreignId('penanggung_jawab_id')->constrained('penanggung_jawabs')->restrictOnDelete();
            $table->decimal('harga', 15, 2);
            $table->foreignId('gudang_id')->constrained('gudangs')->restrictOnDelete();
            $table->foreignId('dinas_id')->constrained('dinas')->cascadeOnDelete()->restrictOnDelete();
            $table->enum('kondisi', ['baik', 'tidak digunakan', 'rusak ringan', 'rusak berat', 'hibah', 'mutasi']);
            $table->string('keterangan', 150)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('barangs');
    }
};
