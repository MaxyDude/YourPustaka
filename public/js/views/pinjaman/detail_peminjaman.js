// Data pemesanan dari database
        const bookingData = {
            ticketCode: window.__VIEW_CONFIG['e1'],
            userName: window.__VIEW_CONFIG['e2'],
            userEmail: window.__VIEW_CONFIG['e3'],
            userPhone: window.__VIEW_CONFIG['e4'],
            bookTitle: window.__VIEW_CONFIG['e5'],
            bookAuthor: window.__VIEW_CONFIG['e6'],
            bookIsbn: window.__VIEW_CONFIG['e7'],
            bookCategory: window.__VIEW_CONFIG['e8'],
            bookYear: window.__VIEW_CONFIG['e9'],
            bookPages: window.__VIEW_CONFIG['e10'],
            bookRating: "4.5",
            loanDate: new Date(window.__VIEW_CONFIG['e11']),
            durationDays: window.__VIEW_CONFIG['e13'],
            status: window.__VIEW_CONFIG['e12']
        };

        // Generate kode tiket random - TIDAK DIGUNAKAN (gunakan dari database)
        // function generateTicketCode() {
        //     const prefix = "YP-";
        //     const chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        //     let code = "";
        //
        //     for (let i = 0; i < 8; i++) {
        //         code += chars.charAt(Math.floor(Math.random() * chars.length));
        //     }
        //
        //     return prefix + code;
        // }

        // Format tanggal singkat
        function formatDateShort(date) {
            const options = {
                day: 'numeric',
                month: 'short',
                year: 'numeric'
            };
            return date.toLocaleDateString('id-ID', options);
        }

        // Format tanggal lengkap
        function formatDateLong(date) {
            const options = {
                day: 'numeric',
                month: 'long',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            };
            return date.toLocaleDateString('id-ID', options);
        }

        // Hitung tanggal pengembalian
        function calculateReturnDate(loanDate, days) {
            const returnDate = new Date(loanDate);
            returnDate.setDate(returnDate.getDate() + days);
            return returnDate;
        }

        // Update status display
        function updateStatusDisplay() {
            const statusBadge = document.getElementById('statusBadge');
            const status = bookingData.status;

            switch(status) {
                case 'approved':
                    statusBadge.className = 'status-badge status-verified';
                    statusBadge.innerHTML = '<i class="fas fa-check-circle"></i> <span id="statusText">Sudah Diverifikasi</span>';

                    // Update timeline
                    document.querySelectorAll('.timeline-item')[1].classList.remove('active');
                    document.querySelectorAll('.timeline-item')[1].classList.add('completed');
                    document.querySelectorAll('.timeline-item')[2].classList.add('active');
                    break;

                case 'active':
                    statusBadge.className = 'status-badge status-taken';
                    statusBadge.innerHTML = '<i class="fas fa-box"></i> <span id="statusText">Buku Sudah Diambil</span>';

                    // Update timeline
                    document.querySelectorAll('.timeline-item')[1].classList.remove('active');
                    document.querySelectorAll('.timeline-item')[1].classList.add('completed');
                    document.querySelectorAll('.timeline-item')[2].classList.remove('active');
                    document.querySelectorAll('.timeline-item')[2].classList.add('completed');
                    document.querySelectorAll('.timeline-item')[3].classList.add('active');
                    break;

                case 'returned':
                    statusBadge.className = 'status-badge status-verified';
                    statusBadge.innerHTML = '<i class="fas fa-check-circle"></i> <span id="statusText">Buku Sudah Dikembalikan</span>';

                    // Update timeline - semua selesai
                    document.querySelectorAll('.timeline-item').forEach(item => {
                        item.classList.remove('active');
                        item.classList.add('completed');
                    });
                    break;

                default: // pending
                    statusBadge.className = 'status-badge status-pending';
                    statusBadge.innerHTML = '<i class="fas fa-clock"></i> <span id="statusText">Menunggu Verifikasi</span>';
            }
        }

        // Update progress bar
        function updateProgressBar() {
            const today = new Date();
            const loanStart = new Date(bookingData.loanDate);
            const daysPassed = Math.floor((today - loanStart) / (1000 * 60 * 60 * 24));
            const progressPercentage = Math.min((daysPassed / bookingData.durationDays) * 100, 100);

            document.getElementById('progressFill').style.width = `${progressPercentage}%`;
            document.getElementById('progressLabel').textContent =
                `${Math.min(daysPassed, bookingData.durationDays)}/${bookingData.durationDays} hari`;
        }

        // Initialize data
        function initializeData() {
            const returnDate = calculateReturnDate(bookingData.loanDate, bookingData.durationDays);

            // Update elements with data
            document.getElementById('ticketCode').textContent = bookingData.ticketCode;
            document.getElementById('userName').textContent = bookingData.userName;
            document.getElementById('userEmail').textContent = bookingData.userEmail;
            document.getElementById('userPhone').textContent = bookingData.userPhone;
            document.getElementById('loanDate').textContent = formatDateShort(bookingData.loanDate);
            document.getElementById('returnDate').textContent = formatDateShort(returnDate);
            document.getElementById('duration').textContent = `${bookingData.durationDays} Hari`;

            // Update print modal
            document.getElementById('modalTicketCode').textContent = bookingData.ticketCode;
            document.getElementById('printDate').textContent = formatDateLong(new Date());

            // Update status
            updateStatusDisplay();

            // Update progress bar
            updateProgressBar();

            // Save booking data
            localStorage.setItem('currentBooking', JSON.stringify(bookingData));
        }

        // Fungsi untuk menampilkan QR Code modal (DIPERKECIL)
        function showQRCode() {
            if (!bookingData.ticketCode) {
                alert('Kode tiket belum tersedia. Silakan ajukan peminjaman terlebih dahulu.');
                return;
            }

            // Hapus modal yang sudah ada jika ada
            const existingModal = document.querySelector('.qr-modal-overlay');
            if (existingModal) {
                existingModal.remove();
            }

            // Buat container modal
            const qrModal = document.createElement('div');
            qrModal.className = 'qr-modal-overlay';
            qrModal.innerHTML = `
                <div class="qr-modal-content">
                    <button class="qr-modal-close" onclick="this.closest('.qr-modal-overlay').remove()">
                        <i class="fas fa-times"></i>
                    </button>

                    <div class="qr-modal-icon">
                        <i class="fas fa-qrcode"></i>
                    </div>
                    <div class="qr-modal-title">Scan QR Code</div>
                    <div class="qr-modal-subtitle">Tunjukkan ke admin perpustakaan</div>

                    <div id="qrcode"></div>

                    <div class="qr-instruction">
                        <p><i class="fas fa-ticket-alt" style="color: #4b6cb7;"></i></p>
                        <p><strong>Kode Tiket:</strong></p>
                        <div class="qr-ticket-code">${bookingData.ticketCode}</div>
                        <p style="font-size: 0.75rem; margin-top: 8px;">Atau sebutkan kode tiket ke admin</p>
                    </div>

                    <div class="qr-actions">
                        <button class="qr-btn qr-btn-primary" onclick="downloadQRCode()">
                            <i class="fas fa-download"></i> Download QR
                        </button>
                        <button class="qr-btn qr-btn-secondary" onclick="printQRCode()">
                            <i class="fas fa-print"></i> Cetak
                        </button>
                    </div>
                </div>
            `;

            document.body.appendChild(qrModal);

            // Generate QR Code
            const qrcodeContainer = document.getElementById('qrcode');
            if (qrcodeContainer) {
                // Clear container
                qrcodeContainer.innerHTML = '';

                // Buat QR code baru
                new QRCode(qrcodeContainer, {
                    // Gunakan payload singkat agar QR lebih mudah dibaca kamera.
                    text: bookingData.ticketCode,
                    width: 200,
                    height: 200,
                    colorDark: "#1e293b",
                    colorLight: "#ffffff",
                    correctLevel: QRCode.CorrectLevel.H
                });
            }
        }

        // Fungsi download QR Code
        window.downloadQRCode = function() {
            const qrElement = document.querySelector('#qrcode img');
            if (qrElement) {
                const link = document.createElement('a');
                link.download = `QR-${bookingData.ticketCode}.png`;
                link.href = qrElement.src;
                link.click();
            }
        };

        // Fungsi cetak QR Code
        window.printQRCode = function() {
            const printContent = document.createElement('div');
            printContent.style.textAlign = 'center';
            printContent.style.padding = '20px';
            printContent.innerHTML = `
                <h2 style="color: #1e293b; margin-bottom: 20px;">QR Code Verifikasi</h2>
                <div style="margin: 20px auto; width: 200px;">
                    <img src="${document.querySelector('#qrcode img').src}" style="width: 100%; height: auto;">
                </div>
                <p style="color: #64748b; margin-top: 20px;">Kode Tiket: ${bookingData.ticketCode}</p>
                <p style="color: #64748b;">YourPustaka - Perpustakaan Digital</p>
            `;

            const printWindow = window.open('', '_blank');
            printWindow.document.write(printContent.innerHTML);
            printWindow.document.close();
            printWindow.focus();
            printWindow.print();
            printWindow.close();
        };

        // Fungsi download PDF seluruh tiket
        async function downloadTicketPDF() {
            const { jsPDF } = window.jspdf;

            // Tampilkan loading
            const loading = document.createElement('div');
            loading.style.cssText = `
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0,0,0,0.5);
                display: flex;
                justify-content: center;
                align-items: center;
                z-index: 3000;
                color: white;
                font-size: 1.2rem;
                backdrop-filter: blur(3px);
            `;
            loading.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyiapkan PDF...';
            document.body.appendChild(loading);

            try {
                const element = document.getElementById('ticketToPrint');
                const canvas = await html2canvas(element, {
                    scale: 2,
                    backgroundColor: '#ffffff',
                    logging: false
                });

                const imgData = canvas.toDataURL('image/png');
                const pdf = new jsPDF({
                    orientation: 'portrait',
                    unit: 'mm',
                    format: 'a4'
                });

                const pdfWidth = pdf.internal.pageSize.getWidth();
                const pdfHeight = pdf.internal.pageSize.getHeight();
                const imgWidth = canvas.width;
                const imgHeight = canvas.height;
                const ratio = Math.min(pdfWidth / imgWidth, pdfHeight / imgHeight);
                const width = imgWidth * ratio;
                const height = imgHeight * ratio;

                pdf.addImage(imgData, 'PNG', (pdfWidth - width) / 2, 10, width, height);
                pdf.save(`Tiket-${bookingData.ticketCode}.pdf`);
            } catch (error) {
                alert('Gagal membuat PDF: ' + error.message);
            } finally {
                document.body.removeChild(loading);
            }
        }

        // Event Listeners
        document.addEventListener('DOMContentLoaded', function() {
            initializeData();

            // QR Code button
            document.getElementById('qrActionBtn').addEventListener('click', showQRCode);

            // Print button
            document.getElementById('printBtn').addEventListener('click', function() {
                if (!bookingData.ticketCode) {
                    alert('Kode tiket belum tersedia.');
                    return;
                }

                document.getElementById('printModal').style.display = 'flex';
                document.body.style.overflow = 'hidden';

                // Generate QR untuk modal print
                setTimeout(() => {
                    const printQrContainer = document.getElementById('printQrCode');
                    printQrContainer.innerHTML = '';
                    new QRCode(printQrContainer, {
                        text: bookingData.ticketCode,
                        width: 180,
                        height: 180
                    });
                }, 100);
            });

            document.getElementById('printActionBtn').addEventListener('click', function() {
                if (!bookingData.ticketCode) {
                    alert('Kode tiket belum tersedia.');
                    return;
                }

                document.getElementById('printModal').style.display = 'flex';
                document.body.style.overflow = 'hidden';

                // Generate QR untuk modal print
                setTimeout(() => {
                    const printQrContainer = document.getElementById('printQrCode');
                    printQrContainer.innerHTML = '';
                    new QRCode(printQrContainer, {
                        text: bookingData.ticketCode,
                        width: 180,
                        height: 180
                    });
                }, 100);
            });

            // Print button in modal
            document.getElementById('printModalBtn').addEventListener('click', function() {
                window.print();
            });

            // PDF button in modal
            document.getElementById('downloadPdfModalBtn').addEventListener('click', downloadTicketPDF);

            // Close modal
            document.getElementById('closeModal').addEventListener('click', function() {
                document.getElementById('printModal').style.display = 'none';
                document.body.style.overflow = 'auto';
            });

            document.getElementById('printModal').addEventListener('click', function(e) {
                if (e.target === this) {
                    this.style.display = 'none';
                    document.body.style.overflow = 'auto';
                }
            });

            // Share functionality
            document.getElementById('shareActionBtn').addEventListener('click', function() {
                const shareText = `Tiket Peminjaman Buku YourPustaka\n\n` +
                                `Kode Tiket: ${bookingData.ticketCode}\n` +
                                `Nama: ${bookingData.userName}\n` +
                                `Buku: ${bookingData.bookTitle}\n` +
                                `Status: ${bookingData.status === 'pending' ? 'Menunggu Verifikasi' :
                                          bookingData.status === 'verified' ? 'Siap Diambil' : 'Sudah Diambil'}\n\n` +
                                `Tunjukkan tiket ini di perpustakaan untuk mengambil buku.`;

                if (navigator.share) {
                    navigator.share({
                        title: 'Tiket Peminjaman Buku',
                        text: shareText,
                        url: window.location.href
                    }).catch(() => {
                        navigator.clipboard.writeText(shareText).then(() => {
                            alert('Tiket berhasil disalin ke clipboard!');
                        });
                    });
                } else {
                    navigator.clipboard.writeText(shareText).then(() => {
                        alert('Tiket berhasil disalin ke clipboard!');
                    });
                }
            });

            // Status sudah diambil dari database, tidak perlu auto verification
            // Check for status updates setiap 5 detik (polling dari database bisa ditambahkan kemudian)
            setInterval(updateStatusDisplay, 5000);
        });

        function showNotification(message) {
            const notification = document.createElement('div');
            notification.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                background: #4caf50;
                color: white;
                padding: 12px 20px;
                border-radius: 8px;
                box-shadow: 0 4px 12px rgba(0,0,0,0.15);
                z-index: 1000;
                display: flex;
                align-items: center;
                gap: 10px;
                animation: slideIn 0.3s ease;
            `;
            notification.innerHTML = `
                <i class="fas fa-check-circle"></i>
                <span>${message}</span>
            `;

            document.body.appendChild(notification);

            setTimeout(() => {
                notification.style.animation = 'slideOut 0.3s ease';
                setTimeout(() => notification.remove(), 300);
            }, 4000);
        }

        // Add animation styles
        const style = document.createElement('style');
        style.textContent = `
            @keyframes slideIn {
                from {
                    transform: translateX(100%);
                    opacity: 0;
                }
                to {
                    transform: translateX(0);
                    opacity: 1;
                }
            }

            @keyframes slideOut {
                from {
                    transform: translateX(0);
                    opacity: 1;
                }
                to {
                    transform: translateX(100%);
                    opacity: 0;
                }
            }
        `;
        document.head.appendChild(style);
