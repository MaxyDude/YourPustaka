<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Kelola Kategori - YourPustaka Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/views/admin/kelola_kategori.css') }}">
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
            <a href="/admin/dashboard" class="menu-item">
                <i class="fas fa-chart-bar"></i>
                <span class="menu-label">Dashboard</span>
            </a>
            <a href="/admin/categories" class="menu-item active">
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
            <div class="page-title">Kelola Kategori Buku</div>
            <div class="user-actions">
                <div class="user-info-dropdown" id="userInfoDropdown">
                    <div class="user-info-container">
                        <div class="avatar">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}{{ strtoupper(substr(explode(' ', Auth::user()->name)[1] ?? '', 0, 1)) }}</div>
                        <div class="user-info">
                            <div class="user-name">{{ Auth::user()->name }}</div>
                            <div class="user-role">{{ ucfirst(Auth::user()->role) }}</div>
                        </div>
                    </div>

                    <div class="dropdown-menu" id="userDropdownMenu">
                        <a href="{{ route('profile.edit') }}" class="dropdown-item">
                            <i class="fas fa-user"></i>
                            <span>Profil Saya</span>
                        </a>
                        <a href="{{ route('dashboard') }}" class="dropdown-item">
                            <i class="fas fa-home"></i>
                            <span>Dashboard User</span>
                        </a>
                        <a href="#" class="dropdown-item">
                            <i class="fas fa-cog"></i>
                            <span>Pengaturan</span>
                        </a>
                        <div class="dropdown-divider"></div>
                        <form method="POST" action="{{ route('logout') }}" style="margin: 0;">
                            @csrf
                            <button type="submit" class="dropdown-item logout">
                                <i class="fas fa-sign-out-alt"></i>
                                <span>Logout</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content Area -->
        <div class="content-area">
            <!-- Page Header -->
            <div class="page-header">
                <h1>Kelola Kategori Buku</h1>
                <button class="btn btn-primary" onclick="openCategoryModal('add')">
                    <i class="fas fa-plus"></i> Tambah Kategori Baru
                </button>
            </div>

            <!-- Stats Cards -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon categories">
                        <i class="fas fa-tags"></i>
                    </div>
                    <div class="stat-info">
                        <div class="stat-number" id="totalCategories">{{ $totalCategories ?? 0 }}</div>
                        <div class="stat-label">Total Kategori</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon books">
                        <i class="fas fa-book"></i>
                    </div>
                    <div class="stat-info">
                        <div class="stat-number" id="totalBooks">{{ $totalBooks ?? 0 }}</div>
                        <div class="stat-label">Total Buku</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon popular">
                        <i class="fas fa-fire"></i>
                    </div>
                    <div class="stat-info">
                        <div class="stat-number" id="popularCategory">{{ $popularCategory ?? 'N/A' }}</div>
                        <div class="stat-label">Kategori Terpopuler</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon recent">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stat-info">
                        <div class="stat-number" id="recentAdded">{{ $recentAdded ?? 0 }}</div>
                        <div class="stat-label">Kategori Baru Bulan Ini</div>
                    </div>
                </div>
            </div>

            <!-- Filter Controls -->
            <div class="filter-controls">
                <div class="filter-search">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Cari nama kategori atau deskripsi..." id="searchCategories">
                </div>

                <div class="filter-group">
                    <label>Status</label>
                    <select id="filterStatus">
                        <option value="">Semua Status</option>
                        <option value="active">Aktif</option>
                        <option value="inactive">Nonaktif</option>
                    </select>
                </div>

                <div class="filter-group">
                    <label>Urutkan</label>
                    <select id="filterSort">
                        <option value="name_asc">Nama (A-Z)</option>
                        <option value="name_desc">Nama (Z-A)</option>
                        <option value="books_desc">Buku Terbanyak</option>
                        <option value="date_desc">Terbaru</option>
                        <option value="date_asc">Terlama</option>
                    </select>
                </div>

                <div class="filter-actions">
                    <button class="btn btn-outline" onclick="resetFilters()">
                        <i class="fas fa-redo"></i> Reset
                    </button>
                    <button class="btn btn-primary" onclick="printCategoryReport()">
                        <i class="fas fa-print"></i> Cetak Laporan
                    </button>
                    <button class="btn btn-primary" onclick="exportCategories()">
                        <i class="fas fa-file-export"></i> Ekspor
                    </button>
                </div>
            </div>

            <!-- Categories Grid -->
            <div class="categories-grid" id="categoriesContainer">
                <!-- Data kategori akan dimuat di sini secara dinamis -->
            </div>

            <!-- Empty State -->
            <div id="emptyState" style="display: none; text-align: center; padding: 60px 20px;">
                <i class="fas fa-tags" style="font-size: 5rem; color: #ddd; margin-bottom: 20px;"></i>
                <h2 style="color: #666; margin-bottom: 15px; font-size: 1.8rem;">Belum Ada Kategori</h2>
                <p style="color: #999; margin-bottom: 30px; font-size: 1.1rem;">Mulai dengan menambahkan kategori pertama untuk mengelompokkan buku-buku Anda.</p>
                <button class="btn btn-primary" onclick="openCategoryModal('add')" style="padding: 12px 30px; font-size: 1rem;">
                    <i class="fas fa-plus"></i> Tambah Kategori Pertama
                </button>
            </div>

            <!-- Loading State -->
            <div id="loadingState" style="text-align: center; padding: 60px 20px;">
                <i class="fas fa-spinner fa-spin" style="font-size: 3rem; color: var(--primary); margin-bottom: 20px;"></i>
                <p style="color: var(--gray);">Memuat kategori...</p>
            </div>
        </div>
    </div>

    <!-- Modal Tambah/Edit Kategori -->
    <div id="categoryModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="categoryModalTitle">Tambah Kategori Baru</h2>
                <button class="close-modal" onclick="closeCategoryModal()">&times;</button>
            </div>
            <div class="modal-body">
                <form id="categoryForm">
                    <div class="form-row">
                        <div class="form-group">
                            <label>Nama Kategori <span style="color: var(--danger);">*</span></label>
                            <input type="text" id="categoryName" placeholder="Contoh: Teknologi, Sastra, Sejarah" required>
                        </div>
                        <div class="form-group">
                            <label>Kode Kategori</label>
                            <input type="text" id="categoryCode" placeholder="Contoh: TECH, LIT, HIST">
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Deskripsi Kategori</label>
                        <textarea id="categoryDescription" rows="3" placeholder="Jelaskan tentang kategori ini..."></textarea>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Warna Kategori</label>
                            <div class="color-picker" id="colorPicker">
                                <!-- Color options will be populated by JavaScript -->
                            </div>
                            <input type="hidden" id="selectedColor" value="#4361ee">
                        </div>
                        <div class="form-group">
                            <label>Ikon Kategori</label>
                            <div class="icon-picker" id="iconPicker">
                                <!-- Icon options will be populated by JavaScript -->
                            </div>
                            <input type="hidden" id="selectedIcon" value="fas fa-tag">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Status</label>
                            <select id="categoryStatus" required>
                                <option value="active">Aktif</option>
                                <option value="inactive">Nonaktif</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Urutan Tampilan</label>
                            <input type="number" id="categoryOrder" min="1" value="1">
                            <small style="color: var(--gray); display: block; margin-top: 5px;">Angka lebih kecil = muncul lebih awal</small>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Meta Keywords (untuk SEO)</label>
                        <input type="text" id="categoryKeywords" placeholder="Contoh: buku teknologi, pemrograman, IT">
                        <small style="color: var(--gray); display: block; margin-top: 5px;">Pisahkan dengan koma</small>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-outline" onclick="closeCategoryModal()">Batal</button>
                <button class="btn btn-primary" onclick="saveCategory()" id="saveCategoryBtn">
                    <i class="fas fa-save"></i> Simpan Kategori
                </button>
            </div>
        </div>
    </div>

    <!-- Modal Konfirmasi Hapus -->
    <div id="deleteModal" class="modal">
        <div class="modal-content" style="max-width: 400px;">
            <div class="modal-header">
                <h2>Konfirmasi Hapus</h2>
                <button class="close-modal" onclick="closeDeleteModal()">&times;</button>
            </div>
            <div class="modal-body">
                <div style="text-align: center;">
                    <i class="fas fa-exclamation-triangle" style="font-size: 3rem; color: var(--warning); margin-bottom: 20px;"></i>
                    <p id="deleteMessage" style="font-size: 1rem; color: var(--dark); margin-bottom: 10px;">
                        Apakah Anda yakin ingin menghapus kategori ini?
                    </p>
                    <p style="color: var(--danger); font-weight: 600; font-size: 1.1rem;" id="categoryToDeleteName"></p>
                    <p style="color: var(--gray); font-size: 0.9rem; margin-top: 15px;">
                        <i class="fas fa-exclamation-circle"></i> Tindakan ini tidak dapat dibatalkan
                    </p>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-outline" onclick="closeDeleteModal()">Batal</button>
                <button class="btn btn-danger" onclick="confirmDelete()">
                    <i class="fas fa-trash"></i> Ya, Hapus
                </button>
            </div>
        </div>
    </div>

    <!-- Modal Detail Kategori -->
    <div id="detailModal" class="modal">
        <div class="modal-content">
            <div class="modal-header" id="detailHeader" style="background: linear-gradient(135deg, var(--primary), var(--secondary)); color: white;">
                <h2>Detail Kategori</h2>
                <button class="close-modal" onclick="closeDetailModal()">&times;</button>
            </div>
            <div class="modal-body">
                <div style="display: flex; align-items: center; gap: 20px; margin-bottom: 25px;">
                    <div id="detailIcon" style="width: 60px; height: 60px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.8rem; color: white;"></div>
                    <div style="flex: 1;">
                        <h3 id="detailName" style="font-size: 1.5rem; margin-bottom: 5px; color: var(--dark);"></h3>
                        <div style="display: flex; align-items: center; gap: 15px;">
                            <span id="detailCode" style="background-color: var(--light); padding: 3px 10px; border-radius: 15px; font-size: 0.8rem; color: var(--gray);"></span>
                            <span id="detailStatus" class="status-badge"></span>
                        </div>
                    </div>
                </div>

                <div style="background-color: #f8f9fa; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                    <h4 style="margin-bottom: 15px; color: var(--dark);">Statistik</h4>
                    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px;">
                        <div style="text-align: center;">
                            <div style="font-size: 1.8rem; font-weight: 700; color: var(--primary);" id="detailTotalBooks">0</div>
                            <div style="font-size: 0.85rem; color: var(--gray);">Total Buku</div>
                        </div>
                        <div style="text-align: center;">
                            <div style="font-size: 1.8rem; font-weight: 700; color: var(--success);" id="detailAvailableBooks">0</div>
                            <div style="font-size: 0.85rem; color: var(--gray);">Tersedia</div>
                        </div>
                        <div style="text-align: center;">
                            <div style="font-size: 1.8rem; font-weight: 700; color: var(--warning);" id="detailBorrowedBooks">0</div>
                            <div style="font-size: 0.85rem; color: var(--gray);">Dipinjam</div>
                        </div>
                    </div>
                </div>

                <div style="margin-bottom: 20px;">
                    <h4 style="margin-bottom: 10px; color: var(--dark);">Deskripsi</h4>
                    <p id="detailDescription" style="color: var(--gray); line-height: 1.6;"></p>
                </div>

                <div style="margin-bottom: 20px;">
                    <h4 style="margin-bottom: 10px; color: var(--dark);">Informasi Tambahan</h4>
                    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 15px;">
                        <div>
                            <p style="color: var(--gray); font-size: 0.9rem; margin-bottom: 5px;">Dibuat Pada</p>
                            <p style="font-weight: 600;" id="detailCreatedAt"></p>
                        </div>
                        <div>
                            <p style="color: var(--gray); font-size: 0.9rem; margin-bottom: 5px;">Diperbarui Pada</p>
                            <p style="font-weight: 600;" id="detailUpdatedAt"></p>
                        </div>
                        <div>
                            <p style="color: var(--gray); font-size: 0.9rem; margin-bottom: 5px;">Urutan Tampilan</p>
                            <p style="font-weight: 600;" id="detailOrder"></p>
                        </div>
                        <div>
                            <p style="color: var(--gray); font-size: 0.9rem; margin-bottom: 5px;">Keywords</p>
                            <p style="font-weight: 600;" id="detailKeywords"></p>
                        </div>
                    </div>
                </div>

                <div>
                    <h4 style="margin-bottom: 10px; color: var(--dark);">Warna Kategori</h4>
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <div id="detailColorPreview" style="width: 40px; height: 40px; border-radius: 8px;"></div>
                        <span id="detailColorCode" style="font-family: monospace; font-size: 0.9rem;"></span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-outline" onclick="closeDetailModal()">Tutup</button>
                <button class="btn btn-primary" onclick="editFromDetail()">
                    <i class="fas fa-edit"></i> Edit Kategori
                </button>
            </div>
        </div>
    </div>

    <script>
window.__VIEW_CONFIG = {
    'e1': @json(route('admin.categories.store')),
    'e2': @json(route('admin.reports.print', ['type' => 'categories'])),
    'e3': @json($categories ?? [])
};
</script>
<script src="{{ asset('js/views/admin/kelola_kategori.js') }}"></script>
</body>
</html>
