# Dokumentasi Integrasi Peminjaman Buku ke Database

## ✅ Perubahan yang Dilakukan

### 1. Routes Configuration (routes/web.php)
**Status**: ✅ UPDATED

Routes yang ditambahkan:
- `POST /loans` → `LoanController@store` - Menyimpan data peminjaman ke database
- `GET /pinjaman/detail/{book_id}` → `LoanController@showDetail` - Menampilkan halaman detail peminjaman
- Mendukung form submission dari `halaman_peminjaman.blade.php`

### 2. LoanController Updates (app/Http/Controllers/LoanController.php)
**Status**: ✅ UPDATED

#### Method: store()
```php
// Validasi form dan simpan ke database
$loan = Loan::create([
    'user_id' => Auth::id(),
    'book_id' => $validated['book_id'],
    'due_date' => $dueDate,
    'barcode_code' => Loan::generateBarcodeCode(),
    'status' => 'pending',
    'notes' => $validated['notes'] ?? null,
]);
// Redirect ke halaman detail
return redirect()->route('loans.detail', ['book_id' => $book_id]);
```

#### Method: showDetail($book_id)
```php
// Menampilkan halaman detail peminjaman dengan data dari database
$book = Book::find($book_id);
$loan = Loan::where('user_id', Auth::id())
            ->where('book_id', $book_id)
            ->orderBy('created_at', 'desc')
            ->first();
return view('pinjaman.detail_peminjaman', compact('book', 'loan'));
```

### 3. Database Schema Update
**Status**: ✅ MIGRATED

Migration: `2026_02_24_update_peminjamanbuku_table.php`

Kolom yang direname:
- `buku_id` → `book_id`
- `tanggal_pinjam` → `loan_date`
- `tanggal_kembali` → `due_date`

Kolom yang ditambahkan:
- `barcode_code` (string, unique) - Kode tiket peminjaman
- `return_date` (date, nullable) - Tanggal pengembalian aktual
- `notes` (text, nullable) - Catatan peminjaman
- `approved_by` (foreignId, nullable) - User yang approve
- `returned_by` (foreignId, nullable) - User yang terima pengembalian
- `denda_total` (decimal, default 0) - Total denda jika terlambat
- `alasan_penolakan` (text, nullable) - Alasan jika peminjaman ditolak

Status enum yang didukung:
- `pending` - Menunggu Verifikasi
- `approved` - Sudah Diverifikasi
- `active` - Buku Sudah Diambil
- `returned` - Buku Sudah Dikembalikan
- `rejected` - Peminjaman Ditolak

### 4. View Updates (resources/views/pinjaman/detail_peminjaman.blade.php)
**Status**: ✅ UPDATED

Fitur yang diintegrasikan dengan database:
- ✅ Menampilkan data user dari `auth()->user()`
- ✅ Menampilkan data buku dari `$book` model
- ✅ Menampilkan data peminjaman dari `$loan` model
- ✅ Menampilkan status peminjaman dari database
- ✅ QR Code berisi barcode dari database
- ✅ Cetak dan Download PDF dengan data real dari database

### 5. Form Submission (halaman_peminjaman.blade.php)
**Status**: ✅ UPDATED

Alur peminjaman:
1. User membuka halaman: `/loans/{book}/borrow`
2. User melihat detail buku (dari database)
3. User mengisi form:
   - Nama Lengkap (read-only dari auth user)
   - Email (read-only dari auth user)
   - Nomor Telepon (input)
   - Durasi Peminjaman (7, 14, atau 30 hari)
   - Persetujuan Syarat & Ketentuan
4. User klik "Ajukan Peminjaman"
5. Form divalidasi di client-side
6. POST ke `/loans` dengan method `store()`
7. Data disimpan ke database `peminjamanbuku`
8. Redirect ke `/pinjaman/detail/{book_id}`
9. User melihat halaman detail peminjaman dengan:
   - Kode Tiket (barcode_code dari database)
   - Status Peminjaman (pending)
   - QR Code untuk verifikasi
   - Opsi: Cetak, Download PDF, Bagikan

## 📊 Data Flow

```
halaman_peminjaman.blade.php (Form)
         ↓ (POST /loans)
LoanController@store()
         ↓ (Validasi & Save)
Database (peminjamanbuku table)
         ↓ (Redirect)
detail_peminjaman.blade.php (Display)
         ↓ (Get data from DB)
Tampilkan Tiket Peminjaman
```

## 🔄 Status Peminjaman yang Dimonitor

| Status | Deskripsi | Timeline |
|--------|-----------|----------|
| pending | Menunggu verifikasi admin | Tahap 1 |
| approved | Sudah diverifikasi, siap diambil | Tahap 2 |
| active | Buku sedang dipinjam | Tahap 3 |
| returned | Buku sudah dikembalikan | Tahap 4 |
| rejected | Peminjaman ditolak | Ditolak |

## 🔐 Validasi & Keamanan

✅ Validasi Input:
- `book_id` harus ada di tabel books
- `duration` hanya bisa 7, 14, atau 30 hari
- User harus authenticated (middleware auth)

✅ Authorization:
- Hanya user yang login bisa mengajukan peminjaman
- User hanya bisa melihat peminjaman mereka sendiri

✅ Database Constraints:
- Foreign key ke users dan books
- Cascade delete untuk menjaga integritas data

## 🧪 Testing & Verifikasi

Untuk test alurnya:

1. Login sebagai user peminjam
2. Buka halaman buku: `/loans/{book_id}/borrow`
3. Isi form dan submit
4. Verifikasi data tersimpan di database:
   ```sql
   SELECT * FROM peminjamanbuku WHERE user_id = {user_id};
   ```
5. Halaman detail harus menampilkan:
   - Barcode code dari database
   - Status: pending
   - QR Code untuk scanning

## 📋 Checklist Implementasi

- ✅ Routes dikonfigurasi
- ✅ Controller methods dibuat
- ✅ Database migration dijalankan
- ✅ Views terintegrasi dengan database
- ✅ Form submission bekerja
- ✅ Data disimpan ke database
- ✅ Redirect ke halaman detail
- ✅ Status tracking dari database
- ✅ QR Code dengan barcode dari database
- ✅ Cetak & Download PDF dengan data real

## 🚀 Next Steps (Opsional)

1. Admin Dashboard - Verifikasi peminjaman
2. Notifikasi Email - Kirim ke user saat peminjaman diapprove
3. Auto Denda - Hitung denda jika lewat deadline
4. Return Process - Scanning barcode untuk pengembalian
5. History - Riwayat peminjaman user

---

**Last Updated**: 24 Feb 2026
**Migration Status**: Completed ✅
**Database Status**: Updated ✅
**Routes Status**: Configured ✅
