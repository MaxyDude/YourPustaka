# Setup Guide - YourPustaka

## Quick Start

### Step 1: Install Dependencies
```bash
cd c:\xampp\htdocs\YourPustaka
composer install
npm install
```

### Step 2: Environment Configuration
```bash
copy .env.example .env
php artisan key:generate
```

Edit `.env` dan atur database:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_DATABASE=yourpustaka
DB_USERNAME=root
DB_PASSWORD=
```

### Step 3: Database Setup
```bash
# Create database di phpMyAdmin atau command line:
# mysql -u root -e "CREATE DATABASE yourpustaka;"

php artisan migrate
php artisan db:seed
```

### Step 4: Build & Run
```bash
npm run build
php artisan serve
```

Buka browser: `http://localhost:8000`

## Akun Test

### Admin
- Email: admin@yourpustaka.com
- Password: password123

### Petugas
- Email: petugas1@yourpustaka.com
- Password: password123

### Peminjam
- Email: peminjam1@example.com (hingga peminjam5)
- Password: password123

## File Penting

- `database/migrations/` - Database schema
- `app/Models/` - Eloquent Models
- `app/Http/Controllers/` - Application Controllers
- `resources/views/` - Blade Templates
- `routes/web.php` - Web Routes

## Troubleshooting

**Error: Class not found**
```bash
composer dump-autoload
```

**Error: SQLSTATE[HY000]**
- Pastikan MySQL running
- Check database configuration di .env

**Error: npm assets not found**
```bash
npm run build
```

## Fitur Utama

✅ Multi-role authentication (Admin, Petugas, Peminjam)
✅ Manajemen buku lengkap
✅ Sistem barcode untuk peminjaman
✅ Dashboard interaktif per role
✅ UI modern dengan Bootstrap 5
✅ Responsive design
✅ Sistem peminjaman lengkap dengan tracking status

Selamat! Aplikasi siap digunakan! 🚀
