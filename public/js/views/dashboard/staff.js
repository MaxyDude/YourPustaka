document.getElementById('scanBtn').addEventListener('click', function() {
        const barcode = document.getElementById('barcodeInput').value;
        
        if (!barcode) {
            alert('Silakan masukkan barcode');
            return;
        }

        fetch(window.__VIEW_CONFIG['e1'], {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
            },
            body: JSON.stringify({ barcode_code: barcode })
        })
        .then(response => response.json())
        .then(data => {
            const resultDiv = document.getElementById('scanResult');
            if (data.success) {
                resultDiv.innerHTML = `
                    <div class="alert alert-success">
                        <h5><i class="fas fa-check-circle"></i> Peminjaman Berhasil!</h5>
                        <p><strong>Peminjam:</strong> ${data.loan.user.name}</p>
                        <p><strong>Buku:</strong> ${data.loan.book.title}</p>
                        <p><strong>Tanggal Peminjaman:</strong> ${new Date(data.loan.loan_date).toLocaleDateString('id-ID')}</p>
                    </div>
                `;
                document.getElementById('barcodeInput').value = '';
                document.getElementById('barcodeInput').focus();
            } else {
                resultDiv.innerHTML = `
                    <div class="alert alert-danger">
                        <i class="fas fa-times-circle"></i> ${data.error}
                    </div>
                `;
            }
        })
        .catch(error => {
            document.getElementById('scanResult').innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-times-circle"></i> Error: ${error.message}
                </div>
            `;
        });
    });
