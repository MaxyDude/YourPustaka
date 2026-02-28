const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const profileUpdateUrl = window.__VIEW_CONFIG['e1'];

    document.querySelectorAll('.profile-menu-item a').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            document.querySelectorAll('.profile-menu-item').forEach(item => item.classList.remove('active'));
            this.parentElement.classList.add('active');
            const target = document.querySelector(this.getAttribute('href'));
            if (target) target.scrollIntoView({ behavior: 'smooth', block: 'start' });
        });
    });

    const valueElements = { name: document.getElementById('nameValue'), email: document.getElementById('emailValue'), phone: document.getElementById('phoneValue') };
    const formElements = { name: document.getElementById('nameForm'), email: document.getElementById('emailForm'), phone: document.getElementById('phoneForm') };
    const inputElements = { name: document.getElementById('nameInput'), email: document.getElementById('emailInput'), phone: document.getElementById('phoneInput') };

    function showEditForm(field) {
        Object.keys(formElements).forEach(f => { formElements[f].style.display = 'none'; valueElements[f].style.display = 'block'; });
        formElements[field].style.display = 'block';
        valueElements[field].style.display = 'none';
        inputElements[field].focus();
    }
    function hideEditForm(field) { formElements[field].style.display = 'none'; valueElements[field].style.display = 'block'; }
    function validateEmail(email) { return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email); }

    async function saveProfile(field) {
        const name = inputElements.name.value.trim();
        const email = inputElements.email.value.trim();
        const phone = inputElements.phone.value.trim();
        if (!name || !email) return alert('Nama dan email wajib diisi.');
        if (!validateEmail(email)) return alert('Format email tidak valid.');

        const payload = new FormData();
        payload.append('_method', 'PATCH');
        payload.append('_token', csrfToken);
        payload.append('name', name);
        payload.append('email', email);
        payload.append('phone', phone);

        try {
            const response = await fetch(profileUpdateUrl, { method: 'POST', headers: { 'Accept': 'application/json' }, body: payload });
            if (!response.ok && response.status !== 302) throw new Error('Gagal menyimpan perubahan profil.');

            valueElements.name.textContent = name;
            valueElements.email.textContent = email;
            valueElements.phone.textContent = phone || '-';
            document.querySelector('.profile-name').textContent = name;
            const avatar = document.getElementById('profileAvatar');
            if (avatar && !avatar.querySelector('img')) {
                const initials = name.split(' ').filter(Boolean).map(v => v[0]).join('').toUpperCase().substring(0, 2) || 'US';
                avatar.childNodes[0].nodeValue = initials;
            }
            hideEditForm(field);
            showNotification('Profil berhasil diperbarui.');
        } catch (e) { alert(e.message); }
    }

    Object.keys(valueElements).forEach(field => valueElements[field].addEventListener('click', () => showEditForm(field)));
    document.getElementById('editProfileBtn').addEventListener('click', () => showEditForm('name'));
    document.getElementById('saveNameBtn').addEventListener('click', () => saveProfile('name'));
    document.getElementById('saveEmailBtn').addEventListener('click', () => saveProfile('email'));
    document.getElementById('savePhoneBtn').addEventListener('click', () => saveProfile('phone'));
    document.getElementById('cancelNameBtn').addEventListener('click', () => hideEditForm('name'));
    document.getElementById('cancelEmailBtn').addEventListener('click', () => hideEditForm('email'));
    document.getElementById('cancelPhoneBtn').addEventListener('click', () => hideEditForm('phone'));

    function bindAvatarEdit() {
        const avatarBtn = document.getElementById('avatarEditBtn');
        if (!avatarBtn) return;

        avatarBtn.onclick = function () {
            const input = document.createElement('input');
            input.type = 'file';
            input.accept = 'image/*';
            input.onchange = function (e) {
                if (!e.target.files || !e.target.files[0]) return;
                const reader = new FileReader();
                reader.onload = function (event) {
                    const avatar = document.getElementById('profileAvatar');
                    avatar.innerHTML = `<img src="${event.target.result}" style="width:100%;height:100%;border-radius:50%;object-fit:cover;">` +
                        '<div class="avatar-edit" id="avatarEditBtn"><i class="fas fa-camera"></i></div>';
                    bindAvatarEdit();
                    showNotification('Preview foto profil berhasil diubah.');
                };
                reader.readAsDataURL(e.target.files[0]);
            };
            input.click();
        };
    }
    bindAvatarEdit();

    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            const filter = this.dataset.filter;
            document.querySelectorAll('.history-item').forEach(item => {
                item.style.display = (filter === 'all' || item.dataset.status === filter) ? 'flex' : 'none';
            });
        });
    });

    document.querySelectorAll('.action-btn.return').forEach(btn => {
        btn.addEventListener('click', function () {
            alert(`Pengembalian "${this.dataset.title || 'buku'}" diproses melalui petugas.`);
        });
    });

    document.querySelectorAll('.action-btn.detail').forEach(btn => {
        btn.addEventListener('click', function () {
            const bookId = this.dataset.bookId;
            if (bookId) {
                window.location.href = `/pinjaman/detail/${bookId}`;
            } else {
                alert(`Judul: ${this.dataset.title || '-'}\nPenulis: ${this.dataset.author || '-'}\nKode Tiket: ${this.dataset.code || '-'}`);
            }
        });
    });

    document.getElementById('changePasswordBtn').addEventListener('click', function () {
        alert('Untuk ubah password, silakan gunakan halaman pengaturan akun.');
    });

    function showNotification(message) {
        const n = document.createElement('div');
        n.style.cssText = 'position:fixed;top:20px;right:20px;background:var(--accent);color:#fff;padding:12px 16px;border-radius:8px;box-shadow:var(--shadow);z-index:1000;';
        n.innerHTML = `<i class="fas fa-check-circle"></i> ${message}`;
        document.body.appendChild(n);
        setTimeout(() => n.remove(), 3000);
    }

    (function updateLastLogin() {
        const now = new Date();
        const time = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', hour12: false });
        const el = document.getElementById('lastLoginValue');
        if (el) el.textContent = `Hari ini, ${time} WIB`;
    })();
