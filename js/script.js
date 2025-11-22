// Genel JavaScript fonksiyonları

document.addEventListener('DOMContentLoaded', function() {
    // Form validasyonu
    initializeFormValidation();

    // Dinamik arama ve filtreleme
    initializeSearchAndFilter();

    // Toast mesajları
    initializeToastMessages();

    // Responsive navigasyon
    initializeMobileNavigation();
});

// Form Validasyonu
function initializeFormValidation() {
    const forms = document.querySelectorAll('form[method="POST"]');

    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const requiredFields = form.querySelectorAll('[required]');
            let isValid = true;

            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    isValid = false;
                    highlightFieldError(field);
                } else {
                    removeFieldError(field);
                }
            });

            // Özel validasyonlar
            if (form.querySelector('#password') && form.querySelector('#confirm_password')) {
                const password = form.querySelector('#password').value;
                const confirmPassword = form.querySelector('#confirm_password').value;

                if (password !== confirmPassword) {
                    isValid = false;
                    showToast('Şifreler eşleşmiyor!', 'error');
                }
            }

            if (!isValid) {
                e.preventDefault();
                showToast('Lütfen tüm gerekli alanları doldurun!', 'error');
            }
        });
    });
}

function highlightFieldError(field) {
    field.style.borderColor = '#ef4444';
    field.style.boxShadow = '0 0 0 3px rgba(239, 68, 68, 0.1)';
}

function removeFieldError(field) {
    field.style.borderColor = '';
    field.style.boxShadow = '';
}

// Arama ve Filtreleme
function initializeSearchAndFilter() {
    const searchInput = document.querySelector('#searchInput');
    const filterSelect = document.querySelector('#filterSelect');

    if (searchInput) {
        searchInput.addEventListener('input', debounce(function(e) {
            filterProducts(e.target.value, filterSelect ? filterSelect.value : '');
        }, 300));
    }

    if (filterSelect) {
        filterSelect.addEventListener('change', function(e) {
            filterProducts(searchInput ? searchInput.value : '', e.target.value);
        });
    }
}

function filterProducts(searchTerm, filterValue) {
    const productCards = document.querySelectorAll('.product-card');

    productCards.forEach(card => {
        const productName = card.querySelector('h4').textContent.toLowerCase();
        const productCategory = card.querySelector('.product-category').textContent.toLowerCase();
        const productDescription = card.querySelector('.product-description')?.textContent.toLowerCase() || '';

        const matchesSearch = productName.includes(searchTerm.toLowerCase()) ||
                            productDescription.includes(searchTerm.toLowerCase());
        const matchesFilter = !filterValue || productCategory === filterValue.toLowerCase();

        if (matchesSearch && matchesFilter) {
            card.style.display = 'block';
        } else {
            card.style.display = 'none';
        }
    });
}

// Debounce fonksiyonu
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Toast Mesajları
function initializeToastMessages() {
    // Sayfa yüklendiğinde otomatik toast'ları kontrol et
    const successMessage = document.querySelector('.alert-success');
    const errorMessage = document.querySelector('.alert-error');

    if (successMessage) {
        showToast(successMessage.textContent, 'success');
    }

    if (errorMessage) {
        showToast(errorMessage.textContent, 'error');
    }
}

function showToast(message, type = 'info') {
    // Toast container oluştur
    let toastContainer = document.getElementById('toast-container');
    if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.id = 'toast-container';
        toastContainer.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 10000;
            display: flex;
            flex-direction: column;
            gap: 10px;
        `;
        document.body.appendChild(toastContainer);
    }

    // Toast oluştur
    const toast = document.createElement('div');
    toast.style.cssText = `
        padding: 12px 20px;
        border-radius: 8px;
        color: white;
        font-weight: 500;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        animation: slideIn 0.3s ease;
        max-width: 300px;
        word-wrap: break-word;
    `;

    // Tip'e göre stil belirle
    switch (type) {
        case 'success':
            toast.style.backgroundColor = '#10b981';
            break;
        case 'error':
            toast.style.backgroundColor = '#ef4444';
            break;
        case 'warning':
            toast.style.backgroundColor = '#f59e0b';
            break;
        default:
            toast.style.backgroundColor = '#64748b';
    }

    toast.textContent = message;

    // Toast'ı container'a ekle
    toastContainer.appendChild(toast);

    // 5 saniye sonra kaldır
    setTimeout(() => {
        toast.style.animation = 'slideOut 0.3s ease';
        setTimeout(() => {
            if (toast.parentNode) {
                toast.parentNode.removeChild(toast);
            }
        }, 300);
    }, 5000);
}

// CSS animasyonları
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

// Mobil Navigasyon
function initializeMobileNavigation() {
    const nav = document.querySelector('.nav');
    const headerContent = document.querySelector('.header-content');

    if (window.innerWidth <= 768 && nav) {
        // Mobil menü butonu oluştur
        const menuButton = document.createElement('button');
        menuButton.innerHTML = '☰';
        menuButton.style.cssText = `
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: var(--text-primary);
        `;

        // Butonu header'a ekle
        headerContent.insertBefore(menuButton, nav);

        // Menüyü gizle
        nav.style.display = 'none';

        // Buton tıklama olayı
        menuButton.addEventListener('click', function() {
            if (nav.style.display === 'none') {
                nav.style.display = 'flex';
                nav.style.flexDirection = 'column';
                nav.style.position = 'absolute';
                nav.style.top = '100%';
                nav.style.left = '0';
                nav.style.right = '0';
                nav.style.background = 'var(--surface-color)';
                nav.style.padding = '1rem';
                nav.style.boxShadow = 'var(--shadow-lg)';
            } else {
                nav.style.display = 'none';
            }
        });

        // Ekran boyutu değiştiğinde kontrol et
        window.addEventListener('resize', function() {
            if (window.innerWidth > 768) {
                nav.style.display = 'flex';
                nav.style.flexDirection = 'row';
                nav.style.position = 'static';
                nav.style.background = 'none';
                nav.style.padding = '0';
                nav.style.boxShadow = 'none';
                menuButton.style.display = 'none';
            } else {
                menuButton.style.display = 'block';
                nav.style.display = 'none';
            }
        });
    }
}

// Sayfa yükleme animasyonu
document.addEventListener('DOMContentLoaded', function() {
    // Sayfa yüklendiğinde fade-in efekti
    document.body.style.opacity = '0';
    document.body.style.transition = 'opacity 0.3s ease';

    setTimeout(() => {
        document.body.style.opacity = '1';
    }, 100);
});

// Form otomatik tamamlama
function autoCompleteForm() {
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.setAttribute('autocomplete', 'on');
    });
}

// Sayfa görünürlüğü kontrolü
document.addEventListener('visibilitychange', function() {
    if (document.visibilityState === 'visible') {
        // Sayfa tekrar görünür olduğunda
        console.log('Sayfa görünür oldu');
    }
});

// Hata yakalama
window.addEventListener('error', function(e) {
    console.error('JavaScript hatası:', e.error);
    showToast('Bir hata oluştu. Lütfen sayfayı yenileyin.', 'error');
});

// Çevrimdışı destek
window.addEventListener('online', function() {
    showToast('İnternet bağlantısı geri geldi', 'success');
});

window.addEventListener('offline', function() {
    showToast('İnternet bağlantısı kesildi', 'warning');
});