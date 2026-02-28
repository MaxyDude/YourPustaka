<?php
/**
 * Test Delete Functionality
 * Script ini memverifikasi bahwa delete bekerja dengan benar
 */

// Setup Laravel
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Book;

echo "\n╔════════════════════════════════════════════════════════════╗\n";
echo "║        TEST DELETE FUNCTIONALITY - FORCE DELETE            ║\n";
echo "╚════════════════════════════════════════════════════════════╝\n\n";

// Test 1: Count books sebelum delete
echo "📊 Test 1: Hitung total buku sebelum delete...\n";
$countBefore = Book::count();
echo "   ✓ Total buku: $countBefore\n\n";

if ($countBefore == 0) {
    echo "❌ Tidak ada buku untuk ditest! Sebelum test, pastikan ada buku di database.\n";
    exit(1);
}

// Test 2: Ambil satu buku untuk didelete
echo "📋 Test 2: Ambil buku untuk didelete...\n";
$bookToDelete = Book::first();
$bookId = $bookToDelete->id;
$bookTitle = $bookToDelete->judul;
echo "   ✓ ID: $bookId\n";
echo "   ✓ Judul: $bookTitle\n";
echo "   ✓ ISBN: " . $bookToDelete->isbn . "\n\n";

// Test 3: Delete buku
echo "🗑️  Test 3: Delete buku dari database (forceDelete)...\n";
try {
    $bookToDelete->forceDelete();
    echo "   ✓ Buku berhasil dihapus\n\n";
} catch (\Exception $e) {
    echo "   ❌ Gagal menghapus buku: " . $e->getMessage() . "\n";
    exit(1);
}

// Test 4: Verifikasi buku sudah tidak ada
echo "🔍 Test 4: Verifikasi buku tidak ada di database...\n";
$countAfter = Book::count();
echo "   ✓ Total buku sebelum: $countBefore\n";
echo "   ✓ Total buku sesudah: $countAfter\n";

// Check specific book
$deletedBook = Book::find($bookId);
if ($deletedBook === null) {
    echo "   ✓ Verifikasi sukses: Buku sudah tidak ada di database!\n\n";
} else {
    echo "   ❌ Verifikasi gagal: Buku masih ada di database!\n";
    exit(1);
}

// Test 5: Verifikasi count berubah
if ($countBefore - $countAfter == 1) {
    echo "✅ Total buku berkurang 1\n\n";
} else {
    echo "❌ Total buku tidak berkurang dengan benar\n";
    exit(1);
}

// Test 6: List sisa buku
echo "📚 Test 5: Daftar buku yang tersisa di database:\n";
$remainingBooks = Book::all();
foreach ($remainingBooks as $book) {
    echo "   - [$book->id] $book->judul\n";
}

echo "\n╔════════════════════════════════════════════════════════════╗\n";
echo "║                 ✅ SEMUA TEST BERHASIL!                    ║\n";
echo "║         Delete functionality bekerja dengan benar!         ║\n";
echo "╚════════════════════════════════════════════════════════════╝\n\n";
