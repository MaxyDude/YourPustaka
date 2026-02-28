<?php

namespace App\Models;

use App\Models\Loan;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * @property Collection<int, Loan> $loans
 * @method \Illuminate\Database\Eloquent\Builder|\App\Models\User whereRole($value)
 */
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'email',
        'username',
        'password',
        'role',
        'no_handphone',
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Relasi: peminjaman buku oleh user ini.
     */
    public function loans(): HasMany
    {
        return $this->hasMany(Loan::class, 'user_id');
    }

    /**
     * Relasi: peminjaman yang disetujui oleh user ini.
     */
    public function approvedLoans(): HasMany
    {
        return $this->hasMany(Loan::class, 'approved_by');
    }

    /**
     * Relasi: pengembalian yang diproses oleh user ini.
     */
    public function processedReturns(): HasMany
    {
        return $this->hasMany(Loan::class, 'returned_by');
    }

    /**
     * Check if user is admin.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is staff (petugas).
     */
    public function isStaff(): bool
    {
        return $this->role === 'petugas';
    }

    /**
     * Check if user is borrower (peminjam).
     */
    public function isBorrower(): bool
    {
        return $this->role === 'peminjam';
    }
}
