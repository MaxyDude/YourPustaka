<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Kategori;
use App\Models\Loan;
use App\Models\Ulasan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class AdminController extends Controller
{
    /**
     * Display the admin dashboard.
     */
    public function dashboard()
    {
        // Get statistics
        $totalBooks = Book::count();
        $totalUsers = User::count();
        $borrowedBooks = Loan::where('status', 'active')->count();
        $pendingLoans = Loan::where('status', 'pending')->count();

        // Get all books for display in grid
        $books = Book::all();

        // Get recent data for tables
        $loans = Loan::with(['user', 'book', 'approver'])
            ->latest()
            ->take(10)
            ->get();
        $users = User::whereIn('role', ['petugas', 'user'])
            ->latest()
            ->get();

        return view($this->panelViewPrefix() . '.dashboard', compact(
            'totalBooks',
            'totalUsers',
            'borrowedBooks',
            'pendingLoans',
            'books',
            'loans',
            'users'
        ));
    }

    /**
     * Print report page for admin modules.
     */
    public function printReport(string $type)
    {
        $type = strtolower($type);
        $generatedAt = now();
        $printedBy = auth()->user()?->name ?? 'Admin';

        if (request()->routeIs('petugas.*') && $type === 'users') {
            abort(403, 'Laporan manajemen pengguna hanya untuk admin.');
        }

        $statusLabels = [
            'pending' => 'Menunggu Konfirmasi',
            'approved' => 'Menunggu Pengambilan',
            'active' => 'Dipinjam',
            'returned' => 'Dikembalikan',
            'rejected' => 'Ditolak',
        ];

        switch ($type) {
            case 'books':
                $books = Book::with('kategoris:id,nama_kategori')
                    ->withCount('ulasan')
                    ->withAvg('ulasan', 'rating')
                    ->orderBy('title')
                    ->get();

                $reportTitle = 'Laporan Data Buku';
                $columns = ['ID', 'Judul', 'Penulis', 'ISBN', 'Kategori', 'Stok', 'Rating'];
                $rows = $books->map(function ($book) {
                    $kategori = $book->kategoris->first()?->nama_kategori ?: ($book->kategori ?: '-');
                    $rating = number_format((float) ($book->ulasan_avg_rating ?? 0), 1);

                    return [
                        $book->id,
                        $book->title,
                        $book->author ?: '-',
                        $book->isbn ?: '-',
                        $kategori,
                        ($book->stok_tersedia ?? 0) . '/' . ($book->total_copies ?? 0),
                        $rating . ' (' . ($book->ulasan_count ?? 0) . ' ulasan)',
                    ];
                })->all();
                break;

            case 'loans':
                $loans = Loan::with(['book:id,title,isbn', 'user:id,name,email', 'approver:id,name'])
                    ->latest()
                    ->get();

                $reportTitle = 'Laporan Data Peminjaman';
                $columns = ['ID', 'Peminjam', 'Email', 'Buku', 'Tgl Pinjam', 'Jatuh Tempo', 'Tgl Kembali', 'Status'];
                $rows = $loans->map(function ($loan) use ($statusLabels) {
                    return [
                        $loan->id,
                        $loan->user_name ?: ($loan->user?->name ?? '-'),
                        $loan->user_email ?: ($loan->user?->email ?? '-'),
                        $loan->book?->title ?? '-',
                        $loan->loan_date?->format('d M Y H:i') ?? '-',
                        $loan->due_date?->format('d M Y H:i') ?? '-',
                        $loan->return_date?->format('d M Y H:i') ?? '-',
                        $statusLabels[$loan->status] ?? ucfirst((string) $loan->status),
                    ];
                })->all();
                break;

            case 'users':
                $users = User::where('role', 'user')
                    ->orderBy('name')
                    ->get();

                $reportTitle = 'Laporan Manajemen Pengguna';
                $columns = ['ID', 'Nama', 'Email', 'Telepon', 'Role', 'Tanggal Daftar'];
                $rows = $users->map(function ($user) {
                    $roleLabel = [
                        'admin' => 'Administrator',
                        'petugas' => 'Petugas',
                        'user' => 'User',
                        'member' => 'Member',
                        'peminjam' => 'Peminjam',
                    ];

                    return [
                        $user->id,
                        $user->name ?? '-',
                        $user->email ?? '-',
                        $user->no_handphone ?? '-',
                        $roleLabel[$user->role] ?? ucfirst((string) $user->role),
                        $user->created_at?->format('d M Y H:i') ?? '-',
                    ];
                })->all();
                break;

            case 'categories':
                $categories = Kategori::withCount('books')->orderBy('urutan')->orderBy('nama_kategori')->get();

                $reportTitle = 'Laporan Data Kategori';
                $columns = ['ID', 'Nama Kategori', 'Status', 'Jumlah Buku', 'Urutan', 'Dibuat'];
                $rows = $categories->map(function ($category) {
                    return [
                        $category->id,
                        $category->nama_kategori,
                        $category->status === 'active' ? 'Aktif' : 'Nonaktif',
                        $category->books_count ?? 0,
                        $category->urutan ?? 0,
                        $category->created_at?->format('d M Y H:i') ?? '-',
                    ];
                })->all();
                break;

            case 'reviews':
                $reviews = Ulasan::with(['book:id,title', 'user:id,name,email'])
                    ->latest()
                    ->get();

                $reportTitle = 'Laporan Data Ulasan';
                $columns = ['ID', 'Buku', 'Pengguna', 'Email', 'Rating', 'Komentar', 'Tanggal'];
                $rows = $reviews->map(function ($review) {
                    return [
                        $review->id,
                        $review->book?->title ?? '-',
                        $review->user?->name ?? 'Anonim',
                        $review->user?->email ?? '-',
                        ($review->rating ?? 0) . '/5',
                        Str::limit((string) ($review->komentar ?? '-'), 80),
                        $review->created_at?->format('d M Y H:i') ?? '-',
                    ];
                })->all();
                break;

            default:
                abort(404);
        }

        return view($this->panelViewPrefix() . '.laporan_print', compact(
            'reportTitle',
            'columns',
            'rows',
            'generatedAt',
            'printedBy'
        ));
    }

    /**
     * Get categories as JSON
     */
    public function getCategoriesJson()
    {
        $categories = Kategori::orderBy('urutan')->get();
        return response()->json($categories);
    }

    /**
     * Store a newly created book in database.
     */
    public function storeBook(Request $request)
    {
        try {
            // Validasi input
            $validated = $request->validate([
                'bookTitle' => 'required|string|max:255',
                'bookAuthor' => 'required|string|max:255',
                'bookIsbn' => 'required|string|max:20|unique:books,isbn',
                'bookYear' => 'required|integer|min:1900|max:2100',
                'bookStock' => 'required|integer|min:1',
                'bookCategoryId' => 'required|integer|exists:kategoribuku,id',
                'bookPublisher' => 'required|string|max:255',
                'bookPages' => 'required|integer|min:1',
                'bookDescription' => 'required|string|max:1000',
                'bookImageUrl' => 'nullable|string',
                'bookImage' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120', // Max 5MB
            ], [
                'bookTitle.required' => 'Judul buku harus diisi',
                'bookAuthor.required' => 'Penulis buku harus diisi',
                'bookIsbn.required' => 'ISBN harus diisi',
                'bookIsbn.unique' => 'ISBN sudah terdaftar dalam sistem',
                'bookYear.required' => 'Tahun terbit harus diisi',
                'bookYear.min' => 'Tahun terbit tidak valid',
                'bookStock.required' => 'Stok buku harus diisi',
                'bookStock.min' => 'Stok buku minimal 1',
                'bookCategoryId.required' => 'Kategori buku harus dipilih',
                'bookCategoryId.exists' => 'Kategori buku tidak valid',
                'bookPublisher.required' => 'Penerbit buku harus diisi',
                'bookPages.required' => 'Jumlah halaman harus diisi',
                'bookPages.min' => 'Jumlah halaman minimal 1',
                'bookDescription.required' => 'Deskripsi buku harus diisi',
                'bookImage.image' => 'File harus berupa gambar',
                'bookImage.mimes' => 'Format gambar harus JPEG, PNG, JPG, GIF, atau WebP',
                'bookImage.max' => 'Ukuran gambar maksimal 5MB',
            ]);

            // Tentukan cover image (dari upload file, data URL, atau dari URL reguler)
            $coverImage = null;

            // Jika ada file upload, proses dan simpan
            if ($request->hasFile('bookImage') && $request->file('bookImage')->isValid()) {
                $file = $request->file('bookImage');
                $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

                // Simpan file ke public/storage/covers
                $file->move(public_path('storage/covers'), $fileName);
                $coverImage = 'storage/covers/' . $fileName;
            }
            // Jika ada data URL (base64 encoded image), decode dan simpan
            elseif (!empty($validated['bookImageUrl']) && strpos($validated['bookImageUrl'], 'data:image') === 0) {
                $coverImage = $this->saveBase64Image($validated['bookImageUrl']);
            }
            // Jika ada URL gambar reguler, gunakan URL tersebut
            elseif (!empty($validated['bookImageUrl'])) {
                $coverImage = $validated['bookImageUrl'];
            }

            // Get kategori untuk mendapatkan nama kategori
            $kategori = Kategori::find($validated['bookCategoryId']);

            // Buat book baru
            $book = Book::create([
                'title' => $validated['bookTitle'],
                'author' => $validated['bookAuthor'],
                'isbn' => $validated['bookIsbn'],
                'publication_date' => $validated['bookYear'] . '-01-01',
                'total_copies' => $validated['bookStock'],
                'stok_tersedia' => $validated['bookStock'],
                'kategori' => $kategori->nama_kategori,
                'publisher' => $validated['bookPublisher'],
                'pages' => $validated['bookPages'],
                'description' => $validated['bookDescription'],
                'cover_image' => $coverImage, // Simpan path file atau URL
            ]);

            // Simpan relasi kategori
            $book->kategoris()->attach($validated['bookCategoryId']);

            return response()->json([
                'success' => true,
                'message' => 'Buku "' . $book->title . '" berhasil ditambahkan ke perpustakaan!',
                'book' => $book
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan validasi',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a book from database.
     */
    public function deleteBook($id)
    {
        try {
            Log::info('Attempting to delete book with ID: ' . $id);

            $book = Book::findOrFail($id);
            $bookTitle = $book->title;

            Log::info('Found book: ' . $bookTitle);

            // Delete book file dari storage jika ada
            if ($book->cover_image && strpos($book->cover_image, 'storage/covers/') === 0) {
                $filePath = public_path($book->cover_image);
                Log::info('Checking file: ' . $filePath);
                if (file_exists($filePath)) {
                    unlink($filePath);
                    Log::info('File deleted: ' . $filePath);
                }
            }

            // Hapus buku dari database (benar-benar dihapus, bukan soft delete)
            $book->forceDelete();

            Log::info('Book permanently deleted: ' . $bookTitle);

            return response()->json([
                'success' => true,
                'message' => 'Buku "' . $bookTitle . '" berhasil dihapus dari perpustakaan!'
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::warning('Book not found with ID: ' . $id);
            return response()->json([
                'success' => false,
                'message' => 'Buku tidak ditemukan!'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Error deleting book: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created user (petugas) in database.
     */
    public function storeUser(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users,email',
                'phone' => 'required|string|max:20',
                'password' => 'required|string|min:8',
            ], [
                'name.required' => 'Nama lengkap wajib diisi.',
                'email.required' => 'Email wajib diisi.',
                'email.email' => 'Format email tidak valid.',
                'email.unique' => 'Email sudah terdaftar.',
                'phone.required' => 'Nomor telepon wajib diisi.',
                'password.required' => 'Password wajib diisi.',
                'password.min' => 'Password minimal 8 karakter.',
            ]);

            $username = $this->generateUniqueUsername($validated['name'], $validated['email']);

            $user = User::create([
                'name' => $validated['name'],
                'username' => $username,
                'email' => $validated['email'],
                'no_handphone' => $validated['phone'],
                'role' => 'petugas',
                'password' => Hash::make($validated['password']),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Pengguna berhasil ditambahkan.',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'no_handphone' => $user->no_handphone,
                    'role' => $user->role,
                ],
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error creating user: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menambahkan pengguna.',
            ], 500);
        }
    }

    /**
     * Update an existing user in database.
     */
    public function updateUser(Request $request, $id)
    {
        try {
            $user = User::findOrFail($id);

            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => [
                    'required',
                    'string',
                    'email',
                    'max:255',
                    Rule::unique('users', 'email')->ignore($user->id),
                ],
                'phone' => 'required|string|max:20',
                'role' => 'nullable|in:admin,petugas,user',
                'password' => 'nullable|string|min:8',
            ], [
                'name.required' => 'Nama lengkap wajib diisi.',
                'email.required' => 'Email wajib diisi.',
                'email.email' => 'Format email tidak valid.',
                'email.unique' => 'Email sudah terdaftar.',
                'phone.required' => 'Nomor telepon wajib diisi.',
                'password.min' => 'Password minimal 8 karakter.',
            ]);

            $user->name = $validated['name'];
            $user->email = $validated['email'];
            $user->no_handphone = $validated['phone'];
            $user->role = $validated['role'] ?? $user->role;

            if (!empty($validated['password'])) {
                $user->password = Hash::make($validated['password']);
            }

            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'Pengguna berhasil diperbarui.',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'no_handphone' => $user->no_handphone,
                    'role' => $user->role,
                ],
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Pengguna tidak ditemukan.',
            ], 404);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error updating user: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memperbarui pengguna.',
            ], 500);
        }
    }

    /**
     * Delete a user from database.
     */
    public function deleteUser($id)
    {
        try {
            Log::info('Attempting to delete user with ID: ' . $id);

            $user = User::findOrFail($id);
            $userName = $user->name;

            Log::info('Found user: ' . $userName);

            // Soft delete atau hard delete tergantung model
            // Jika User model menggunakan SoftDeletes, gunakan delete(), jika tidak gunakan forceDelete()
            if (in_array('Illuminate\Database\Eloquent\SoftDeletes', class_uses($user))) {
                $user->delete();
                Log::info('User soft deleted: ' . $userName);
            } else {
                $user->forceDelete();
                Log::info('User permanently deleted: ' . $userName);
            }

            return response()->json([
                'success' => true,
                'message' => 'Pengguna "' . $userName . '" berhasil dihapus dari sistem!'
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::warning('User not found with ID: ' . $id);
            return response()->json([
                'success' => false,
                'message' => 'Pengguna tidak ditemukan!'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Error deleting user: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Convert base64 data URL to image file and save to storage
     */
    private function saveBase64Image($dataUrl)
    {
        try {
            // Parse the data URL: data:image/jpeg;base64,<base64_data>
            if (preg_match('/^data:image\/(\w+);base64,(.+)$/', $dataUrl, $matches)) {
                $imageType = $matches[1];
                $base64Data = $matches[2];

                // Validate image type
                $allowedTypes = ['jpeg', 'jpg', 'png', 'gif', 'webp'];
                if (!in_array($imageType, $allowedTypes)) {
                    throw new \Exception('Format gambar tidak didukung. Gunakan JPEG, PNG, GIF, atau WebP.');
                }

                // Decode base64
                $imageData = base64_decode($base64Data, true);
                if ($imageData === false) {
                    throw new \Exception('Data URL gambar tidak valid.');
                }

                // Generate unique filename
                $fileName = time() . '_' . uniqid() . '.' . $imageType;
                $filePath = public_path('storage/covers') . DIRECTORY_SEPARATOR . $fileName;

                // Ensure directory exists
                if (!is_dir(public_path('storage/covers'))) {
                    mkdir(public_path('storage/covers'), 0755, true);
                }

                // Save file
                file_put_contents($filePath, $imageData);

                // Check file size (max 5MB)
                if (filesize($filePath) > 5242880) {
                    unlink($filePath);
                    throw new \Exception('Ukuran gambar melebihi batas maksimal 5MB.');
                }

                return 'storage/covers/' . $fileName;
            } else {
                throw new \Exception('Format data URL tidak valid.');
            }
        } catch (\Exception $e) {
            Log::error('Error saving base64 image: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Generate unique username based on name/email.
     */
    private function generateUniqueUsername(string $name, string $email): string
    {
        $base = Str::slug($name, '');
        if ($base === '') {
            $base = Str::before($email, '@');
            $base = preg_replace('/[^a-zA-Z0-9]/', '', (string) $base) ?: 'user';
        }

        $username = strtolower($base);
        $counter = 1;

        while (User::where('username', $username)->exists()) {
            $username = strtolower($base) . $counter;
            $counter++;
        }

        return $username;
    }

    /**
     * Display the categories management page
     */
    public function categories()
    {
        $categories = Kategori::withCount('books')->orderBy('urutan')->get();

        $totalCategories = $categories->count();
        $totalBooks = $categories->sum('books_count');
        $popularCategory = optional($categories->sortByDesc('books_count')->first())->nama_kategori ?? 'N/A';
        $recentAdded = $categories->filter(function ($category) {
            return $category->created_at
                && $category->created_at->month === now()->month
                && $category->created_at->year === now()->year;
        })->count();

        return view($this->panelViewPrefix() . '.kelola_kategori', compact(
            'categories',
            'totalCategories',
            'totalBooks',
            'popularCategory',
            'recentAdded'
        ));
    }

    /**
     * Store a newly created category in database
     */
    public function storeCategory(Request $request)
    {
        try {
            $validated = $request->validate([
                'nama_kategori' => 'required|string|max:255|unique:kategoribuku,nama_kategori',
                'deskripsi' => 'nullable|string|max:1000',
                'warna' => 'required|string|max:10',
                'icon' => 'required|string|max:255',
                'urutan' => 'nullable|integer|min:0',
                'kata_kunci' => 'nullable|string|max:500',
                'status' => 'required|in:active,inactive'
            ]);

            $category = Kategori::create([
                'nama_kategori' => $validated['nama_kategori'],
                'slug' => Str::slug($validated['nama_kategori']),
                'deskripsi' => $validated['deskripsi'],
                'warna' => $validated['warna'],
                'icon' => $validated['icon'],
                'urutan' => $validated['urutan'] ?? 0,
                'kata_kunci' => $validated['kata_kunci'],
                'status' => $validated['status']
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Kategori berhasil ditambahkan',
                'category' => $category
            ], 201);
        } catch (\Exception $e) {
            Log::error('Error creating category: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan kategori: ' . $e->getMessage()
            ], 422);
        }
    }

    /**
     * Update a category
     */
    public function updateCategory(Request $request, $id)
    {
        try {
            $category = Kategori::findOrFail($id);

            $validated = $request->validate([
                'nama_kategori' => 'required|string|max:255|unique:kategoribuku,nama_kategori,' . $id,
                'deskripsi' => 'nullable|string|max:1000',
                'warna' => 'required|string|max:10',
                'icon' => 'required|string|max:255',
                'urutan' => 'nullable|integer|min:0',
                'kata_kunci' => 'nullable|string|max:500',
                'status' => 'required|in:active,inactive'
            ]);

            $category->update([
                'nama_kategori' => $validated['nama_kategori'],
                'slug' => Str::slug($validated['nama_kategori']),
                'deskripsi' => $validated['deskripsi'],
                'warna' => $validated['warna'],
                'icon' => $validated['icon'],
                'urutan' => $validated['urutan'] ?? 0,
                'kata_kunci' => $validated['kata_kunci'],
                'status' => $validated['status']
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Kategori berhasil diperbarui',
                'category' => $category
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error updating category: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui kategori: ' . $e->getMessage()
            ], 422);
        }
    }

    /**
     * Delete a category
     */
    public function deleteCategory($id)
    {
        try {
            $category = Kategori::findOrFail($id);
            $category->delete();

            return response()->json([
                'success' => true,
                'message' => 'Kategori berhasil dihapus'
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error deleting category: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus kategori: ' . $e->getMessage()
            ], 422);
        }
    }

    /**
     * Kelola Ulasan - Display all books with their reviews
     */
    public function kelolaUlasan()
    {
        $books = Book::with(['ulasan.user', 'kategoris'])
            ->withCount('ulasan')
            ->orderBy('title')
            ->paginate(12);

        return view($this->panelViewPrefix() . '.kelola_ulasan', compact('books'));
    }

    /**
     * Get reviews for a specific book
     */
    public function getUlasanBuku($bookId)
    {
        $book = Book::with(['ulasan.user'])->findOrFail($bookId);
        
        return response()->json([
            'success' => true,
            'book' => $book,
            'reviews' => $book->ulasan()->with('user')->get()
        ]);
    }

    /**
     * Determine active admin panel view namespace based on route.
     */
    private function panelViewPrefix(): string
    {
        return request()->routeIs('petugas.*') ? 'petugas' : 'admin';
    }
}
