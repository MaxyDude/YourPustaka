<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    DB::statement('SET FOREIGN_KEY_CHECKS=0');
    DB::statement('DROP TABLE IF EXISTS migrations');
    DB::statement('DROP TABLE IF EXISTS books');
    DB::statement('DROP TABLE IF EXISTS peminjamanbuku');
    DB::statement('DROP TABLE IF EXISTS ulasan');
    DB::statement('DROP TABLE IF EXISTS koleksipribadi');
    DB::statement('DROP TABLE IF EXISTS denda');
    DB::statement('DROP TABLE IF EXISTS kategoribuku_relasi');
    DB::statement('DROP TABLE IF EXISTS kategoribuku');
    DB::statement('DROP TABLE IF EXISTS users');
    DB::statement('DROP TABLE IF EXISTS sessions');
    DB::statement('DROP TABLE IF EXISTS cache');
    DB::statement('SET FOREIGN_KEY_CHECKS=1');
    echo "✓ All tables dropped!\n";
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}
?>
