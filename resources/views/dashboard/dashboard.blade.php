<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>YourPustaka - Dashboard Perpustakaan Online</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/views/dashboard/dashboard.css') }}">
</head>
<body>
    <!-- Navbar -->
    <div class="navbar">
        <div class="navbar-left">
            <div class="logo">
                <i class="fas fa-book"></i>
                <h1>YourPustaka</h1>
            </div>

            <ul class="menu">
                <li class="menu-item">
                    <a href="#">
                        <i class="fas fa-book-open"></i>
                        <span class="menu-text">Katalog</span>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="#">
                        <i class="fas fa-exchange-alt"></i>
                        <span class="menu-text">Peminjaman</span>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="#">
                        <i class="fas fa-history"></i>
                        <span class="menu-text">Riwayat</span>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="#">
                        <i class="fas fa-bell"></i>
                        <span class="menu-text">Notifikasi</span>
                    </a>
                </li>
            </ul>
        </div>

        <div class="user-info" id="userInfoDropdown">
            <div class="user-info-container">
                <div class="user-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 2)) }}</div>
                <div class="user-details">
                    <h4>{{ auth()->user()->name }}</h4>
                    <p>{{ ucfirst(auth()->user()->role) }}</p>
                </div>
            </div>

            <div class="dropdown-menu" id="userDropdownMenu">
                <a href="/profile" class="dropdown-item">
                    <i class="fas fa-user-circle"></i>
                    <span>Profile</span>
                </a>
                @if(auth()->user()->role === 'admin')
                <a href="{{ route('admin.dashboard') }}" class="dropdown-item">
                    <i class="fas fa-cog"></i>
                    <span>Admin Panel</span>
                </a>
                @endif
                @if(auth()->user()->role === 'petugas')
                <a href="{{ route('petugas.dashboard') }}" class="dropdown-item">
                    <i class="fas fa-user-tie"></i>
                    <span>Dashboard Petugas</span>
                </a>
                @endif
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

    <div class="container">
        <!-- Main Content -->
        <div class="main-content">
            <!-- Header -->
            <div class="header">
                <div class="header-title">
                    <h2>Dashboard YourPustaka</h2>
                    <p>Website Perpustakaan Online Lengkap - Mudah, Cepat, dan Aman</p>
                </div>
            </div>

            <!-- Search and Filter (preserved) -->
            <div class="search-filter">
                <div class="search-bar">
                    <input type="text" placeholder="Cari buku berdasarkan judul, penulis, atau ISBN..." id="searchBooksInput">
                    <button id="searchBooksButton"><i class="fas fa-search"></i> Cari</button>
                </div>

                <div class="filter-section">
                    <h3>Filter Buku</h3>
                    <div class="filter-buttons">
                        <button class="filter-btn active" data-category="">Semua Kategori</button>
                        @foreach(($filter_categories ?? collect()) as $category)
                        <button class="filter-btn" data-category="{{ strtolower(trim((string) $category)) }}">{{ $category }}</button>
                        @endforeach
                    </div>
                </div>
            </div>

            <main>
                <!-- Bagian Rekomendasi -->
                <div class="rekomendasi">
                    <h2 class="section-title">
                        <span><i class="fas fa-star"></i> Rekomendasi untuk Anda</span>
                        <span class="scroll-indicator"><i class="fas fa-arrows-alt-h"></i> Geser untuk melihat lebih banyak</span>
                    </h2>
                    <p class="section-subtitle">Buku-buku terpilih yang mungkin Anda sukai</p>

                    <div class="slider-container" id="rekomendasiContainer">
                        <button class="slider-btn prev" id="prevRekomendasi" aria-label="Slide buku sebelumnya">
                            <i class="fas fa-chevron-left" aria-hidden="true"></i>
                        </button>

                        <div class="books-slider" id="rekomendasiSlider">
                            @forelse($recommended_books as $book)
                            <div class="book-card"
                                data-title="{{ strtolower((string) $book->title) }}"
                                data-author="{{ strtolower((string) $book->author) }}"
                                data-isbn="{{ strtolower((string) ($book->isbn ?? '')) }}"
                                data-category="{{ strtolower(trim((string) ($book->kategori ?? 'umum'))) }}">
                                @php
                                    $rating = (float) ($book->ulasan_avg_rating ?? 0);
                                @endphp
                                <div class="book-rating">
                                    <div class="stars">
                                        @for($i = 1; $i <= 5; $i++)
                                            @if($rating >= $i)
                                                <i class="fas fa-star"></i>
                                            @elseif($rating >= $i - 0.5)
                                                <i class="fas fa-star-half-alt"></i>
                                            @else
                                                <i class="far fa-star"></i>
                                            @endif
                                        @endfor
                                    </div>
                                    <span class="rating-value">{{ number_format($rating, 1) }}</span>
                                </div>
                                <div class="book-category">{{ $book->kategori ?? 'Umum' }}</div>
                                <div class="book-image-container">
                                    @if($book->cover_image)
                                    <img src="{{ asset($book->cover_image) }}" alt="{{ $book->title }}" class="book-image" onerror="this.src='https://via.placeholder.com/220x300?text=No%20Image'">
                                    @else
                                    <img src="https://via.placeholder.com/220x300?text={{ str_replace([' ', '+'], '%20', $book->title) }}" alt="{{ $book->title }}" class="book-image" onerror="this.src='https://via.placeholder.com/220x300?text=No%20Image'">
                                    @endif
                                </div>
                                <div class="book-info">
                                    <h2 class="book-title">{{ $book->title }}</h2>
                                    <p class="book-author">{{ $book->author }}</p>
                                    <a href="{{ route('loans.borrow', $book->id) }}" class="btn-baca" data-book-id="{{ $book->id }}">Request Sekarang</a>
                                </div>
                            </div>
                            @empty
                            <div style="padding: 40px; text-align: center; color: #999; grid-column: 1 / -1;">
                                <i class="fas fa-inbox" style="font-size: 3rem; margin-bottom: 15px; display: block;"></i>
                                <p>Tidak ada buku yang ditemukan</p>
                            </div>
                            @endempty
                        </div>

                        <button class="slider-btn next" id="nextRekomendasi" aria-label="Slide buku berikutnya">
                            <i class="fas fa-chevron-right" aria-hidden="true"></i>
                        </button>
                        <div class="momentum-line" id="momentumRekomendasi"></div>
                    </div>
                </div>

                <!-- Bagian Populer -->
                <div class="populer">
                    <h2 class="section-title">
                        <span><i class="fas fa-fire"></i> Buku Populer</span>
                        <span class="scroll-indicator"><i class="fas fa-arrows-alt-h"></i> Geser untuk melihat lebih banyak</span>
                    </h2>
                    <p class="section-subtitle">Buku-buku yang sedang banyak diminati</p>

                    <div class="slider-container" id="populerContainer">
                        <button class="slider-btn prev" id="prevPopuler" aria-label="Slide buku sebelumnya">
                            <i class="fas fa-chevron-left" aria-hidden="true"></i>
                        </button>

                        <div class="books-slider" id="populerSlider">
                            @forelse($popular_books as $book)
                            <div class="book-card"
                                data-title="{{ strtolower((string) $book->title) }}"
                                data-author="{{ strtolower((string) $book->author) }}"
                                data-isbn="{{ strtolower((string) ($book->isbn ?? '')) }}"
                                data-category="{{ strtolower(trim((string) ($book->kategori ?? 'umum'))) }}">
                                @php
                                    $rating = (float) ($book->ulasan_avg_rating ?? 0);
                                @endphp
                                <div class="book-rating">
                                    <div class="stars">
                                        @for($i = 1; $i <= 5; $i++)
                                            @if($rating >= $i)
                                                <i class="fas fa-star"></i>
                                            @elseif($rating >= $i - 0.5)
                                                <i class="fas fa-star-half-alt"></i>
                                            @else
                                                <i class="far fa-star"></i>
                                            @endif
                                        @endfor
                                    </div>
                                    <span class="rating-value">{{ number_format($rating, 1) }}</span>
                                </div>
                                <div class="book-category">{{ $book->kategori ?? 'Umum' }}</div>
                                <div class="book-image-container">
                                    @if($book->cover_image)
                                    <img src="{{ asset($book->cover_image) }}" alt="{{ $book->title }}" class="book-image" onerror="this.src='https://via.placeholder.com/220x300?text=No%20Image'">
                                    @else
                                    <img src="https://via.placeholder.com/220x300?text={{ str_replace([' ', '+'], '%20', $book->title) }}" alt="{{ $book->title }}" class="book-image" onerror="this.src='https://via.placeholder.com/220x300?text=No%20Image'">
                                    @endif
                                </div>
                                <div class="book-info">
                                    <h2 class="book-title">{{ $book->title }}</h2>
                                    <p class="book-author">{{ $book->author }}</p>
                                    <a href="{{ route('loans.borrow', $book->id) }}" class="btn-baca" data-book-id="{{ $book->id }}">Baca Sekarang</a>
                                </div>
                            </div>
                            @empty
                            <div style="padding: 40px; text-align: center; color: #999; grid-column: 1 / -1;">
                                <i class="fas fa-inbox" style="font-size: 3rem; margin-bottom: 15px; display: block;"></i>
                                <p>Tidak ada buku yang ditemukan</p>
                            </div>
                            @endempty
                        </div>

                        <button class="slider-btn next" id="nextPopuler" aria-label="Slide buku berikutnya">
                            <i class="fas fa-chevron-right" aria-hidden="true"></i>
                        </button>
                        <div class="momentum-line" id="momentumPopuler"></div>
                    </div>
                </div>
            </main>

            <footer>
                <p>&copy; 2023 YourPustaka - Perpustakaan Online Terbaik. All rights reserved.</p>
            </footer>
        </div>
    </div>

    <script src="{{ asset('js/views/dashboard/dashboard.js') }}"></script>
</body>
</html>
