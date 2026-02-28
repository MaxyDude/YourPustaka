<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Peminjaman Buku - YourPustaka</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/views/pinjaman/halaman_peminjaman.css') }}">
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
                            @if($book->cover_image)
                                <img src="{{ asset($book->cover_image) }}" alt="{{ $book->title }}" onerror="this.src='https://via.placeholder.com/220x300?text=No%20Image'">
                            @else
                                <img src="https://via.placeholder.com/220x300?text={{ str_replace([' ', '+'], '%20', $book->title) }}" alt="{{ $book->title }}" onerror="this.src='https://via.placeholder.com/220x300?text=No%20Image'">
                            @endif
                        </div>

                        <div class="book-details">
                            <h2 class="book-title">{{ $book->title }}</h2>
                            <p class="book-author">oleh {{ $book->author }}</p>

                            <div class="book-meta">
                                <div class="meta-item">
                                    <div class="meta-label">Kategori</div>
                                    <div class="meta-value">{{ $book->kategori ?? 'Umum' }}</div>
                                </div>
                                <div class="meta-item">
                                    <div class="meta-label">Rating</div>
                                    <div class="meta-value">
                                        <i class="fas fa-star" style="color: #FFD700;"></i> 4.5/5
                                    </div>
                                </div>
                                <div class="meta-item">
                                    <div class="meta-label">Tahun Terbit</div>
                                    <div class="meta-value">{{ $book->publication_date ? date('Y', strtotime($book->publication_date)) : 'N/A' }}</div>
                                </div>
                                <div class="meta-item">
                                    <div class="meta-label">ISBN</div>
                                    <div class="meta-value">{{ $book->isbn ?? 'N/A' }}</div>
                                </div>
                            </div>

                            <!-- Statistik buku -->
                            <div class="book-stats">
                                <div class="stat-item">
                                    <div class="stat-value">{{ $book->pages ?? 'N/A' }}</div>
                                    <div class="stat-label">Halaman</div>
                                </div>
                                <div class="stat-item">
                                    <div class="stat-value">{{ $book->stok_tersedia ?? 0 }}</div>
                                    <div class="stat-label">Stok Tersedia</div>
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
                            @if($book->description)
                                {!! nl2br(e($book->description)) !!}
                            @else
                                <p>Sinopsis buku tidak tersedia.</p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Formulir peminjaman -->
                <div class="borrow-form">
                    <h3 class="form-title">
                        <i class="fas fa-clipboard-list"></i> Formulir Peminjaman
                    </h3>

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <strong>Terjadi kesalahan:</strong>
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form id="borrowFormElement" method="POST" action="{{ route('loans.store') }}">
                        @csrf

                        <input type="hidden" name="book_id" value="{{ $book->id }}">

                        <div class="form-group">
                            <label class="form-label" for="name">Nama Lengkap</label>
                            <input type="text" id="name" name="name" class="form-control" placeholder="Masukkan nama lengkap Anda" value="{{ auth()->user()->name }}" readonly>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="email">Email</label>
                            <input type="email" id="email" name="email" class="form-control" placeholder="nama@contoh.com" value="{{ auth()->user()->email }}" readonly>
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
                            <input type="hidden" id="duration" name="duration" value="14">
                        </div>

                        <div class="terms-group">
                            <input type="checkbox" id="terms" name="terms" class="terms-checkbox" required>
                            <label for="terms" class="terms-label">
                                Saya menyetujui <a href="#">Syarat dan Ketentuan</a> peminjaman buku dari YourPustaka. Saya akan mengembalikan buku tepat waktu dan menjaga kondisi buku dengan baik.
                            </label>
                        </div>

                        <button type="submit" id="submitBtn" class="submit-btn">Ajukan Peminjaman</button>
                    </form>
                </div>
            </div>

            <!-- Review Section -->
            <div class="review-section">
                <h3 class="review-title">
                    <i class="fas fa-star"></i> Ulasan & Rating Buku
                </h3>

                <!-- Form untuk memberikan ulasan -->
                <div class="review-form">
                    <form id="reviewFormElement">
                        <input type="hidden" name="book_id" value="{{ $book->id }}">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">

                        <!-- Rating Section -->
                        <div class="rating-group">
                            <label class="rating-label">
                                Berikan Rating
                                <span class="rating-count" id="ratingCount">0 / 5</span>
                            </label>
                            <div class="star-rating" id="starRating">
                                <input type="radio" name="rating" id="star1" class="star-input" value="1">
                                <label for="star1" class="star-label">&#9733;</label>

                                <input type="radio" name="rating" id="star2" class="star-input" value="2">
                                <label for="star2" class="star-label">&#9733;</label>

                                <input type="radio" name="rating" id="star3" class="star-input" value="3">
                                <label for="star3" class="star-label">&#9733;</label>

                                <input type="radio" name="rating" id="star4" class="star-input" value="4">
                                <label for="star4" class="star-label">&#9733;</label>

                                <input type="radio" name="rating" id="star5" class="star-input" value="5">
                                <label for="star5" class="star-label">&#9733;</label>
                            </div>
                        </div>

                        <!-- Comment Section -->
                        <div class="comment-group">
                            <label class="comment-label" for="reviewComment">Tulis Ulasan Anda (Opsional)</label>
                            <textarea id="reviewComment" name="comment" class="comment-textarea" placeholder="Bagikan pengalaman Anda membaca buku ini..." maxlength="500"></textarea>
                            <span class="char-count"><span id="charCount">0</span>/500 karakter</span>
                        </div>

                        <button type="submit" class="submit-review-btn" id="submitReviewBtn">
                            <i class="fas fa-paper-plane"></i> Kirim Ulasan
                        </button>

                        <div class="review-success-message" id="reviewSuccessMessage">
                            <i class="fas fa-check-circle"></i> Terima kasih! Ulasan Anda telah berhasil dikirim.
                        </div>
                    </form>
                </div>

                <!-- Existing Reviews -->
                <div class="existing-reviews">
                    <h4 class="existing-reviews-title">
                        <i class="fas fa-comments"></i> Ulasan dari Pembaca Lain
                    </h4>
                    <div id="reviewsList">
                        <div class="no-reviews">
                            <p>Belum ada ulasan. Jadilah yang pertama memberikan ulasan!</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pesan konfirmasi setelah submit -->
            <div id="confirmationMessage" class="confirmation-message">
                <div class="success-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <h2 class="confirmation-title">Peminjaman Berhasil Diajukan!</h2>
                <p class="confirmation-text">
                    Terima kasih <span id="userName"></span>! Peminjaman buku <strong>"{{ $book->title }}"</strong> telah berhasil diajukan.<br>
                    Anda akan menerima email konfirmasi di <span id="userEmail"></span> dalam beberapa menit.
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
            <p>&copy; 2024 YourPustaka - Perpustakaan Online Terbaik. All rights reserved.</p>
        </footer>
    </div>

    <script>
window.__VIEW_CONFIG = {
    'e1': @json(route("loans.store")),
    'e2': @json(route("reviews.store"))
};
</script>
<script src="{{ asset('js/views/pinjaman/halaman_peminjaman.js') }}"></script>
</body>
</html>

