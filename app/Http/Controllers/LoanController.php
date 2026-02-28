<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Loan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LoanController extends Controller
{
    /**
     * Display a listing of loans.
     */
    public function index()
    {
        /** @var User $user */
        $user = Auth::user();

        if ($user->role === 'peminjam') {
            $loans = $user->loans()->paginate(10);
        } else {
            $loans = Loan::paginate(10);
        }

        return view('pinjaman.index', compact('loans'));
    }

    /**
     * Show the form for creating a new loan.
     */
    public function create()
    {
        $books = Book::where('available_copies', '>', 0)->get();
        return view('pinjaman.create', compact('books'));
    }

    /**
     * Show borrow form untuk buku spesifik.
     */
    public function showBorrowForm(Book $book)
    {
        return view('pinjaman.halaman_peminjaman', compact('book'));
    }

    /**
     * Temporary: Show borrow form using query params (no DB lookup).
     * Useful for prototype/testing without requiring the Book record.
     */
    public function showBorrowTemp(Request $request)
    {
        $data = $request->only([
            'id', 'title', 'author', 'cover_image', 'category', 'rating', 'published_year', 'isbn', 'pages', 'recommendation_percentage', 'description', 'available_copies'
        ]);

        $defaults = [
            'id' => null,
            'title' => 'Judul Buku',
            'author' => 'Penulis',
            'cover_image' => 'https://via.placeholder.com/220x300',
            'category' => 'Umum',
            'rating' => '4.5',
            'published_year' => now()->year,
            'isbn' => 'N/A',
            'pages' => '300',
            'recommendation_percentage' => '80',
            'description' => 'Deskripsi buku tidak tersedia.',
            'available_copies' => 1,
        ];

        $bookData = array_merge($defaults, array_filter($data, function ($v) { return $v !== null && $v !== ''; }));

        // Convert to object so the Blade template can use -> syntax
        $book = (object) $bookData;

        return view('pinjaman.borrow', compact('book'));
    }

    /**
     * Store a newly created loan in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'book_id' => 'required|exists:books,id',
            'duration' => 'nullable|integer|in:7,14,30',
            'phone' => 'required|string|min:10|max:13',
            'notes' => 'nullable|string',
        ]);

        return DB::transaction(function () use ($validated, $request) {
            $book = Book::where('id', $validated['book_id'])
                ->lockForUpdate()
                ->first();

            if (!$book) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Buku tidak ditemukan',
                    ], 404);
                }
                return redirect()->back()->with('error', 'Buku tidak ditemukan');
            }

            if ($this->getBookAvailableStock($book) < 1) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Buku tidak tersedia',
                    ], 422);
                }
                return redirect()->back()->with('error', 'Buku tidak tersedia');
            }

            // Kurangi stok saat peminjam mengajukan agar stok langsung ter-reserve.
            $this->decrementBookStock($book);

            // Hitung due_date dari duration
            $duration = (int) ($validated['duration'] ?? 14);
            $loanDate = now();
            $dueDate = $loanDate->copy()->addDays($duration);

            Loan::create([
                'user_name' => Auth::user()->name,
                'user_email' => Auth::user()->email,
                'user_phone' => $validated['phone'] ?? Auth::user()->no_handphone,
                'book_id' => $validated['book_id'],
                'loan_date' => $loanDate,
                'due_date' => $dueDate,
                'barcode_code' => Loan::generateBarcodeCode(),
                'status' => 'pending',
                'notes' => $validated['notes'] ?? null,
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Permintaan peminjaman berhasil dibuat',
                    'redirect_url' => route('loans.detail', ['book_id' => $book->id]),
                ], 201);
            }

            // Redirect ke halaman detail peminjaman
            return redirect()->route('loans.detail', ['book_id' => $book->id])->with('success', 'Permintaan peminjaman berhasil dibuat');
        });
    }

    /**
     * Show detail page setelah peminjaman diajukan
     */
    public function showDetail($book_id)
    {
        $book = Book::find($book_id);

        if (!$book) {
            return redirect()->route('dashboard')->with('error', 'Buku tidak ditemukan');
        }

        // Ambil loan terakhir dari user untuk buku ini
        // Gunakan user_email karena user_id sudah dihapus
        $loan = Loan::where('user_email', Auth::user()->email)
                    ->where('book_id', $book_id)
                    ->orderBy('created_at', 'desc')
                    ->first();

        if (!$loan) {
            return redirect()->route('dashboard')->with('error', 'Data peminjaman tidak ditemukan');
        }

        if (empty($loan->barcode_code)) {
            $loan->barcode_code = Loan::generateBarcodeCode();
            $loan->save();
        }

        return view('pinjaman.detail_peminjaman', compact('book', 'loan'));
    }
    public function show(Loan $loan)
    {
        $this->authorize('view', $loan);
        return view('pinjaman.show', compact('loan'));
    }

    /**
     * Show pending loans for approval (Petugas).
     */
    public function pending()
    {
        $this->authorize('isStaff');

        $loans = Loan::where('status', 'pending')->paginate(10);
        return view('pinjaman.pending', compact('loans'));
    }

    /**
     * Approve a loan (Petugas).
     */
    public function approve(Loan $loan)
    {
        $this->authorize('isStaff');

        if ($loan->status !== 'pending') {
            return redirect()->back()->with('error', 'Loan tidak dapat disetujui');
        }

        $loan->update([
            'status' => 'approved',
            'approved_by' => Auth::id(),
        ]);

        return redirect()->back()->with('success', 'Peminjaman disetujui');
    }

    /**
     * Scan and activate loan (Petugas).
     */
    public function scanBarcode(Request $request)
    {
        $this->authorize('isStaff');

        $validated = $request->validate([
            'barcode_code' => 'required|string',
        ]);

        $rawCode = trim((string) $validated['barcode_code']);
        $barcodeCode = $rawCode;

        // Support beberapa format input: plain code, JSON object, JSON string.
        if (str_starts_with($rawCode, '{') || str_starts_with($rawCode, '"')) {
            $parsed = json_decode($rawCode, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                if (is_array($parsed)) {
                    $barcodeCode = (string) (
                        $parsed['barcode_code']
                        ?? $parsed['ticketCode']
                        ?? $parsed['code']
                        ?? $rawCode
                    );
                } elseif (is_string($parsed)) {
                    $barcodeCode = $parsed;
                }
            }
        }

        // Support jika scanner mengembalikan URL atau teks panjang yang memuat kode tiket.
        if (preg_match('/([A-Z]{2,4}-[A-Z0-9]{6,16})/i', $barcodeCode, $matches)) {
            $barcodeCode = $matches[1];
        }

        $barcodeCode = strtoupper(trim($barcodeCode));

        return DB::transaction(function () use ($barcodeCode) {
            $loan = Loan::with('book')
                ->whereRaw('UPPER(barcode_code) = ?', [$barcodeCode])
                ->lockForUpdate()
                ->first();

            if (!$loan) {
                return response()->json(['error' => 'Kode tiket tidak ditemukan'], 404);
            }

            if (in_array($loan->status, ['returned', 'rejected', 'cancelled'], true)) {
                return response()->json([
                    'error' => 'Status peminjaman tidak dapat diaktifkan lagi',
                ], 400);
            }

            if ($loan->status === 'active') {
                return response()->json([
                    'success' => true,
                    'message' => 'Peminjaman sudah berstatus dipinjam',
                    'loan' => $loan,
                ]);
            }

            if (!in_array($loan->status, ['pending', 'approved'], true)) {
                return response()->json([
                    'error' => 'Status peminjaman tidak valid untuk proses scan',
                ], 400);
            }

            $updates = [
                'status' => 'active',
                'loan_date' => now(),
            ];

            // Jika masih pending, auto-konfirmasi oleh admin/petugas yang melakukan scan.
            if ($loan->status === 'pending') {
                $updates['approved_by'] = Auth::id();
            }

            $loan->update($updates);

            // Stok sudah dikurangi saat pengajuan (store), jadi tidak dikurangi lagi saat scan.

            $loan->refresh()->load('book');

            return response()->json([
                'success' => true,
                'message' => 'Tiket berhasil diverifikasi dan status diubah menjadi dipinjam',
                'loan' => $loan,
            ]);
        });
    }

    /**
     * Process return (Petugas).
     */
    public function processReturn(Request $request, Loan $loan)
    {
        $this->authorize('isStaff');

        if ($loan->status !== 'active') {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Peminjaman tidak aktif',
                ], 422);
            }

            return redirect()->back()->with('error', 'Peminjaman tidak aktif');
        }

        $loan->update([
            'status' => 'returned',
            'return_date' => now(),
            'returned_by' => Auth::id(),
        ]);

        if ($loan->book) {
            $this->incrementBookStock($loan->book);
        }

        if ($request->expectsJson()) {
            $freshBook = $loan->book ? $loan->book->fresh() : null;

            return response()->json([
                'success' => true,
                'message' => 'Pengembalian berhasil diproses',
                'loan' => [
                    'id' => $loan->id,
                    'status' => $loan->status,
                ],
                'book' => $freshBook ? [
                    'id' => $freshBook->id,
                    'stok_tersedia' => $freshBook->stok_tersedia ?? null,
                    'available_copies' => $freshBook->available_copies ?? null,
                ] : null,
            ], 200);
        }

        return redirect()->back()->with('success', 'Pengembalian berhasil diproses');
    }

    /**
     * Show return form for petugas.
     */
    public function returnForm()
    {
        $this->authorize('isStaff');

        $activeLoans = Loan::where('status', 'active')->paginate(10);
        return view('pinjaman.return-form', compact('activeLoans'));
    }

    /**
     * Ambil stok tersedia dari kolom yang aktif dipakai sistem.
     */
    private function getBookAvailableStock(Book $book): int
    {
        $attributes = $book->getAttributes();

        if (array_key_exists('stok_tersedia', $attributes)) {
            return max(0, (int) $book->stok_tersedia);
        }

        if (array_key_exists('available_copies', $attributes)) {
            return max(0, (int) $book->available_copies);
        }

        return 0;
    }

    /**
     * Kurangi stok buku pada semua kolom stok yang tersedia (sinkronisasi legacy).
     */
    private function decrementBookStock(Book $book): void
    {
        $attributes = $book->getAttributes();
        $updates = [];
        $nextStock = max(0, $this->getBookAvailableStock($book) - 1);

        if (array_key_exists('stok_tersedia', $attributes)) {
            $updates['stok_tersedia'] = $nextStock;
        }

        if (array_key_exists('available_copies', $attributes)) {
            $updates['available_copies'] = $nextStock;
        }

        if (!empty($updates)) {
            DB::table('books')->where('id', $book->id)->update($updates);
        }
    }

    /**
     * Tambah stok buku pada semua kolom stok yang tersedia (sinkronisasi legacy).
     */
    private function incrementBookStock(Book $book): void
    {
        $attributes = $book->getAttributes();
        $updates = [];
        $nextStock = max(0, $this->getBookAvailableStock($book) + 1);

        if (array_key_exists('stok_tersedia', $attributes)) {
            $updates['stok_tersedia'] = $nextStock;
        }

        if (array_key_exists('available_copies', $attributes)) {
            $updates['available_copies'] = $nextStock;
        }

        if (!empty($updates)) {
            DB::table('books')->where('id', $book->id)->update($updates);
        }
    }
}
