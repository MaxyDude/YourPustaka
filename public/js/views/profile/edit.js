// Edit profile functionality
        const valueElements = {
            name: document.getElementById('nameValue'),
            email: document.getElementById('emailValue')
        };

        const formElements = {
            name: document.getElementById('nameForm'),
            email: document.getElementById('emailForm')
        };

        const inputElements = {
            name: document.getElementById('nameInput'),
            email: document.getElementById('emailInput')
        };

        // Make all editable fields clickable
        Object.keys(valueElements).forEach(field => {
            if (valueElements[field]) {
                valueElements[field].addEventListener('click', function() {
                    showEditForm(field);
                });
            }
        });

        // Show edit form for a specific field
        function showEditForm(field) {
            // Hide all other forms first
            Object.keys(formElements).forEach(f => {
                if (formElements[f]) {
                    formElements[f].style.display = 'none';
                    if (valueElements[f]) {
                        valueElements[f].style.display = 'block';
                    }
                }
            });

            // Show the selected form
            if (formElements[field]) {
                formElements[field].style.display = 'block';
                valueElements[field].style.display = 'none';

                // Focus on input
                if (inputElements[field]) {
                    inputElements[field].focus();
                }
            }
        }

        // Hide edit form for a specific field
        function hideEditForm(field) {
            if (formElements[field]) {
                formElements[field].style.display = 'none';
                valueElements[field].style.display = 'block';
            }
        }

        // Save functions
        document.getElementById('saveNameBtn')?.addEventListener('click', function() {
            const newValue = inputElements.name.value;
            valueElements.name.textContent = newValue;
            hideEditForm('name');
            // Update avatar initials
            const avatar = document.querySelector('.profile-avatar');
            const initials = newValue.split(' ').map(n => n[0]).join('').toUpperCase();
            if (avatar && initials.length >= 2) {
                avatar.textContent = initials.substring(0, 2);
                document.querySelector('.profile-name').textContent = newValue;
            }
            showNotification('Nama berhasil diperbarui!');
        });

        document.getElementById('saveEmailBtn')?.addEventListener('click', function() {
            const newValue = inputElements.email.value;
            if (!validateEmail(newValue)) {
                alert('Format email tidak valid!');
                return;
            }
            valueElements.email.textContent = newValue;
            hideEditForm('email');
            showNotification('Email berhasil diperbarui!');
        });

        // Cancel functions
        document.getElementById('cancelNameBtn')?.addEventListener('click', function() {
            hideEditForm('name');
        });

        document.getElementById('cancelEmailBtn')?.addEventListener('click', function() {
            hideEditForm('email');
        });

        // Edit profile button (main)
        document.getElementById('editProfileBtn')?.addEventListener('click', function() {
            showEditForm('name');
        });

        // Avatar edit
        document.getElementById('avatarEditBtn')?.addEventListener('click', function() {
            const input = document.createElement('input');
            input.type = 'file';
            input.accept = 'image/*';
            input.onchange = function(e) {
                if (e.target.files && e.target.files[0]) {
                    const reader = new FileReader();
                    reader.onload = function(event) {
                        const avatar = document.querySelector('.profile-avatar');
                        avatar.innerHTML = `<img src="${event.target.result}" style="width:100%;height:100%;border-radius:50%;object-fit:cover;">`;
                        showNotification('Foto profil berhasil diubah!');
                    };
                    reader.readAsDataURL(e.target.files[0]);
                }
            };
            input.click();
        });

        // Email validation
        function validateEmail(email) {
            const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return re.test(email);
        }

        // Notification function
        function showNotification(message) {
            // Create notification element
            const notification = document.createElement('div');
            notification.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                background: var(--accent);
                color: white;
                padding: 15px 20px;
                border-radius: 8px;
                box-shadow: var(--shadow);
                z-index: 1000;
                animation: slideIn 0.3s ease;
                display: flex;
                align-items: center;
                gap: 10px;
            `;

            notification.innerHTML = `
                <i class="fas fa-check-circle"></i>
                <span>${message}</span>
            `;

            document.body.appendChild(notification);

            // Remove after 3 seconds
            setTimeout(() => {
                notification.style.animation = 'slideOut 0.3s ease';
                setTimeout(() => notification.remove(), 300);
            }, 3000);
        }

        // Add CSS for animations
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

        // Change password button
        document.getElementById('changePasswordBtn')?.addEventListener('click', function() {
            const currentPassword = prompt('Masukkan password saat ini:');
            if (currentPassword) {
                const newPassword = prompt('Masukkan password baru (minimal 6 karakter):');
                if (newPassword && newPassword.length >= 6) {
                    const confirmPassword = prompt('Konfirmasi password baru:');
                    if (newPassword === confirmPassword) {
                        showNotification('Password berhasil diubah!');
                    } else {
                        alert('Password konfirmasi tidak cocok!');
                    }
                } else if (newPassword) {
                    alert('Password minimal 6 karakter!');
                }
            }
        });
