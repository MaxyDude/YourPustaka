<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Verifikasi Tiket - Petugas Panel - YourPustaka</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Library untuk QR Code Scanner -->
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
    <link rel="stylesheet" href="{{ asset('css/views/petugas/cari_tiket.css') }}">
</head>
<body>
    <!-- Sidebar -->
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
            <a href="/petugas/cari-tiket" class="menu-item active">
                <i class="fas fa-ticket-alt"></i>
                <span class="menu-label">Cari Tiket</span>
            </a>
            <a href="/petugas/kelola-ulasan" class="menu-item">
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
            <div class="page-title">Verifikasi Tiket Peminjaman</div>
            <div class="user-actions">
                <div class="user-profile">
                    <div class="avatar">AS</div>
                    <div class="user-info">
                        <div class="user-name">Petugas</div>
                        <div class="user-role">Petugas</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content Area -->
        <div class="content-area">
            <!-- Konten Verifikasi Tiket -->
            <div class="content-wrapper">
            <!-- Form Verifikasi -->
            <div class="verification-card">
                <h2 class="section-title">
                    <i class="fas fa-qrcode"></i> Verifikasi Tiket
                </h2>

                <!-- Tab Navigation -->
                <div class="verification-tabs">
                    <button class="tab-btn active" id="scanTabBtn" onclick="switchTab('scan')">
                        <i class="fas fa-camera"></i> Scan QR Code
                    </button>
                    <button class="tab-btn" id="manualTabBtn" onclick="switchTab('manual')">
                        <i class="fas fa-keyboard"></i> Input Manual
                    </button>
                </div>

                <!-- Tab Scan QR Code -->
                <div class="tab-content active" id="scanTab">
                    <div class="scanner-container">
                        <div id="qr-reader"></div>

                        <div class="scanner-controls">
                            <button class="scanner-btn btn-start" id="startScannerBtn" onclick="startScanner()">
                                <i class="fas fa-play"></i> Mulai Scan
                            </button>
                            <button class="scanner-btn btn-stop" id="stopScannerBtn" onclick="stopScanner()" disabled>
                                <i class="fas fa-stop"></i> Hentikan Scan
                            </button>
                        </div>

                        <!-- Hasil Scan -->
                        <div class="scan-result" id="scanResult">
                            <i class="fas fa-qrcode" style="color: #4b6cb7;"></i>
                            <p style="margin: 10px 0; font-weight: 600;">Kode QR Terdeteksi:</p>
                            <div class="scan-result-code" id="scannedCode"></div>
                            <button class="verify-btn" id="useScannedCodeBtn" style="padding: 12px; font-size: 1rem; margin-top: 10px;" onclick="useScannedCode()">
                                <i class="fas fa-check-circle"></i> Gunakan Kode Ini
                            </button>
                        </div>

                        <div style="margin-top: 15px; font-size: 0.9rem; color: #666; text-align: center;">
                            <i class="fas fa-info-circle"></i> Arahkan kamera ke QR Code tiket peminjaman
                        </div>
                    </div>
                </div>

                <!-- Tab Input Manual -->
                <div class="tab-content" id="manualTab">
                    <div class="form-group">
                        <label class="form-label" for="ticketInput">Masukkan Kode Tiket</label>
                        <input type="text" id="ticketInput" class="form-control"
                               placeholder="Contoh: LN-8A2B4C9D" maxlength="20"
                               style="text-transform: uppercase;">
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="adminPin">PIN Petugas (Opsional)</label>
                        <input type="password" id="adminPin" class="form-control"
                               placeholder="****" maxlength="4">
                    </div>

                    <button id="verifyTicketBtn" class="verify-btn" onclick="verifyManualTicket()">
                        <i class="fas fa-search"></i> Cari & Verifikasi Tiket
                    </button>
                </div>

                <!-- Pesan Hasil -->
                <div id="resultMessage" class="result-message">
                    <i class="fas fa-check-circle"></i> <span id="messageText"></span>
                </div>
            </div>

            <!-- Info Tiket -->
            <div class="ticket-info-card">
                <h2 class="section-title">
                    <i class="fas fa-ticket-alt"></i> Detail Tiket
                </h2>

                <div id="ticketDetails" class="ticket-details">
                    <div class="ticket-header">
                        <div style="font-size: 0.9rem; opacity: 0.9;">KODE TIKET</div>
                        <div class="ticket-code-large" id="displayTicketCode">YP-8A2B4C9D</div>
                        <div class="status-badge status-pending" id="ticketStatusBadge">
                            Menunggu Verifikasi
                        </div>
                    </div>

                    <!-- Grid Info -->
                    <div class="info-grid">
                        <div class="info-item">
                            <span class="info-label">Nama Peminjam</span>
                            <span class="info-value" id="ticketUserName">-</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Tanggal Pinjam</span>
                            <span class="info-value" id="ticketLoanDate">-</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Tanggal Kembali</span>
                            <span class="info-value" id="ticketReturnDate">-</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Durasi</span>
                            <span class="info-value" id="ticketDuration">-</span>
                        </div>
                    </div>

                    <!-- Info Buku -->
                    <div style="background: #f8f9fa; padding: 20px; border-radius: 10px; margin: 20px 0;">
                        <h4 style="color: #182848; margin-bottom: 15px;">
                            <i class="fas fa-book"></i> Buku yang Dipinjam
                        </h4>
                        <p style="font-weight: 600; font-size: 1.1rem;" id="ticketBookTitle">-</p>
                        <p style="color: #666;" id="ticketBookAuthor">-</p>
                        <div style="margin-top: 10px; display: flex; gap: 15px; color: #666; flex-wrap: wrap;">
                            <span><strong>ISBN:</strong> <span id="ticketISBN">-</span></span>
                            <span><strong>Kategori:</strong> <span id="ticketCategory">-</span></span>
                        </div>
                    </div>

                    <!-- Tombol Aksi Admin -->
                    <div class="admin-actions">
                        <button id="confirmBtn" class="action-btn btn-confirm" onclick="confirmTicket()">
                            <i class="fas fa-check-circle"></i> Verifikasi Tiket
                        </button>
                        <button id="takenBtn" class="action-btn btn-taken" onclick="markAsTaken()" disabled>
                            <i class="fas fa-box"></i> Buku Sudah Diambil
                        </button>
                        <button id="cancelBtn" class="action-btn btn-cancel" onclick="cancelTicket()">
                            <i class="fas fa-times-circle"></i> Batalkan Peminjaman
                        </button>
                    </div>

                    <!-- Timestamp -->
                    <div style="margin-top: 25px; padding-top: 15px; border-top: 1px solid #e0e0e0;">
                        <div style="font-size: 0.85rem; color: #666;">
                            <i class="fas fa-clock"></i>
                            Terakhir diupdate: <span id="lastUpdated">-</span>
                        </div>
                    </div>
                </div>

                <!-- Placeholder sebelum tiket ditemukan -->
                <div id="noTicketMessage" style="text-align: center; padding: 50px 20px; color: #999;">
                    <i class="fas fa-ticket-alt" style="font-size: 3rem; margin-bottom: 20px; opacity: 0.3;"></i>
                    <h3 style="color: #666; margin-bottom: 10px;">Belum Ada Tiket Dipilih</h3>
                    <p>Scan QR Code atau masukkan kode tiket untuk melihat detail dan melakukan verifikasi.</p>
                </div>
            </div>
            </div>

            <!-- History Panel -->
            <div class="history-panel">
                <h2 class="section-title">
                    <i class="fas fa-history"></i> Riwayat Verifikasi Hari Ini
                </h2>

                <table class="history-table">
                    <thead>
                        <tr>
                            <th>Waktu</th>
                            <th>Kode Tiket</th>
                            <th>Nama Peminjam</th>
                            <th>Buku</th>
                            <th>Status</th>
                            <th>Admin</th>
                            <th>Metode</th>
                        </tr>
                    </thead>
                    <tbody id="historyTableBody">
                        <!-- Data akan diisi oleh JavaScript -->
                    </tbody>
                </table>

                <div style="text-align: center; margin-top: 20px;">
                    <button id="refreshHistoryBtn" class="action-btn btn-taken" style="min-width: 200px;" onclick="refreshHistory()">
                        <i class="fas fa-sync-alt"></i> Refresh Riwayat
                    </button>
                </div>
            </div>
        </div>
    </div>
    <script>
window.__VIEW_CONFIG = {
    'e1': @json(route('loans.scan-barcode'))
};
</script>
<script src="{{ asset('js/views/petugas/cari_tiket.js') }}"></script>
</body>
</html>




