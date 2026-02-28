// Data kategori dari database
        let categories = window.__VIEW_CONFIG['e3'];

        let currentCategoryId = null;
        let isEditing = false;

        // Warna yang tersedia untuk kategori
        const availableColors = [
            "#4361ee", "#2ecc71", "#f39c12", "#9b59b6",
            "#e74c3c", "#3498db", "#1abc9c", "#e67e22",
            "#95a5a6", "#34495e", "#d35400", "#16a085",
            "#8e44ad", "#2c3e50", "#27ae60", "#2980b9"
        ];

        // Ikon yang tersedia untuk kategori
        const availableIcons = [
            "fas fa-tag", "fas fa-book", "fas fa-laptop-code", "fas fa-book-open",
            "fas fa-landmark", "fas fa-flask", "fas fa-dragon", "fas fa-graduation-cap",
            "fas fa-chart-line", "fas fa-palette", "fas fa-music", "fas fa-camera",
            "fas fa-code", "fas fa-globe", "fas fa-heart", "fas fa-star",
            "fas fa-lightbulb", "fas fa-rocket", "fas fa-microscope", "fas fa-pen-fancy"
        ];

        // Initialize page
        document.addEventListener('DOMContentLoaded', function() {
            initUserDropdown();
            loadCategories();
            initColorPicker();
            initIconPicker();
            initSearch();
            initFilters();
        });

        // User dropdown functionality
        function initUserDropdown() {
            const userInfoDropdown = document.getElementById('userInfoDropdown');
            const userDropdownMenu = document.getElementById('userDropdownMenu');

            if (userInfoDropdown && userDropdownMenu) {
                userInfoDropdown.addEventListener('click', function(e) {
                    e.stopPropagation();
                    userDropdownMenu.classList.toggle('active');
                });

                document.addEventListener('click', function(e) {
                    if (!userInfoDropdown.contains(e.target)) {
                        userDropdownMenu.classList.remove('active');
                    }
                });
            }
        }

        // Load categories to the grid
        function loadCategories() {
            const container = document.getElementById('categoriesContainer');
            const loadingState = document.getElementById('loadingState');
            const emptyState = document.getElementById('emptyState');

            // Simulate loading delay
            setTimeout(() => {
                if (categories.length === 0) {
                    container.style.display = 'none';
                    loadingState.style.display = 'none';
                    emptyState.style.display = 'block';
                    updateStats();
                    return;
                }

                container.innerHTML = '';

                categories.forEach(category => {
                    const card = createCategoryCard(category);
                    container.appendChild(card);
                });

                loadingState.style.display = 'none';
                emptyState.style.display = 'none';
                container.style.display = 'grid';

                updateStats();
            }, 800);
        }

        // Create category card HTML
        function createCategoryCard(category) {
            const card = document.createElement('div');
            card.className = 'category-card';
            card.dataset.id = category.id;
            card.dataset.status = category.status;

            const safeColor = category.warna || '#4361ee';
            const safeIcon = category.icon || 'fas fa-tag';
            const safeDescription = category.deskripsi || 'Tidak ada deskripsi';
            const safeSlug = category.slug || '-';
            const statusClass = category.status === 'active' ? 'status-active' : 'status-inactive';
            const statusText = category.status === 'active' ? 'Aktif' : 'Nonaktif';
            const createdDate = category.created_at
                ? new Date(category.created_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' })
                : '-';

            card.innerHTML = `
                <div class="category-header" style="background: linear-gradient(135deg, ${safeColor}, ${darkenColor(safeColor, 20)});">
                    <div class="category-icon">
                        <i class="${safeIcon}"></i>
                    </div>
                    <div class="category-title">
                        <div class="category-name">${category.nama_kategori}</div>
                        <div class="category-id">Slug: ${safeSlug}</div>
                    </div>
                    <div class="category-status">${statusText}</div>
                </div>
                <div class="category-body">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 15px; padding-bottom: 15px; border-bottom: 1px solid #f0f0f0;">
                        <div style="text-align: center; flex: 1;">
                            <div style="font-size: 1.5rem; font-weight: 700; color: var(--primary);">${category.books_count || 0}</div>
                            <div style="font-size: 0.8rem; color: var(--gray);">Buku</div>
                        </div>
                    </div>
                    <div class="category-description">
                        ${safeDescription}
                    </div>
                </div>
                <div class="category-footer">
                    <div class="category-date">
                        <i class="far fa-calendar"></i> ${createdDate}
                    </div>
                    <div class="category-actions">
                        <button class="btn btn-outline btn-sm" onclick="viewCategoryDetail(${category.id})">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button class="btn btn-outline btn-sm" onclick="openCategoryModal('edit', ${category.id})">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-outline btn-sm" onclick="confirmDeleteCategory(${category.id}, '${category.nama_kategori}')">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            `;

            return card;
        }

        // Helper function to darken color
        function darkenColor(color, percent) {
            const num = parseInt(color.replace("#", ""), 16);
            const amt = Math.round(2.55 * percent);
            const R = (num >> 16) - amt;
            const G = (num >> 8 & 0x00FF) - amt;
            const B = (num & 0x0000FF) - amt;
            return "#" + (0x1000000 + (R < 255 ? R < 1 ? 0 : R : 255) * 0x10000 +
                (G < 255 ? G < 1 ? 0 : G : 255) * 0x100 +
                (B < 255 ? B < 1 ? 0 : B : 255)).toString(16).slice(1);
        }

        // Update statistics
        function updateStats() {
            const safeCategories = Array.isArray(categories) ? categories : [];
            const totalCategories = safeCategories.length;
            const totalBooks = safeCategories.reduce((sum, cat) => sum + (Number(cat.books_count) || 0), 0);

            if (totalCategories === 0) {
                document.getElementById('totalCategories').textContent = 0;
                document.getElementById('totalBooks').textContent = 0;
                document.getElementById('popularCategory').textContent = 'N/A';
                document.getElementById('recentAdded').textContent = 0;
                return;
            }

            // Find popular category (most books)
            const popularCategory = safeCategories.reduce((currentPopular, cat) => {
                return (Number(cat.books_count) || 0) > (Number(currentPopular.books_count) || 0)
                    ? cat
                    : currentPopular;
            }, safeCategories[0]);

            // Count recent categories (this month)
            const currentDate = new Date();
            const currentMonth = currentDate.getMonth();
            const currentYear = currentDate.getFullYear();
            const recentAdded = safeCategories.filter(cat => {
                if (!cat.created_at) return false;
                const catDate = new Date(cat.created_at);
                if (Number.isNaN(catDate.getTime())) return false;
                return catDate.getMonth() === currentMonth && catDate.getFullYear() === currentYear;
            }).length;

            document.getElementById('totalCategories').textContent = totalCategories;
            document.getElementById('totalBooks').textContent = totalBooks;
            document.getElementById('popularCategory').textContent = popularCategory.nama_kategori || 'N/A';
            document.getElementById('recentAdded').textContent = recentAdded;
        }

        // Initialize color picker
        function initColorPicker() {
            const colorPicker = document.getElementById('colorPicker');
            colorPicker.innerHTML = '';

            availableColors.forEach(color => {
                const colorOption = document.createElement('div');
                colorOption.className = 'color-option';
                colorOption.style.backgroundColor = color;
                colorOption.dataset.color = color;
                colorOption.onclick = () => selectColor(color);

                colorPicker.appendChild(colorOption);
            });

            // Select first color by default
            selectColor(availableColors[0]);
        }

        // Select color function
        function selectColor(color) {
            document.getElementById('selectedColor').value = color;

            // Remove selected class from all
            document.querySelectorAll('.color-option').forEach(opt => {
                opt.classList.remove('selected');
            });

            // Add selected class to clicked color
            const selectedOption = document.querySelector(`.color-option[data-color="${color}"]`);
            if (selectedOption) {
                selectedOption.classList.add('selected');
            }
        }

        // Initialize icon picker
        function initIconPicker() {
            const iconPicker = document.getElementById('iconPicker');
            iconPicker.innerHTML = '';

            availableIcons.forEach(icon => {
                const iconOption = document.createElement('div');
                iconOption.className = 'icon-option';
                iconOption.innerHTML = `<i class="${icon}"></i>`;
                iconOption.dataset.icon = icon;
                iconOption.onclick = () => selectIcon(icon);

                iconPicker.appendChild(iconOption);
            });

            // Select first icon by default
            selectIcon(availableIcons[0]);
        }

        // Select icon function
        function selectIcon(icon) {
            document.getElementById('selectedIcon').value = icon;

            // Remove selected class from all
            document.querySelectorAll('.icon-option').forEach(opt => {
                opt.classList.remove('selected');
            });

            // Add selected class to clicked icon
            const selectedOption = document.querySelector(`.icon-option[data-icon="${icon}"]`);
            if (selectedOption) {
                selectedOption.classList.add('selected');
            }
        }

        // Initialize search functionality
        function initSearch() {
            const searchInput = document.getElementById('searchCategories');
            searchInput.addEventListener('input', filterCategories);
        }

        // Initialize filter functionality
        function initFilters() {
            const statusFilter = document.getElementById('filterStatus');
            const sortFilter = document.getElementById('filterSort');

            statusFilter.addEventListener('change', filterCategories);
            sortFilter.addEventListener('change', filterCategories);
        }

        // Filter and sort categories
        function filterCategories() {
            const searchTerm = document.getElementById('searchCategories').value.toLowerCase();
            const statusFilter = document.getElementById('filterStatus').value;
            const sortFilter = document.getElementById('filterSort').value;

            let filtered = [...categories];

            // Apply search filter
            if (searchTerm) {
                filtered = filtered.filter(cat =>
                    (cat.nama_kategori || '').toLowerCase().includes(searchTerm) ||
                    (cat.deskripsi || '').toLowerCase().includes(searchTerm) ||
                    (cat.slug || '').toLowerCase().includes(searchTerm)
                );
            }

            // Apply status filter
            if (statusFilter) {
                filtered = filtered.filter(cat => cat.status === statusFilter);
            }

            // Apply sorting
            filtered.sort((a, b) => {
                switch(sortFilter) {
                    case 'name_asc':
                        return a.nama_kategori.localeCompare(b.nama_kategori);
                    case 'name_desc':
                        return b.nama_kategori.localeCompare(a.nama_kategori);
                    case 'books_desc':
                        return (b.books_count || 0) - (a.books_count || 0);
                    case 'date_desc':
                        return new Date(b.created_at) - new Date(a.created_at);
                    case 'date_asc':
                        return new Date(a.created_at) - new Date(b.created_at);
                    default:
                        return 0;
                }
            });

            // Update display
            const container = document.getElementById('categoriesContainer');
            container.innerHTML = '';

            if (filtered.length === 0) {
                document.getElementById('emptyState').style.display = 'block';
                container.style.display = 'none';
            } else {
                document.getElementById('emptyState').style.display = 'none';
                filtered.forEach(category => {
                    const card = createCategoryCard(category);
                    container.appendChild(card);
                });
                container.style.display = 'grid';
            }
        }

        // Reset filters
        function resetFilters() {
            document.getElementById('searchCategories').value = '';
            document.getElementById('filterStatus').value = '';
            document.getElementById('filterSort').value = 'name_asc';
            filterCategories();
        }

        // Open category modal
        function openCategoryModal(action, categoryId = null) {
            const modal = document.getElementById('categoryModal');
            const title = document.getElementById('categoryModalTitle');
            const form = document.getElementById('categoryForm');

            if (action === 'add') {
                isEditing = false;
                title.textContent = 'Tambah Kategori Baru';
                form.reset();
                document.getElementById('categoryOrder').value = categories.length + 1;
                document.getElementById('categoryStatus').value = 'active';

                // Reset color and icon selections
                selectColor(availableColors[0]);
                selectIcon(availableIcons[0]);

            } else if (action === 'edit' && categoryId) {
                isEditing = true;
                currentCategoryId = categoryId;
                title.textContent = 'Edit Kategori';

                const category = categories.find(cat => cat.id === categoryId);
                if (category) {
                    document.getElementById('categoryName').value = category.nama_kategori;
                    document.getElementById('categoryDescription').value = category.deskripsi || '';
                    document.getElementById('categoryStatus').value = category.status;
                    document.getElementById('categoryOrder').value = category.urutan || 0;
                    document.getElementById('categoryKeywords').value = category.kata_kunci || '';

                    // Set color and icon
                    selectColor(category.warna || availableColors[0]);
                    selectIcon(category.icon || availableIcons[0]);
                }
            }

            modal.style.display = 'flex';
        }

        // Close category modal
        function closeCategoryModal() {
            document.getElementById('categoryModal').style.display = 'none';
            currentCategoryId = null;
            isEditing = false;
        }

        // Save category
        function saveCategory() {
            const name = document.getElementById('categoryName').value.trim();
            const description = document.getElementById('categoryDescription').value.trim();
            const color = document.getElementById('selectedColor').value;
            const icon = document.getElementById('selectedIcon').value;
            const status = document.getElementById('categoryStatus').value;
            const order = parseInt(document.getElementById('categoryOrder').value);
            const keywords = document.getElementById('categoryKeywords').value.trim();

            // Validation
            if (!name) {
                alert('Nama kategori wajib diisi!');
                return;
            }

            // Prepare save button
            const saveBtn = document.getElementById('saveCategoryBtn');
            const originalText = saveBtn.innerHTML;
            saveBtn.disabled = true;
            saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';

            // Get CSRF token
            const token = document.querySelector('meta[name="csrf-token"]')?.content || '';

            // Prepare data
            const data = {
                nama_kategori: name,
                deskripsi: description,
                warna: color,
                icon: icon,
                status: status,
                urutan: order,
                kata_kunci: keywords
            };

            try {
                let url = '';
                let method = 'POST';

                if (isEditing && currentCategoryId) {
                    // Update existing category
                    url = `/admin/categories/${currentCategoryId}`;
                    method = 'PUT';
                } else {
                    // Add new category
                    url = window.__VIEW_CONFIG['e1'];
                    method = 'POST';
                }

                fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token
                    },
                    body: JSON.stringify(data)
                })
                .then(response => response.json())
                .then(result => {
                    saveBtn.disabled = false;
                    saveBtn.innerHTML = originalText;

                    if (result.success) {
                        alert(result.message);
                        closeCategoryModal();
                        location.reload(); // Reload to update from database
                    } else {
                        alert('Error: ' + result.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Gagal menyimpan kategori');
                    saveBtn.disabled = false;
                    saveBtn.innerHTML = originalText;
                });
            } catch (error) {
                console.error('Error:', error);
                alert('Gagal menyimpan kategori');
                saveBtn.disabled = false;
                saveBtn.innerHTML = originalText;
            }
        }

        // View category detail
        function viewCategoryDetail(categoryId) {
            const modal = document.getElementById('detailModal');
            const category = categories.find(cat => cat.id === categoryId);

            if (!category) return;

            const safeColor = category.warna || '#4361ee';
            const safeIcon = category.icon || 'fas fa-tag';

            // Set modal header color
            document.getElementById('detailHeader').style.background =
                `linear-gradient(135deg, ${safeColor}, ${darkenColor(safeColor, 20)})`;

            // Set icon
            const detailIcon = document.getElementById('detailIcon');
            detailIcon.style.backgroundColor = safeColor;
            detailIcon.innerHTML = `<i class="${safeIcon}"></i>`;

            // Set other details
            document.getElementById('detailName').textContent = category.nama_kategori;
            document.getElementById('detailCode').textContent = `Slug: ${category.slug}`;

            const statusText = category.status === 'active' ? 'Aktif' : 'Nonaktif';
            const statusClass = category.status === 'active' ? 'status-active' : 'status-inactive';
            document.getElementById('detailStatus').textContent = statusText;
            document.getElementById('detailStatus').className = `status-badge ${statusClass}`;

            document.getElementById('detailDescription').textContent = category.deskripsi || 'Tidak ada deskripsi';
            const createdDate = new Date(category.created_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
            const updatedDate = new Date(category.updated_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
            document.getElementById('detailCreatedAt').textContent = createdDate;
            document.getElementById('detailUpdatedAt').textContent = updatedDate;
            document.getElementById('detailOrder').textContent = category.urutan || 0;
            document.getElementById('detailKeywords').textContent = category.kata_kunci || 'Tidak ada keywords';

            // Set color preview
            document.getElementById('detailColorPreview').style.backgroundColor = safeColor;
            document.getElementById('detailColorCode').textContent = safeColor;

            modal.style.display = 'flex';
            currentCategoryId = categoryId;
        }

        // Close detail modal
        function closeDetailModal() {
            document.getElementById('detailModal').style.display = 'none';
            currentCategoryId = null;
        }

        // Edit from detail modal
        function editFromDetail() {
            closeDetailModal();
            setTimeout(() => {
                if (currentCategoryId) {
                    openCategoryModal('edit', currentCategoryId);
                }
            }, 300);
        }

        // Confirm delete category
        function confirmDeleteCategory(categoryId, categoryName) {
            const modal = document.getElementById('deleteModal');
            document.getElementById('categoryToDeleteName').textContent = categoryName;
            currentCategoryId = categoryId;
            modal.style.display = 'flex';
        }

        // Close delete modal
        function closeDeleteModal() {
            document.getElementById('deleteModal').style.display = 'none';
            currentCategoryId = null;
        }

        // Confirm delete
        function confirmDelete() {
            if (!currentCategoryId) return;

            const token = document.querySelector('meta[name="csrf-token"]')?.content || '';

            if (confirm('Apakah Anda yakin ingin menghapus kategori ini?')) {
                fetch(`/admin/categories/${currentCategoryId}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token
                    }
                })
                .then(response => response.json())
                .then(result => {
                    closeDeleteModal();

                    if (result.success) {
                        alert(result.message);
                        location.reload();
                    } else {
                        alert('Error: ' + result.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Gagal menghapus kategori');
                });
            }
        }

        // Export categories
        function exportCategories() {
            // Generate CSV export
            let csv = 'ID,Nama Kategori,Deskripsi,Status,Dibuat Pada\n';
            categories.forEach(cat => {
                csv += `${cat.id},"${cat.nama_kategori}","${cat.deskripsi || ''}","${cat.status}","${cat.created_at || ''}"\n`;
            });

            const encodedUri = encodeURI('data:text/csv;charset=utf-8,' + csv);
            const link = document.createElement('a');
            link.setAttribute('href', encodedUri);
            link.setAttribute('download', 'kategori_buku.csv');
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }

        function printCategoryReport() {
            const printWindow = window.open(window.__VIEW_CONFIG['e2'], '_blank');
            if (!printWindow) {
                alert('Popup diblokir browser. Mohon izinkan popup untuk mencetak laporan.');
            }
        }

        // Close modals when clicking outside
        window.addEventListener('click', function(e) {
            const modals = ['categoryModal', 'deleteModal', 'detailModal'];
            modals.forEach(modalId => {
                const modal = document.getElementById(modalId);
                if (e.target === modal) {
                    modal.style.display = 'none';
                }
            });
        });
