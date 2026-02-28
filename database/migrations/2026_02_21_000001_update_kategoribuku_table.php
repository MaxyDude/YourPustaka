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
        Schema::table('kategoribuku', function (Blueprint $table) {
            if (!Schema::hasColumn('kategoribuku', 'deskripsi')) {
                $table->text('deskripsi')->nullable()->after('slug');
            }
            if (!Schema::hasColumn('kategoribuku', 'warna')) {
                $table->string('warna')->default('#4361ee')->after('deskripsi');
            }
            if (!Schema::hasColumn('kategoribuku', 'icon')) {
                $table->string('icon')->default('fas fa-folder')->after('warna');
            }
            if (!Schema::hasColumn('kategoribuku', 'urutan')) {
                $table->integer('urutan')->default(0)->after('icon');
            }
            if (!Schema::hasColumn('kategoribuku', 'kata_kunci')) {
                $table->text('kata_kunci')->nullable()->after('urutan');
            }
            if (!Schema::hasColumn('kategoribuku', 'status')) {
                $table->enum('status', ['active', 'inactive'])->default('active')->after('kata_kunci');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kategoribuku', function (Blueprint $table) {
            $table->dropColumn(['deskripsi', 'warna', 'icon', 'urutan', 'kata_kunci', 'status']);
        });
    }
};
