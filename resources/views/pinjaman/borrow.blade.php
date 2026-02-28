<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Peminjaman Buku - YourPustaka</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/views/pinjaman/borrow.css') }}">
</head>
<body>
    <!-- Tombol Kembali di Sisi Kiri Layar (Desktop) -->
    <a href="{{ route('dashboard') }}" class="back-btn-fixed">
        <i class="fas fa-arrow-left"></i>
        <span class="back-btn-text">Kembali</span>
    </a>

    <div class="container">
        <!-- Tombol Kembali untuk Mobile -->
        <a href="{{ route('dashboard') }}" class="back-btn-mobile">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>

        <div class="borrow-container">
            <div id="borrowForm">
                <!-- Ringkasan informasi buku -->
                <div class="book-info-summary">
                    <!-- Bagian atas: Gambar dan detail buku -->
                    <div class="book-header">
                        <div class="book-cover">
                            <img src="{{ $book->cover_image ?? 'https://via.placeholder.com/220x300' }}"
                                 alt="{{ $book->title }}">
                        </div>

                        <div class="book-details">
                            <h2 class="book-title">{{ $book->title }}</h2>
                            <p class="book-author">oleh {{ $book->author }}</p>

                            <div class="book-meta">
                                <div class="meta-item">
                                    <div class="meta-label">Kategori</div>
                                    <div class="meta-value">{{ $book->category ?? 'Umum' }}</div>
                                </div>
                                <div class="meta-item">
                                    <div class="meta-label">Rating</div>
                                    <div class="meta-value">
                                        <i class="fas fa-star" style="color: #FFD700;"></i> {{ $book->rating ?? '4.5' }}/5
                                    </div>
                                </div>
                                <div class="meta-item">
                                    <div class="meta-label">Tahun Terbit</div>
                                    <div class="meta-value">{{ $book->published_year ?? now()->year }}</div>
                                </div>
                                <div class="meta-item">
                                    <div class="meta-label">ISBN</div>
                                    <div class="meta-value">{{ $book->isbn ?? 'N/A' }}</div>
                                </div>
                            </div>

                            <!-- Statistik buku (tanpa "Terjual") -->
                            <div class="book-stats">
                                <div class="stat-item">
                                    <div class="stat-value">{{ $book->pages ?? '300' }}</div>
                                    <div class="stat-label">Halaman</div>
                                </div>
                                <div class="stat-item">
                                    <div class="stat-value">{{ $book->recommendation_percentage ?? '85' }}%</div>
                                    <div class="stat-label">Rekomendasi</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sinopsis Lebar Penuh -->
                    <div class="book-synopsis-full">
                        <h3 class="synopsis-title">
                            <i class="fas fa-align-left"></i> Sinopsis Buku
                        </h3>
                        <div class="synopsis-text">
                            <p>{{ $book->description ?? 'Deskripsi buku tidak tersedia.' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Formulir peminjaman -->
                <div class="borrow-form">
                    <h3 class="form-title">
                        <i class="fas fa-clipboard-list"></i> Formulir Peminjaman
                    </h3>

                    <form id="borrowFormElement" method="POST" action="{{ route('loans.store') }}">
                        @csrf
                        <input type="hidden" name="book_id" value="{{ $book->id }}">
                        <input type="hidden" id="duration" name="duration" value="14">

                        <div class="form-group">
                            <label class="form-label" for="name">Nama Lengkap</label>
                            <input type="text" id="name" class="form-control" value="{{ Auth::user()->name }}" disabled>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="email">Email</label>
                            <input type="email" id="email" class="form-control" value="{{ Auth::user()->email }}" disabled>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="phone">Nomor Telepon</label>
                            <input type="tel" id="phone" name="phone" class="form-control" placeholder="08xxxxxxxxxx" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Durasi Peminjaman</label>
                            <div class="duration-options">
                                <div class="duration-option" data-days="7">
                                    <div class="duration-days">7 Hari</div>
                                    <div class="duration-label">Standar</div>
                                </div>
                                <div class="duration-option selected" data-days="14">
                                    <div class="duration-days">14 Hari</div>
                                    <div class="duration-label">Disarankan</div>
                                </div>
                                <div class="duration-option" data-days="30">
                                    <div class="duration-days">30 Hari</div>
                                    <div class="duration-label">Extended</div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="notes">Catatan (Opsional)</label>
                            <textarea id="notes" name="notes" class="form-control" rows="3" placeholder="Masukkan catatan tambahan jika diperlukan"></textarea>
                        </div>

                        <div class="terms-group">
                            <input type="checkbox" id="terms" class="terms-checkbox" required>
                            <label for="terms" class="terms-label">
                                Saya menyetujui <a href="#">Syarat dan Ketentuan</a> peminjaman buku dari YourPustaka. Saya akan mengembalikan buku tepat waktu dan menjaga kondisi buku dengan baik.
                            </label>
                        </div>

                        <button type="submit" id="submitBtn" class="submit-btn">Ajukan Peminjaman</button>
                    </form>
                </div>
            </div>

            <!-- Pesan konfirmasi setelah submit -->
            <div id="confirmationMessage" class="confirmation-message">
                <div class="success-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <h2 class="confirmation-title">Peminjaman Berhasil Diajukan!</h2>
                <p class="confirmation-text">
                    Terima kasih! Peminjaman buku <strong>"{{ $book->title }}"</strong> telah berhasil diajukan.<br>
                    Anda akan menerima email konfirmasi dalam beberapa menit.
                </p>
                <p class="confirmation-text">
                    <strong>Durasi Peminjaman:</strong> <span id="loanDuration"></span> hari<br>
                    <strong>Tanggal Pengembalian:</strong> <span id="returnDate"></span>
                </p>

                <div class="action-buttons">
                    <a href="{{ route('dashboard') }}" class="btn btn-primary">Kembali ke Koleksi Buku</a>
                    <button id="printBtn" class="btn btn-secondary">
                        <i class="fas fa-print"></i> Cetak Konfirmasi
                    </button>
                </div>
            </div>
        </div>

        <footer>
            <p>&copy; 2023 YourPustaka - Perpustakaan Online Terbaik. All rights reserved.</p>
        </footer>
    </div>

    <script src="{{ asset('js/views/pinjaman/borrow.js') }}"></script>
</body>
</html>
