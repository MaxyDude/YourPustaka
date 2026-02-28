<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $user_name
 * @property string $user_email
 * @property string $user_phone
 * @property int $book_id
 * @property string $barcode_code
 * @property Carbon $loan_date
 * @property Carbon $due_date
 * @property Carbon|null $return_date
 * @property string $status
 * @property string|null $notes
 * @property int|null $approved_by
 * @property int|null $returned_by
 * @property int $denda_total
 * @property string|null $alasan_penolakan
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon|null $deleted_at
 */
class Loan extends Model
{
    use SoftDeletes;

    protected $table = 'peminjamanbuku';

    protected $fillable = [
        'user_name',
        'user_email',
        'user_phone',
        'book_id',
        'barcode_code',
        'loan_date',
        'due_date',
        'return_date',
        'status',
        'notes',
        'approved_by',
        'returned_by',
        'denda_total',
        'alasan_penolakan',
    ];

    protected $casts = [
        'loan_date' => 'datetime',
        'due_date' => 'datetime',
        'return_date' => 'datetime',
    ];

    // Status constants (mapping ke label UI)
    public const STATUS_PENDING = 'pending'; // Menunggu Konfirmasi
    public const STATUS_APPROVED = 'approved'; // Menunggu Pengambilan
    public const STATUS_ACTIVE = 'active'; // Sedang Dipinjam
    public const STATUS_RETURNED = 'returned'; // Selesai
    public const STATUS_REJECTED = 'rejected';

    /**
     * Get the book.
     */
    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    /**
     * Get the user who made the loan (by email).
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_email', 'email');
    }

    /**
     * Get the user who approved the loan.
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Get the user who processed the return.
     */
    public function returnedBy()
    {
        return $this->belongsTo(User::class, 'returned_by');
    }

    /**
     * Generate a unique barcode code (Kode Peminjaman).
     */
    public static function generateBarcodeCode()
    {
        do {
            $code = 'LN-' . strtoupper(substr(md5(uniqid('', true) . random_int(1000, 9999)), 0, 8));
        } while (self::where('barcode_code', $code)->exists());

        return $code;
    }

    /**
     * Accessor: apakah terlambat (computed)
     */
    public function getIsLateAttribute(): bool
    {
        if ($this->status !== self::STATUS_ACTIVE || !$this->due_date) {
            return false;
        }
        return now()->startOfDay()->gt(optional($this->due_date)->startOfDay());
    }

    /**
     * Accessor: denda berjalan (tanpa mempengaruhi denda_total tersimpan)
     */
    public function getRunningFineAttribute(): int
    {
        if (!$this->due_date) return 0;
        $end = $this->return_date ?: now();
        $lateDays = max(0, $this->due_date->startOfDay()->diffInDays($end->startOfDay(), false));
        return $lateDays * 30000;
    }

    /**
     * Helper: label status untuk UI
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING => 'Menunggu Konfirmasi',
            self::STATUS_APPROVED => 'Menunggu Pengambilan',
            self::STATUS_ACTIVE => $this->is_late ? 'Terlambat' : 'Sedang Dipinjam',
            self::STATUS_RETURNED => 'Selesai',
            self::STATUS_REJECTED => 'Ditolak',
            default => ucfirst($this->status),
        };
    }
}
