<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Profil Pengguna - YourPustaka</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/views/profile/profile.css') }}">
</head>
<body>
@php
    $roleMap = ['admin' => 'Administrator', 'petugas' => 'Petugas', 'peminjam' => 'Anggota', 'user' => 'Anggota'];
    $profileRole = $roleMap[strtolower($user->role ?? 'user')] ?? ucfirst((string) $user->role);
    $initials = collect(explode(' ', trim($user->name)))->filter()->map(fn ($part) => strtoupper(substr($part, 0, 1)))->take(2)->implode('');
    $initials = $initials ?: 'US';
@endphp

<div class="container">
    <div class="page-header">
        <div class="page-title">
            <h1>Profil Pengguna</h1>
            <p>Kelola informasi profil, riwayat pembacaan, dan buku favorit</p>
        </div>
        <a href="{{ route('dashboard') }}" class="back-button"><i class="fas fa-arrow-left"></i> Kembali ke Dashboard</a>
    </div>

    <div class="profile-container">
        <div class="profile-sidebar">
            <div class="profile-header">
                <div class="profile-avatar" id="profileAvatar">{{ $initials }}
                    <div class="avatar-edit" id="avatarEditBtn"><i class="fas fa-camera"></i></div>
                </div>
                <h2 class="profile-name">{{ $user->name }}</h2>
                <div class="profile-role">{{ $profileRole }}</div>
                <p style="color:var(--gray);font-size:14px;">Bergabung {{ $user->created_at?->format('F Y') }}</p>
            </div>

            <div class="profile-stats">
                <div class="stat-item"><span class="stat-value">{{ $totalBooks }}</span><span class="stat-label">Total Buku</span></div>
                <div class="stat-item"><span class="stat-value">{{ $borrowedCount }}</span><span class="stat-label">Dipinjam</span></div>
                <div class="stat-item"><span class="stat-value">{{ $completedCount }}</span><span class="stat-label">Selesai</span></div>
            </div>

            <ul class="profile-menu">
                <li class="profile-menu-item active"><a href="#info"><i class="fas fa-user-circle"></i><span>Informasi Profil</span></a></li>
                <li class="profile-menu-item"><a href="#history"><i class="fas fa-history"></i><span>Riwayat Pembacaan</span></a></li>
                <li class="profile-menu-item"><a href="#favorites"><i class="fas fa-heart"></i><span>Buku Favorit</span></a></li>
            </ul>
        </div>

        <div class="profile-content">
            <div class="content-section" id="info">
                <div class="section-title">
                    <span><i class="fas fa-user-circle"></i> Informasi Pribadi</span>
                    <button class="btn-edit" id="editProfileBtn"><i class="fas fa-edit"></i> Edit Profil</button>
                </div>

                <div class="info-grid">
                    <div>
                        <span class="info-label">Nama Lengkap</span>
                        <span class="info-value editable" id="nameValue">{{ $user->name }}</span>
                        <div class="edit-form" id="nameForm">
                            <input type="text" class="form-control" id="nameInput" value="{{ $user->name }}">
                            <div class="form-actions">
                                <button class="btn btn-primary btn-small" id="saveNameBtn">Simpan</button>
                                <button class="btn btn-secondary btn-small" id="cancelNameBtn">Batal</button>
                            </div>
                        </div>
                    </div>
                    <div>
                        <span class="info-label">Email</span>
                        <span class="info-value editable" id="emailValue">{{ $user->email }}</span>
                        <div class="edit-form" id="emailForm">
                            <input type="email" class="form-control" id="emailInput" value="{{ $user->email }}">
                            <div class="form-actions">
                                <button class="btn btn-primary btn-small" id="saveEmailBtn">Simpan</button>
                                <button class="btn btn-secondary btn-small" id="cancelEmailBtn">Batal</button>
                            </div>
                        </div>
                    </div>
                    <div>
                        <span class="info-label">Nomor Telepon</span>
                        <span class="info-value editable" id="phoneValue">{{ $user->no_handphone ?? '-' }}</span>
                        <div class="edit-form" id="phoneForm">
                            <input type="tel" class="form-control" id="phoneInput" value="{{ $user->no_handphone ?? '' }}">
                            <div class="form-actions">
                                <button class="btn btn-primary btn-small" id="savePhoneBtn">Simpan</button>
                                <button class="btn btn-secondary btn-small" id="cancelPhoneBtn">Batal</button>
                            </div>
                        </div>
                    </div>
                    <div>
                        <span class="info-label">Tanggal Bergabung</span>
                        <span class="info-value">{{ $user->created_at?->format('d F Y') }}</span>
                    </div>
                    <div>
                        <span class="info-label">Terakhir Login</span>
                        <span class="info-value" id="lastLoginValue">Hari ini</span>
                    </div>
                </div>

                <div style="margin-top:30px;padding:20px;background:#e8f4f8;border-radius:10px;border-left:4px solid var(--primary);">
                    <h4 style="color:var(--primary);margin-bottom:15px;"><i class="fas fa-key"></i> Keamanan Akun</h4>
                    <button class="btn btn-primary" id="changePasswordBtn"><i class="fas fa-key"></i> Ubah Password</button>
                </div>
            </div>

            <div class="content-section" id="history">
                <div class="section-title"><span><i class="fas fa-history"></i> Riwayat Pembacaan</span></div>
                <div class="history-filters">
                    <button class="filter-btn active" data-filter="all">Semua</button>
                    <button class="filter-btn" data-filter="borrowed">Sedang Dipinjam</button>
                    <button class="filter-btn" data-filter="returned">Sudah Dikembalikan</button>
                    <button class="filter-btn" data-filter="overdue">Jatuh Tempo</button>
                </div>
                <ul class="history-list">
                    @forelse($loans as $loan)
                        @php
                            $book = $loan->book;
                            $loanDate = $loan->loan_date ? \Carbon\Carbon::parse($loan->loan_date) : null;
                            $dueDate = $loan->due_date ? \Carbon\Carbon::parse($loan->due_date) : null;
                            $returnDate = $loan->return_date ? \Carbon\Carbon::parse($loan->return_date) : null;
                            $itemClass = 'borrowed'; $badgeClass = 'status-borrowed'; $badgeText = 'Sedang Dipinjam'; $thirdText = 'Sisa: -'; $thirdIcon = 'fas fa-hourglass-half';
                            if ($loan->status === 'returned') { $itemClass = 'returned'; $badgeClass = 'status-returned'; $badgeText = 'Sudah Dikembalikan'; $thirdText = 'Dikembalikan: ' . ($returnDate ? $returnDate->format('d M Y') : '-'); $thirdIcon = 'fas fa-check-circle'; }
                            elseif ($loan->status === 'active' && $dueDate && now()->gt($dueDate)) { $itemClass = 'overdue'; $badgeClass = 'status-overdue'; $badgeText = 'Jatuh Tempo'; $thirdText = 'Terlambat: ' . $dueDate->diffInDays(now()) . ' hari'; $thirdIcon = 'fas fa-clock'; }
                            elseif ($loan->status === 'active' && $dueDate) { $thirdText = 'Sisa: ' . now()->diffInDays($dueDate, false) . ' hari'; }
                            elseif (in_array($loan->status, ['pending','approved'], true)) { $badgeText = 'Menunggu Konfirmasi'; $thirdText = 'Status: Menunggu diproses'; $thirdIcon = 'fas fa-clock'; }
                        @endphp
                        <li class="history-item {{ $itemClass }}" data-status="{{ $itemClass }}">
                            <div class="book-info">
                                <div class="book-title">{{ $book?->title ?? 'Buku tidak ditemukan' }}</div>
                                <div class="book-author">{{ $book?->author ?? '-' }}</div>
                                <div class="book-details">
                                    <div class="book-detail"><i class="far fa-calendar"></i><span>Dipinjam: {{ $loanDate ? $loanDate->format('d M Y') : '-' }}</span></div>
                                    <div class="book-detail"><i class="far fa-clock"></i><span>Jatuh Tempo: {{ $dueDate ? $dueDate->format('d M Y') : '-' }}</span></div>
                                    <div class="book-detail"><i class="{{ $thirdIcon }}"></i><span>{{ $thirdText }}</span></div>
                                </div>
                            </div>
                            <div class="status-badge {{ $badgeClass }}">{{ $badgeText }}</div>
                            <div class="action-buttons">
                                @if(in_array($itemClass, ['borrowed','overdue'], true))
                                    <button class="action-btn return" data-title="{{ $book?->title ?? 'Buku' }}"><i class="fas fa-undo"></i> Kembalikan</button>
                                @endif
                                <button class="action-btn detail" data-book-id="{{ $book?->id }}" data-title="{{ $book?->title ?? 'Buku' }}" data-author="{{ $book?->author ?? '-' }}" data-code="{{ $loan->barcode_code ?? '-' }}"><i class="fas fa-info-circle"></i> Detail</button>
                            </div>
                        </li>
                    @empty
                        <li class="empty-history"><i class="fas fa-book-open"></i><h4>Belum ada riwayat pembacaan</h4><p>Mulai pinjam buku untuk melihat riwayat Anda.</p></li>
                    @endforelse
                </ul>
            </div>

            <div class="content-section" id="favorites">
                <div class="section-title">
                    <span><i class="fas fa-heart"></i> Koleksi Buku Favorit</span>
                    <a href="{{ route('dashboard') }}" class="btn-edit"><i class="fas fa-plus"></i> Tambah Buku</a>
                </div>
                <div class="favorites-grid">
                    @forelse($favorites as $book)
                        @php
                            $coverUrl = '';
                            if (!empty($book->cover_image)) {
                                $coverUrl = str_starts_with($book->cover_image, 'http') || str_starts_with($book->cover_image, '/') ? $book->cover_image : '/' . $book->cover_image;
                            }
                            $rating = number_format((float) ($book->ulasan_avg_rating ?? 0), 1);
                        @endphp
                        <div class="favorite-book">
                            <div class="book-cover" style="{{ $coverUrl ? "background-image:url('{$coverUrl}');" : '' }}">
                                @if(!$coverUrl)<i class="fas fa-book"></i>@endif
                            </div>
                            <div class="book-info-small">
                                <div class="book-title-small">{{ $book->title }}</div>
                                <div class="book-author-small">{{ $book->author }}</div>
                                <div style="font-size:11px;color:var(--accent);margin-top:5px;"><i class="fas fa-star"></i> {{ $rating }}/5 - {{ $book->kategori ?? 'Umum' }}</div>
                            </div>
                        </div>
                    @empty
                        <div class="empty-history"><i class="fas fa-heart-broken"></i><h4>Belum ada buku favorit</h4><p>Tambahkan buku ke koleksi pribadi Anda.</p></div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <div class="page-footer"><div class="footer-left">&copy; {{ now()->year }} YourPustaka - Perpustakaan Online</div></div>
</div>

<script>
window.__VIEW_CONFIG = {
    'e1': @json(route('profile.update'))
};
</script>
<script src="{{ asset('js/views/profile/profile.js') }}"></script>
</body>
</html>

