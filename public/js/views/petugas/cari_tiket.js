// Inisialisasi variabel global
        let currentTicket = null;
        let html5QrCode = null;
        let scannedCode = null;
        let scannerRunning = false;
        let isProcessingScan = false;
        const scanBarcodeUrl = window.__VIEW_CONFIG['e1'];
        const csrfToken = document.querySelector('meta[name=\"csrf-token\"]')?.getAttribute('content') || '';

        function extractTicketCode(rawValue) {
            const raw = String(rawValue || '').trim();
            if (!raw) return null;

            try {
                const parsed = JSON.parse(raw);
                if (typeof parsed === 'string') {
                    const codeFromString = parsed.trim();
                    if (codeFromString) return codeFromString.toUpperCase();
                }

                if (parsed && typeof parsed === 'object') {
                    const code = parsed.ticketCode || parsed.barcode_code || parsed.code;
                    if (code) return String(code).trim().toUpperCase();
                }
            } catch (_) {
                // QR bisa berisi plain text; abaikan parse error.
            }

            // Jika QR berisi URL, coba ambil param kode.
            if (/^https?:\/\//i.test(raw)) {
                try {
                    const url = new URL(raw);
                    const codeFromUrl = url.searchParams.get('ticket')
                        || url.searchParams.get('code')
                        || url.searchParams.get('barcode_code')
                        || url.searchParams.get('ticketCode');
                    if (codeFromUrl) return codeFromUrl.trim().toUpperCase();
                } catch (_) {
                    // Abaikan URL parsing error.
                }
            }

            const matched = raw.match(/([A-Z]{2,4}-[A-Z0-9]{6,16})/i);
            if (matched) {
                return matched[1].toUpperCase();
            }

            return raw.toUpperCase();
        }

        function toDate(value) {
            if (!value) return null;
            const date = new Date(value);
            return Number.isNaN(date.getTime()) ? null : date;
        }

        function mapLoanToTicket(loan) {
            const loanDate = toDate(loan.loan_date) || new Date();
            const dueDate = toDate(loan.due_date);
            const durationDays = dueDate
                ? Math.max(1, Math.round((dueDate - loanDate) / (1000 * 60 * 60 * 24)))
                : 14;

            return {
                ticketCode: loan.barcode_code,
                userName: loan.user_name || '-',
                userEmail: loan.user_email || '-',
                userPhone: loan.user_phone || '-',
                bookTitle: loan.book?.title || '-',
                bookAuthor: loan.book?.author || '-',
                isbn: loan.book?.isbn || '-',
                category: loan.book?.kategori || 'Umum',
                loanDate,
                dueDate,
                durationDays,
                status: loan.status || 'pending',
                createdAt: toDate(loan.created_at) || new Date()
            };
        }

        async function activateTicketByCode(rawCode, method = 'QR Code') {
            const ticketCode = extractTicketCode(rawCode);
            if (!ticketCode) {
                showResultMessage('Kode tiket tidak valid.', false);
                return null;
            }

            try {
                const response = await fetch(scanBarcodeUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({ barcode_code: ticketCode })
                });

                let data = null;
                try {
                    data = await response.json();
                } catch (_) {
                    throw new Error('Gagal membaca respons server. Coba muat ulang halaman.');
                }

                if (!response.ok || !data.success) {
                    let message = data.error || data.message || 'Gagal memproses tiket.';
                    if (data.errors && typeof data.errors === 'object') {
                        const firstError = Object.values(data.errors).flat()[0];
                        if (firstError) {
                            message = firstError;
                        }
                    }
                    throw new Error(message);
                }

                const ticket = mapLoanToTicket(data.loan);
                updateTicketDisplay(ticket, method);
                showResultMessage(data.message || `Tiket ${ticketCode} berhasil diproses.`, true);
                return ticket;
            } catch (error) {
                showResultMessage(error.message || 'Terjadi kesalahan saat memproses tiket.', false);
                return null;
            }
        }

        // ==================== FUNGSI SCANNER ====================

        // Memulai scanner
        async function startScanner() {
            const qrReaderElement = document.getElementById('qr-reader');

            // Bersihkan konten sebelumnya
            qrReaderElement.innerHTML = '';

            // Konfigurasi scanner
            html5QrCode = new Html5Qrcode("qr-reader");

            const config = {
                fps: 12,
                qrbox: { width: 250, height: 250 },
                rememberLastUsedCamera: true,
                showTorchButtonIfSupported: true,
                aspectRatio: 1.0
            };
            if (typeof Html5QrcodeSupportedFormats !== 'undefined') {
                config.formatsToSupport = [Html5QrcodeSupportedFormats.QR_CODE];
            }

            try {
                const cameras = await Html5Qrcode.getCameras();
                let cameraConfig = { facingMode: "environment" };

                if (cameras && cameras.length > 0) {
                    const preferred = cameras.find(cam =>
                        /back|rear|environment|belakang/i.test(cam.label || '')
                    );
                    cameraConfig = preferred ? preferred.id : cameras[0].id;
                }

                // Mulai scanning
                await html5QrCode.start(
                    cameraConfig,
                    config,
                    onScanSuccess,
                    onScanError
                );

                scannerRunning = true;
                document.getElementById('startScannerBtn').disabled = true;
                document.getElementById('stopScannerBtn').disabled = false;
                showResultMessage('Scanner aktif - Arahkan ke QR Code', true);
            } catch (err) {
                showResultMessage('Gagal mengakses kamera: ' + err, false);
            }
        }

        // Hentikan scanner
        function stopScanner(showNotice = true) {
            if (html5QrCode && scannerRunning) {
                html5QrCode.stop().then(() => {
                    scannerRunning = false;
                    document.getElementById('startScannerBtn').disabled = false;
                    document.getElementById('stopScannerBtn').disabled = true;
                    document.getElementById('qr-reader').innerHTML = '';
                    if (showNotice) {
                        showResultMessage('Scanner dihentikan', true);
                    }
                }).catch((err) => {
                    showResultMessage('Gagal menghentikan scanner: ' + err, false);
                });
            }
        }

        // Callback ketika scan berhasil
        async function onScanSuccess(decodedText, decodedResult) {
            if (isProcessingScan) return;

            scannedCode = extractTicketCode(decodedText);

            if (!scannedCode) {
                showResultMessage('QR Code tidak berisi kode tiket yang valid.', false);
                return;
            }

            document.getElementById('scannedCode').textContent = scannedCode;
            document.getElementById('scanResult').classList.add('active');

            if (navigator.vibrate) {
                navigator.vibrate(200);
            }

            isProcessingScan = true;
            document.getElementById('useScannedCodeBtn').disabled = true;

            const ticket = await activateTicketByCode(scannedCode, 'QR Code Otomatis');
            if (ticket) {
                document.getElementById('scanResult').classList.remove('active');
                scannedCode = null;
                stopScanner(false);
            }

            document.getElementById('useScannedCodeBtn').disabled = false;
            isProcessingScan = false;
        }

        // Callback ketika scan error
        function onScanError(errorMessage) {
            // Abaikan error (biasanya karena tidak ada QR code)
            // console.log(errorMessage);
        }

        // Gunakan kode yang sudah discan
        async function useScannedCode() {
            if (!scannedCode) {
                showResultMessage('Tidak ada kode QR yang dipindai', false);
                return;
            }

            const ticket = await activateTicketByCode(scannedCode, 'QR Code');
            if (!ticket) return;

            // Sembunyikan hasil scan
            document.getElementById('scanResult').classList.remove('active');

            // Reset scanned code
            scannedCode = null;

            // Hentikan scanner
            stopScanner(false);
        }

        // ==================== FUNGSI TAB ====================

        // Switch tab
        function switchTab(tab) {
            const scanTab = document.getElementById('scanTab');
            const manualTab = document.getElementById('manualTab');
            const scanTabBtn = document.getElementById('scanTabBtn');
            const manualTabBtn = document.getElementById('manualTabBtn');

            if (tab === 'scan') {
                scanTab.classList.add('active');
                manualTab.classList.remove('active');
                scanTabBtn.classList.add('active');
                manualTabBtn.classList.remove('active');
            } else {
                manualTab.classList.add('active');
                scanTab.classList.remove('active');
                manualTabBtn.classList.add('active');
                scanTabBtn.classList.remove('active');

                // Hentikan scanner jika pindah ke tab manual
                if (scannerRunning) {
                    stopScanner();
                }
            }
        }

        // ==================== FUNGSI VERIFIKASI MANUAL ====================

        async function verifyManualTicket() {
            const ticketCode = document.getElementById('ticketInput').value.trim().toUpperCase();

            if (!ticketCode) {
                showResultMessage('Harap masukkan kode tiket!', false);
                return;
            }

            // Validasi format kode tiket
            const ticketRegex = /^[A-Z]{2,4}-[A-Z0-9]{6,16}$/;
            if (!ticketRegex.test(ticketCode)) {
                showResultMessage('Format kode tiket tidak valid! Contoh: LN-XXXXXXXX', false);

                // Animasi shake
                document.getElementById('ticketInput').style.animation = 'none';
                setTimeout(() => {
                    document.getElementById('ticketInput').style.animation = 'shake 0.5s ease';
                }, 10);
                return;
            }

            const ticket = await activateTicketByCode(ticketCode, 'Manual Input');
            if (!ticket) return;

            document.getElementById('ticketInput').value = '';
        }

        // ==================== FUNGSI TIKET ====================

        // Format tanggal
        function formatDate(date) {
            const options = {
                year: 'numeric',
                month: 'short',
                day: 'numeric'
            };
            return date.toLocaleDateString('id-ID', options);
        }

        function formatDateTime(date) {
            const options = {
                year: 'numeric',
                month: 'short',
                day: 'numeric',
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

        // Dapatkan teks status
        function getStatusText(status) {
            switch(status) {
                case 'pending': return 'Menunggu Verifikasi';
                case 'approved':
                case 'verified': return 'Sudah Diverifikasi';
                case 'active':
                case 'taken': return 'Sedang Dipinjam';
                case 'returned': return 'Sudah Dikembalikan';
                case 'rejected': return 'Ditolak';
                case 'cancelled': return 'Dibatalkan';
                default: return 'Tidak Diketahui';
            }
        }

        function getStatusClass(status) {
            switch (status) {
                case 'approved':
                case 'verified':
                case 'returned':
                    return 'verified';
                case 'active':
                case 'taken':
                    return 'taken';
                case 'rejected':
                case 'cancelled':
                    return 'cancelled';
                default:
                    return 'pending';
            }
        }

        // Update tampilan detail tiket
        function updateTicketDisplay(ticket, method = 'Manual Input') {
            currentTicket = ticket;

            // Tampilkan detail tiket, sembunyikan placeholder
            document.getElementById('ticketDetails').classList.add('active');
            document.getElementById('noTicketMessage').style.display = 'none';

            const loanDate = toDate(ticket.loanDate) || new Date();
            const returnDate = toDate(ticket.dueDate) || calculateReturnDate(loanDate, ticket.durationDays);

            // Update data
            document.getElementById('displayTicketCode').textContent = ticket.ticketCode;
            document.getElementById('ticketUserName').textContent = ticket.userName;
            document.getElementById('ticketLoanDate').textContent = formatDate(loanDate);
            document.getElementById('ticketReturnDate').textContent = formatDate(returnDate);
            document.getElementById('ticketDuration').textContent = `${ticket.durationDays} Hari`;
            document.getElementById('ticketBookTitle').textContent = ticket.bookTitle;
            document.getElementById('ticketBookAuthor').textContent = `oleh ${ticket.bookAuthor}`;
            document.getElementById('ticketISBN').textContent = ticket.isbn;
            document.getElementById('ticketCategory').textContent = ticket.category;
            document.getElementById('lastUpdated').textContent = formatDateTime(new Date());

            // Update status badge
            const statusBadge = document.getElementById('ticketStatusBadge');
            statusBadge.textContent = getStatusText(ticket.status);
            statusBadge.className = `status-badge status-${getStatusClass(ticket.status)}`;

            // Update tombol aksi berdasarkan status
            updateActionButtons(ticket.status);

            // Tambahkan ke history dengan metode verifikasi
            addToHistory(ticket, method);
        }

        // Update tombol aksi berdasarkan status
        function updateActionButtons(status) {
            const confirmBtn = document.getElementById('confirmBtn');
            const takenBtn = document.getElementById('takenBtn');
            const cancelBtn = document.getElementById('cancelBtn');

            confirmBtn.innerHTML = '<i class="fas fa-check-circle"></i> Verifikasi Tiket';
            takenBtn.innerHTML = '<i class="fas fa-box"></i> Buku Sudah Diambil';
            cancelBtn.innerHTML = '<i class="fas fa-times-circle"></i> Batalkan Peminjaman';

            switch(status) {
                case 'pending':
                case 'approved':
                    confirmBtn.disabled = false;
                    takenBtn.disabled = false;
                    cancelBtn.disabled = true;
                    break;

                case 'active':
                case 'taken':
                    confirmBtn.disabled = true;
                    takenBtn.disabled = true;
                    cancelBtn.disabled = true;
                    takenBtn.innerHTML = '<i class="fas fa-box"></i> Sedang Dipinjam';
                    break;

                case 'returned':
                    confirmBtn.disabled = true;
                    takenBtn.disabled = true;
                    cancelBtn.disabled = true;
                    confirmBtn.innerHTML = '<i class="fas fa-check-circle"></i> Sudah Dikembalikan';
                    break;

                case 'rejected':
                case 'cancelled':
                    confirmBtn.disabled = true;
                    takenBtn.disabled = true;
                    cancelBtn.disabled = true;
                    cancelBtn.innerHTML = '<i class="fas fa-times-circle"></i> Dibatalkan';
                    break;
            }
        }

        // Tambahkan ke history
        function addToHistory(ticket, method = 'Manual Input') {
            const historyTable = document.getElementById('historyTableBody');

            // Cek apakah sudah ada di history
            const existingRow = document.querySelector(`[data-ticket="${ticket.ticketCode}"]`);
            if (existingRow) {
                existingRow.remove();
            }

            // Buat row baru
            const row = document.createElement('tr');
            row.setAttribute('data-ticket', ticket.ticketCode);

            const statusClass = `status-${getStatusClass(ticket.status)}`;
            const statusText = getStatusText(ticket.status);

            row.innerHTML = `
                <td>${formatDateTime(new Date())}</td>
                <td><strong>${ticket.ticketCode}</strong></td>
                <td>${ticket.userName}</td>
                <td>${ticket.bookTitle}</td>
                <td><span class="status-cell ${statusClass}">${statusText}</span></td>
                <td>Admin-001</td>
                <td><span class="badge" style="background: #e2e3e5; padding: 3px 8px; border-radius: 12px;">${method}</span></td>
            `;

            // Tambahkan ke paling atas
            historyTable.insertBefore(row, historyTable.firstChild);

            // Batasi jumlah row
            const rows = historyTable.querySelectorAll('tr');
            if (rows.length > 10) {
                rows[rows.length - 1].remove();
            }
        }

        // Tampilkan pesan hasil
        function showResultMessage(message, isSuccess) {
            const resultDiv = document.getElementById('resultMessage');
            const messageText = document.getElementById('messageText');

            resultDiv.className = `result-message ${isSuccess ? 'result-success' : 'result-error'}`;
            messageText.innerHTML = message;
            resultDiv.style.display = 'block';

            // Sembunyikan setelah 5 detik
            setTimeout(() => {
                resultDiv.style.display = 'none';
            }, 5000);
        }

        // ==================== FUNGSI AKSI ADMIN ====================

        // Konfirmasi tiket
        async function confirmTicket() {
            if (!currentTicket) return;

            if (confirm(`Proses tiket ${currentTicket.ticketCode} sekarang?\nStatus peminjaman akan diubah menjadi dipinjam.`)) {
                await activateTicketByCode(currentTicket.ticketCode, 'Verifikasi Manual');
            }
        }

        // Tandai sebagai sudah diambil
        async function markAsTaken() {
            if (!currentTicket) return;

            if (confirm(`Konfirmasi buku sudah diberikan kepada ${currentTicket.userName}?\nStatus peminjaman akan diubah menjadi dipinjam.`)) {
                await activateTicketByCode(currentTicket.ticketCode, 'Konfirmasi Ambil');
            }
        }

        // Batalkan tiket
        function cancelTicket() {
            if (!currentTicket) return;

            showResultMessage('Fitur pembatalan dari halaman ini belum diaktifkan.', false);
        }

        // Refresh history
        function refreshHistory() {
            const historyTable = document.getElementById('historyTableBody');
            historyTable.innerHTML = '';

            if (currentTicket) {
                addToHistory(currentTicket, 'Data Terakhir');
                showResultMessage('Riwayat diperbarui!', true);
                return;
            }

            showResultMessage('Belum ada data tiket untuk ditampilkan.', false);
        }

        // ==================== EVENT LISTENERS ====================

        // Event listener untuk Enter key pada input tiket
        document.getElementById('ticketInput').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                verifyManualTicket();
            }
        });

        // Cleanup saat halaman ditutup
        window.addEventListener('beforeunload', function() {
            if (scannerRunning) {
                stopScanner();
            }
        });

        // Inisialisasi saat halaman dimuat
        window.addEventListener('DOMContentLoaded', function() {
            // Cek jika ada parameter ticket di URL
            const urlParams = new URLSearchParams(window.location.search);
            const ticketParam = urlParams.get('ticket');

            if (ticketParam) {
                document.getElementById('ticketInput').value = ticketParam;
                verifyManualTicket();
            }
        });
