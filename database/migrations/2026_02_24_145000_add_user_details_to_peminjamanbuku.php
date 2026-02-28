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
            // Add user details columns if they don't exist
            if (!Schema::hasColumn('peminjamanbuku', 'user_name')) {
                $table->string('user_name')->nullable()->after('user_id');
            }

            if (!Schema::hasColumn('peminjamanbuku', 'user_email')) {
                $table->string('user_email')->nullable()->after('user_name');
            }

            if (!Schema::hasColumn('peminjamanbuku', 'user_phone')) {
                $table->string('user_phone')->nullable()->after('user_email');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('peminjamanbuku', function (Blueprint $table) {
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
