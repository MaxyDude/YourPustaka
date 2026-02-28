## Testing Checklist - Dashboard Book Display

### ✅ Dataset Ready
- [x] 5 test books created with proper metadata
- [x] All books have `cover_buku = NULL` (no hardcoded URLs)
- [x] IDs: 17-21 with full Indonesian metadata

### 📋 Dashboard Display Testing

#### 1. Rekomendasi Section (Latest Books)
- [ ] Open http://localhost/dashboard
- [ ] Verify 5 books appear in "Rekomendasi" slider
- [ ] Check each book title displays correctly:
  - "Pemrograman Web Modern"
  - "Database Design Essentials"
  - "Sastra Indonesia Klasik"
  - "Sejarah Dunia Ringkas"
  - "Fisika Kuantum untuk Pemula"
- [ ] Verify placeholder images show with proper URLs (check DevTools)
  - Should see: `https://via.placeholder.com/220x300?text=Pemrograman%20Web%20Modern`
  - NOT: `https://via.placeholder.com/220x300?text=Pemrograman+Web+Modern` (with +)
- [ ] Images render without broken link indicators

#### 2. Populer Section (Rating-based Books)  
- [ ] Verify 5 books appear in "Populer" slider
- [ ] Check same proper URL encoding (%20 for spaces)
- [ ] Verify rating displays (currently default 4.5)
- [ ] Images render correctly with fallback handler

#### 3. URL Encoding Verification
In browser DevTools (F12 > Network tab):
- [ ] Check image requests use %20 (not +)
- [ ] Examples of correct URLs:
  ```
  https://via.placeholder.com/220x300?text=Pemrograman%20Web%20Modern
  https://via.placeholder.com/220x300?text=Database%20Design%20Essentials
  https://via.placeholder.com/220x300?text=Sastra%20Indonesia%20Klasik
  ```

#### 4. Fallback Image Testing
- [ ] Open browser DevTools (F12 > Console)
- [ ] Run: `document.querySelector('.book-image').src = 'invalid'`
- [ ] Verify image switches to "No Image" placeholder:
  ```
  https://via.placeholder.com/220x300?text=No%20Image
  ```

#### 5. Interaction Testing
- [ ] Slider navigation works (left/right arrows if present)
- [ ] "Request Sekarang" buttons visible
- [ ] Hover effects on book cards work
- [ ] Category badges display: "Teknologi", "Database", "Sastra", etc.

#### 6. Source Code Verification (DevTools > Elements)
Rekomendasi section image should look like:
```html
<img src="https://via.placeholder.com/220x300?text=Pemrograman%20Web%20Modern" 
     alt="Pemrograman Web Modern" 
     class="book-image" 
     onerror="this.src='https://via.placeholder.com/220x300?text=No%20Image'">
```

### 🔧 Admin Dashboard Testing

#### 1. Admin Profile Dropdown
- [ ] Admin logged in on /admin/dashboard
- [ ] Click profile avatar in top right
- [ ] Dropdown menu appears with:
  - "Profil Saya"
  - "Dashboard Pengguna"
  - "Pengaturan"
  - "Logout"
- [ ] Click outside to close dropdown

#### 2. Delete Functionality
- [ ] Click delete button on a book card
- [ ] Confirm delete prompt appears
- [ ] Click confirm
- [ ] Book is removed from admin dashboard
- [ ] Refresh page → book should NOT reappear (permanent delete with forceDelete)
- [ ] Verify with `php verify_books.php` that book is gone from database

#### 3. Add New Book
- [ ] Try adding new book from admin modal
- [ ] Verify it appears with null cover in dashboard sliders
- [ ] Check URL encoding is correct for new title

### 📊 Database Verification Commands

After testing, run these to verify state:
```bash
# Check total books
php -r "require 'vendor/autoload.php'; require 'bootstrap/app.php'; echo 'Total books: ' . App\Models\Book::count() . PHP_EOL;"

# Verify covers are null
php verify_books.php
```

### 🐛 Expected Issues & Solutions

| Issue | Expected? | Solution |
|-------|-----------|----------|
| Images show with `+` instead of `%20` | NO | View already fixed with `str_replace()` |
| Placeholder says "No+Image" | NO | Fixed in fallback onerror attribute |
| Books don't display in slider | MAYBE | Check if DashboardController queries are working |
| Delete doesn't remove permanently | NO | Using `forceDelete()` not soft delete |
| Dropdown menu not visible | MAYBE | Check CSS @media queries for small screens |

### ✨ Success Criteria

All below should be TRUE:
- [x] 5 books in database with NULL covers
- [ ] Dashboard displays all 5 books in Rekomendasi
- [ ] Dashboard displays all 5 books in Populer
- [ ] All image URLs use %20 encoding
- [ ] Fallback images work with %20 encoding
- [ ] Delete removes books permanently (verified by refresh & DB check)
- [ ] Admin profile dropdown displays correctly

**When all tests pass, system is ready for production testing!**
