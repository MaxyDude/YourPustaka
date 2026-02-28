<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('peminjamanbuku', function (Blueprint $table) {
            // Disable foreign key constraint checks temporarily
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');

            // Try to drop the foreign key constraint
            try {
                $table->dropForeign('peminjamanbuku_user_id_foreign');
            } catch (\Exception $e) {
                // Foreign key may not exist, continue anyway
            }
        });

        Schema::table('peminjamanbuku', function (Blueprint $table) {
            // Drop user_id column
            if (Schema::hasColumn('peminjamanbuku', 'user_id')) {
                $table->dropColumn('user_id');
            }

            // Enable foreign key constraint checks
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            // Ensure user details columns exist
            if (!Schema::hasColumn('peminjamanbuku', 'user_name')) {
                $table->string('user_name')->first();
            }

            if (!Schema::hasColumn('peminjamanbuku', 'user_email')) {
                $table->string('user_email')->after('user_name');
            }

            if (!Schema::hasColumn('peminjamanbuku', 'user_phone')) {
                $table->string('user_phone')->after('user_email');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('peminjamanbuku', function (Blueprint $table) {
            // Restore user_id foreign key
            if (!Schema::hasColumn('peminjamanbuku', 'user_id')) {
                $table->foreignId('user_id')
                      ->after('id')
                      ->constrained('users')
                      ->cascadeOnDelete();
            }

            // Drop user details columns
            if (Schema::hasColumn('peminjamanbuku', 'user_name')) {
                $table->dropColumn('user_name');
            }

            if (Schema::hasColumn('peminjamanbuku', 'user_email')) {
                $table->dropColumn('user_email');
            }

            if (Schema::hasColumn('peminjamanbuku', 'user_phone')) {
                $table->dropColumn('user_phone');
            }
        });
    }
};
