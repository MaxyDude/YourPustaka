// Tambahkan interaktivitas footer
            document.addEventListener('DOMContentLoaded', function() {
                const links = document.querySelectorAll('.footer-links a');

                links.forEach(link => {
                    link.addEventListener('mouseenter', function() {
                        this.style.fontWeight = '600';
                    });

                    link.addEventListener('mouseleave', function() {
                        this.style.fontWeight = '400';
                    });
                });

                // Animasi untuk kontak info
                const contactItems = document.querySelectorAll('.contact-info li');
                contactItems.forEach(item => {
                    item.addEventListener('mouseenter', function() {
                        this.style.transform = 'translateX(5px)';
                    });

                    item.addEventListener('mouseleave', function() {
                        this.style.transform = 'translateX(0)';
                    });
                });
            });
