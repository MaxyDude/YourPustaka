<?php
/**
 * Reset Books dan Recreate dengan Fixed Version
 */

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Book;

echo "\n╔════════════════════════════════════════════════════════════╗\n";
echo "║          RESET & RECREATE BOOKS - FIXED VERSION           ║\n";
echo "╚════════════════════════════════════════════════════════════╝\n\n";

// Delete semua buku lama
echo "🗑️  Menghapus buku lama...\n";
Book::query()->forceDelete();
echo "✓ Deleted semua buku\n\n";

// Recreate dengan versi yang diperbaiki
echo "📝 Membuat buku test dengan versi yang diperbaiki...\n\n";

$testBooks = [
    [
        'judul' => 'Pemrograman Web Modern',
        'pengarang' => 'Ahmad Rizki',
        'isbn' => 'ISBN-WEB-2024',
        'tahun_terbit' => '2024-01-15',
        'stok' => 5,
        'stok_tersedia' => 5,
        'kategori' => 'Teknologi',
        'penerbit' => 'TechPress Indonesia',
        'jumlah_halaman' => 450,
        'sinopsis' => 'Panduan lengkap membuat aplikasi web modern dengan framework terkini.',
        'cover_buku' => null
    ],
    [
        'judul' => 'Database Design Essentials',
        'pengarang' => 'Budi Santoso',
        'isbn' => 'ISBN-DB-2024',
        'tahun_terbit' => '2023-06-20',
        'stok' => 3,
        'stok_tersedia' => 3,
        'kategori' => 'Database',
        'penerbit' => 'Data Publishing',
        'jumlah_halaman' => 380,
        'sinopsis' => 'Desain database yang efisien dan scalable untuk aplikasi enterprise.',
        'cover_buku' => null
    ],
    [
        'judul' => 'Sastra Indonesia Klasik',
        'pengarang' => 'Siti Nurhaliza',
        'isbn' => 'ISBN-SAT-2024',
        'tahun_terbit' => '2022-03-10',
        'stok' => 8,
        'stok_tersedia' => 8,
        'kategori' => 'Sastra',
        'penerbit' => 'Pusaka Nusantara',
        'jumlah_halaman' => 520,
        'sinopsis' => 'Kumpulan karya sastra terbaik dari pengarang-pengarang klasik Indonesia.',
        'cover_buku' => null
    ],
    [
        'judul' => 'Sejarah Dunia Ringkas',
        'pengarang' => 'Dr. Hendra Wijaya',
        'isbn' => 'ISBN-HIS-2024',
        'tahun_terbit' => '2023-11-05',
        'stok' => 6,
        'stok_tersedia' => 6,
        'kategori' => 'Sejarah',
        'penerbit' => 'History Press',
        'jumlah_halaman' => 620,
        'sinopsis' => 'Ringkasan penting dari peristiwa-peristiwa sejarah dunia yang perlu diketahui.',
        'cover_buku' => null
    ],
    [
        'judul' => 'Fisika Kuantum untuk Pemula',
        'pengarang' => 'Prof. Joni Hardjanta',
        'isbn' => 'ISBN-FIS-2024',
        'tahun_terbit' => '2024-02-01',
        'stok' => 4,
        'stok_tersedia' => 4,
        'kategori' => 'Sains',
        'penerbit' => 'Science Books Indonesia',
        'jumlah_halaman' => 480,
        'sinopsis' => 'Pengenalan konsep fisika kuantum yang mudah dipahami oleh pemula.',
        'cover_buku' => null
    ]
];

$created = 0;
foreach ($testBooks as $bookData) {
    try {
        $book = Book::create($bookData);
        echo "   ✓ [$book->id] $book->judul\n";
        $created++;
    } catch (\Exception $e) {
        echo "   ❌ Gagal: " . $bookData['judul'] . " - " . $e->getMessage() . "\n";
    }
}

echo "\n╔════════════════════════════════════════════════════════════╗\n";
echo "║              ✅ $created BUKU BERHASIL DIBUAT              ║\n";
echo "║                                                            ║\n";
echo "║  ✓ Cover akan di-generate otomatis dari judul buku        ║\n";
echo "║  ✓ URL encoding sudah diperbaiki (%20 bukan +)             ║\n";
echo "║  ✓ Fallback image jika cover tidak tersedia               ║\n";
echo "╚════════════════════════════════════════════════════════════╝\n\n";
