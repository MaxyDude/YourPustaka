// Pilih durasi peminjaman
        document.querySelectorAll('.duration-option').forEach(option => {
            option.addEventListener('click', function() {
                // Hapus kelas selected dari semua opsi
                document.querySelectorAll('.duration-option').forEach(opt => {
                    opt.classList.remove('selected');
                });

                // Tambahkan kelas selected ke opsi yang diklik
                this.classList.add('selected');

                // Set nilai input hidden
                const days = this.getAttribute('data-days');
                document.getElementById('duration').value = days;
            });
        });

        // Validasi dan pengiriman formulir
        document.getElementById('borrowFormElement').addEventListener('submit', function(e) {
            e.preventDefault();

            const phone = document.getElementById('phone').value.trim();
            const terms = document.getElementById('terms').checked;

            // Validasi
            if (!phone) {
                alert('Harap lengkapi semua field yang wajib diisi!');
                return;
            }

            if (!terms) {
                alert('Anda harus menyetujui syarat dan ketentuan!');
                return;
            }

            // Validasi nomor telepon sederhana
            const phoneRegex = /^[0-9]{10,13}$/;
            if (!phoneRegex.test(phone.replace(/\D/g, ''))) {
                alert('Harap masukkan nomor telepon yang valid (10-13 digit)!');
                return;
            }

            // Tampilkan pesan loading
            document.getElementById('submitBtn').textContent = "Memproses...";
            document.getElementById('submitBtn').disabled = true;

            // Simulasi proses pengiriman data
            setTimeout(() => {
                // Hitung tanggal pengembalian
                const duration = parseInt(document.getElementById('duration').value);
                const today = new Date();
                const returnDate = new Date(today);
                returnDate.setDate(today.getDate() + duration);

                // Format tanggal
                const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
                const formattedReturnDate = returnDate.toLocaleDateString('id-ID', options);

                // Set nilai untuk konfirmasi
                document.getElementById('loanDuration').textContent = duration;
                document.getElementById('returnDate').textContent = formattedReturnDate;

                // Tampilkan pesan konfirmasi, sembunyikan formulir
                document.getElementById('borrowForm').style.display = 'none';
                document.getElementById('confirmationMessage').style.display = 'block';

                // Scroll ke atas
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }, 1500);
        });

        // Tombol cetak konfirmasi
        document.getElementById('printBtn').addEventListener('click', function() {
            window.print();
        });
