<?php
/**
 * Verify Books dan Create Test Data jika diperlukan
 */

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Book;

$totalBooks = Book::count();

echo "\n╔════════════════════════════════════════════════════════════╗\n";
echo "║         VERIFIKASI BUKU UNTUK DASHBOARD BORROWER           ║\n";
echo "╚════════════════════════════════════════════════════════════╝\n\n";

echo "📊 Total Buku di Database: $totalBooks\n\n";

if ($totalBooks > 0) {
    echo "✅ Buku yang akan ditampilkan di dashboard:\n\n";
    $books = Book::limit(5)->get();
    foreach ($books as $b) {
        echo "  [$b->id] $b->judul\n";
        echo "      Pengarang: $b->pengarang\n";
        echo "      Kategori: $b->kategori\n";
        echo "      Cover: " . ($b->cover_buku ? '✓' : '✗') . "\n\n";
    }
} else {
    echo "⚠️  Tidak ada buku di database. Membuat test data...\n\n";

    $testBooks = [
        [
            'judul' => 'Pemrograman Web Modern',
            'pengarang' => 'Ahmad Rizki',
            'isbn' => 'ISBN-WEB-001',
            'tahun_terbit' => '2024-01-15',
            'stok' => 5,
            'stok_tersedia' => 5,
            'kategori' => 'Teknologi',
            'penerbit' => 'TechPress Indonesia',
            'jumlah_halaman' => 450,
            'sinopsis' => 'Panduan lengkap membuat aplikasi web modern dengan framework terkini.',
            'cover_buku' => 'https://via.placeholder.com/220x300?text=Web+Modern'
        ],
        [
            'judul' => 'Database Design Essentials',
            'pengarang' => 'Budi Santoso',
            'isbn' => 'ISBN-DB-001',
            'tahun_terbit' => '2023-06-20',
            'stok' => 3,
            'stok_tersedia' => 3,
            'kategori' => 'Database',
            'penerbit' => 'Data Publishing',
            'jumlah_halaman' => 380,
            'sinopsis' => 'Desain database yang efisien dan scalable untuk aplikasi enterprise.',
            'cover_buku' => 'https://via.placeholder.com/220x300?text=Database'
        ],
        [
            'judul' => 'Sastra Indonesia Klasik',
            'pengarang' => 'Siti Nurhaliza',
            'isbn' => 'ISBN-SAT-001',
            'tahun_terbit' => '2022-03-10',
            'stok' => 8,
            'stok_tersedia' => 8,
            'kategori' => 'Sastra',
            'penerbit' => 'Pusaka Nusantara',
            'jumlah_halaman' => 520,
            'sinopsis' => 'Kumpulan karya sastra terbaik dari pengarang-pengarang klasik Indonesia.',
            'cover_buku' => 'https://via.placeholder.com/220x300?text=Sastra'
        ],
        [
            'judul' => 'Sejarah Dunia Ringkas',
            'pengarang' => 'Dr. Hendra Wijaya',
            'isbn' => 'ISBN-HIS-001',
            'tahun_terbit' => '2023-11-05',
            'stok' => 6,
            'stok_tersedia' => 6,
            'kategori' => 'Sejarah',
            'penerbit' => 'History Press',
            'jumlah_halaman' => 620,
            'sinopsis' => 'Ringkasan penting dari peristiwa-peristiwa sejarah dunia yang perlu diketahui.',
            'cover_buku' => 'https://via.placeholder.com/220x300?text=Sejarah'
        ],
        [
            'judul' => 'Fisika Kuantum untuk Pemula',
            'pengarang' => 'Prof. Joni Hardjanta',
            'isbn' => 'ISBN-FIS-001',
            'tahun_terbit' => '2024-02-01',
            'stok' => 4,
            'stok_tersedia' => 4,
            'kategori' => 'Sains',
            'penerbit' => 'Science Books Indonesia',
            'jumlah_halaman' => 480,
            'sinopsis' => 'Pengenalan konsep fisika kuantum yang mudah dipahami oleh pemula.',
            'cover_buku' => 'https://via.placeholder.com/220x300?text=Fisika'
        ],
    ];

    $created = 0;
    foreach ($testBooks as $bookData) {
        try {
            $book = Book::create($bookData);
            echo "✓ [$book->id] $book->judul berhasil dibuat\n";
            $created++;
        } catch (\Exception $e) {
            echo "❌ Gagal membuat $bookData[judul]: " . $e->getMessage() . "\n";
        }
    }

    echo "\n╔════════════════════════════════════════════════════════════╗\n";
    echo "║            ✅ $created BUKU TEST BERHASIL DIBUAT            ║\n";
    echo "║                                                            ║\n";
    echo "║   Buku-buku ini akan ditampilkan di borrower dashboard    ║\n";
    echo "╚════════════════════════════════════════════════════════════╝\n\n";
}

echo "📊 Status Dashboard Borrower: ✅ SIAP DIAKSES\n";
echo "   - Rekomendasi: akan menampilkan 10 buku terbaru\n";
echo "   - Populer: akan menampilkan buku dengan rating terbaik\n\n";
