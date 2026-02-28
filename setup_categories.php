<?php
/**
 * Script untuk setup kategori buku ke database
 * Jalankan: php setup_categories.php
 */

// Run migrations
echo "Running migrations...\n";
exec('php artisan migrate 2>&1', $output, $return_code);
echo implode("\n", $output) . "\n";

if ($return_code !== 0) {
    echo "Migration failed!\n";
    exit(1);
}

// Run seeders
echo "\nSeeding categories...\n";
exec('php artisan db:seed --class=KategoriSeeder 2>&1', $output, $return_code);
echo implode("\n", $output) . "\n";

if ($return_code === 0) {
    echo "\n✓ Setup completed successfully!\n";
} else {
    echo "\n✗ Seeding failed!\n";
    exit(1);
}
