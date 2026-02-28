// Toggle user dropdown menu
        const userInfoDropdown = document.getElementById('userInfoDropdown');
        const userDropdownMenu = document.getElementById('userDropdownMenu');

        if (userInfoDropdown && userDropdownMenu) {
            userInfoDropdown.addEventListener('click', function(e) {
                e.stopPropagation();
                userDropdownMenu.classList.toggle('active');
            });

            // Close dropdown when clicking outside
            document.addEventListener('click', function(e) {
                if (!userInfoDropdown.contains(e.target)) {
                    userDropdownMenu.classList.remove('active');
                }
            });

            // Close dropdown when clicking on menu items
            document.querySelectorAll('.dropdown-item').forEach(item => {
                if (!item.closest('form')) {
                    item.addEventListener('click', function() {
                        userDropdownMenu.classList.remove('active');
                    });
                }
            });
        }

        // Aktifkan menu navbar saat diklik
        document.querySelectorAll('.menu-item').forEach(item => {
            item.addEventListener('click', function() {
                document.querySelectorAll('.menu-item').forEach(i => i.classList.remove('active'));
                this.classList.add('active');
            });
        });

        function applyBookFilters() {
            const searchInput = document.getElementById('searchBooksInput');
            const activeFilterButton = document.querySelector('.filter-btn.active');
            const searchTerm = (searchInput?.value || '').trim().toLowerCase();
            const selectedCategory = (activeFilterButton?.dataset.category || '').trim().toLowerCase();
            const cards = document.querySelectorAll('.book-card');

            cards.forEach(card => {
                const title = (card.dataset.title || '').toLowerCase();
                const author = (card.dataset.author || '').toLowerCase();
                const isbn = (card.dataset.isbn || '').toLowerCase();
                const category = (card.dataset.category || '').toLowerCase();

                const matchesSearch = !searchTerm
                    || title.includes(searchTerm)
                    || author.includes(searchTerm)
                    || isbn.includes(searchTerm);
                const matchesCategory = !selectedCategory || category === selectedCategory;

                card.style.display = (matchesSearch && matchesCategory) ? '' : 'none';
            });

            // Sinkronkan visibilitas tombol slider setelah kartu difilter
            toggleSliderButtons('rekomendasiSlider', 'prevRekomendasi', 'nextRekomendasi');
            toggleSliderButtons('populerSlider', 'prevPopuler', 'nextPopuler');
        }

        // Filter kategori
        document.querySelectorAll('.filter-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                applyBookFilters();
            });
        });

        // Search berdasarkan data buku dari database yang sudah dirender
        document.getElementById('searchBooksButton')?.addEventListener('click', function() {
            applyBookFilters();
        });

        document.getElementById('searchBooksInput')?.addEventListener('input', function() {
            applyBookFilters();
        });

        // Fungsi scan barcode
        document.querySelectorAll('.btn-primary').forEach(btn => {
            btn.addEventListener('click', function() {
                alert("Fitur Scan Barcode diaktifkan. Arahkan kamera ke barcode buku.");
                // Di sini biasanya akan ada integrasi dengan API scan barcode
            });
        });

        // Fungsi detail buku
        document.querySelectorAll('.btn-secondary').forEach(btn => {
            btn.addEventListener('click', function() {
                const bookTitle = this.closest('.book-card').querySelector('h4').textContent;
                alert(`Menampilkan detail untuk buku: "${bookTitle}"`);
                // Di sini biasanya akan ada navigasi ke halaman detail buku
            });
        });

        // Fungsi untuk menampilkan/menyembunyikan tombol slider berdasarkan overflow
        function toggleSliderButtons(sliderId, prevBtnId, nextBtnId) {
            const slider = document.getElementById(sliderId);
            const prevBtn = document.getElementById(prevBtnId);
            const nextBtn = document.getElementById(nextBtnId);

            function checkScroll() {
                if (!slider || !prevBtn || !nextBtn) return;

                const hasOverflow = slider.scrollWidth > slider.clientWidth;

                // Tampilkan tombol hanya jika ada overflow
                prevBtn.style.display = hasOverflow ? 'flex' : 'none';
                nextBtn.style.display = hasOverflow ? 'flex' : 'none';

                // Update disabled state
                prevBtn.disabled = slider.scrollLeft <= 10;
                const maxScrollLeft = slider.scrollWidth - slider.clientWidth;
                nextBtn.disabled = slider.scrollLeft >= (maxScrollLeft - 10);
            }

            // Check on load dan resize
            checkScroll();

            if (!slider.dataset.sliderToggleBound) {
                window.addEventListener('resize', checkScroll);
                slider.addEventListener('scroll', checkScroll);
                slider.dataset.sliderToggleBound = '1';
            }
        }

        // Inisialisasi toggle untuk kedua slider
        document.addEventListener('DOMContentLoaded', function() {
            toggleSliderButtons('rekomendasiSlider', 'prevRekomendasi', 'nextRekomendasi');
            toggleSliderButtons('populerSlider', 'prevPopuler', 'nextPopuler');
            applyBookFilters();
        });

        // Tambahkan click handler untuk tombol
        function setupSliderButtons(prevBtnId, nextBtnId, sliderId) {
            const prevBtn = document.getElementById(prevBtnId);
            const nextBtn = document.getElementById(nextBtnId);
            const slider = document.getElementById(sliderId);

            if (!prevBtn || !nextBtn || !slider) return;

            const scrollAmount = 250;

            prevBtn.addEventListener('click', () => {
                slider.scrollBy({ left: -scrollAmount, behavior: 'smooth' });
            });

            nextBtn.addEventListener('click', () => {
                slider.scrollBy({ left: scrollAmount, behavior: 'smooth' });
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            setupSliderButtons('prevRekomendasi', 'nextRekomendasi', 'rekomendasiSlider');
            setupSliderButtons('prevPopuler', 'nextPopuler', 'populerSlider');
        });

        // Handle btn-baca click - untuk backward compatibility dengan hardcoded cards
        document.querySelectorAll('.btn-baca').forEach(btn => {
            btn.addEventListener('click', function(e) {
                // Jika sudah ada href, biarkan bekerja normal
                if (this.href && this.href !== '#') {
                    return;
                }

                // Untuk card tanpa href/db, collect data dari DOM
                e.preventDefault();
                const card = this.closest('.book-card');
                if (!card) return;

                const title = (card.querySelector('.book-title') || card.querySelector('h2') || card.querySelector('h4'))?.textContent?.trim() || '';
                const author = (card.querySelector('.book-author') || card.querySelector('p'))?.textContent?.trim() || '';
                const imgEl = card.querySelector('img');
                const cover_image = imgEl ? imgEl.src : '';
                const category = card.querySelector('.book-category')?.textContent?.trim() || '';
                const rating = card.querySelector('.rating-value')?.textContent?.trim() || '';

                const params = new URLSearchParams({
                    title: title,
                    author: author.replace(/^oleh\s+/i, ''),
                    cover_image: cover_image,
                    category: category,
                    rating: rating,
                });

                const id = this.dataset.bookId;
                if (id) params.set('id', id);

                window.location.href = '/loans/borrow?' + params.toString();
            });
        });
