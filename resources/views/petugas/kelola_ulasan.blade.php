<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Kelola Ulasan - YourPustaka Petugas</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/views/petugas/kelola_ulasan.css') }}">
</head>
<body>
<div class="sidebar">
    <div class="sidebar-header">
        <i class="fas fa-book-open"></i>
        <h2>YourPustaka Petugas</h2>
    </div>

    <div class="sidebar-menu">
        <div class="menu-section">Laporan</div>
        <a href="/petugas/dashboard" class="menu-item">
            <i class="fas fa-chart-bar"></i>
            <span class="menu-label">Dashboard</span>
        </a>
        <a href="/petugas/categories" class="menu-item">
            <i class="fas fa-th-list"></i>
            <span class="menu-label">Kelola Kategori</span>
        </a>
        <a href="/petugas/cari-tiket" class="menu-item">
            <i class="fas fa-ticket-alt"></i>
            <span class="menu-label">Cari Tiket</span>
        </a>
        <a href="/petugas/kelola-ulasan" class="menu-item active">
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
        <div class="page-title-top"><i class="fas fa-star"></i> Kelola Ulasan</div>
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
        <div class="page-header">
            <div class="page-title">
                <h1>Kelola Ulasan</h1>
                <p>Lihat dan kelola ulasan buku dari pengguna</p>
            </div>
            <button class="btn btn-primary" onclick="printReviewReport()">
                <i class="fas fa-print"></i> Cetak Laporan
            </button>
        </div>

        <div class="books-grid">
            @forelse($books as $book)
            @php
                $avgRating = $book->ulasan->avg('rating') ?? 0;
                $avgRating = number_format((float) $avgRating, 1);
                $coverUrl = '';
                if (!empty($book->cover_image)) {
                    $coverUrl = str_starts_with($book->cover_image, 'http') || str_starts_with($book->cover_image, '/') ? $book->cover_image : '/' . $book->cover_image;
                }
            @endphp
            <div class="book-card">
                <div class="book-cover {{ $coverUrl ? '' : 'no-image' }}">
                    @if($coverUrl)
                        <img src="{{ $coverUrl }}" alt="{{ $book->title }}">
                    @else
                        <i class="fas fa-book"></i>
                    @endif
                </div>
                <div class="book-info">
                    <h3 class="book-title">{{ $book->title }}</h3>
                    <p class="book-author">{{ $book->author }}</p>
                    @if($book->kategoris && $book->kategoris->count() > 0)
                        <a href="#" class="book-category">{{ $book->kategoris->first()->nama_kategori }}</a>
                    @endif
                    
                    <div class="book-stats">
                        <div class="stat">
                            <span class="stat-label">Rating</span>
                            <span class="stat-value">{{ $avgRating }}</span>
                        </div>
                        <div class="stat">
                            <span class="stat-label">Ulasan</span>
                            <span class="stat-value">{{ $book->ulasan_count }}</span>
                        </div>
                    </div>
                    
                    <div class="book-actions">
                        <button class="btn btn-primary" onclick="openReviewModal({{ $book->id }}, '{{ addslashes($book->title) }}')">
                            <i class="fas fa-comments"></i> Lihat Ulasan
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div style="grid-column: 1/-1; text-align: center; padding: 40px 20px;">
                <i class="fas fa-inbox" style="font-size: 48px; color: #ddd; margin-bottom: 15px; display: block;"></i>
                <h3 style="color: var(--gray); margin-bottom: 10px;">Belum Ada Buku</h3>
                <p style="color: var(--gray);">Tidak ada buku dalam sistem untuk ditampilkan.</p>
            </div>
        @endforelse
        </div>

        @if($books->hasPages())
            <div class="pagination">
                {{ $books->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Modal untuk menampilkan ulasan -->
<div class="modal" id="reviewModal" onclick="event.target === this && closeReviewModal()">
    <div class="modal-content">
        <div class="modal-header">
            <div>
                <h2 id="modalBookTitle">Judul Buku</h2>
                <div id="modalBookRating" style="color: var(--accent); margin-top: 8px; font-size: 14px;"></div>
            </div>
            <button class="modal-close" onclick="closeReviewModal()"><i class="fas fa-times"></i></button>
        </div>
        <div class="modal-body">
            <div id="reviewsContainer" style="min-height: 200px;">
                <div class="loading"><div class="spinner"></div> Memuat ulasan...</div>
            </div>
        </div>
    </div>
</div>

<script>
window.__VIEW_CONFIG = {
    'e1': @json(route('petugas.reports.print', ['type' => 'reviews']))
};
</script>
<script src="{{ asset('js/views/petugas/kelola_ulasan.js') }}"></script>
</body>
</html>



