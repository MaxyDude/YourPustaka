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
        Schema::table('peminjamanbuku', function (Blueprint $table) {
            // Rename columns to match the model
            if (!Schema::hasColumn('peminjamanbuku', 'book_id')) {
                $table->renameColumn('buku_id', 'book_id');
            }

            if (!Schema::hasColumn('peminjamanbuku', 'loan_date')) {
                $table->renameColumn('tanggal_pinjam', 'loan_date');
            }

            if (!Schema::hasColumn('peminjamanbuku', 'due_date')) {
                $table->renameColumn('tanggal_kembali', 'due_date');
            }

            // Add new columns if they don't exist
            if (!Schema::hasColumn('peminjamanbuku', 'barcode_code')) {
                $table->string('barcode_code')->unique()->nullable();
            }

            if (!Schema::hasColumn('peminjamanbuku', 'return_date')) {
                $table->date('return_date')->nullable();
            }

            if (!Schema::hasColumn('peminjamanbuku', 'notes')) {
                $table->text('notes')->nullable();
            }

            if (!Schema::hasColumn('peminjamanbuku', 'approved_by')) {
                $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            }

            if (!Schema::hasColumn('peminjamanbuku', 'returned_by')) {
                $table->foreignId('returned_by')->nullable()->constrained('users')->nullOnDelete();
            }

            if (!Schema::hasColumn('peminjamanbuku', 'denda_total')) {
                $table->decimal('denda_total', 10, 2)->default(0);
            }

            if (!Schema::hasColumn('peminjamanbuku', 'alasan_penolakan')) {
                $table->text('alasan_penolakan')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('peminjamanbuku', function (Blueprint $table) {
            // Restore original column names
            if (Schema::hasColumn('peminjamanbuku', 'book_id')) {
                $table->renameColumn('book_id', 'buku_id');
            }

            if (Schema::hasColumn('peminjamanbuku', 'loan_date')) {
                $table->renameColumn('loan_date', 'tanggal_pinjam');
            }

            if (Schema::hasColumn('peminjamanbuku', 'due_date')) {
                $table->renameColumn('due_date', 'tanggal_kembali');
            }

            // Drop new columns
            if (Schema::hasColumn('peminjamanbuku', 'barcode_code')) {
                $table->dropColumn('barcode_code');
            }

            if (Schema::hasColumn('peminjamanbuku', 'return_date')) {
                $table->dropColumn('return_date');
            }

            if (Schema::hasColumn('peminjamanbuku', 'notes')) {
                $table->dropColumn('notes');
            }

            if (Schema::hasColumn('peminjamanbuku', 'approved_by')) {
                $table->dropForeign(['approved_by']);
                $table->dropColumn('approved_by');
            }

            if (Schema::hasColumn('peminjamanbuku', 'returned_by')) {
                $table->dropForeign(['returned_by']);
                $table->dropColumn('returned_by');
            }

            if (Schema::hasColumn('peminjamanbuku', 'denda_total')) {
                $table->dropColumn('denda_total');
            }

            if (Schema::hasColumn('peminjamanbuku', 'alasan_penolakan')) {
                $table->dropColumn('alasan_penolakan');
            }
        });
    }
};
