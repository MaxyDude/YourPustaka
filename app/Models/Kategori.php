<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kategori extends Model
{
    use HasFactory;

    protected $table = 'kategoribuku';

    protected $fillable = [
        'nama_kategori',
        'slug',
        'deskripsi',
        'warna',
        'icon',
        'urutan',
        'kata_kunci',
        'status'
    ];

    protected $casts = [
        'status' => 'string',
    ];

    public function books()
    {
        return $this->belongsToMany(Book::class, 'kategoribuku_relasi', 'kategoribuku_id', 'buku_id');
    }
}
