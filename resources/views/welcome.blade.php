<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>YourPustaka - Sistem Manajemen Peminjaman Buku</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif
        <link rel="stylesheet" href="{{ asset('css/views/welcome.css') }}">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    </head>
    <body>
        <nav class="nav-custom">
            <div class="nav-container">
                <div class="nav-logo">
                    <div class="logo-icon">
                        <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zm0 6a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zm10 0a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"/>
                        </svg>
                    </div>
                    <h1 class="logo-text">YourPustaka</h1>
                </div>
                <div class="nav-links">
                    <a href="{{ route('login') }}" class="nav-link nav-link-auth">Login</a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="nav-link nav-link-primary">Register</a>
                    @endif
                </div>
            </div>
        </nav>

        <header class="header-main">
            <div class="hero-section">
                <div class="hero-content">
                    <h2 class="hero-title">Tempat Peminjaman Buku Terbaik</h2>
                    <p class="hero-subtitle">YourPustaka adalah platform perpustakaan digital yang dirancang untuk
                        menghubungkan pecinta buku dengan koleksi literasi terbaik secara praktis, cepat, dan aman.
                        Kami hadir untuk mempermudah proses peminjaman buku, memungkinkan Anda menikmati pengalaman
                        layanan perpustakaan modern tanpa harus meninggalkan kenyamanan rumah.</p>
                    <a href="{{ route('login') }}" class="btn btn-primary" style="margin-top: 20px; display: inline-block; max-width: 180px; padding: 10px 20px;">Mulai Baca Sekarang</a>
                </div>
                <div class="hero-image-container" style="background-image: url('https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQffRH8Zi0H-rVsM_5vsvPsfO2nRtj-5OAxiA&s');"></div>
            </div>

            <!-- Stats Section -->
            <div class="stats-container">
                <div class="stat-item">
                    <div class="stat-number">500+</div>
                    <div class="stat-label">Buku Tersedia</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">1000+</div>
                    <div class="stat-label">Pengguna Aktif</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">99%</div>
                    <div class="stat-label">Kepuasan</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">24/7</div>
                    <div class="stat-label">Dukungan</div>
                </div>
            </div>

            <!-- Features Carousel -->
            <div class="carousel-container">
                <div class="carousel-wrapper" id="featuresCarousel">
                    <div class="carousel-slide">
                        <div class="feature-card" style="max-width: 350px; width: 100%;">
                            <div class="feature-icon-wrapper feature-icon-purple">
                                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                </svg>
                            </div>
                            <h3 class="feature-card-title">Barcode Scanning</h3>
                            <p class="feature-card-text">Proses peminjaman cepat dengan teknologi barcode modern</p>
                        </div>
                    </div>
                    <div class="carousel-slide">
                        <div class="feature-card" style="max-width: 350px; width: 100%;">
                            <div class="feature-icon-wrapper feature-icon-indigo">
                                <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"/>
                                </svg>
                            </div>
                            <h3 class="feature-card-title">Multi-Role System</h3>
                            <p class="feature-card-text">Admin, Petugas, dan Peminjam dengan hak akses berbeda</p>
                        </div>
                    </div>
                    <div class="carousel-slide">
                        <div class="feature-card" style="max-width: 350px; width: 100%;">
                            <div class="feature-icon-wrapper feature-icon-blue">
                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <h3 class="feature-card-title">Dashboard Real-Time</h3>
                            <p class="feature-card-text">Statistik dan laporan lengkap dalam satu tempat</p>
                        </div>
                    </div>
                    <div class="carousel-slide">
                        <div class="feature-card" style="max-width: 350px; width: 100%;">
                            <div class="feature-icon-wrapper feature-icon-purple">
                                <svg class="check-icon" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <h3 class="feature-card-title">Manajemen Katalog Buku</h3>
                            <p class="feature-card-text">Kelola koleksi buku dengan sistem yang terorganisir dan mudah diakses oleh pengguna</p>
                        </div>
                    </div>
                    <div class="carousel-slide">
                        <div class="feature-card" style="max-width: 350px; width: 100%;">
                            <div class="feature-icon-wrapper feature-icon-indigo">
                                <svg class="check-icon" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <h3 class="feature-card-title">Tracking Status Peminjaman</h3>
                            <p class="feature-card-text">Pantau status buku yang dipinjam secara real-time dan terima notifikasi otomatis</p>
                        </div>
                    </div>
                    <div class="carousel-slide">
                        <div class="feature-card" style="max-width: 350px; width: 100%;">
                            <div class="feature-icon-wrapper feature-icon-blue">
                                <svg class="check-icon" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <h3 class="feature-card-title">Notifikasi Keterlambatan</h3>
                            <p class="feature-card-text">Dapatkan pemberitahuan otomatis untuk buku yang kembali terlambat beserta denda</p>
                        </div>
                    </div>
                    <div class="carousel-slide">
                        <div class="feature-card" style="max-width: 350px; width: 100%;">
                            <div class="feature-icon-wrapper feature-icon-purple">
                                <svg class="check-icon" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <h3 class="feature-card-title">Laporan Komprehensif</h3>
                            <p class="feature-card-text">Buat laporan detail tentang peminjaman, pengembalian, dan statistik perpustakaan</p>
                        </div>
                    </div>
                </div>
                <button class="carousel-nav prev" onclick="previousSlideWithReset()">&#10094;</button>
                <button class="carousel-nav next" onclick="nextSlideWithReset()">&#10095;</button>
                <div class="carousel-controls">
                    <button class="carousel-dot active" onclick="goToSlideWithReset(0)"></button>
                    <button class="carousel-dot" onclick="goToSlideWithReset(1)"></button>
                    <button class="carousel-dot" onclick="goToSlideWithReset(2)"></button>
                    <button class="carousel-dot" onclick="goToSlideWithReset(3)"></button>
                    <button class="carousel-dot" onclick="goToSlideWithReset(4)"></button>
                </div>
            </div>

            <div class="btn-container">
                <a href="{{ route('login') }}" class="btn btn-primary">Masuk Sekarang</a>
                @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="btn btn-secondary">Daftar Akun Baru</a>
                @endif
            </div>

        </header>

        <footer>
            <div class="footer-container">
                <div class="footer-logo">
                    <h2>YourPustaka</h2>
                    <p>Platform perpustakaan modern yang memudahkan akses ke ribuan koleksi buku dengan sistem digital yang efisien, aman, dan terpercaya.</p>
                </div>

                <div class="footer-section">
                    <h3>Link Cepat</h3>
                    <ul class="footer-links">
                        <li><a href="#"><i class="fas fa-chevron-right"></i> Tentang Kami</a></li>
                        <li><a href="#"><i class="fas fa-chevron-right"></i> Fitur</a></li>
                        <li><a href="#"><i class="fas fa-chevron-right"></i> Bantuan</a></li>
                        <li><a href="#"><i class="fas fa-chevron-right"></i> Syarat & Ketentuan</a></li>
                    </ul>
                </div>

                <div class="footer-section">
                    <h3>Kontak</h3>
                    <ul class="contact-info">
                        <li><i class="fas fa-envelope"></i> info@yourpustaka.com</li>
                        <li><i class="fas fa-phone"></i> (021) 1234-5678</li>
                        <li><i class="fas fa-map-marker-alt"></i> Jakarta, Indonesia</li>
                    </ul>
                </div>
            </div>

            <div class="copyright">
                <p>&copy; {{ date('Y') }} YourPustaka. All rights reserved.</p>
                <div class="footer-policies">
                    <a href="https://www.yourpustaka.com/privacy-policy">Privacy Policy</a>
                    <a href="https://www.yourpustaka.com/terms-of-service">Terms of Service</a>
                    <a href="https://www.yourpustaka.com/cookie-policy">Cookie Policy</a>
                </div>
            </div>
        </footer>

        <script src="{{ asset('js/carousel.js') }}"></script>
        <script src="{{ asset('js/views/welcome.js') }}"></script>
    </body>
</html>
