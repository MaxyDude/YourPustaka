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
        Schema::create('denda', function (Blueprint $table) {
            $table->id();

            $table->foreignId('peminjamanbuku_id')
                  ->constrained('peminjamanbuku',)
                  ->cascadeOnDelete();

            $table->foreignId('user_id')
                  ->constrained('users')
                  ->cascadeOnDelete();

            $table->foreignId('books_id')
                  ->constrained('books',)
                  ->cascadeOnDelete();

            $table->decimal('jumlah_denda', 10, 2);
            $table->date('tanggal_denda');


            $table->timestamps();


        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('denda_tabel');
    }
};
