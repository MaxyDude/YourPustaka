<?php
/**
 * Debug: Check Books Data in Database
 */

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Book;

echo "\n╔════════════════════════════════════════════════════════════╗\n";
echo "║             DEBUG: CHECK BOOKS DATA IN DATABASE            ║\n";
echo "╚════════════════════════════════════════════════════════════╝\n\n";

$books = Book::all();

if ($books->count() == 0) {
    echo "❌ Tidak ada buku di database!\n\n";
    exit;
}

echo "📊 Total Buku: " . $books->count() . "\n\n";

foreach ($books as $book) {
    echo "═══════════════════════════════════════════════════════════\n";
    echo "ID: $book->id\n";
    echo "Judul: $book->judul\n";
    echo "Pengarang: $book->pengarang\n";
    echo "ISBN: $book->isbn\n";
    echo "Kategori: $book->kategori\n";
    echo "Tahun Terbit: $book->tahun_terbit (Type: " . gettype($book->tahun_terbit) . ")\n";
    echo "Stok: $book->stok\n";
    echo "Stok Tersedia: $book->stok_tersedia\n";
    echo "Penerbit: $book->penerbit\n";
    echo "Jumlah Halaman: $book->jumlah_halaman\n";
    echo "Sinopsis: " . substr($book->sinopsis, 0, 50) . "...\n";
    echo "Cover Buku: " . ($book->cover_buku ? "✓ Ada (URL: " . substr($book->cover_buku, 0, 50) . "...)" : "✗ Tidak ada") . "\n";
    echo "\n";
}

echo "╔════════════════════════════════════════════════════════════╗\n";
echo "║                   DEBUG COMPLETE                           ║\n";
echo "╚════════════════════════════════════════════════════════════╝\n\n";
