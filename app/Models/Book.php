<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $title
 * @property string $author
 * @property string $isbn
 * @property string|null $description
 * @property int $total_copies
 * @property int $stok_tersedia
 * @property string|null $kategori
 * @property Carbon|null $publication_date
 * @property string|null $publisher
 * @property string|null $cover_image
 * @property int|null $pages
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon|null $deleted_at
 */
class Book extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'author',
        'isbn',
        'description',
        'total_copies',
        'stok_tersedia',
        'kategori',
        'publication_date',
        'publisher',
        'cover_image',
        'pages',
    ];

    protected $casts = [
        'publication_date' => 'date',
    ];

    /**
     * Get the loans for the book.
     */
    public function loans()
    {
        return $this->hasMany(Loan::class);
    }

    /**
     * Get active loans for the book.
     */
    public function activeLoans()
    {
        return $this->loans()->whereIn('status', ['pending', 'approved', 'active']);
    }

    /**
     * Get the reviews for the book.
     */
    public function ulasan()
    {
        return $this->hasMany(Ulasan::class, 'buku_id');
    }

    /**
     * Get the categories for the book (many-to-many).
     */
    public function kategoris()
    {
        return $this->belongsToMany(Kategori::class, 'kategoribuku_relasi', 'buku_id', 'kategoribuku_id');
    }

    /**
     * Get the average rating for the book.
     */
    public function getAverageRatingAttribute()
    {
        return $this->ulasan()->avg('rating') ?? 0;
    }
}
