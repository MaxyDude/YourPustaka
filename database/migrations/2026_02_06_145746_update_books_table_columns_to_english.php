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
        Schema::table('books', function (Blueprint $table) {
            // Rename columns from Indonesian to English
            $table->renameColumn('cover_buku', 'cover_image');
            $table->renameColumn('judul', 'title');
            $table->renameColumn('pengarang', 'author');
            $table->renameColumn('penerbit', 'publisher');
            $table->renameColumn('tahun_terbit', 'publication_date');
            $table->renameColumn('jumlah_halaman', 'pages');
            $table->renameColumn('stok', 'total_copies');
            $table->renameColumn('sinopsis', 'description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('books', function (Blueprint $table) {
            // Rename back to Indonesian
            $table->renameColumn('cover_image', 'cover_buku');
            $table->renameColumn('title', 'judul');
            $table->renameColumn('author', 'pengarang');
            $table->renameColumn('publisher', 'penerbit');
            $table->renameColumn('publication_date', 'tahun_terbit');
            $table->renameColumn('pages', 'jumlah_halaman');
            $table->renameColumn('total_copies', 'stok');
            $table->renameColumn('description', 'sinopis');
        });
    }
};
