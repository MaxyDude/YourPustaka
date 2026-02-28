<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Pengguna - YourPustaka</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/views/profile/edit.css') }}">
</head>
<body>
    <div class="container">
        <!-- Page Header -->
        <div class="page-header">
            <div class="page-title">
                <h1>Profil Pengguna</h1>
                <p>Kelola informasi profil dan pengaturan akun Anda</p>
            </div>
            <a href="{{ route('dashboard') }}" class="back-button">
                <i class="fas fa-arrow-left"></i>
                Kembali ke Dashboard
            </a>
        </div>

        <!-- Profile Sidebar - FIXED -->
        <div class="profile-sidebar">
            <div class="profile-header">
                <div class="profile-avatar">
                    {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                    <div class="avatar-edit" id="avatarEditBtn">
                        <i class="fas fa-camera"></i>
                    </div>
                </div>
                <h2 class="profile-name">{{ auth()->user()->name }}</h2>
                <div class="profile-role">{{ ucfirst(auth()->user()->role) }}</div>
                <p class="text-muted" style="color: var(--gray); font-size: 14px;">Bergabung {{ auth()->user()->created_at->format('F Y') }}</p>
            </div>

            <div class="profile-stats">
                <div class="stat-item">
                    <span class="stat-value">-</span>
                    <span class="stat-label">Total Buku</span>
                </div>
                <div class="stat-item">
                    <span class="stat-value">-</span>
                    <span class="stat-label">Dipinjam</span>
                </div>
                <div class="stat-item">
                    <span class="stat-value">-</span>
                    <span class="stat-label">Selesai</span>
                </div>
            </div>

            <ul class="profile-menu">
                <li class="profile-menu-item active">
                    <a href="#info">
                        <i class="fas fa-user-circle"></i>
                        <span>Informasi Profil</span>
                    </a>
                </li>
            </ul>

            <!-- Keamanan Akun di bagian bawah sidebar -->
            <div style="margin-top: auto; padding-top: 20px; border-top: 1px solid #eee;">
                <h4 style="color: var(--primary); margin-bottom: 15px; font-size: 16px;">
                    <i class="fas fa-key"></i> Keamanan Akun
                </h4>
                <button class="btn btn-primary" id="changePasswordBtn" style="width: 100%;">
                    <i class="fas fa-key"></i> Ubah Password
                </button>
            </div>
        </div>

        <!-- Profile Content -->
        <div class="profile-content">
            <!-- Informasi Profil -->
            <div class="content-section" id="info">
                <div class="section-title">
                    <span><i class="fas fa-user-circle"></i> Informasi Pribadi</span>
                    <button class="btn-edit" id="editProfileBtn">
                        <i class="fas fa-edit"></i> Edit Profil
                    </button>
                </div>

                <div class="info-grid">
                    <div class="info-item">
                        <span class="info-label">Nama Lengkap</span>
                        <span class="info-value editable" id="nameValue">{{ auth()->user()->name }}</span>
                        <div class="edit-form" id="nameForm">
                            <div class="form-group">
                                <input type="text" class="form-control" id="nameInput" value="{{ auth()->user()->name }}">
                            </div>
                            <div class="form-actions">
                                <button type="button" class="btn btn-primary btn-small" id="saveNameBtn">Simpan</button>
                                <button type="button" class="btn btn-secondary btn-small" id="cancelNameBtn">Batal</button>
                            </div>
                        </div>
                    </div>

                    <div class="info-item">
                        <span class="info-label">Email</span>
                        <span class="info-value editable" id="emailValue">{{ auth()->user()->email }}</span>
                        <div class="edit-form" id="emailForm">
                            <div class="form-group">
                                <input type="email" class="form-control" id="emailInput" value="{{ auth()->user()->email }}">
                            </div>
                            <div class="form-actions">
                                <button type="button" class="btn btn-primary btn-small" id="saveEmailBtn">Simpan</button>
                                <button type="button" class="btn btn-secondary btn-small" id="cancelEmailBtn">Batal</button>
                            </div>
                        </div>
                    </div>

                    <div class="info-item">
                        <span class="info-label">Tanggal Bergabung</span>
                        <span class="info-value">{{ auth()->user()->created_at->format('d F Y') }}</span>
                    </div>

                    <div class="info-item">
                        <span class="info-label">Role</span>
                        <span class="info-value">{{ ucfirst(auth()->user()->role) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="page-footer">
            <div class="footer-left">
                &copy; 2024 YourPustaka - Perpustakaan Online Terbaik
            </div>
        </div>
    </div>

    <script src="{{ asset('js/views/profile/edit.js') }}"></script>
</body>
</html>
