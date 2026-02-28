# Konfigurasi Autentikasi YourPustaka

## Status Sistem Autentikasi ✅

Sistem login dan register **sudah terhubung dengan database** dengan konfigurasi lengkap.

---

## 📋 Alur Autentikasi

### 1. **REGISTER (Pendaftaran)**
- **Halaman**: `http://localhost/register`
- **Form**: 
  - Nama Lengkap (required)
  - Email (required, unique)
  - Telepon (required)
  - Alamat (required)
  - Nomor Identitas (required, unique)
  - Password (required, min 8 karakter)
  - Konfirmasi Password (required)

- **Proses**:
  - Data disimpan ke tabel `users`
  - Password di-hash otomatis dengan bcrypt
  - Role default: `peminjam`
  - User otomatis login setelah registrasi
  - **Redirect**: Ke halaman dashboard

- **File Controller**: [app/Http/Controllers/Auth/RegisteredUserController.php](app/Http/Controllers/Auth/RegisteredUserController.php)

---

### 2. **LOGIN (Masuk)**
- **Halaman**: `http://localhost/login`
- **Form**:
  - Email (required)
  - Password (required)
  - Checkbox "Ingat Saya" (optional)

- **Proses**:
  - Email & password diverifikasi dengan database
  - Session dibuat dan diregenerasi (keamanan CSRF)
  - **Redirect**: Ke halaman dashboard (atau halaman sebelumnya jika ada)

- **File Controller**: [app/Http/Controllers/Auth/AuthenticatedSessionController.php](app/Http/Controllers/Auth/AuthenticatedSessionController.php)

---

### 3. **DASHBOARD (Setelah Login)**
- **Route**: `http://localhost/dashboard`
- **Middleware Protection**: `auth`, `verified`
- **Controller**: [app/Http/Controllers/DashboardController.php](app/Http/Controllers/DashboardController.php)

**Dashboard berubah sesuai role user:**
- **Admin** → Dashboard Admin (statistik seluruh sistem)
- **Petugas** → Dashboard Staff (pengelolaan peminjaman)
- **Peminjam** → Dashboard Borrower (daftar peminjaman user)

**View Files:**
- [resources/views/dashboard/admin.blade.php](resources/views/dashboard/admin.blade.php)
- [resources/views/dashboard/staff.blade.php](resources/views/dashboard/staff.blade.php)
- [resources/views/dashboard/borrower.blade.php](resources/views/dashboard/borrower.blade.php)

---

## 🗄️ Database Configuration

**Database**: MySQL (yerpustaka)
**Connection**: Dikonfigurasi di `.env`

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=yourpustaka
DB_USERNAME=root
DB_PASSWORD=
```

**Tabel Users** mencakup field:
- `id` - Primary Key
- `name` - Nama pengguna
- `email` - Email (unique)
- `email_verified_at` - Verifikasi email
- `phone` - Nomor telepon
- `address` - Alamat
- `id_number` - Nomor identitas (unique)
- `password` - Password (hashed)
- `role` - Role (admin, petugas, peminjam)
- `remember_token` - Token "ingat saya"
- `timestamps` - Created/updated at

---

## 🔒 Security Features

✅ Password Hashing (bcrypt)
✅ CSRF Protection (token @csrf)
✅ Rate Limiting (login attempts)
✅ Session Management (database driver)
✅ Unique Email & ID Number validation
✅ Password Confirmation validation

---

## 🧪 Testing

### Cara Test Login:

1. **Buka halaman register:**
   ```
   http://localhost/register
   ```

2. **Isi form dengan data:**
   - Nama: Test User
   - Email: test@example.com
   - Telepon: 081234567890
   - Alamat: Jl. Test No. 1
   - Nomor Identitas: 1234567890123456
   - Password: Password123

3. **Submit** → Otomatis login dan redirect ke dashboard

4. **Atau langsung login** dengan akun yang sudah dibuat

---

## 📁 File-file Penting

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Auth/
│   │   │   ├── AuthenticatedSessionController.php  (Login Logic)
│   │   │   └── RegisteredUserController.php         (Register Logic)
│   │   └── DashboardController.php                  (Dashboard Logic)
│   └── Requests/
│       └── Auth/
│           └── LoginRequest.php                     (Login Validation)
├── Models/
│   └── User.php                                     (User Model)

routes/
├── auth.php                                         (Auth Routes)
└── web.php                                          (Web Routes)

resources/views/
├── auth/
│   ├── login.blade.php                             (Login Page)
│   └── register.blade.php                          (Register Page)
└── dashboard/
    ├── admin.blade.php
    ├── staff.blade.php
    └── borrower.blade.php

config/
└── auth.php                                         (Auth Configuration)
```

---

## 🚀 Routes Tersedia

| Method | Route | Controller | Middleware |
|--------|-------|-----------|------------|
| GET | /register | RegisteredUserController@create | guest |
| POST | /register | RegisteredUserController@store | guest |
| GET | /login | AuthenticatedSessionController@create | guest |
| POST | /login | AuthenticatedSessionController@store | guest |
| POST | /logout | AuthenticatedSessionController@destroy | auth |
| GET | /dashboard | DashboardController@index | auth, verified |

---

## ⚙️ Cara Mengubah Redirect Setelah Login

Jika ingin mengubah halaman redirect setelah login, edit file:
[app/Http/Controllers/Auth/AuthenticatedSessionController.php](app/Http/Controllers/Auth/AuthenticatedSessionController.php)

**Baris yang perlu diubah:**
```php
return redirect()->intended(route('dashboard', absolute: false));
```

Ubah `'dashboard'` menjadi nama route lain yang diinginkan.

---

## 🐛 Troubleshooting

### Login gagal padahal data benar
- Pastikan MySQL running
- Cek file `.env` konfigurasi database
- Jalankan `php artisan migrate` untuk inisialisasi tabel

### Halaman login tidak load
- Cek apakah view `resources/views/auth/login.blade.php` ada
- Pastikan routes di `routes/auth.php` terdefinisi

### Redirect tidak bekerja
- Cek `DashboardController::index()` apakah sudah return view
- Verifikasi middleware `auth` dan `verified` di routes

---

## 📞 Kontak & Support

Untuk pertanyaan teknis, silakan periksa dokumentasi Laravel:
- https://laravel.com/docs/11/authentication
- https://laravel.com/docs/11/middleware

Dibuat: 24 Januari 2026
Version: 1.0
