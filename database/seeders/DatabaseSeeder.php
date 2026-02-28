<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Book;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed categories first
        $this->call(KategoriSeeder::class);

        // Create Admin User
        User::create([
            'name' => 'Admin Perpustakaan',
            'email' => 'admin@yourpustaka.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'phone' => '081234567890',
            'address' => 'Jl. Perpustakaan No. 1',
            'id_number' => 'ADMIN001',
        ]);

        // Create Staff Users
        User::create([
            'name' => 'Petugas 1',
            'email' => 'petugas1@yourpustaka.com',
            'password' => Hash::make('password123'),
            'role' => 'petugas',
            'phone' => '081234567891',
            'address' => 'Jl. Perpustakaan No. 2',
            'id_number' => 'PETUGAS001',
        ]);

        User::create([
            'name' => 'Petugas 2',
            'email' => 'petugas2@yourpustaka.com',
            'password' => Hash::make('password123'),
            'role' => 'petugas',
            'phone' => '081234567892',
            'address' => 'Jl. Perpustakaan No. 3',
            'id_number' => 'PETUGAS002',
        ]);

        // Create Borrower Users
        for ($i = 1; $i <= 5; $i++) {
            User::create([
                'name' => 'Peminjam ' . $i,
                'email' => 'peminjam' . $i . '@example.com',
                'password' => Hash::make('password123'),
                'role' => 'peminjam',
                'phone' => '08123456789' . $i,
                'address' => 'Jl. Peminjam No. ' . $i,
                'id_number' => 'MEMBER' . str_pad($i, 3, '0', STR_PAD_LEFT),
            ]);
        }

        // Create Sample Books
        $books = [
            [
                'title' => 'Laskar Pelangi',
                'author' => 'Andrea Hirata',
                'isbn' => '978-979-9018-77-2',
                'description' => 'Kisah inspiratif tentang sekelompok anak-anak dari keluarga miskin yang bersemangat mengejar pendidikan di sebuah sekolah kecil di Belitung.',
                'total_copies' => 5,
                'category' => 'Fiksi',
                'publication_date' => '2005-09-25',
                'publisher' => 'Bentang',
            ],
            [
                'title' => 'Harry Potter and the Philosopher\'s Stone',
                'author' => 'J.K. Rowling',
                'isbn' => '978-0-7475-3269-9',
                'description' => 'Petualangan seorang anak laki-laki yang baru mengetahui bahwa dia adalah penyihir dan diundang untuk belajar di sekolah sihir Hogwarts.',
                'total_copies' => 8,
                'category' => 'Fantasi',
                'publication_date' => '1997-06-26',
                'publisher' => 'Bloomsbury',
            ],
            [
                'title' => 'Buku Pintar JavaScript',
                'author' => 'Romi Satria Wahono',
                'isbn' => '978-602-262-051-3',
                'description' => 'Panduan lengkap mempelajari JavaScript dari dasar hingga lanjutan untuk membuat aplikasi web interaktif.',
                'total_copies' => 4,
                'category' => 'Teknologi',
                'publication_date' => '2013-03-15',
                'publisher' => 'Elex Media Komputindo',
            ],
            [
                'title' => 'Sapiens',
                'author' => 'Yuval Noah Harari',
                'isbn' => '978-0-06-213003-4',
                'description' => 'Perjalanan manusia dari era zaman batu hingga era modern melalui revolusi kognitif, pertanian, dan industri.',
                'total_copies' => 6,
                'category' => 'Non-Fiksi',
                'publication_date' => '2011-09-01',
                'publisher' => 'Harvill Secker',
            ],
            [
                'title' => 'The Alchemist',
                'author' => 'Paulo Coelho',
                'isbn' => '978-0-06-085494-9',
                'description' => 'Kisah perjalanan spiritual seorang penggembala muda yang mencari harta karun dan menemukan makna hidup sejati.',
                'total_copies' => 7,
                'category' => 'Fiksi',
                'publication_date' => '1988-05-15',
                'publisher' => 'HarperCollins',
            ],
            [
                'title' => 'Clean Code',
                'author' => 'Robert C. Martin',
                'isbn' => '978-0-13-235088-4',
                'description' => 'Panduan praktis menulis kode yang bersih, mudah dipahami, dan dapat dipelihara dengan baik.',
                'total_copies' => 3,
                'category' => 'Teknologi',
                'publication_date' => '2008-08-01',
                'publisher' => 'Prentice Hall',
            ],
        ];

        foreach ($books as $book) {
            Book::create([
                ...$book,
                'available_copies' => $book['total_copies'],
            ]);
        }
    }
}
