@php
/**
 * @var int $totalBooks
 * @var int $totalUsers
 * @var int $borrowedBooks
 * @var int $pendingLoans
 * @var \Illuminate\Database\Eloquent\Collection|\App\Models\Book[] $books
 * @var \Illuminate\Database\Eloquent\Collection|\App\Models\Loan[] $loans
 * @var \Illuminate\Database\Eloquent\Collection|\App\Models\User[] $users
 */
@endphp

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin Panel - YourPustaka</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/views/admin/dashboard.css') }}">
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <i class="fas fa-book-open"></i>
            <h2>YourPustaka Admin</h2>
        </div>

        <div class="sidebar-menu">
            <div class="menu-section">Laporan</div>
            <a href="/admin/dashboard" class="menu-item active">
                <i class="fas fa-chart-bar"></i>
                <span class="menu-label">Dashboard</span>
            </a>
            <a href="/admin/categories" class="menu-item">
                <i class="fas fa-th-list"></i>
                <span class="menu-label">Kelola Kategori</span>
            </a>
            <a href="/admin/cari-tiket" class="menu-item">
                <i class="fas fa-ticket-alt"></i>
                <span class="menu-label">Cari Tiket</span>
            </a>
            <a href="/admin/kelola-ulasan" class="menu-item">
                <i class="fas fa-star"></i>
                <span class="menu-label">Kelola Ulasan</span>
            </a>
        </div>

        <div class="sidebar-bottom">
            <form action="{{ route('logout') }}" method="POST" class="logout-form">
                @csrf
                <button type="submit" class="logout-btn">
                    <i class="fas fa-sign-out-alt"></i>
                    <span class="menu-label">Logout</span>
                </button>
            </form>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Topbar -->
        <div class="topbar">
            <div class="page-title">Dashboard Admin</div>
            <div class="user-actions">
                <div class="user-profile">
                    <div class="avatar">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}{{ strtoupper(substr(explode(' ', Auth::user()->name)[1] ?? '', 0, 1)) }}</div>
                    <div class="user-info">
                        <div class="user-name">{{ Auth::user()->name }}</div>
                        <div class="user-role">{{ ucfirst(Auth::user()->role) }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content Area -->
        <div class="content-area">
            <!-- Stats Cards -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon books">
                        <i class="fas fa-book"></i>
                    </div>
                    <div class="stat-info">
                        <div class="stat-number">{{ $totalBooks }}</div>
                        <div class="stat-label">Total Buku</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon users">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-info">
                        <div class="stat-number">{{ $totalUsers }}</div>
                        <div class="stat-label">Pengguna Aktif</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon borrowed">
                        <i class="fas fa-exchange-alt"></i>
                    </div>
                    <div class="stat-info">
                        <div class="stat-number">{{ $borrowedBooks }}</div>
                        <div class="stat-label">Sedang Dipinjam</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon pending">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stat-info">
                        <div class="stat-number">{{ $pendingLoans }}</div>
                        <div class="stat-label">Menunggu Konfirmasi</div>
                    </div>
                </div>
            </div>

            <!-- Center Navigation Tabs -->
            <div class="center-navigation">
                <div class="tab-wrapper">
                    <div class="tab-indicator" id="tabIndicator"></div>
                    <button class="tab-btn active" onclick="openTab('tabBooks', event)">
                        <i class="fas fa-book"></i> Semua Buku
                    </button>
                    <button class="tab-btn" onclick="openTab('tabBorrow', event)">
                        <i class="fas fa-bookmark"></i> Peminjaman
                    </button>
                    <button class="tab-btn" onclick="openTab('tabUsers', event)">
                        <i class="fas fa-users"></i> Manajemen Pengguna
                    </button>
                </div>
            </div>

            <!-- Content Wrapper with Slide Effect -->
            <div class="content-wrapper">
                <!-- Tab 1: Semua Buku -->
                <div id="tabBooks" class="tab-content active">
                    <!-- Filter Controls dengan Search -->
                    <div class="filter-controls">
                        <div class="filter-search">
                            <i class="fas fa-search"></i>
                            <input type="text" placeholder="Cari judul, penulis, atau ID buku..." id="searchBooks">
                        </div>

                        <div class="filter-group">
                            <label>Kategori</label>
                            <select id="filterCategory">
                                <option value="">Semua Kategori</option>
                                @php
                                    $dashboardCategories = $books
                                        ->pluck('kategori')
                                        ->filter(fn($category) => !empty(trim((string) $category)))
                                        ->map(fn($category) => trim((string) $category))
                                        ->unique()
                                        ->sort()
                                        ->values();
                                @endphp
                                @foreach($dashboardCategories as $category)
                                    <option value="{{ $category }}">{{ $category }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="filter-group">
                            <label>Status Stok</label>
                            <select id="filterStatus">
                                <option value="">Semua Status</option>
                                <option value="available">Tersedia</option>
                                <option value="borrowed">Dipinjam</option>
                                <option value="low">Stok Sedikit</option>
                            </select>
                        </div>

                        <div class="filter-actions">
                            <button class="btn btn-outline" onclick="resetFilters()">
                                <i class="fas fa-redo"></i> Reset
                            </button>
                            <div class="book-primary-actions">
                                <button class="btn btn-primary" onclick="printBooksReport()">
                                    <i class="fas fa-print"></i> Cetak Laporan
                                </button>
                                <button class="btn btn-primary" onclick="openBookModal('add')">
                                    <i class="fas fa-plus"></i> Tambah Buku
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Books Grid -->
                    <div class="books-grid">
                        @forelse($books as $book)
                        <!-- Book Card -->
                        <div class="book-card" data-book-id="{{ $book->id }}" data-category-id="{{ $book->kategoris->first()?->id ?? '' }}">
                            <div class="book-cover" @if($book->cover_image) style="background-image: url('{{ asset($book->cover_image) }}')" @else class="no-image" @endif>
                                @php
                                    $rating = $book->ulasan->count() > 0 ? round($book->ulasan->avg('rating'), 1) : 0;
                                    $stokTersedia = $book->stok_tersedia;
                                @endphp
                                <div class="book-rating">
                                    <i class="fas fa-star"></i>
                                    <span>{{ $rating }}</span>
                                </div>
                                <div class="book-category">{{ $book->kategori }}</div>
                                <div class="book-stock">
                                    <i class="fas fa-box"></i> Stok: {{ $stokTersedia }}
                                </div>
                            </div>
                            <div class="book-info">
                                <h3 class="book-title">{{ $book->title }}</h3>
                                <div class="book-author">
                                    <i class="fas fa-user-edit"></i>
                                    <span>{{ $book->author }}</span>
                                </div>

                                <div class="book-meta">
                                    <div class="meta-row">
                                        <span class="meta-label">ID Buku:</span>
                                        <span class="meta-value">{{ $book->id }}</span>
                                    </div>
                                    <div class="meta-row">
                                        <span class="meta-label">ISBN:</span>
                                        <span class="meta-value">{{ $book->isbn }}</span>
                                    </div>
                                    <div class="meta-row">
                                        <span class="meta-label">Tahun:</span>
                                        <span class="meta-value">{{ $book->publication_date?->format('Y') ?? '-' }}</span>
                                    </div>
                                    <div class="meta-row">
                                        <span class="meta-label">Rating:</span>
                                        <span class="meta-value">
                                            <div class="rating-stars">
                                                @for($i = 1; $i <= 5; $i++)
                                                    @if($rating >= $i)
                                                        <i class="fas fa-star"></i>
                                                    @elseif($rating >= $i - 0.5)
                                                        <i class="fas fa-star-half-alt"></i>
                                                    @else
                                                        <i class="fas fa-star empty"></i>
                                                    @endif
                                                @endfor
                                            </div>
                                        </span>
                                    </div>
                                </div>

                                <div class="book-actions">
                                    <button class="btn btn-outline btn-sm" onclick="openBookModal('edit', this)">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                    <button class="btn btn-success btn-sm" onclick="viewBookDetail(this)">
                                        <i class="fas fa-eye"></i> Detail
                                    </button>
                                    <button class="btn btn-danger btn-sm" onclick="deleteBook(this, {{ $book->id }})">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div style="grid-column: 1 / -1; text-align: center; padding: 40px; color: #6c757d;">
                            <i class="fas fa-book" style="font-size: 3rem; margin-bottom: 15px; display: block; opacity: 0.5;"></i>
                            <p>Belum ada buku yang terdaftar</p>
                        </div>
                        @endforelse
                    </div>
                </div>

                <!-- Tab 2: Peminjaman -->
                <div id="tabBorrow" class="tab-content">
                    <div class="table-container">
                        <div class="table-header">
                            <div class="table-title">Daftar Peminjaman Aktif</div>
                            <div class="table-actions">
                                <button class="btn btn-primary" onclick="printBorrowReport()">
                                    <i class="fas fa-print"></i> Cetak Laporan
                                </button>
                                <button class="btn btn-success" onclick="openBorrowModal('add')">
                                    <i class="fas fa-plus"></i> Tambah Peminjaman
                                </button>
                            </div>
                        </div>
                        <table>
                            <thead>
                                <tr>
                                    <th>ID Peminjaman</th>
                                    <th>Nama Peminjam</th>
                                    <th>ID Buku</th>
                                    <th>Judul Buku</th>
                                    <th>Tanggal Pinjam</th>
                                    <th>Batas Kembali</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($loans as $loan)
                                <tr
                                    data-borrower-id="{{ $loan->user?->id ?? '-' }}"
                                    data-borrower-email="{{ $loan->user_email ?? $loan->user?->email ?? '-' }}"
                                    data-book-author="{{ $loan->book?->author ?? '-' }}"
                                    data-book-isbn="{{ $loan->book?->isbn ?? '-' }}"
                                    data-book-category="{{ $loan->book?->kategori ?? 'Umum' }}"
                                    data-book-cover="{{ $loan->book?->cover_image ?? '' }}"
                                    data-loan-notes="{{ $loan->notes ?? 'Tidak ada catatan' }}"
                                    data-officer-name="{{ $loan->approver?->name ?? 'Admin Perpustakaan' }}"
                                    data-confirm-date="{{ $loan->loan_date?->format('d M Y') ?? 'N/A' }}"
                                >
                                    <td>{{ $loan->id }}</td>
                                    <td>{{ $loan->user_name ?? $loan->user?->name ?? 'Unknown' }}</td>
                                    <td>{{ $loan->book?->id ?? '-' }}</td>
                                    <td>{{ $loan->book?->title ?? '-' }}</td>
                                    <td>{{ $loan->loan_date?->format('d M Y') ?? 'N/A' }}</td>
                                    <td>{{ $loan->due_date?->format('d M Y') ?? 'N/A' }}</td>
                                    <td>
                                        @php
                                            $statusMappings = [
                                                'pending' => ['Menunggu Konfirmasi', 'status-pending'],
                                                'approved' => ['Menunggu Pengambilan', 'status-pending'],
                                                'active' => ['Dipinjam', 'status-borrowed'],
                                                'returned' => ['Dikembalikan', 'status-returned'],
                                                'rejected' => ['Ditolak', 'status-overdue'],
                                            ];
                                            $status = $statusMappings[$loan->status] ?? ['Unknown', 'status-pending'];
                                        @endphp
                                        <span class="status-badge {{ $status[1] }}">{{ $status[0] }}</span>
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            @if($loan->status === 'active')
                                            <button class="btn btn-success btn-sm" onclick="confirmReturn(this)">
                                                <i class="fas fa-check"></i> Kembali
                                            </button>
                                            @endif
                                            <button class="btn btn-outline btn-sm" onclick="viewBorrowDetail(this)">
                                                <i class="fas fa-eye"></i> Detail
                                            </button>
                                            @if($loan->status === 'returned')
                                            <button class="btn btn-danger btn-sm" onclick="deleteBorrow(this)">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" style="text-align: center; color: #6c757d;">Belum ada data peminjaman</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Tab 3: Manajemen Pengguna -->
                <div id="tabUsers" class="tab-content">
                    <div class="table-container">
                        <div class="table-header">
                            <div class="table-title" id="userListTitle">Daftar Petugas</div>
                            <div class="table-actions">
                                <button class="btn btn-primary" onclick="printUsersReport()">
                                    <i class="fas fa-print"></i> Cetak Laporan
                                </button>
                                <button class="btn btn-outline" id="toggleUserListBtn" onclick="toggleUserListView()">
                                    <i class="fas fa-user"></i> Daftar Pengguna
                                </button>
                                <button class="btn btn-primary" id="addPetugasBtn" onclick="openUserModal('add')">
                                    <i class="fas fa-plus"></i> Tambah Petugas
                                </button>
                            </div>
                        </div>
                        <table>
                            <thead>
                                <tr>
                                    <th>ID Anggota</th>
                                    <th>Nama Lengkap</th>
                                    <th>Email</th>
                                    <th>Telepon</th>
                                    <th>Role</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="usersTableBody">
                                @foreach($users as $user)
                                @php
                                    $roleRaw = strtolower((string) $user->role);
                                @endphp
                                <tr class="user-row" data-role="{{ $roleRaw }}">
                                    <td>{{ $user->id }}</td>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>{{ $user->no_handphone ?? '-' }}</td>
                                    <td>
                                        @php
                                            $roles = [
                                                'admin' => 'Administrator',
                                                'petugas' => 'Petugas',
                                                'user' => 'User',
                                                'member' => 'Peminjam',
                                            ];
                                        @endphp
                                        {{ $roles[$roleRaw] ?? $user->role }}
                                    </td>
                                    <td><span class="status-badge status-active">Aktif</span></td>
                                    <td>
                                        <div class="action-buttons">
                                            <button class="btn btn-outline btn-sm" onclick="openUserModal('edit', this)">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-danger btn-sm" onclick="deleteUser(this, {{ $user->id }})">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                                <tr id="usersEmptyState" @if($users->isNotEmpty()) style="display: none;" @endif>
                                    <td colspan="7" style="text-align: center; color: #6c757d;">Belum ada data petugas</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Buku -->
    <div id="bookModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="bookModalTitle">Tambah Buku Baru</h2>
                <button class="close-modal" onclick="closeBookModal()">&times;</button>
            </div>
            <div class="modal-body">
                <form id="bookForm" enctype="multipart/form-data">
                    <!-- Layout: Preview di kiri, Image upload form di kanan -->
                    <div style="display: flex; gap: 25px; align-items: flex-start; margin-bottom: 30px;">
                        <!-- LEFT SIDE: Book Cover Preview -->
                        <div style="flex: 0 0 200px; text-align: center;">
                            <label style="font-weight: 600; display: block; margin-bottom: 12px;">Preview Cover</label>
                            <div id="imagePreview" class="image-preview no-image" style="height: 300px; width: 200px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.15);">
                                <i class="fas fa-book-open" style="font-size: 3rem; color: #adb5bd;"></i>
                                <span style="margin-top: 10px; font-size: 0.85rem;">Preview</span>
                            </div>
                        </div>

                        <!-- RIGHT SIDE: Image Upload Controls Only -->
                        <div style="flex: 1;">
                            <div class="form-group">
                                <label>Gambar Cover Buku</label>
                                <div style="display: flex; gap: 8px; margin-bottom: 15px;">
                                    <button type="button" id="btnImageUrl" class="btn btn-primary" style="flex: 1; padding: 10px;" onclick="switchImageMode('url')">
                                        <i class="fas fa-link"></i> Gunakan URL
                                    </button>
                                    <button type="button" id="btnImageFile" class="btn btn-outline" style="flex: 1; padding: 10px;" onclick="switchImageMode('file')">
                                        <i class="fas fa-upload"></i> Upload File
                                    </button>
                                </div>

                                <!-- URL Input -->
                                <div id="imageUrlSection" class="form-group" style="display: block;">
                                    <label style="font-size: 0.9rem;">URL Gambar Cover</label>
                                    <input type="text" id="bookImageUrl" placeholder="https://example.com/book-cover.jpg" oninput="previewBookImage(this.value)">
                                    <small style="color: var(--gray); display: block; margin-top: 5px;">
                                        <i class="fas fa-info-circle"></i> Masukkan URL gambar atau data URL (base64) dari Google/internet
                                    </small>
                                </div>

                                <!-- File Input -->
                                <div id="imageFileSection" class="form-group" style="display: none;">
                                    <label style="font-size: 0.9rem; margin-bottom: 10px;">Drag & Drop atau Pilih File</label>

                                    <!-- Drag and Drop Zone -->
                                    <div id="dropZone" style="border: 2px dashed var(--primary); border-radius: 8px; padding: 30px; text-align: center; cursor: pointer; transition: all 0.3s; background-color: #f8f9ff;">
                                        <i class="fas fa-cloud-upload-alt" style="font-size: 2.5rem; color: var(--primary); margin-bottom: 10px; display: block;"></i>
                                        <p style="color: var(--dark); font-weight: 600; margin: 10px 0 5px;">Drag gambar di sini</p>
                                        <p style="color: var(--gray); font-size: 0.9rem; margin: 5px 0;">atau klik untuk memilih file</p>
                                    </div>

                                    <!-- Hidden File Input -->
                                    <input type="file" id="bookImage" accept="image/*" onchange="previewBookImageFile(this)" style="display: none;">

                                    <small style="color: var(--gray); display: block; margin-top: 8px;">
                                        <i class="fas fa-info-circle"></i> Format: JPEG, PNG, GIF, WebP (Max 5MB)
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- All other form fields below -->
                    <div class="form-group">
                        <label>ISBN</label>
                        <input type="text" id="bookIsbn" required>
                    </div>

                    <div class="form-group">
                        <label>Judul Buku</label>
                        <input type="text" id="bookTitle" required>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Penulis</label>
                            <input type="text" id="bookAuthor" required>
                        </div>
                        <div class="form-group">
                            <label>Penerbit</label>
                            <input type="text" id="bookPublisher" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Tahun Terbit</label>
                            <input type="number" id="bookYear" required>
                        </div>
                        <div class="form-group">
                            <label>Kategori <span style="color: var(--danger);">*</span></label>
                            <select id="bookCategoryId" required>
                                <option value="">-- Pilih Kategori --</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Jumlah Halaman</label>
                            <input type="number" id="bookPages" required>
                        </div>
                        <div class="form-group">
                            <label>Stok Buku</label>
                            <input type="number" id="bookStock" required min="0">
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Deskripsi Buku</label>
                        <textarea id="bookDescription" rows="4" required></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-outline" onclick="closeBookModal()">Batal</button>
                <button id="saveBookBtn" class="btn btn-primary" onclick="saveBook()">Simpan Buku</button>
            </div>
        </div>
    </div>

    <!-- Modal Detail Buku -->
    <div id="bookDetailModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Detail Buku</h2>
                <button class="close-modal" onclick="closeBookDetailModal()">&times;</button>
            </div>
            <div class="modal-body">
                <div style="display: flex; gap: 20px; margin-bottom: 20px;">
                    <div style="flex: 0 0 150px;">
                        <div id="detailBookCover" style="width: 150px; height: 200px; background-size: cover; background-position: center; border-radius: 8px; position: relative;">
                            <div style="position: absolute; top: 10px; left: 10px; background-color: rgba(0, 0, 0, 0.7); padding: 5px 8px; border-radius: 15px; font-size: 0.8rem; display: flex; align-items: center; gap: 3px; color: #FFD700;">
                                <i class="fas fa-star" style="font-size: 0.7rem;"></i>
                                <span id="detailBookRatingNumber">4.5</span>
                            </div>
                        </div>
                    </div>
                    <div style="flex: 1;">
                        <h3 id="detailBookTitle" style="margin-bottom: 10px;">Pemrograman Web Modern</h3>
                        <p><strong>Penulis:</strong> <span id="detailBookAuthor">Budi Santoso</span></p>
                        <p><strong>ID Buku:</strong> <span id="detailBookId">101</span></p>
                        <p><strong>ISBN:</strong> <span id="detailBookIsbn">978-623-456-789</span></p>
                        <p><strong>Stok:</strong> <span id="detailBookStock">3 buku tersedia</span></p>
                        <p><strong>Status:</strong> <span id="detailBookStatus" class="status-badge status-available">Tersedia</span></p>
                    </div>
                </div>

                <div style="background-color: #f8f9fa; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                    <h4 style="margin-bottom: 10px;">Informasi Lengkap</h4>
                    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 10px;">
                        <div>
                            <p><strong>Penerbit:</strong> <span id="detailBookPublisher">Pustaka Digital</span></p>
                            <p><strong>Tahun:</strong> <span id="detailBookYear">2023</span></p>
                            <p><strong>Halaman:</strong> <span id="detailBookPages">320</span></p>
                        </div>
                        <div>
                            <p><strong>Kategori:</strong> <span id="detailBookCategory">Teknologi</span></p>
                            <p><strong>Rating:</strong> <span id="detailBookRating">
                                <div class="rating-stars" id="detailRatingStars">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star-half-alt"></i>
                                    <span style="margin-left: 5px;">(4.5/5)</span>
                                </div>
                            </span></p>
                            <p><strong>Total Review:</strong> <span id="detailBookReviews">128 review</span></p>
                        </div>
                    </div>
                </div>

                <div>
                    <h4 style="margin-bottom: 10px;">Deskripsi</h4>
                    <p id="detailBookDescription">Buku ini membahas secara komprehensif tentang pengembangan web modern dengan teknologi terkini.</p>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-outline" onclick="closeBookDetailModal()">Tutup</button>
                <button class="btn btn-primary" onclick="editFromDetail()">Edit Buku</button>
            </div>
        </div>
    </div>

    <!-- Modal Detail Peminjaman (DENGAN DURASI BERJALAN - STOPWATCH) -->
    <div id="borrowDetailModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Detail Peminjaman</h2>
                <button class="close-modal" onclick="closeBorrowDetailModal()">&times;</button>
            </div>
            <div class="modal-body">
                <div style="margin-bottom: 25px;">
                    <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 15px;">
                        <div style="width: 60px; height: 60px; background-color: var(--primary); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 1.2rem;">
                            <span id="borrowerInitials">AS</span>
                        </div>
                        <div>
                            <h3 id="borrowerName" style="margin-bottom: 5px;">Andi Susanto</h3>
                            <p style="color: var(--gray); font-size: 0.9rem;" id="borrowerInfo">ID Anggota: 1001 • Email: andi@example.com</p>
                        </div>
                    </div>

                    <div style="background-color: #f8f9fa; padding: 15px; border-radius: 8px;">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                            <div>
                                <p style="color: var(--gray); font-size: 0.85rem;">ID Peminjaman</p>
                                <p style="font-weight: 600; font-size: 1.1rem;" id="borrowId">201</p>
                            </div>
                            <div>
                                <p style="color: var(--gray); font-size: 0.85rem;">Status</p>
                                <p id="borrowStatus"><span class="status-badge status-borrowed">Dipinjam</span></p>
                            </div>
                        </div>
                    </div>
                </div>

                <div style="margin-bottom: 25px;">
                    <h4 style="margin-bottom: 15px; color: var(--dark);">Informasi Buku</h4>
                    <div style="display: flex; gap: 15px; margin-bottom: 15px;">
                        <div id="borrowBookCover" style="width: 100px; height: 140px; background-size: cover; background-position: center; border-radius: 6px; background-color: #e9ecef;"></div>
                        <div style="flex: 1;">
                            <h4 id="borrowBookTitle" style="margin-bottom: 8px;">Pemrograman Web Modern: Panduan Lengkap untuk Developer</h4>
                            <p style="color: var(--primary); margin-bottom: 8px;" id="borrowBookAuthor"><strong>Penulis:</strong> Budi Santoso</p>
                            <div style="display: flex; gap: 20px; font-size: 0.9rem;">
                                <div>
                                    <p style="color: var(--gray); margin-bottom: 3px;">ID Buku</p>
                                    <p style="font-weight: 600;" id="borrowBookId">101</p>
                                </div>
                                <div>
                                    <p style="color: var(--gray); margin-bottom: 3px;">ISBN</p>
                                    <p style="font-weight: 600;" id="borrowBookIsbn">978-623-456-789</p>
                                </div>
                                <div>
                                    <p style="color: var(--gray); margin-bottom: 3px;">Kategori</p>
                                    <p style="font-weight: 600;" id="borrowBookCategory">Teknologi</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div style="margin-bottom: 25px;">
                    <h4 style="margin-bottom: 15px; color: var(--dark);">Jadwal Peminjaman</h4>
                    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px;">
                        <div style="background-color: #f8f9fa; padding: 15px; border-radius: 8px; text-align: center;">
                            <p style="color: var(--gray); font-size: 0.85rem; margin-bottom: 5px;">Tanggal Pinjam</p>
                            <p style="font-weight: 600; font-size: 1.1rem; color: var(--primary);" id="borrowDate">15 Mar 2024</p>
                        </div>
                        <div style="background-color: #f8f9fa; padding: 15px; border-radius: 8px; text-align: center;">
                            <p style="color: var(--gray); font-size: 0.85rem; margin-bottom: 5px;">Batas Kembali</p>
                            <p style="font-weight: 600; font-size: 1.1rem; color: var(--warning);" id="dueDate">22 Mar 2024</p>
                        </div>
                        <div style="background-color: #f8f9fa; padding: 15px; border-radius: 8px; text-align: center;">
                            <p style="color: var(--gray); font-size: 0.85rem; margin-bottom: 5px;">Durasi Total</p>
                            <p style="font-weight: 600; font-size: 1.1rem; color: var(--success);" id="borrowDuration">7 hari</p>
                        </div>
                    </div>
                </div>

                <!-- DURASI BERJALAN DENGAN STOPWATCH -->
                <div style="margin-bottom: 25px;">
                    <h4 style="margin-bottom: 15px; color: var(--dark);">Durasi Berjalan</h4>
                    <div style="background-color: #f8f9fa; padding: 20px; border-radius: 8px;">
                        <div style="margin-bottom: 15px;">
                            <div class="progress-label">
                                <span>Progress Peminjaman</span>
                                <span id="progressPercentage">50%</span>
                            </div>
                            <div class="progress-bar">
                                <div class="progress-fill" id="progressFill" style="width: 50%;"></div>
                            </div>
                        </div>
                        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 15px;">
                            <div>
                                <p style="color: var(--gray); font-size: 0.85rem; margin-bottom: 5px;">Hari Ke</p>
                                <p style="font-weight: 600; font-size: 1.1rem;" id="dayNumber">4</p>
                            </div>
                            <div>
                                <p style="color: var(--gray); font-size: 0.85rem; margin-bottom: 5px;">Sisa Waktu</p>
                                <p style="font-weight: 600; font-size: 1.1rem; color: var(--success);" id="remainingTime">3 hari lagi</p>
                            </div>
                            <div>
                                <p style="color: var(--gray); font-size: 0.85rem; margin-bottom: 5px;">Telah Berlalu</p>
                                <p style="font-weight: 600; font-size: 1.1rem;" id="elapsedTime">4 hari</p>
                            </div>
                            <div>
                                <p style="color: var(--gray); font-size: 0.85rem; margin-bottom: 5px;">Status Waktu</p>
                                <p style="font-weight: 600; font-size: 1.1rem;" id="timeStatus"><span class="status-badge status-active">Aman</span></p>
                            </div>
                        </div>
                    </div>
                </div>

                <div>
                    <h4 style="margin-bottom: 15px; color: var(--dark);">Informasi Tambahan</h4>
                    <div style="background-color: #f8f9fa; padding: 15px; border-radius: 8px;">
                        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 15px;">
                            <div>
                                <p style="color: var(--gray); font-size: 0.85rem; margin-bottom: 5px;">Petugas yang Melayani</p>
                                <p style="font-weight: 600;" id="officerName">Admin Perpustakaan</p>
                            </div>
                            <div>
                                <p style="color: var(--gray); font-size: 0.85rem; margin-bottom: 5px;">Tanggal Konfirmasi</p>
                                <p style="font-weight: 600;" id="confirmDate">15 Mar 2024</p>
                            </div>
                            <div>
                                <p style="color: var(--gray); font-size: 0.85rem; margin-bottom: 5px;">Catatan</p>
                                <p style="font-weight: 600;" id="borrowNotes">Peminjaman reguler untuk studi</p>
                            </div>
                            <!-- HILANGKAN LOKASI PINJAM -->
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-outline" onclick="closeBorrowDetailModal()">Tutup</button>
                <button class="btn btn-primary" onclick="printBorrowDetail()">
                    <i class="fas fa-print"></i> Cetak Detail
                </button>
                <button class="btn btn-success" id="returnBtn" onclick="confirmReturnFromDetail()">
                    <i class="fas fa-check"></i> Konfirmasi Pengembalian
                </button>
            </div>
        </div>
    </div>

    <!-- Modal Pengguna -->
    <div id="userModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="userModalTitle">Tambah Petugas Baru</h2>
                <button class="close-modal" onclick="closeUserModal()">&times;</button>
            </div>
            <div class="modal-body">
                <form id="userForm">
                    <div class="form-row">
                        <div class="form-group">
                            <label>Role</label>
                            <select id="userRole" required>
                                <option value="petugas" selected>Petugas</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Nama Lengkap</label>
                        <input type="text" id="userName" required>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" id="userEmail" required>
                        </div>
                        <div class="form-group">
                            <label>No. Telepon</label>
                            <input type="tel" id="userPhone" required>
                        </div>
                    </div>

                    <!-- HANYA 1 FIELD PASSWORD TANPA KONFIRMASI DAN TANPA ALAMAT -->
                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" id="userPassword" required minlength="8">
                        <small style="color: var(--gray); display: block; margin-top: 5px;" id="passwordHint">Password minimal 8 karakter</small>
                    </div>

                    <!-- TIDAK ADA FIELD ALAMAT -->
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-outline" onclick="closeUserModal()">Batal</button>
                <button class="btn btn-primary" onclick="saveUser()">Simpan Pengguna</button>
            </div>
        </div>
    </div>

    <script>
window.__VIEW_CONFIG = {
    'e1': @json(route('admin.categories.json')),
    'e2': @json(route('admin.reports.print', ['type' => 'books'])),
    'e3': @json(route('admin.reports.print', ['type' => 'loans'])),
    'e4': @json(route('admin.reports.print', ['type' => 'users'])),
    'e5': @json(url('/loans')),
    'e6': @json(route('admin.users.store')),
    'e7': @json(url('/admin/users'))
};
</script>
<script src="{{ asset('js/views/admin/dashboard.js') }}"></script>

    <!-- Custom Popup Modal -->
    <div id="customPopup" class="custom-popup">
        <div class="popup-content">
            <div class="popup-icon" id="popupIcon"></div>
            <div class="popup-title" id="popupTitle"></div>
            <div class="popup-message" id="popupMessage"></div>
            <div class="popup-buttons" id="popupButtons"></div>
        </div>
    </div>

</body>
</html>
