<?php

namespace Database\Seeders;

use App\Models\Kategori;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class KategoriSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'nama_kategori' => 'Teknologi',
                'deskripsi' => 'Buku tentang teknologi, programming, dan inovasi digital',
                'warna' => '#4361ee',
                'icon' => 'fas fa-laptop-code',
                'urutan' => 1,
                'kata_kunci' => 'teknologi, programming, IT, coding, software',
                'status' => 'active'
            ],
            [
                'nama_kategori' => 'Sastra',
                'deskripsi' => 'Koleksi karya sastra, puisi, dan novel klasik',
                'warna' => '#2ecc71',
                'icon' => 'fas fa-book',
                'urutan' => 2,
                'kata_kunci' => 'sastra, puisi, novel, fiksi, cerita',
                'status' => 'active'
            ],
            [
                'nama_kategori' => 'Sejarah',
                'deskripsi' => 'Buku sejarah dari berbagai era dan zona geografis',
                'warna' => '#f39c12',
                'icon' => 'fas fa-clock',
                'urutan' => 3,
                'kata_kunci' => 'sejarah, masa lalu, warisan, peradaban',
                'status' => 'active'
            ],
            [
                'nama_kategori' => 'Sains',
                'deskripsi' => 'Buku ilmu pengetahuan alam dan penelitian sains',
                'warna' => '#9b59b6',
                'icon' => 'fas fa-flask',
                'urutan' => 4,
                'kata_kunci' => 'sains, penelitian, alam, biologi, kimia, fisika',
                'status' => 'active'
            ],
            [
                'nama_kategori' => 'Fiksi',
                'deskripsi' => 'Cerita fiksi, misteri, dan fantasi yang menarik',
                'warna' => '#e74c3c',
                'icon' => 'fas fa-mask',
                'urutan' => 5,
                'kata_kunci' => 'fiksi, cerita, fantasi, misteri, petualangan',
                'status' => 'active'
            ],
            [
                'nama_kategori' => 'Pelajaran',
                'deskripsi' => 'Buku pelajaran dan materi edukasi untuk semua tingkat',
                'warna' => '#3498db',
                'icon' => 'fas fa-graduation-cap',
                'urutan' => 6,
                'kata_kunci' => 'pelajaran, edukasi, pendidikan, belajar, sekolah',
                'status' => 'active'
            ],
            [
                'nama_kategori' => 'Bisnis',
                'deskripsi' => 'Panduan bisnis, entrepreneurship, dan manajemen',
                'warna' => '#1abc9c',
                'icon' => 'fas fa-chart-line',
                'urutan' => 7,
                'kata_kunci' => 'bisnis, entrepreneurship, manajemen, ekonomi, keuangan',
                'status' => 'active'
            ],
            [
                'nama_kategori' => 'Seni',
                'deskripsi' => 'Buku tentang seni, desain, dan kreativitas',
                'warna' => '#e67e22',
                'icon' => 'fas fa-palette',
                'urutan' => 8,
                'kata_kunci' => 'seni, desain, kreativitas, lukisan, kerajinan',
                'status' => 'active'
            ]
        ];

        foreach ($categories as $category) {
            Kategori::create([
                'nama_kategori' => $category['nama_kategori'],
                'slug' => Str::slug($category['nama_kategori']),
                'deskripsi' => $category['deskripsi'],
                'warna' => $category['warna'],
                'icon' => $category['icon'],
                'urutan' => $category['urutan'],
                'kata_kunci' => $category['kata_kunci'],
                'status' => $category['status']
            ]);
        }
    }
}
