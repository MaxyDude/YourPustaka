<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>YourPustaka - Sistem Manajemen Peminjaman Buku</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

                @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @else
            <link rel="stylesheet" href="{{ asset('css/views/welcome_old.css') }}">
        @endif

        <!-- Styles / Scripts -->
        <link rel="stylesheet" href="{{ asset('css/welcome.css') }}">
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
                @if (Route::has('login'))
                    <div class="nav-links">
                        @auth
                            <a href="{{ url('/dashboard') }}" class="nav-link nav-link-primary">
                                Dashboard
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="nav-link nav-link-auth">
                                Login
                            </a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="nav-link nav-link-primary">
                                    Register
                                </a>
                            @endif
                        @endauth
                    </div>
                @endif
            </div>
        </nav>

        <header class="header-main">
            <div class="hero-section">
                <div class="hero-content">
                    <h2 class="hero-title">
                        Tempat Peminjaman Buku Terbaik
                    </h2>
                    <p class="hero-subtitle">
                        Website Perpustakaan Online Yang Lengkap dengan mudah, cepat, dan aman
                    </p>
                </div>
                <div class="hero-image-container" style="background-image: url('https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQffRH8Zi0H-rVsM_5vsvPsfO2nRtj-5OAxiA&s');"></div>
            </div>

            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon-wrapper feature-icon-purple">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                    </div>
                    <h3 class="feature-card-title">Barcode Scanning</h3>
                    <p class="feature-card-text">Proses peminjaman yang cepat dengan teknologi barcode modern</p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon-wrapper feature-icon-indigo">
                        <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"/>
                        </svg>
                    </div>
                    <h3 class="feature-card-title">Multi-Role System</h3>
                    <p class="feature-card-text">Admin, Petugas, dan Peminjam dengan hak akses yang berbeda</p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon-wrapper feature-icon-blue">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h3 class="feature-card-title">Dashboard Real-Time</h3>
                    <p class="feature-card-text">Statistik dan laporan lengkap dalam satu tempat</p>
                </div>
            </div>

            @if (!Auth::check())
                <div class="btn-container">
                    <a href="{{ route('login') }}" class="btn btn-primary">
                        Masuk Sekarang
                    </a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="btn btn-secondary">
                            Daftar Akun Baru
                        </a>
                    @endif
                </div>
            @endif

            <div class="features-section">
                <div class="features-content">
                    <div>
                        <h3 class="section-title section-title-primary">Fitur Utama</h3>
                        <ul class="features-list">
                            <li class="features-list-item">
                                <svg class="check-icon" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                <span>Manajemen katalog buku</span>
                            </li>
                            <li class="features-list-item">
                                <svg class="check-icon" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                <span>Tracking status peminjaman</span>
                            </li>
                            <li class="features-list-item">
                                <svg class="check-icon" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                <span>Notifikasi keterlambatan</span>
                            </li>
                            <li class="features-list-item">
                                <svg class="check-icon" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                <span>Laporan komprehensif</span>
                            </li>
                        </ul>
                    </div>
                    <div>
                        <h3 class="section-title section-title-secondary">Akun Demo</h3>
                        <div class="demo-accounts">
                            <div class="demo-accounts-item">
                                <p class="demo-accounts-label">Admin:</p>
                                <p class="demo-accounts-value">admin@yourpustaka.com</p>
                            </div>
                            <div class="demo-accounts-item">
                                <p class="demo-accounts-label">Petugas:</p>
                                <p class="demo-accounts-value">petugas1@yourpustaka.com</p>
                            </div>
                            <div class="demo-accounts-item">
                                <p class="demo-accounts-label">Peminjam:</p>
                                <p class="demo-accounts-value">peminjam1@example.com</p>
                            </div>
                            <div class="demo-accounts-password">
                                <p class="demo-accounts-value">Password: <span class="password-badge">password123</span></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <footer class="footer-custom">
            <div class="footer-content">
                &copy; {{ date('Y') }} YourPustaka. All rights reserved.
            </div>
        </footer>
    </body>
    <script src="{{ asset('js/welcome.js') }}"></script>
</html>
