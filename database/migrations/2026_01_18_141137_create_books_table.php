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
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->text('cover_buku')->nullable();
            $table->string('judul');
            $table->string('pengarang');
            $table->string('penerbit');
            $table->date('tahun_terbit')->nullable();
            $table->string('isbn')->unique();
            $table->integer('jumlah_halaman')->default(0);
            $table->integer('stok')->default(0);
            $table->integer('stok_tersedia')->default(0);
            $table->string('kategori')->nullable();
            $table->text('sinopsis')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
