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
            // Change publication_date from year to date format if needed
            $table->date('publication_date')->change();

            // Add available_copies column if it doesn't exist
            if (!Schema::hasColumn('books', 'available_copies')) {
                $table->integer('available_copies')->default(0)->after('total_copies');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('books', function (Blueprint $table) {
            if (Schema::hasColumn('books', 'available_copies')) {
                $table->dropColumn('available_copies');
            }
        });
    }
};
