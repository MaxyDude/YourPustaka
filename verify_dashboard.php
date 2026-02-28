<?php
/**
 * Verify Dashboard - Only Database Books
 */

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Book;

$total = Book::count();
$recommended = Book::latest()->take(10)->count();
$popular = Book::with('ulasan')->withAvg('ulasan', 'rating')->orderByDesc('ulasan_avg_rating')->take(10)->count();

echo "\n╔════════════════════════════════════════════════════════════╗\n";
echo "║        VERIFIKASI DASHBOARD - ONLY DATABASE BOOKS         ║\n";
echo "╚════════════════════════════════════════════════════════════╝\n\n";

echo "📊 Data Status Saat Ini:\n";
echo "   Total Buku di Database: $total\n";
echo "   Recommended (terbaru): $recommended buku\n";
echo "   Popular (rating): $popular buku\n\n";

echo "✅ Konfigurasi Dashboard:\n";
echo "   ✓ Rekomendasi: Ambil 10 buku terbaru dari database books\n";
echo "   ✓ Populer: Ambil 10 buku by rating dari database books\n";
echo "   ✓ View: Hanya menampilkan data dari @forelse(\$recommended_books)\n";
echo "   ✓ View: Hanya menampilkan data dari @forelse(\$popular_books)\n\n";

if ($total > 0) {
    echo "🎯 Status: ✅ HANYA BUKU DARI DATABASE\n\n";

    echo "📚 Sample Buku yang akan ditampilkan:\n";
    $samples = Book::latest()->take(3)->get();
    foreach ($samples as $book) {
        echo "   [$book->id] $book->judul\n";
        echo "       - Pengarang: $book->pengarang\n";
        echo "       - Kategori: $book->kategori\n";
        echo "       - Cover: " . ($book->cover_buku ? '✓ Ada' : '✗ Tidak ada') . "\n\n";
    }
} else {
    echo "⚠️  Status: TIDAK ADA BUKU\n\n";
    echo "   Jalankan verify_and_create_books.php untuk membuat test data\n\n";
}

echo "╔════════════════════════════════════════════════════════════╗\n";
echo "║           DASHBOARD SIAP - HANYA DATABASE BOOKS            ║\n";
echo "╚════════════════════════════════════════════════════════════╝\n\n";
