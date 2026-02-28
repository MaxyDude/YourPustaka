<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Book;
use Illuminate\Support\Facades\DB;

echo "\n╔════════════════════════════════════════════════════════════╗\n";
echo "║              VERIFIKASI DATA BUKU DI DATABASE             ║\n";
echo "╚════════════════════════════════════════════════════════════╝\n\n";

$books = Book::all();
echo "Total buku: " . $books->count() . "\n\n";

foreach ($books as $book) {
    echo "┌─────────────────────────────────────────────────────────┐\n";
    echo "│ ID: {$book->id}\n";
    echo "│ Judul: {$book->judul}\n";
    echo "│ Pengarang: {$book->pengarang}\n";
    echo "│ Cover: " . ($book->cover_buku === null ? "NULL ✓" : "'" . $book->cover_buku . "'") . "\n";
    echo "└─────────────────────────────────────────────────────────┘\n";
}

echo "\n✓ Verifikasi selesai!\n";
echo "\nSetiap buku akan menampilkan placeholder dengan judul:\n";
echo "https://via.placeholder.com/220x300?text=Pemrograman%20Web%20Modern\n";
echo "(perhatikan %20 untuk space, bukan +)\n\n";
