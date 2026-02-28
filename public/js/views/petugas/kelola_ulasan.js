const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const modal = document.getElementById('reviewModal');

    async function openReviewModal(bookId, bookTitle) {
        modal.classList.add('active');
        document.getElementById('modalBookTitle').textContent = bookTitle;
        document.getElementById('modalBookRating').innerHTML = '<i class="fas fa-star"></i> Loading...';
        
        const reviewsContainer = document.getElementById('reviewsContainer');
        reviewsContainer.innerHTML = '<div class="loading"><div class="spinner"></div> Memuat ulasan...</div>';

        try {
            const response = await fetch(`/petugas/ulasan-buku/${bookId}`);
            if (!response.ok) throw new Error('Gagal memuat ulasan');
            
            const data = await response.json();
            const reviews = data.reviews || [];
            const avgRating = reviews.length > 0 
                ? (reviews.reduce((sum, r) => sum + r.rating, 0) / reviews.length).toFixed(1)
                : '0.0';

            document.getElementById('modalBookRating').innerHTML = `
                <i class="fas fa-star" style="color: var(--warning);"></i> 
                ${avgRating}/5 (${reviews.length} ulasan)
            `;

            if (reviews.length === 0) {
                reviewsContainer.innerHTML = `
                    <div class="empty-reviews">
                        <i class="fas fa-comments"></i>
                        <h4>Belum Ada Ulasan</h4>
                        <p>Buku ini belum mendapatkan ulasan dari pengguna.</p>
                    </div>
                `;
                return;
            }

            reviewsContainer.innerHTML = reviews.map(review => `
                <div class="review-item">
                    <div class="review-header">
                        <div class="reviewer-info">
                            <span class="reviewer-name">${review.user?.name || 'Pengguna Anonim'}</span>
                            <span class="reviewer-email">${review.user?.email || 'email@example.com'}</span>
                            <div class="review-date">${new Date(review.created_at).toLocaleDateString('id-ID', { year: 'numeric', month: 'long', day: 'numeric' })}</div>
                        </div>
                        <div class="review-rating">
                            <span class="stars">${'â˜…'.repeat(review.rating)}${'â˜†'.repeat(5 - review.rating)}</span>
                            <span class="rating-value">${review.rating}/5</span>
                        </div>
                    </div>
                    <div class="review-text">${review.komentar || 'Tidak ada komentar'}</div>
                </div>
            `).join('');
        } catch (error) {
            reviewsContainer.innerHTML = `
                <div class="empty-reviews">
                    <i class="fas fa-exclamation-circle"></i>
                    <h4>Gagal Memuat Ulasan</h4>
                    <p>${error.message}</p>
                </div>
            `;
        }
    }

    function closeReviewModal() {
        modal.classList.remove('active');
    }

    function printReviewReport() {
        const printWindow = window.open(window.__VIEW_CONFIG['e1'], '_blank');
        if (!printWindow) {
            alert('Popup diblokir browser. Mohon izinkan popup untuk mencetak laporan.');
        }
    }

    // Close modal when pressing Escape
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') closeReviewModal();
    });
