<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('peminjamanbuku', function (Blueprint $table) {
            // Rename foreign key buku_id -> book_id if exists
            if (Schema::hasColumn('peminjamanbuku', 'buku_id')) {
                $table->renameColumn('buku_id', 'book_id');
            }

            // Standardize date columns
            if (Schema::hasColumn('peminjamanbuku', 'tanggal_pinjam')) {
                $table->renameColumn('tanggal_pinjam', 'loan_date');
            }
            if (Schema::hasColumn('peminjamanbuku', 'tanggal_kembali')) {
                $table->renameColumn('tanggal_kembali', 'due_date');
            }

            // Ensure datetime types
            if (!Schema::hasColumn('peminjamanbuku', 'return_date')) {
                $table->dateTime('return_date')->nullable()->after('due_date');
            }

            // Replace enum status with string flexible status
            if (Schema::hasColumn('peminjamanbuku', 'status')) {
                // Change to string; different DBs need different ops; use change() where supported
                try {
                    $table->string('status', 32)->default('pending')->change();
                } catch (\Throwable $e) {
                    // Fallback: if change() not supported on enum, add new column and migrate later
                    if (!Schema::hasColumn('peminjamanbuku', 'status_str')) {
                        $table->string('status_str', 32)->default('pending')->after('status');
                    }
                }
            } else {
                $table->string('status', 32)->default('pending');
            }

            // Operational columns
            if (!Schema::hasColumn('peminjamanbuku', 'barcode_code')) {
                $table->string('barcode_code', 32)->nullable()->unique()->after('book_id');
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
                $table->unsignedBigInteger('denda_total')->nullable();
            }
            if (!Schema::hasColumn('peminjamanbuku', 'alasan_penolakan')) {
                $table->string('alasan_penolakan')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('peminjamanbuku', function (Blueprint $table) {
            if (Schema::hasColumn('peminjamanbuku', 'alasan_penolakan')) {
                $table->dropColumn('alasan_penolakan');
            }
            if (Schema::hasColumn('peminjamanbuku', 'denda_total')) {
                $table->dropColumn('denda_total');
            }
            if (Schema::hasColumn('peminjamanbuku', 'returned_by')) {
                $table->dropForeign(['returned_by']);
                $table->dropColumn('returned_by');
            }
            if (Schema::hasColumn('peminjamanbuku', 'approved_by')) {
                $table->dropForeign(['approved_by']);
                $table->dropColumn('approved_by');
            }
            if (Schema::hasColumn('peminjamanbuku', 'notes')) {
                $table->dropColumn('notes');
            }
            if (Schema::hasColumn('peminjamanbuku', 'barcode_code')) {
                $table->dropUnique(['barcode_code']);
                $table->dropColumn('barcode_code');
            }
            if (Schema::hasColumn('peminjamanbuku', 'return_date')) {
                $table->dropColumn('return_date');
            }
            if (Schema::hasColumn('peminjamanbuku', 'status_str')) {
                $table->dropColumn('status_str');
            }
        });
    }
};
