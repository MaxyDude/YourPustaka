let currentUserListMode = 'petugas';

        // Initialize tab indicator
        document.addEventListener('DOMContentLoaded', function() {
            updateTabIndicator();
            loadCategories();
            applyUserListView();
        });

        // Load categories from database
        function loadCategories() {
            fetch(window.__VIEW_CONFIG['e1'])
                .then(response => response.json())
                .then(categories => {
                    const select = document.getElementById('bookCategoryId');
                    categories.forEach(cat => {
                        const option = document.createElement('option');
                        option.value = cat.id;
                        option.textContent = cat.nama_kategori;
                        select.appendChild(option);
                    });
                })
                .catch(error => console.error('Error loading categories:', error));
        }

        // Tab Navigation with Slide Effect
        let currentTab = 'tabBooks';
        let isAnimating = false;

        function openTab(tabName, event) {
            if (isAnimating || tabName === currentTab) return;

            isAnimating = true;

            // Get button position for indicator
            const button = event.currentTarget;
            updateTabIndicatorPosition(button);

            // Update active button
            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            button.classList.add('active');

            // Animate current tab out
            const currentTabElement = document.getElementById(currentTab);
            currentTabElement.classList.add('slide-out');

            // After animation, switch to new tab
            setTimeout(() => {
                currentTabElement.classList.remove('active');
                currentTabElement.classList.remove('slide-out');

                // Show new tab
                const newTabElement = document.getElementById(tabName);
                newTabElement.classList.add('active');

                currentTab = tabName;
                isAnimating = false;
            }, 300);
        }

        function updateTabIndicatorPosition(button) {
            const indicator = document.getElementById('tabIndicator');
            const buttonRect = button.getBoundingClientRect();
            const wrapperRect = button.parentElement.getBoundingClientRect();

            indicator.style.width = buttonRect.width + 'px';
            indicator.style.left = (buttonRect.left - wrapperRect.left) + 'px';
        }

        function updateTabIndicator() {
            const activeButton = document.querySelector('.tab-btn.active');
            if (activeButton) {
                updateTabIndicatorPosition(activeButton);
            }
        }

        // Preview Image in Modal
        function previewBookImage(url) {
            const previewDiv = document.getElementById('imagePreview');

            if (url && url.trim() !== '') {
                // Support both HTTP URLs and data URLs (base64)
                if (url.startsWith('http') || url.startsWith('data:image')) {
                    previewDiv.style.backgroundImage = `url('${url}')`;
                    previewDiv.classList.remove('no-image');
                    previewDiv.innerHTML = '';
                } else {
                    previewDiv.style.backgroundImage = 'none';
                    previewDiv.classList.add('no-image');
                    previewDiv.innerHTML = `
                        <i class="fas fa-exclamation-triangle" style="font-size: 2rem; color: #f39c12;"></i>
                        <span style="margin-top: 10px;">URL tidak valid</span>
                    `;
                }
            } else {
                previewDiv.style.backgroundImage = 'none';
                previewDiv.classList.add('no-image');
                previewDiv.innerHTML = `
                    <i class="fas fa-book-open" style="font-size: 3rem; color: #adb5bd;"></i>
                    <span style="margin-top: 10px;">Preview akan muncul di sini</span>
                `;
            }
        }

        // Switch Image Mode (URL or File)
        function switchImageMode(mode) {
            const btnUrl = document.getElementById('btnImageUrl');
            const btnFile = document.getElementById('btnImageFile');
            const urlSection = document.getElementById('imageUrlSection');
            const fileSection = document.getElementById('imageFileSection');

            if (mode === 'url') {
                // Show URL section
                urlSection.style.display = 'block';
                fileSection.style.display = 'none';

                // Update button styles
                btnUrl.classList.remove('btn-outline');
                btnUrl.classList.add('btn-primary');
                btnFile.classList.remove('btn-primary');
                btnFile.classList.add('btn-outline');

                // Clear file input
                document.getElementById('bookImage').value = '';
            } else if (mode === 'file') {
                // Show File section
                urlSection.style.display = 'none';
                fileSection.style.display = 'block';

                // Update button styles
                btnFile.classList.remove('btn-outline');
                btnFile.classList.add('btn-primary');
                btnUrl.classList.remove('btn-primary');
                btnUrl.classList.add('btn-outline');

                // Clear URL input
                document.getElementById('bookImageUrl').value = '';

                // Initialize drag and drop zone
                initializeDropZone();
            }

            // Reset preview
            const previewDiv = document.getElementById('imagePreview');
            previewDiv.style.backgroundImage = 'none';
            previewDiv.classList.add('no-image');
            previewDiv.innerHTML = `
                <i class="fas fa-book-open" style="font-size: 3rem; color: #adb5bd;"></i>
                <span style="margin-top: 10px;">Preview akan muncul di sini</span>
            `;
        }

        // Initialize Drag and Drop Zone
        function initializeDropZone() {
            const dropZone = document.getElementById('dropZone');
            const fileInput = document.getElementById('bookImage');

            if (!dropZone) return;

            // Click to open file dialog
            dropZone.addEventListener('click', function() {
                fileInput.click();
            });

            // Drag and drop events
            dropZone.addEventListener('dragover', handleDragOver);
            dropZone.addEventListener('dragenter', handleDragEnter);
            dropZone.addEventListener('dragleave', handleDragLeave);
            dropZone.addEventListener('drop', handleDrop);

            // Prevent default browser behavior
            document.addEventListener('dragover', preventDefaults);
            document.addEventListener('drop', preventDefaults);
        }

        // Prevent default browser behavior for drag and drop
        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }

        // Handle dragover event
        function handleDragOver(e) {
            e.preventDefault();
            e.stopPropagation();
        }

        // Handle dragenter event
        function handleDragEnter(e) {
            e.preventDefault();
            e.stopPropagation();
            const dropZone = document.getElementById('dropZone');
            if (dropZone) {
                dropZone.style.backgroundColor = 'rgba(67, 97, 238, 0.1)';
                dropZone.style.borderColor = 'var(--secondary)';
                dropZone.style.transform = 'scale(1.02)';
            }
        }

        // Handle dragleave event
        function handleDragLeave(e) {
            e.preventDefault();
            e.stopPropagation();
            const dropZone = document.getElementById('dropZone');
            if (dropZone) {
                dropZone.style.backgroundColor = '#f8f9ff';
                dropZone.style.borderColor = 'var(--primary)';
                dropZone.style.transform = 'scale(1)';
            }
        }

        // Handle drop event
        function handleDrop(e) {
            e.preventDefault();
            e.stopPropagation();

            const dropZone = document.getElementById('dropZone');
            const fileInput = document.getElementById('bookImage');

            // Reset style
            if (dropZone) {
                dropZone.style.backgroundColor = '#f8f9ff';
                dropZone.style.borderColor = 'var(--primary)';
                dropZone.style.transform = 'scale(1)';
            }

            // Get files from drop event
            const files = e.dataTransfer.files;

            if (files && files.length > 0) {
                // Set the files to the file input
                fileInput.files = files;

                // Trigger the change event to preview
                const event = new Event('change', { bubbles: true });
                fileInput.dispatchEvent(event);
            }
        }

        // Preview Image from File Upload
        function previewBookImageFile(input) {
            const previewDiv = document.getElementById('imagePreview');

            if (input.files && input.files[0]) {
                const file = input.files[0];

                // Validasi ukuran file (max 5MB)
                if (file.size > 5 * 1024 * 1024) {
                    showPopup('Ukuran file terlalu besar! Maksimal 5MB.', 'error', 'File Terlalu Besar');
                    input.value = '';
                    return;
                }

                // Validasi tipe file
                const validTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                if (!validTypes.includes(file.type)) {
                    showPopup('Format file tidak didukung! Gunakan JPEG, PNG, GIF, atau WebP.', 'error', 'Format Tidak Didukung');
                    input.value = '';
                    return;
                }

                // Baca file dan tampilkan preview
                const reader = new FileReader();

                reader.onload = function(e) {
                    previewDiv.style.backgroundImage = `url('${e.target.result}')`;
                    previewDiv.classList.remove('no-image');
                    previewDiv.innerHTML = '';

                    // Clear URL input jika file dipilih
                    document.getElementById('bookImageUrl').value = '';
                };

                reader.readAsDataURL(file);
            } else {
                previewDiv.style.backgroundImage = 'none';
                previewDiv.classList.add('no-image');
                previewDiv.innerHTML = `
                    <i class="fas fa-book-open" style="font-size: 3rem; color: #adb5bd;"></i>
                    <span style="margin-top: 10px;">Preview akan muncul di sini</span>
                `;
            }
        }

        // Book Modal Functions
        function openBookModal(action, button = null) {
            const modal = document.getElementById('bookModal');
            const title = document.getElementById('bookModalTitle');
            const form = document.getElementById('bookForm');

            // Reset preview image
            document.getElementById('imagePreview').style.backgroundImage = 'none';
            document.getElementById('imagePreview').classList.add('no-image');
            document.getElementById('imagePreview').innerHTML = `
                <i class="fas fa-book-open" style="font-size: 3rem; color: #adb5bd;"></i>
                <span style="margin-top: 10px;">Preview akan muncul di sini</span>
            `;

            // Reset file input
            if (document.getElementById('bookImage')) {
                document.getElementById('bookImage').value = '';
            }

            // Initialize to URL mode (default)
            switchImageMode('url');

            if (action === 'add') {
                title.textContent = 'Tambah Buku Baru';
                form.reset();
                document.getElementById('bookStock').value = 1;
                document.getElementById('bookYear').value = new Date().getFullYear();
                document.getElementById('bookImageUrl').value = '';
                document.getElementById('bookPages').value = 300;
                document.getElementById('bookDescription').value = 'Deskripsi buku yang menarik dan informatif.';
            } else if (action === 'edit' && button) {
                title.textContent = 'Edit Data Buku';
                const card = button.closest('.book-card');
                const titleText = card.querySelector('.book-title').textContent;
                const authorText = card.querySelector('.book-author span').textContent;
                const metaRows = card.querySelectorAll('.meta-row');
                const cover = card.querySelector('.book-cover');

                const bgImage = cover.style.backgroundImage;
                let imageUrl = '';
                if (bgImage && bgImage !== 'none') {
                    imageUrl = bgImage.replace(/url\(['"]?(.*?)['"]?\)/i, '$1');
                }

                let isbn = '', year = '';
                metaRows.forEach(row => {
                    const label = row.querySelector('.meta-label').textContent;
                    const value = row.querySelector('.meta-value').textContent;

                    if (label.includes('ISBN')) isbn = value;
                    if (label.includes('Tahun')) year = value;
                });

                const stockText = card.querySelector('.book-stock').textContent;
                const stock = parseInt(stockText.match(/\d+/)[0]) || 0;
                const categoryId = card.getAttribute('data-category-id');
                const ratingText = card.querySelector('.book-rating span').textContent;

                let rating = 4.0;
                const ratingStars = card.querySelector('.rating-stars');
                if (ratingStars) {
                    const filledStars = ratingStars.querySelectorAll('.fas.fa-star').length;
                    const halfStars = ratingStars.querySelectorAll('.fas.fa-star-half-alt').length;
                    rating = filledStars + (halfStars * 0.5);
                }

                document.getElementById('bookTitle').value = titleText;
                document.getElementById('bookAuthor').value = authorText;
                document.getElementById('bookIsbn').value = isbn;
                document.getElementById('bookYear').value = year;
                document.getElementById('bookStock').value = stock;
                document.getElementById('bookCategoryId').value = categoryId;
                document.getElementById('bookPublisher').value = 'Pustaka Digital';
                document.getElementById('bookPages').value = '320';
                document.getElementById('bookDescription').value = 'Deskripsi buku yang informatif dan menarik.';
                document.getElementById('bookImageUrl').value = imageUrl;

                if (imageUrl) {
                    previewBookImage(imageUrl);
                }
            } else {
                title.textContent = 'Tambah Buku Baru';
                form.reset();
                document.getElementById('bookStock').value = 1;
                document.getElementById('bookYear').value = new Date().getFullYear();
                document.getElementById('bookImageUrl').value = '';
            }

            modal.style.display = 'flex';
        }

        function closeBookModal() {
            document.getElementById('bookModal').style.display = 'none';
        }

        function saveBook(event) {
            const bookTitle = document.getElementById('bookTitle').value;
            const bookAuthor = document.getElementById('bookAuthor').value;
            const bookIsbn = document.getElementById('bookIsbn').value;
            const bookYear = document.getElementById('bookYear').value;
            const bookStock = document.getElementById('bookStock').value;
            const bookCategoryId = document.getElementById('bookCategoryId').value;
            const bookPublisher = document.getElementById('bookPublisher').value;
            const bookPages = document.getElementById('bookPages').value;
            const bookDescription = document.getElementById('bookDescription').value;
            const bookImageFile = document.getElementById('bookImage').files[0];
            const bookImageUrl = document.getElementById('bookImageUrl').value;

            // Validasi field wajib
            if (!bookTitle || !bookAuthor || !bookIsbn || !bookYear || !bookStock || !bookCategoryId || !bookPublisher || !bookPages) {
                showPopup('Harap isi semua field yang wajib diisi!', 'warning', 'Validasi Error');
                return;
            }

            // Cek apakah ada gambar (file atau URL)
            if (!bookImageFile && !bookImageUrl) {
                showPopup('Harap upload atau masukkan URL gambar cover buku!', 'warning', 'Gambar Diperlukan');
                return;
            }

            // Buat FormData untuk mengirim file
            const formData = new FormData();
            formData.append('bookTitle', bookTitle);
            formData.append('bookAuthor', bookAuthor);
            formData.append('bookIsbn', bookIsbn);
            formData.append('bookYear', bookYear);
            formData.append('bookStock', bookStock);
            formData.append('bookCategoryId', bookCategoryId);
            formData.append('bookPublisher', bookPublisher);
            formData.append('bookPages', bookPages);
            formData.append('bookDescription', bookDescription);
            formData.append('bookImageUrl', bookImageUrl);

            // Tambahkan file jika ada
            if (bookImageFile) {
                formData.append('bookImage', bookImageFile);
            }

            // Show loading state
            const saveBtn = document.getElementById('saveBookBtn');
            const originalText = saveBtn.innerHTML;
            saveBtn.disabled = true;
            saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';

            // Kirim ke backend
            fetch('/admin/books/store', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    showPopup('Buku "' + bookTitle + '" berhasil disimpan!', 'success', 'Sukses', () => {
                        closeBookModal();
                        location.reload();
                    });
                } else {
                    showPopup('Gagal menyimpan buku: ' + (data.message || 'Terjadi kesalahan'), 'error', 'Error');
                    saveBtn.disabled = false;
                    saveBtn.innerHTML = originalText;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showPopup('Terjadi kesalahan saat menyimpan buku: ' + error.message, 'error', 'Error');
                saveBtn.disabled = false;
                saveBtn.innerHTML = originalText;
            });
        }

        function deleteBook(button, bookId) {
            if (confirm('Apakah Anda yakin ingin menghapus buku ini?')) {
                const card = button.closest('.book-card');
                const originalDisplay = card.style.display;

                // Show loading state
                button.disabled = true;
                button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menghapus...';

                fetch(`/admin/books/${bookId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        // Animate removal
                        card.style.transition = 'all 0.3s ease';
                        card.style.opacity = '0';
                        card.style.transform = 'scale(0.9)';

                        setTimeout(() => {
                            card.remove();
                        }, 300);

                        showPopup(data.message || 'Buku berhasil dihapus!', 'success', 'Dihapus');
                    } else {
                        button.disabled = false;
                        button.innerHTML = '<i class="fas fa-trash"></i>';
                        showPopup('Gagal menghapus buku: ' + (data.message || 'Unknown error'), 'error', 'Error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    button.disabled = false;
                    button.innerHTML = '<i class="fas fa-trash"></i>';
                    showPopup('Terjadi kesalahan saat menghapus buku: ' + error.message, 'error', 'Error');
                });
            }
        }

        // Detail Buku Functions
        function viewBookDetail(button) {
            const modal = document.getElementById('bookDetailModal');
            const card = button.closest('.book-card');

            if (!card) return;

            const title = card.querySelector('.book-title').textContent;
            const author = card.querySelector('.book-author span').textContent;
            const metaRows = card.querySelectorAll('.meta-row');
            const cover = card.querySelector('.book-cover');
            const category = card.querySelector('.book-category').textContent;
            const stockText = card.querySelector('.book-stock').textContent;
            const ratingElement = card.querySelector('.book-rating span');
            const rating = ratingElement ? ratingElement.textContent : '4.0';

            const bgImage = cover.style.backgroundImage;
            let imageUrl = '';
            if (bgImage && bgImage !== 'none') {
                imageUrl = bgImage.replace(/url\(['"]?(.*?)['"]?\)/i, '$1');
            }

            let bookId = '', isbn = '', year = '';
            metaRows.forEach(row => {
                const label = row.querySelector('.meta-label').textContent;
                const value = row.querySelector('.meta-value').textContent;

                if (label.includes('ID Buku')) bookId = value;
                if (label.includes('ISBN')) isbn = value;
                if (label.includes('Tahun')) year = value;
            });

            const stockMatch = stockText.match(/\d+/);
            const stock = stockMatch ? parseInt(stockMatch[0]) : 0;
            const status = stock > 0 ? 'Tersedia' : 'Habis';
            const statusClass = stock > 0 ? 'status-available' : 'status-pending';

            document.getElementById('detailBookTitle').textContent = title;
            document.getElementById('detailBookAuthor').textContent = author;
            document.getElementById('detailBookId').textContent = bookId;
            document.getElementById('detailBookIsbn').textContent = isbn;
            document.getElementById('detailBookStock').textContent = `${stock} buku tersedia`;
            document.getElementById('detailBookPublisher').textContent = 'Pustaka Digital';
            document.getElementById('detailBookYear').textContent = year || '2023';
            document.getElementById('detailBookPages').textContent = '320';
            document.getElementById('detailBookCategory').textContent = category;
            document.getElementById('detailBookRatingNumber').textContent = rating;
            document.getElementById('detailBookReviews').textContent = `${Math.floor(Math.random() * 200) + 50} review`;
            document.getElementById('detailBookDescription').textContent = 'Buku ini membahas secara komprehensif tentang topik yang relevan dengan pembaca modern. ' +
                'Ditulis dengan gaya yang mudah dipahami dan dilengkapi dengan contoh-contoh praktis.';

            const statusElement = document.getElementById('detailBookStatus');
            statusElement.textContent = status;
            statusElement.className = `status-badge ${statusClass}`;

            const detailCover = document.getElementById('detailBookCover');
            if (imageUrl) {
                detailCover.style.backgroundImage = `url('${imageUrl}')`;
                detailCover.innerHTML = `<div style="position: absolute; top: 10px; left: 10px; background-color: rgba(0, 0, 0, 0.7); padding: 5px 8px; border-radius: 15px; font-size: 0.8rem; display: flex; align-items: center; gap: 3px; color: #FFD700;">
                    <i class="fas fa-star" style="font-size: 0.7rem;"></i>
                    <span id="detailBookRatingNumber">${rating}</span>
                </div>`;
            } else {
                detailCover.style.backgroundImage = `linear-gradient(135deg, #667eea 0%, #764ba2 100%)`;
                detailCover.innerHTML = `
                    <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); color: white; text-align: center;">
                        <i class="fas fa-book" style="font-size: 2rem;"></i><br>
                        <span>No Cover</span>
                    </div>
                    <div style="position: absolute; top: 10px; left: 10px; background-color: rgba(0, 0, 0, 0.7); padding: 5px 8px; border-radius: 15px; font-size: 0.8rem; display: flex; align-items: center; gap: 3px; color: #FFD700;">
                        <i class="fas fa-star" style="font-size: 0.7rem;"></i>
                        <span id="detailBookRatingNumber">${rating}</span>
                    </div>
                `;
            }

            const ratingValue = parseFloat(rating);
            const ratingStarsContainer = document.getElementById('detailRatingStars');
            if (ratingStarsContainer) {
                let starsHTML = '';
                for (let i = 1; i <= 5; i++) {
                    if (ratingValue >= i) {
                        starsHTML += '<i class="fas fa-star"></i>';
                    } else if (ratingValue >= i - 0.5) {
                        starsHTML += '<i class="fas fa-star-half-alt"></i>';
                    } else {
                        starsHTML += '<i class="fas fa-star empty"></i>';
                    }
                }
                starsHTML += `<span style="margin-left: 5px;">(${rating}/5)</span>`;
                ratingStarsContainer.innerHTML = starsHTML;
            }

            modal.style.display = 'flex';
        }

        function closeBookDetailModal() {
            document.getElementById('bookDetailModal').style.display = 'none';
        }

        function editFromDetail() {
            closeBookDetailModal();
            setTimeout(() => {
                openBookModal('edit');
            }, 300);
        }

        // Fungsi untuk format durasi dari milidetik menjadi string
        function formatDuration(milliseconds) {
            const totalSeconds = Math.floor(milliseconds / 1000);
            const days = Math.floor(totalSeconds / (24 * 60 * 60));
            const hours = Math.floor((totalSeconds % (24 * 60 * 60)) / (60 * 60));
            const minutes = Math.floor((totalSeconds % (60 * 60)) / 60);
            const seconds = totalSeconds % 60;

            let result = '';
            if (days > 0) result += `${days} hari `;
            if (hours > 0) result += `${hours} jam `;
            if (minutes > 0) result += `${minutes} menit `;
            result += `${seconds} detik`;

            return result.trim();
        }

        // Fungsi untuk parse tanggal dari string format "15 Mar 2024"
        function parseDate(dateStr) {
            // Format: "15 Mar 2024"
            const months = {
                'Jan': 0, 'Feb': 1, 'Mar': 2, 'Apr': 3, 'Mei': 4, 'May': 4,
                'Jun': 5, 'Jul': 6, 'Aug': 7, 'Sep': 8, 'Okt': 9, 'Oct': 9,
                'Nov': 10, 'Des': 11, 'Dec': 11
            };

            const parts = dateStr.split(' ');
            const day = parseInt(parts[0]);
            const month = months[parts[1]];
            const year = parseInt(parts[2]);

            // Tambahkan jam 10:00:00 sebagai waktu peminjaman standar
            return new Date(year, month, day, 10, 0, 0);
        }

        function resolveCoverUrl(rawCover) {
            if (!rawCover) return '';
            if (rawCover.startsWith('http://') || rawCover.startsWith('https://') || rawCover.startsWith('/')) {
                return rawCover;
            }
            return `/${rawCover}`;
        }

        function getStatusClass(status) {
            const statusMap = {
                'Dipinjam': 'status-borrowed',
                'Terlambat': 'status-overdue',
                'Dikembalikan': 'status-returned',
                'Menunggu': 'status-pending',
                'Menunggu Konfirmasi': 'status-pending',
                'Menunggu Pengambilan': 'status-pending',
                'Ditolak': 'status-overdue'
            };
            return statusMap[status] || 'status-borrowed';
        }

        // Detail Peminjaman Functions (DENGAN DURASI BERJALAN - STOPWATCH)
        let borrowDetailInterval = null;
        let currentBorrowRow = null;

        function viewBorrowDetail(button) {
            const modal = document.getElementById('borrowDetailModal');
            const row = button.closest('tr');
            currentBorrowRow = row;

            if (!row) return;

            const cells = row.querySelectorAll('td');
            if (cells.length < 7) return;

            // Data dari tabel
            const borrowId = cells[0].textContent;
            const borrowerName = cells[1].textContent;
            const bookId = cells[2].textContent;
            const bookTitle = cells[3].textContent;
            const borrowDateStr = cells[4].textContent;
            const dueDateStr = cells[5].textContent;
            const statusElement = cells[6].querySelector('.status-badge');
            const status = statusElement ? statusElement.textContent.trim() : 'Dipinjam';
            const statusClass = statusElement ? statusElement.className : 'status-badge status-borrowed';
            const isReturned = status === 'Dikembalikan';
            const isLoanStarted = status === 'Dipinjam' || status === 'Terlambat';
            const borrowerId = row.dataset.borrowerId || '-';
            const borrowerEmail = row.dataset.borrowerEmail || '-';
            const bookAuthor = row.dataset.bookAuthor || '-';
            const bookIsbn = row.dataset.bookIsbn || '-';
            const bookCategory = row.dataset.bookCategory || 'Umum';
            const bookCoverRaw = row.dataset.bookCover || '';
            const officerName = row.dataset.officerName || 'Admin Perpustakaan';
            const confirmDate = row.dataset.confirmDate || borrowDateStr;
            const loanNotes = row.dataset.loanNotes || 'Tidak ada catatan';

            // Parse tanggal
            const borrowDate = parseDate(borrowDateStr);
            const dueDate = parseDate(dueDateStr);
            const now = new Date();

            // Generate initials from borrower name
            const nameParts = borrowerName.split(' ');
            const initials = nameParts.length >= 2
                ? (nameParts[0].charAt(0) + nameParts[1].charAt(0)).toUpperCase()
                : borrowerName.substring(0, 2).toUpperCase();

            // Set data ke modal
            document.getElementById('borrowerInitials').textContent = initials;
            document.getElementById('borrowerName').textContent = borrowerName;
            document.getElementById('borrowerInfo').textContent = `ID Anggota: ${borrowerId} | Email: ${borrowerEmail}`;
            document.getElementById('borrowId').textContent = borrowId;
            document.getElementById('borrowStatus').innerHTML = `<span class="status-badge ${getStatusClass(status)}">${status}</span>`;
            document.getElementById('borrowBookTitle').textContent = bookTitle;
            document.getElementById('borrowBookAuthor').innerHTML = `<strong>Penulis:</strong> ${bookAuthor}`;
            document.getElementById('borrowBookId').textContent = bookId;
            document.getElementById('borrowBookIsbn').textContent = bookIsbn;
            document.getElementById('borrowBookCategory').textContent = bookCategory;
            document.getElementById('borrowDate').textContent = borrowDateStr;
            document.getElementById('dueDate').textContent = dueDateStr;

            // Hitung durasi total
            const totalMs = dueDate - borrowDate;
            const totalDays = Math.ceil(totalMs / (1000 * 60 * 60 * 24));
            document.getElementById('borrowDuration').textContent = `${totalDays} hari`;

            // Set cover buku
            const bookCover = document.getElementById('borrowBookCover');
            const coverUrl = resolveCoverUrl(bookCoverRaw);
            if (coverUrl) {
                bookCover.style.backgroundImage = `url('${coverUrl}')`;
                bookCover.innerHTML = '';
            } else {
                bookCover.style.backgroundImage = 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)';
                bookCover.innerHTML = '<div style="display: flex; align-items: center; justify-content: center; height: 100%; color: white;"><i class="fas fa-book"></i></div>';
            }

            // Set informasi tambahan
            document.getElementById('officerName').textContent = officerName;
            document.getElementById('confirmDate').textContent = confirmDate;
            document.getElementById('borrowNotes').textContent = loanNotes;

            // Fungsi untuk memperbarui stopwatch
            function updateStopwatch() {
                const now = new Date();
                const elapsedMs = now - borrowDate;
                const remainingMs = dueDate - now;

                // Format waktu yang telah berlalu (STOPWATCH)
                const elapsedFormatted = formatDuration(elapsedMs);
                document.getElementById('elapsedTime').textContent = elapsedFormatted;

                // Format sisa waktu
                if (remainingMs > 0) {
                    const remainingFormatted = formatDuration(remainingMs);
                    document.getElementById('remainingTime').textContent = remainingFormatted;
                    document.getElementById('remainingTime').style.color = 'var(--success)';
                } else {
                    const overdueFormatted = formatDuration(-remainingMs);
                    document.getElementById('remainingTime').textContent = `Terlambat ${overdueFormatted}`;
                    document.getElementById('remainingTime').style.color = 'var(--danger)';
                }

                // Hitung progress percentage
                const progressPercentage = Math.min(Math.max((elapsedMs / totalMs) * 100, 0), 100);
                document.getElementById('progressPercentage').textContent = `${progressPercentage.toFixed(1)}%`;

                // Update progress bar
                const progressFill = document.getElementById('progressFill');
                progressFill.style.width = `${progressPercentage}%`;

                // Hitung hari ke-
                const elapsedDays = Math.floor(elapsedMs / (1000 * 60 * 60 * 24)) + 1;
                document.getElementById('dayNumber').textContent = Math.min(elapsedDays, totalDays);

                // Update warna progress berdasarkan status
                if (remainingMs < 0) {
                    progressFill.className = 'progress-fill danger';
                    document.getElementById('timeStatus').innerHTML = '<span class="status-badge status-overdue">Terlambat</span>';
                } else if (remainingMs < 2 * 24 * 60 * 60 * 1000) { // Kurang dari 2 hari
                    progressFill.className = 'progress-fill warning';
                    document.getElementById('timeStatus').innerHTML = '<span class="status-badge status-pending">Hampir Jatuh Tempo</span>';
                } else {
                    progressFill.className = 'progress-fill';
                    document.getElementById('timeStatus').innerHTML = '<span class="status-badge status-active">Aman</span>';
                }
            }

            // Hentikan interval sebelumnya jika ada
            if (borrowDetailInterval) {
                clearInterval(borrowDetailInterval);
            }

            // Durasi berjalan hanya aktif setelah tiket terkonfirmasi/scan (status Dipinjam/Terlambat)
            if (isLoanStarted) {
                // Update segera
                updateStopwatch();

                // Update setiap detik (STOPWATCH REAL-TIME)
                borrowDetailInterval = setInterval(updateStopwatch, 1000);
            } else if (isReturned) {
                // Untuk status dikembalikan, hitung total durasi
                const totalMs = dueDate - borrowDate;
                const elapsedFormatted = formatDuration(totalMs);
                document.getElementById('elapsedTime').textContent = elapsedFormatted;
                document.getElementById('remainingTime').textContent = 'Selesai';
                document.getElementById('remainingTime').style.color = 'var(--success)';
                document.getElementById('progressPercentage').textContent = '100%';
                document.getElementById('progressFill').style.width = '100%';
                document.getElementById('progressFill').className = 'progress-fill';
                document.getElementById('dayNumber').textContent = totalDays;
                document.getElementById('timeStatus').innerHTML = '<span class="status-badge status-returned">Selesai</span>';
            } else {
                // Status belum aktif: progress belum berjalan
                document.getElementById('elapsedTime').textContent = 'Belum dimulai';
                document.getElementById('remainingTime').textContent = 'Menunggu scan / konfirmasi tiket';
                document.getElementById('remainingTime').style.color = 'var(--gray)';
                document.getElementById('progressPercentage').textContent = '0%';
                document.getElementById('progressFill').style.width = '0%';
                document.getElementById('progressFill').className = 'progress-fill';
                document.getElementById('dayNumber').textContent = '-';
                document.getElementById('timeStatus').innerHTML = '<span class="status-badge status-pending">Belum Aktif</span>';
            }

            // Update tombol berdasarkan status
            const returnBtn = document.getElementById('returnBtn');
            if (isReturned || !isLoanStarted) {
                returnBtn.style.display = 'none';
            } else {
                returnBtn.style.display = 'inline-flex';
                returnBtn.onclick = function() { confirmReturnFromDetail(); };
            }

            modal.style.display = 'flex';
        }

        function closeBorrowDetailModal() {
            document.getElementById('borrowDetailModal').style.display = 'none';

            // Hentikan interval stopwatch
            if (borrowDetailInterval) {
                clearInterval(borrowDetailInterval);
                borrowDetailInterval = null;
            }

            currentBorrowRow = null;
        }

        function confirmReturnFromDetail() {
            if (currentBorrowRow) {
                confirmReturnFromRow(currentBorrowRow);
                closeBorrowDetailModal();
            }
        }

        function confirmReturnFromRow(row) {
            const borrowId = row.cells[0].textContent;
            const loanId = parseInt(borrowId, 10);
            const bookId = parseInt(row.cells[2].textContent, 10);
            const returnUrlBase = window.__VIEW_CONFIG['e5'];

            showPopup(`Konfirmasi pengembalian untuk peminjaman ID: ${borrowId}?`, 'info', 'Konfirmasi', () => {
                if (!loanId) {
                    showPopup('ID peminjaman tidak valid.', 'error', 'Error');
                    return;
                }

                fetch(`${returnUrlBase}/${loanId}/return`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    }
                })
                .then(async response => {
                    let data = {};

                    try {
                        data = await response.json();
                    } catch (error) {
                        data = {};
                    }

                    if (!response.ok || !data.success) {
                        throw new Error(data.message || 'Gagal memproses pengembalian.');
                    }

                    return data;
                })
                .then(() => {
                    const statusCell = row.cells[6];
                    statusCell.innerHTML = '<span class="status-badge status-returned">Dikembalikan</span>';

                    // Update action buttons
                    const actionCell = row.cells[7];
                    actionCell.innerHTML = `
                        <div class="action-buttons">
                            <button class="btn btn-outline btn-sm" onclick="viewBorrowDetail(this)">
                                <i class="fas fa-eye"></i> Detail
                            </button>
                            <button class="btn btn-danger btn-sm" onclick="deleteBorrow(this)">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    `;

                    // Sinkronkan stok pada kartu buku jika ada di tab buku
                    if (bookId) {
                        const card = document.querySelector(`.book-card[data-book-id="${bookId}"]`);
                        if (card) {
                            const stockEl = card.querySelector('.book-stock');
                            if (stockEl) {
                                const match = stockEl.textContent.match(/\d+/);
                                const currentStock = match ? parseInt(match[0], 10) : 0;
                                const updatedStock = currentStock + 1;
                                stockEl.innerHTML = `<i class="fas fa-box"></i> Stok: ${updatedStock}`;
                            }
                        }
                    }

                    showPopup('Pengembalian berhasil dikonfirmasi! Stok buku telah ditambahkan kembali.', 'success', 'Berhasil');
                })
                .catch(error => {
                    showPopup('Gagal mengonfirmasi pengembalian: ' + error.message, 'error', 'Error');
                });
            }, () => {});
        }

        function printBorrowDetail() {
            showPopup('Mencetak detail peminjaman...', 'info', 'Cetak');
        }

        // Peminjaman Functions
        function confirmReturn(button) {
            const row = button.closest('tr');
            confirmReturnFromRow(row);
        }

        function sendReminder(button) {
            const row = button.closest('tr');
            const borrower = row.cells[1].textContent;
            const bookTitle = row.cells[3].textContent;
            const dueDate = row.cells[5].textContent;

            showPopup(`Mengirim pengingat kepada ${borrower} untuk mengembalikan buku "${bookTitle}" yang jatuh tempo pada ${dueDate}`, 'info', 'Pengingat Dikirim');
        }

        function deleteBorrow(button) {
            showPopup('Apakah Anda yakin ingin menghapus data peminjaman ini?', 'warning', 'Konfirmasi Hapus', () => {
                const row = button.closest('tr');
                row.remove();
                showPopup('Data peminjaman berhasil dihapus!', 'success', 'Dihapus');
            });
        }

        function toggleUserListView() {
            currentUserListMode = currentUserListMode === 'petugas' ? 'user' : 'petugas';
            applyUserListView();
        }

        function applyUserListView() {
            const titleEl = document.getElementById('userListTitle');
            const toggleBtn = document.getElementById('toggleUserListBtn');
            const addBtn = document.getElementById('addPetugasBtn');
            const rows = document.querySelectorAll('#usersTableBody .user-row');
            const emptyStateRow = document.getElementById('usersEmptyState');

            let visibleCount = 0;
            rows.forEach(row => {
                const role = (row.dataset.role || '').toLowerCase();
                const shouldShow = role === currentUserListMode;
                row.style.display = shouldShow ? '' : 'none';
                const editBtn = row.querySelector('.btn-outline.btn-sm');
                if (editBtn) {
                    editBtn.style.display = currentUserListMode === 'petugas' ? '' : 'none';
                }
                if (shouldShow) visibleCount++;
            });

            if (titleEl) {
                titleEl.textContent = currentUserListMode === 'petugas' ? 'Daftar Petugas' : 'Daftar Pengguna';
            }

            if (toggleBtn) {
                if (currentUserListMode === 'petugas') {
                    toggleBtn.innerHTML = '<i class="fas fa-user"></i> Daftar Pengguna';
                } else {
                    toggleBtn.innerHTML = '<i class="fas fa-user-tie"></i> Daftar Petugas';
                }
            }

            if (addBtn) {
                addBtn.style.display = currentUserListMode === 'petugas' ? '' : 'none';
            }

            if (emptyStateRow) {
                const emptyMessage = currentUserListMode === 'petugas'
                    ? 'Belum ada data petugas'
                    : 'Belum ada data pengguna';

                const messageCell = emptyStateRow.querySelector('td');
                if (messageCell) {
                    messageCell.textContent = emptyMessage;
                }

                emptyStateRow.style.display = visibleCount === 0 ? '' : 'none';
            }
        }

        // User Modal Functions (PASSWORD MINIMAL 8 KARAKTER)
        function openUserModal(action, button = null) {
            const modal = document.getElementById('userModal');
            const title = document.getElementById('userModalTitle');
            const passwordField = document.getElementById('userPassword');
            const passwordHint = document.getElementById('passwordHint');
            const form = document.getElementById('userForm');

            if (action === 'add') {
                title.textContent = 'Tambah Petugas Baru';
                form.reset();
                form.dataset.userId = '';
                // Set default role to "Petugas"
                document.getElementById('userRole').value = 'petugas';
                // Password wajib untuk tambah pengguna
                passwordField.setAttribute('required', 'required');
                passwordField.setAttribute('minlength', '8');
                passwordField.placeholder = 'Masukkan password (min 8 karakter)';
                passwordHint.textContent = 'Password minimal 8 karakter';
            } else if (action === 'edit' && button) {
                title.textContent = 'Edit Data Pengguna';
                const row = button.closest('tr');
                const cells = row.querySelectorAll('td');

                if (cells.length >= 5) {
                    form.dataset.userId = cells[0].textContent.trim();
                    document.getElementById('userName').value = cells[1].textContent;
                    document.getElementById('userEmail').value = cells[2].textContent;
                    document.getElementById('userPhone').value = cells[3].textContent;
                    document.getElementById('userRole').value = 'petugas';

                    // Untuk edit, password tidak wajib
                    passwordField.removeAttribute('required');
                    passwordField.removeAttribute('minlength');
                    passwordField.value = ''; // Kosongkan password
                    passwordField.placeholder = 'Kosongkan jika tidak ingin mengubah password';
                    passwordHint.textContent = 'Kosongkan jika tidak ingin mengubah password';
                }
            } else {
                title.textContent = 'Tambah Petugas Baru';
                form.reset();
                form.dataset.userId = '';
                document.getElementById('userRole').value = 'petugas';
                passwordField.setAttribute('required', 'required');
                passwordField.setAttribute('minlength', '8');
                passwordField.placeholder = 'Masukkan password (min 8 karakter)';
                passwordHint.textContent = 'Password minimal 8 karakter';
            }

            modal.style.display = 'flex';
        }

        function closeUserModal() {
            // Reset password requirement untuk mode tambah
            const passwordField = document.getElementById('userPassword');
            passwordField.setAttribute('required', 'required');
            passwordField.setAttribute('minlength', '8');
            passwordField.placeholder = 'Masukkan password (min 8 karakter)';
            document.getElementById('passwordHint').textContent = 'Password minimal 8 karakter';

            document.getElementById('userModal').style.display = 'none';
        }

        function saveUser() {
            const usersStoreUrl = window.__VIEW_CONFIG['e6'];
            const usersBaseUrl = window.__VIEW_CONFIG['e7'];
            const userName = document.getElementById('userName').value;
            const userEmail = document.getElementById('userEmail').value;
            const userId = document.getElementById('userForm').dataset.userId || '';
            const userPassword = document.getElementById('userPassword').value;
            const userPhone = document.getElementById('userPhone').value;
            const userRole = document.getElementById('userRole').value;

            // Validasi form (TANPA ALAMAT)
            if (!userName || !userEmail || !userPhone || !userRole) {
                showPopup('Harap isi semua field yang wajib diisi!', 'warning', 'Validasi Error');
                return;
            }

            // Validasi email
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(userEmail)) {
                showPopup('Format email tidak valid!', 'error', 'Email Invalid');
                return;
            }

            // Validasi password hanya untuk tambah pengguna baru
            const isEdit = document.getElementById('userModalTitle').textContent.includes('Edit');

            if (isEdit && !userId) {
                showPopup('ID pengguna untuk edit tidak ditemukan.', 'error', 'Error');
                return;
            }

            if (!isEdit) {
                // Validasi untuk tambah pengguna baru
                if (!userPassword) {
                    showPopup('Harap isi password!', 'warning', 'Password Diperlukan');
                    return;
                }

                if (userPassword.length < 8) {
                    showPopup('Password minimal 8 karakter!', 'error', 'Password Terlalu Pendek');
                    return;
                }
            } else {
                // Untuk edit, jika password diisi, harus minimal 8 karakter
                if (userPassword && userPassword.length < 8) {
                    showPopup('Password minimal 8 karakter!', 'error', 'Password Terlalu Pendek');
                    return;
                }
            }

            const endpoint = isEdit ? `${usersBaseUrl}/${userId}` : usersStoreUrl;
            const method = isEdit ? 'PUT' : 'POST';
            const payload = {
                name: userName,
                email: userEmail,
                phone: userPhone,
                role: 'petugas'
            };

            if (userPassword) {
                payload.password = userPassword;
            }

            const saveButton = document.querySelector('#userModal .modal-footer .btn.btn-primary');
            const originalButtonHtml = saveButton ? saveButton.innerHTML : '';
            if (saveButton) {
                saveButton.disabled = true;
                saveButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';
            }

            fetch(endpoint, {
                method: method,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(payload)
            })
            .then(async response => {
                let data = {};

                try {
                    data = await response.json();
                } catch (error) {
                    data = {};
                }

                if (!response.ok || !data.success) {
                    let errorMessage = data.message || 'Terjadi kesalahan saat menyimpan data pengguna.';

                    if (data.errors && typeof data.errors === 'object') {
                        const firstErrorKey = Object.keys(data.errors)[0];
                        if (firstErrorKey && Array.isArray(data.errors[firstErrorKey]) && data.errors[firstErrorKey][0]) {
                            errorMessage = data.errors[firstErrorKey][0];
                        }
                    }

                    throw new Error(errorMessage);
                }

                return data;
            })
            .then(data => {
                const savedUserId = data.user?.id || userId;
                if (isEdit) {
                    showPopup(`Data pengguna "${userName}" (ID: ${savedUserId}) berhasil diperbarui!`, 'success', 'Diperbarui');
                } else {
                    showPopup(`Petugas "${userName}" (ID: ${savedUserId}) berhasil ditambahkan!`, 'success', 'Ditambahkan');
                }

                closeUserModal();
                setTimeout(() => window.location.reload(), 700);
            })
            .catch(error => {
                showPopup('Gagal menyimpan pengguna: ' + error.message, 'error', 'Error');
            })
            .finally(() => {
                if (saveButton) {
                    saveButton.disabled = false;
                    saveButton.innerHTML = originalButtonHtml;
                }
            });
        }

        function deleteUser(button, userId) {
            const usersBaseUrl = window.__VIEW_CONFIG['e7'];
            if (confirm('Apakah Anda yakin ingin menghapus pengguna ini?')) {
                const row = button.closest('tr');

                // Show loading state
                button.disabled = true;
                button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

                fetch(`${usersBaseUrl}/${userId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        // Animate removal
                        row.style.transition = 'all 0.3s ease';
                        row.style.opacity = '0';
                        row.style.transform = 'translateX(-20px)';

                        setTimeout(() => {
                            row.remove();
                        }, 300);

                        showPopup(data.message || 'Pengguna berhasil dihapus!', 'success', 'Dihapus');
                    } else {
                        button.disabled = false;
                        button.innerHTML = '<i class="fas fa-trash"></i>';
                        showPopup('Gagal menghapus pengguna: ' + (data.message || 'Unknown error'), 'error', 'Error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    button.disabled = false;
                    button.innerHTML = '<i class="fas fa-trash"></i>';
                    showPopup('Terjadi kesalahan saat menghapus pengguna: ' + error.message, 'error', 'Error');
                });
            }
        }

        // Filter Functions
        function resetFilters() {
            document.getElementById('filterCategory').value = '';
            document.getElementById('filterStatus').value = '';
            document.getElementById('searchBooks').value = '';
            filterBooks();
            showPopup('Filter telah direset!', 'success', 'Direset');
        }

        // Search functionality
        document.getElementById('searchBooks').addEventListener('input', function(e) {
            filterBooks();
        });

        // Category filter
        document.getElementById('filterCategory').addEventListener('change', function(e) {
            filterBooks();
        });

        // Stock filter
        document.getElementById('filterStatus').addEventListener('change', function(e) {
            filterBooks();
        });

        function filterBooks() {
            const searchTerm = document.getElementById('searchBooks').value.toLowerCase();
            const selectedCategory = document.getElementById('filterCategory').value.trim().toLowerCase();
            const selectedStatus = document.getElementById('filterStatus').value;
            const books = document.querySelectorAll('.book-card');

            books.forEach(book => {
                const title = book.querySelector('.book-title').textContent.toLowerCase();
                const author = book.querySelector('.book-author span').textContent.toLowerCase();
                const metaRows = book.querySelectorAll('.meta-row');
                const category = book.querySelector('.book-category').textContent.trim().toLowerCase();
                const stockText = book.querySelector('.book-stock').textContent;
                const stockMatch = stockText.match(/\d+/);
                const stock = stockMatch ? parseInt(stockMatch[0], 10) : 0;

                let bookId = '';
                metaRows.forEach(row => {
                    const label = row.querySelector('.meta-label').textContent;
                    const value = row.querySelector('.meta-value').textContent;
                    if (label.includes('ID Buku')) bookId = value;
                });

                let show = true;

                if (searchTerm && !title.includes(searchTerm) && !author.includes(searchTerm) && !bookId.includes(searchTerm)) {
                    show = false;
                }

                if (selectedCategory && category !== selectedCategory) {
                    show = false;
                }

                if (selectedStatus === 'available' && stock === 0) show = false;
                if (selectedStatus === 'borrowed' && stock > 0) show = false;
                if (selectedStatus === 'low' && stock > 2) show = false;

                book.style.display = show ? 'block' : 'none';
            });
        }

        // Close modals when clicking outside
        window.addEventListener('click', function(e) {
            const modals = ['bookModal', 'bookDetailModal', 'borrowDetailModal', 'userModal'];
            modals.forEach(modalId => {
                const modal = document.getElementById(modalId);
                if (e.target === modal) {
                    modal.style.display = 'none';
                }
            });
        });

        // Update tab indicator on window resize
        window.addEventListener('resize', updateTabIndicator);

        // Custom Popup Function
        function showPopup(message, type = 'info', title = null, onConfirm = null, onCancel = null) {
            const popup = document.getElementById('customPopup');
            const popupIcon = document.getElementById('popupIcon');
            const popupTitle = document.getElementById('popupTitle');
            const popupMessage = document.getElementById('popupMessage');
            const popupButtons = document.getElementById('popupButtons');

            // Set icon based on type
            const iconMap = {
                'success': '<i class="fas fa-check-circle"></i>',
                'error': '<i class="fas fa-exclamation-circle"></i>',
                'warning': '<i class="fas fa-exclamation-triangle"></i>',
                'info': '<i class="fas fa-info-circle"></i>'
            };

            // Set default title based on type
            const titleMap = {
                'success': 'Sukses',
                'error': 'Error',
                'warning': 'Perhatian',
                'info': 'Informasi'
            };

            popupIcon.innerHTML = iconMap[type] || iconMap['info'];
            popupIcon.className = `popup-icon ${type}`;
            popupTitle.textContent = title || titleMap[type] || 'Notifikasi';
            popupMessage.textContent = message;

            // Create buttons
            popupButtons.innerHTML = '';

            if (onConfirm || onCancel) {
                // Yes/No scenario
                if (onCancel) {
                    const cancelBtn = document.createElement('button');
                    cancelBtn.className = 'popup-btn popup-btn-secondary';
                    cancelBtn.textContent = 'Batal';
                    cancelBtn.onclick = () => {
                        closePopup();
                        onCancel();
                    };
                    popupButtons.appendChild(cancelBtn);
                }

                const confirmBtn = document.createElement('button');
                confirmBtn.className = 'popup-btn popup-btn-primary';
                confirmBtn.textContent = 'Ya';
                confirmBtn.onclick = () => {
                    closePopup();
                    onConfirm();
                };
                popupButtons.appendChild(confirmBtn);
            } else {
                // OK button only
                const okBtn = document.createElement('button');
                okBtn.className = 'popup-btn popup-btn-primary';
                okBtn.textContent = 'OK';
                okBtn.onclick = closePopup;
                popupButtons.appendChild(okBtn);
            }

            // Show popup
            popup.classList.add('active');

            // Close popup when clicking outside
            popup.addEventListener('click', function(e) {
                if (e.target === popup) {
                    closePopup();
                }
            });
        }

        function closePopup() {
            const popup = document.getElementById('customPopup');
            popup.classList.remove('active');
        }

        function openPrintReport(url) {
            const printWindow = window.open(url, '_blank');
            if (!printWindow) {
                showPopup('Popup diblokir browser. Mohon izinkan popup untuk mencetak laporan.', 'warning', 'Popup Diblokir');
            }
        }

        function printBooksReport() {
            openPrintReport(window.__VIEW_CONFIG['e2']);
        }

        function printBorrowReport() {
            openPrintReport(window.__VIEW_CONFIG['e3']);
        }

        function printUsersReport() {
            openPrintReport(window.__VIEW_CONFIG['e4']);
        }

        function openBorrowModal(action) {
            showPopup('Membuka form tambah peminjaman...', 'info', 'Form Peminjaman');
        }
