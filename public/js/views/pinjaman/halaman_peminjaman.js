// Pilih durasi peminjaman
        document.querySelectorAll('.duration-option').forEach(option => {
            option.addEventListener('click', function() {
                document.querySelectorAll('.duration-option').forEach(opt => {
                    opt.classList.remove('selected');
                });

                this.classList.add('selected');
                const days = this.getAttribute('data-days');
                document.getElementById('duration').value = days;
            });
        });

        // Handle form submission
        document.getElementById('borrowFormElement').addEventListener('submit', function(e) {
            e.preventDefault();

            const name = document.getElementById('name').value.trim();
            const email = document.getElementById('email').value.trim();
            const phone = document.getElementById('phone').value.trim();
            const terms = document.getElementById('terms').checked;
            const form = this;
            const bookId = document.querySelector('input[name="book_id"]').value;
            const duration = document.getElementById('duration').value;

            console.log('Form submission started');
            console.log('Phone value:', phone);
            console.log('Phone digits only:', phone.replace(/\D/g, ''));

            // Basic validation
            if (!name || !email || !phone) {
                alert('Harap lengkapi semua field yang wajib diisi!');
                return;
            }

            if (!terms) {
                alert('Anda harus menyetujui syarat dan ketentuan!');
                return;
            }

            // Email validation
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                alert('Harap masukkan alamat email yang valid!');
                return;
            }

            // Phone validation - simplified
            const phoneDigits = phone.replace(/\D/g, '');
            if (phoneDigits.length < 10 || phoneDigits.length > 13) {
                alert('Nomor telepon harus 10-13 digit! Anda memasukkan: ' + phoneDigits.length + ' digit');
                return;
            }

            const submitBtn = document.getElementById('submitBtn');
            submitBtn.textContent = "Memproses...";
            submitBtn.disabled = true;

            // Submit menggunakan FormData dan fetch untuk better error handling
            const formData = new FormData(form);

            console.log('Sending form data to:', window.__VIEW_CONFIG['e1']);
            console.log('FormData contents:');
            for (let [key, value] of formData.entries()) {
                console.log(`  ${key}: ${value}`);
            }

            fetch(window.__VIEW_CONFIG['e1'], {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content ||
                                   document.querySelector('input[name="_token"]')?.value,
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(async response => {
                console.log('Response status:', response.status);
                console.log('Response headers:', {
                    'content-type': response.headers.get('content-type'),
                    'location': response.headers.get('location')
                });

                const contentType = response.headers.get('content-type') || '';
                let data = null;
                if (contentType.includes('application/json')) {
                    data = await response.json();
                }

                if (response.ok) {
                    // Success - redirect to detail page
                    console.log('Form submitted successfully, redirecting...');
                    const redirectUrl = data?.redirect_url || `/pinjaman/detail/${bookId}`;
                    setTimeout(() => {
                        window.location.href = redirectUrl;
                    }, 500);
                } else if (response.status === 422 || response.status === 404) {
                    // Validation error
                    console.error('Validation errors:', data);
                    const errors = data?.errors || {};
                    const errorMessages = Object.values(errors).flat().join('\n');
                    const message = data?.message || errorMessages || 'Terjadi kesalahan validasi.';
                    alert(message);
                    submitBtn.textContent = "Ajukan Peminjaman";
                    submitBtn.disabled = false;
                } else {
                    // Handle non-422 errors - try to get response text
                    return response.text().then(text => {
                        console.error('Error response:', text);
                        console.error('Status:', response.status, response.statusText);
                        alert(`Error ${response.status}: ${response.statusText}\n\nResponse: ${text.substring(0, 500)}`);
                        submitBtn.textContent = "Ajukan Peminjaman";
                        submitBtn.disabled = false;
                    });
                }
            })
            .catch(error => {
                console.error('Network error:', error);
                console.error('Error stack:', error.stack);
                alert('Network error: ' + error.message);
                submitBtn.textContent = "Ajukan Peminjaman";
                submitBtn.disabled = false;
            });
        });

        // Tombol cetak konfirmasi
        document.getElementById('printBtn').addEventListener('click', function() {
            window.print();
        });

        // ======================= REVIEW FUNCTIONALITY =======================

        // Star Rating Interaction
        const starRating = document.getElementById('starRating');
        const starLabels = starRating.querySelectorAll('.star-label');
        const ratingCount = document.getElementById('ratingCount');

        function getLabelValue(label) {
            const targetId = label.getAttribute('for');
            const targetInput = targetId ? document.getElementById(targetId) : null;
            return targetInput ? parseInt(targetInput.value, 10) : 0;
        }

        starLabels.forEach((label) => {
            // Hover effect
            label.addEventListener('mouseenter', function() {
                const hoveredValue = getLabelValue(label);
                starLabels.forEach((l) => {
                    if (getLabelValue(l) <= hoveredValue) {
                        l.classList.add('hover');
                    } else {
                        l.classList.remove('hover');
                    }
                });
            });

            // Click to select
            label.addEventListener('click', function() {
                const targetId = label.getAttribute('for');
                const targetInput = targetId ? document.getElementById(targetId) : null;
                if (targetInput) {
                    targetInput.checked = true;
                }
                updateRatingDisplay();
            });
        });

        // Remove hover effect when leaving
        starRating.addEventListener('mouseleave', function() {
            starLabels.forEach(l => l.classList.remove('hover'));
        });

        // Update rating display
        function updateRatingDisplay() {
            const selectedRating = document.querySelector('input[name="rating"]:checked');
            if (selectedRating) {
                const rating = parseInt(selectedRating.value, 10);
                ratingCount.textContent = rating + ' / 5';
                // Update star display
                starLabels.forEach((label) => {
                    if (getLabelValue(label) <= rating) {
                        label.classList.add('checked');
                    } else {
                        label.classList.remove('checked');
                    }
                });
            }
        }

        // Character counter for textarea
        const reviewComment = document.getElementById('reviewComment');
        const charCount = document.getElementById('charCount');

        reviewComment.addEventListener('input', function() {
            charCount.textContent = this.value.length;
        });

        // Handle review form submission
        document.getElementById('reviewFormElement').addEventListener('submit', function(e) {
            e.preventDefault();

            const selectedRating = document.querySelector('input[name="rating"]:checked');
            const comment = reviewComment.value.trim();
            const bookId = document.querySelector('input[name="book_id"]').value;

            // Validate rating is selected
            if (!selectedRating) {
                alert('Harap pilih rating terlebih dahulu!');
                return;
            }

            const rating = selectedRating.value;
            const submitBtn = document.getElementById('submitReviewBtn');
            const successMessage = document.getElementById('reviewSuccessMessage');

            // Disable button and show loading state
            submitBtn.disabled = true;
            submitBtn.textContent = 'Mengirim...';

            // Prepare form data
            const formData = new FormData();
            formData.append('book_id', bookId);
            formData.append('rating', rating);
            formData.append('comment', comment);
            formData.append('_token', document.querySelector('input[name="_token"]').value);

            // Send review to server (you'll need to create an endpoint for this)
            fetch(window.__VIEW_CONFIG['e2'], {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content ||
                                   document.querySelector('input[name="_token"]')?.value,
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(response => {
                if (response.ok) {
                    return response.json();
                } else if (response.status === 422) {
                    return response.json().then(data => {
                        throw new Error(Object.values(data.errors || {}).flat().join('\n'));
                    });
                } else {
                    throw new Error('Gagal mengirim ulasan. Silakan coba lagi.');
                }
            })
            .then(data => {
                // Show success message
                successMessage.style.display = 'block';

                // Reset form
                document.getElementById('reviewFormElement').reset();
                reviewComment.value = '';
                charCount.textContent = '0';
                starLabels.forEach(label => label.classList.remove('checked'));
                ratingCount.textContent = '0 / 5';

                // Reset button
                submitBtn.disabled = false;
                submitBtn.textContent = 'Kirim Ulasan';

                // Hide success message after 3 seconds
                setTimeout(() => {
                    successMessage.style.display = 'none';
                }, 3000);

                // Reload all reviews from database
                loadReviewsFromDatabase();
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error: ' + error.message);
                submitBtn.disabled = false;
                submitBtn.textContent = 'Kirim Ulasan';
            });
        });

        // Function to add review to the list
        function addReviewToList(review) {
            const reviewsList = document.getElementById('reviewsList');

            // Remove "no reviews" message if exists
            const noReviews = reviewsList.querySelector('.no-reviews');
            if (noReviews) {
                noReviews.remove();
            }

            // Create review item
            const reviewItem = document.createElement('div');
            reviewItem.classList.add('review-item');

            const starsHtml = Array.from({length: 5}, (_, i) =>
                `<span class="review-star ${i < review.rating ? '' : 'empty'}">★</span>`
            ).join('');

            reviewItem.innerHTML = `
                <div class="review-header">
                    <div>
                        <div class="reviewer-name">${review.user_name || 'Anonim'}</div>
                        <div class="review-date">${review.created_at || new Date().toLocaleDateString('id-ID')}</div>
                    </div>
                </div>
                <div class="review-rating-stars">
                    ${starsHtml}
                </div>
                ${review.comment ? `<p class="review-comment">${review.comment}</p>` : ''}
            `;

            reviewsList.insertBefore(reviewItem, reviewsList.firstChild);
        }

        // Load existing reviews from database on page load
        document.addEventListener('DOMContentLoaded', function() {
            loadReviewsFromDatabase();
        });

        // Load reviews from database
        function loadReviewsFromDatabase() {
            const bookId = document.querySelector('input[name="book_id"]').value;

            fetch(`/reviews/book/${bookId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.reviews.length > 0) {
                        const reviewsList = document.getElementById('reviewsList');
                        reviewsList.innerHTML = ''; // Clear placeholder

                        data.reviews.forEach(review => {
                            addReviewToList(review);
                        });
                    }
                    // If no reviews (data.reviews.length === 0), keep showing "Belum ada ulasan"
                })
                .catch(error => {
                    console.log('Tidak ada ulasan atau terjadi error:', error);
                });
        }
