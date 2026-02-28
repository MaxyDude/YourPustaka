<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tiket Peminjaman - YourPustaka</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Library QR Code -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <!-- Library html2canvas untuk convert ke PDF -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <!-- Library jspdf untuk generate PDF -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <link rel="stylesheet" href="{{ asset('css/views/pinjaman/detail_peminjaman.css') }}">
</head>
<body>
    <div class="container">
        <!-- Compact Header -->
        <div class="compact-header no-print">
            <div class="logo">
                <i class="fas fa-book"></i>
                YourPustaka
            </div>
            <div class="nav-buttons">
                <a href="index.html" class="nav-btn">
                    <i class="fas fa-home"></i> Beranda
                </a>
                <button id="printBtn" class="nav-btn primary">
                    <i class="fas fa-print"></i> Cetak Tiket
                </button>
            </div>
        </div>

        <!-- Main Ticket -->
        <div class="ticket-compact" id="ticketToPrint">
            <!-- Top Section -->
            <div class="ticket-top">
                <h1 class="ticket-title">TIKET PEMINJAMAN BUKU</h1>
                <p class="ticket-subtitle">Tiket digital untuk pengambilan buku di perpustakaan</p>

                <div class="status-row">
                    <div class="status-badge status-pending" id="statusBadge">
                        <i class="fas fa-clock"></i>
                        <span id="statusText">Menunggu Verifikasi</span>
                    </div>

                    <div class="ticket-code-box">
                        <div class="code-label">KODE TIKET</div>
                        <div class="ticket-code" id="ticketCode">YP-8A2B4C9D</div>
                    </div>
                </div>
            </div>

            <!-- Main Content Grid -->
            <div class="content-grid">
                <!-- Info Panel -->
                <div class="info-panel fade-in">
                    <h3 class="panel-title">
                        <i class="fas fa-user"></i> Data Peminjam
                    </h3>

                    <div class="info-list">
                        <div class="info-item">
                            <div class="info-label">
                                <i class="fas fa-user"></i> Nama Lengkap
                            </div>
                            <div class="info-value" id="userName">{{ auth()->user()->name }}</div>
                        </div>

                        <div class="info-item">
                            <div class="info-label">
                                <i class="fas fa-envelope"></i> Email
                            </div>
                            <div class="info-value" id="userEmail">{{ auth()->user()->email }}</div>
                        </div>

                        <div class="info-item">
                            <div class="info-label">
                                <i class="fas fa-phone"></i> Telepon
                            </div>
                            <div class="info-value" id="userPhone">{{ $loan ? $loan->user_phone : auth()->user()->phone ?? 'Tidak tersedia' }}</div>
                        </div>

                        <div class="info-item">
                            <div class="info-label">
                                <i class="fas fa-calendar"></i> Tanggal Pinjam
                            </div>
                            <div class="info-value" id="loanDate">{{ $loan ? $loan->created_at->format('d M Y') : now()->format('d M Y') }}</div>
                        </div>

                        <div class="info-item">
                            <div class="info-label">
                                <i class="fas fa-calendar-check"></i> Tanggal Kembali
                            </div>
                            <div class="info-value" id="returnDate">{{ $loan ? $loan->due_date->format('d M Y') : now()->addDays(14)->format('d M Y') }}</div>
                        </div>

                        <div class="info-item">
                            <div class="info-label">
                                <i class="fas fa-clock"></i> Durasi
                            </div>
                            <div class="info-value" id="duration">{{ $loan ? abs($loan->due_date->diffInDays($loan->loan_date)) : 14 }} Hari</div>
                        </div>
                    </div>
                </div>

                <!-- Book Panel -->
                <div class="book-panel fade-in">
                    <div class="book-header">
                        <div class="book-cover-small">
                            @if($book->cover_image)
                                <img src="{{ asset($book->cover_image) }}" alt="{{ $book->title }}">
                            @else
                                <img src="https://via.placeholder.com/220x300?text={{ str_replace([' ', '+'], '%20', $book->title) }}" alt="{{ $book->title }}" onerror="this.src='https://via.placeholder.com/220x300?text=No%20Image'">
                            @endif
                        </div>

                        <div class="book-info-compact">
                            <h3>{{ $book->title }}</h3>
                            <p class="book-author">oleh {{ $book->author }}</p>

                            <div class="book-meta-grid">
                                <div class="meta-item">
                                    <i class="fas fa-tag"></i>
                                    <span>{{ $book->kategori ?? 'Umum' }}</span>
                                </div>
                                <div class="meta-item">
                                    <i class="fas fa-star"></i>
                                    <span>4.5/5</span>
                                </div>
                                <div class="meta-item">
                                    <i class="fas fa-barcode"></i>
                                    <span>{{ $book->isbn ?? 'N/A' }}</span>
                                </div>
                                <div class="meta-item">
                                    <i class="fas fa-file-alt"></i>
                                    <span>{{ $book->pages ?? 'N/A' }} halaman</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="book-progress">
                        <div class="progress-header">
                            <span>Waktu Peminjaman</span>
                            <span id="progressLabel">0/14 hari</span>
                        </div>
                        <div class="progress-bar-container">
                            <div class="progress-fill" id="progressFill"></div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="actions-row no-print">
                    <div class="action-item">
                        <div class="action-icon">
                            <i class="fas fa-print"></i>
                        </div>
                        <div class="action-title">Cetak Tiket</div>
                        <div class="action-desc">
                            Cetak tiket untuk ditunjukkan ke admin
                        </div>
                        <button id="printActionBtn" class="action-btn">
                            <i class="fas fa-print"></i> Cetak
                        </button>
                    </div>

                    <div class="action-item">
                        <div class="action-icon">
                            <i class="fas fa-share-alt"></i>
                        </div>
                        <div class="action-title">Bagikan</div>
                        <div class="action-desc">
                            Bagikan tiket via email atau WA
                        </div>
                        <button id="shareActionBtn" class="action-btn secondary">
                            <i class="fas fa-share-alt"></i> Bagikan
                        </button>
                    </div>

                    <div class="action-item">
                        <div class="action-icon">
                            <i class="fas fa-qrcode"></i>
                        </div>
                        <div class="action-title">Kode QR</div>
                        <div class="action-desc">
                            Tampilkan QR untuk verifikasi
                        </div>
                        <button id="qrActionBtn" class="action-btn">
                            <i class="fas fa-eye"></i> Lihat QR
                        </button>
                    </div>
                </div>

                <!-- Timeline -->
                <div class="timeline-section">
                    <h3 class="panel-title">
                        <i class="fas fa-list-ol"></i> Status Peminjaman
                    </h3>

                    <div class="timeline">
                        <div class="timeline-item completed">
                            <div class="timeline-dot"></div>
                            <div class="timeline-content">
                                <h4>Pemesanan Berhasil</h4>
                                <p>Tiket telah dibuat dan siap untuk verifikasi</p>
                            </div>
                        </div>

                        <div class="timeline-item active">
                            <div class="timeline-dot"></div>
                            <div class="timeline-content">
                                <h4>Verifikasi Admin</h4>
                                <p>Menunggu verifikasi oleh admin perpustakaan</p>
                            </div>
                        </div>

                        <div class="timeline-item">
                            <div class="timeline-dot"></div>
                            <div class="timeline-content">
                                <h4>Pengambilan Buku</h4>
                                <p>Tunjukkan tiket di perpustakaan untuk mengambil buku</p>
                            </div>
                        </div>

                        <div class="timeline-item">
                            <div class="timeline-dot"></div>
                            <div class="timeline-content">
                                <h4>Pengembalian</h4>
                                <p>Kembalikan buku sebelum tanggal 29 Maret 2024</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="ticket-footer no-print">
                <div class="footer-text">
                    <i class="fas fa-info-circle"></i>
                    Simpan tiket ini hingga buku dikembalikan
                </div>
                <div class="footer-links">
                    <a href="#" class="footer-link">Bantuan</a>
                    <a href="#" class="footer-link">Syarat & Ketentuan</a>
                    <a href="admin-verifikasi.html" class="footer-link">Panel Admin</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Print Modal -->
    <div id="printModal" class="print-modal">
        <div class="print-content">
            <button class="close-modal" id="closeModal">&times;</button>

            <div class="modal-header">
                <h2 class="modal-title">TIKET PEMINJAMAN BUKU</h2>
                <p>YourPustaka - Perpustakaan Digital</p>
                <div class="modal-code" id="modalTicketCode">YP-8A2B4C9D</div>
            </div>

            <div class="modal-body">
                <div class="modal-grid">
                    <div class="modal-section">
                        <h3><i class="fas fa-user"></i> Data Peminjam</h3>
                        <p><strong>Nama:</strong> {{ $loan ? $loan->user_name : auth()->user()->name }}</p>
                        <p><strong>Email:</strong> {{ $loan ? $loan->user_email : auth()->user()->email }}</p>
                        <p><strong>Telepon:</strong> {{ $loan ? $loan->user_phone : auth()->user()->phone ?? 'Tidak tersedia' }}</p>
                        <p><strong>Tanggal Cetak:</strong> <span id="printDate">{{ now()->format('d M Y H:i') }}</span></p>
                    </div>

                    <div class="modal-section">
                        <h3><i class="fas fa-book"></i> Detail Peminjaman</h3>
                        <p><strong>Judul Buku:</strong> {{ $book->title }}</p>
                        <p><strong>Penulis:</strong> {{ $book->author }}</p>
                        <p><strong>Tanggal Pinjam:</strong> {{ $loan ? $loan->loan_date->format('d M Y') : now()->format('d M Y') }}</p>
                        <p><strong>Tanggal Kembali:</strong> {{ $loan ? $loan->due_date->format('d M Y') : now()->addDays(14)->format('d M Y') }}</p>
                        <p><strong>Durasi:</strong> {{ $loan ? abs($loan->due_date->diffInDays($loan->loan_date)) : 14 }} Hari</p>
                    </div>
                </div>

                <div class="qr-section">
                    <h3>Kode QR untuk Verifikasi</h3>
                    <div id="printQrCode" style="display: flex; justify-content: center; margin: 20px 0;"></div>
                    <p>Scan kode QR untuk verifikasi cepat oleh admin perpustakaan</p>
                </div>

                <div style="text-align: center; margin-top: 25px; padding-top: 15px; border-top: 1px solid #e2e8f0;">
                    <p style="color: #64748b; font-size: 0.85rem;">
                        <strong>YourPustaka - Perpustakaan Digital</strong><br>
                        Jl. Perpustakaan No. 123, Kota Buku | Telp: (021) 1234-5678
                    </p>
                </div>

                <div class="modal-actions" style="display: flex; gap: 10px; margin-top: 25px; padding-top: 20px; border-top: 1px solid #e2e8f0; justify-content: center;">
                    <button id="printModalBtn" class="modal-action-btn" style="padding: 10px 20px; background-color: #3b82f6; color: white; border: none; border-radius: 6px; cursor: pointer; display: flex; align-items: center; gap: 8px;">
                        <i class="fas fa-print"></i> Cetak
                    </button>
                    <button id="downloadPdfModalBtn" class="modal-action-btn" style="padding: 10px 20px; background-color: #22c55e; color: white; border: none; border-radius: 6px; cursor: pointer; display: flex; align-items: center; gap: 8px;">
                        <i class="fas fa-file-pdf"></i> Download PDF
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
window.__VIEW_CONFIG = {
    'e1': @json($loan?->barcode_code ?? ''),
    'e2': @json($loan ? $loan->user_name : auth()->user()->name),
    'e3': @json($loan ? $loan->user_email : auth()->user()->email),
    'e4': @json($loan ? $loan->user_phone : (auth()->user()->phone ?? '082xxxxxxxxx')),
    'e5': @json($book->title),
    'e6': @json($book->author),
    'e7': @json($book->isbn ?? 'N/A'),
    'e8': @json($book->kategori ?? 'Umum'),
    'e9': @json($book->publication_date ? date('Y', strtotime($book->publication_date)) : 'N/A'),
    'e10': @json($book->pages ?? 'N/A'),
    'e11': @json($loan ? $loan->loan_date : now()),
    'e12': @json($loan ? $loan->status : 'pending'),
    'e13': @json($loan ? abs((int)($loan->due_date->diffInDays($loan->loan_date))) : 14)
};
</script>
<script src="{{ asset('js/views/pinjaman/detail_peminjaman.js') }}"></script>
</body>
</html>
