// تهيئة التطبيق عند تحميل الصفحة
document.addEventListener('DOMContentLoaded', function() {
    initializeTheme();
    initializeScrollToTop();
    loadFeaturedEvents();
    loadLatestEvents();
    initializeBookingSystem();
    initializeContactForm();
    initializeSearchFilter();
});

// ==================== نظام Dark Mode ====================
function initializeTheme() {
    const themeToggle = document.getElementById('themeToggle');
    if (!themeToggle) return;
    
    const currentTheme = localStorage.getItem('theme') || 'light';
    applyTheme(currentTheme);
    
    themeToggle.addEventListener('click', function() {
        const currentTheme = document.documentElement.getAttribute('data-theme');
        const newTheme = currentTheme === 'light' ? 'dark' : 'light';
        applyTheme(newTheme);
        localStorage.setItem('theme', newTheme);
    });
}

function applyTheme(theme) {
    document.documentElement.setAttribute('data-theme', theme);
    const themeToggle = document.getElementById('themeToggle');
    if (themeToggle) {
        themeToggle.innerHTML = theme === 'light' ? '🌙' : '☀️';
        themeToggle.title = theme === 'light' ? 'تفعيل الوضع الليلي' : 'تفعيل الوضع النهاري';
    }
}

// ==================== زر العودة للأعلى ====================
function initializeScrollToTop() {
    const scrollButton = document.getElementById('scrollToTop');
    if (!scrollButton) return;
    
    window.addEventListener('scroll', function() {
        if (window.pageYOffset > 300) {
            scrollButton.classList.add('show');
        } else {
            scrollButton.classList.remove('show');
        }
    });
    
    scrollButton.addEventListener('click', function() {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });
}

// ==================== تحميل الفعاليات ====================
async function loadFeaturedEvents() {
    try {
        const response = await fetch('api_get_events.php?limit=5');
        const events = await response.json();
        displayFeaturedEvents(events);
    } catch (error) {
        console.error('Error loading featured events:', error);
    }
}

async function loadLatestEvents() {
    try {
        const response = await fetch('api_get_events.php');
        const events = await response.json();
        displayLatestEvents(events);
    } catch (error) {
        console.error('Error loading latest events:', error);
    }
}

function displayFeaturedEvents(events) {
    const sliderContent = document.getElementById('sliderContent');
    if (!sliderContent || !events.length) return;
    
    let slidesHTML = '';
    events.forEach((event, index) => {
        const activeClass = index === 0 ? 'active' : '';
        slidesHTML += `
            <div class="carousel-item ${activeClass}">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <img src="${event.image}" class="d-block w-100 rounded" alt="${event.title}" style="height: 300px; object-fit: cover;">
                    </div>
                    <div class="col-md-6">
                        <h3>${event.title}</h3>
                        <p>${event.description.substring(0, 150)}...</p>
                        <p><strong>📅 ${formatDate(event.event_date)}</strong></p>
                        <p><strong>📍 ${event.location}</strong></p>
                        <a href="event.php?id=${event.id}" class="btn btn-light">عرض التفاصيل</a>
                    </div>
                </div>
            </div>
        `;
    });
    
    sliderContent.innerHTML = slidesHTML;
}

function displayLatestEvents(events) {
    const eventsGrid = document.getElementById('eventsGrid');
    if (!eventsGrid) return;
    
    if (!events.length) {
        eventsGrid.innerHTML = '<div class="col-12"><p class="text-center">لا توجد فعاليات متاحة حالياً.</p></div>';
        return;
    }
    
    let eventsHTML = '';
    events.slice(0, 6).forEach(event => {
        eventsHTML += `
            <div class="col-md-4 mb-4 fade-in">
                <div class="card h-100">
                    <img src="${event.image}" class="card-img-top" alt="${event.title}">
                    <div class="card-body">
                        <h5 class="card-title">${event.title}</h5>
                        <p class="card-text">${event.description.substring(0, 100)}...</p>
                        <p class="text-muted">
                            <small>📅 ${formatDate(event.event_date)}</small><br>
                            <small>📍 ${event.location}</small><br>
                            <small>🏷️ ${event.category}</small>
                        </p>
                    </div>
                    <div class="card-footer bg-transparent border-0">
                        <a href="event.php?id=${event.id}" class="btn btn-primary me-2">التفاصيل</a>
                        <button class="btn btn-success book-event" 
                                data-event-id="${event.id}" 
                                data-event-title="${event.title}">
                            احجز الآن
                        </button>
                    </div>
                </div>
            </div>
        `;
    });
    
    eventsGrid.innerHTML = eventsHTML;
    initializeBookingButtons(); // إعادة تهيئة أزرار الحجز للأحداث الجديدة
}

// ==================== نظام الحجز ====================
function initializeBookingSystem() {
    initializeBookingButtons();
    initializeBookingModal();
}

function initializeBookingButtons() {
    const bookingButtons = document.querySelectorAll('.book-event');
    bookingButtons.forEach(button => {
        button.addEventListener('click', function() {
            const eventId = this.getAttribute('data-event-id');
            const eventTitle = this.getAttribute('data-event-title');
            openBookingModal(eventId, eventTitle);
        });
    });
}

function initializeBookingModal() {
    const bookingForm = document.getElementById('bookingForm');
    if (bookingForm) {
        bookingForm.addEventListener('submit', handleBookingSubmit);
    }
}

function openBookingModal(eventId, eventTitle) {
    const modalElement = document.getElementById('bookingModal');
    if (!modalElement) return;
    
    document.getElementById('bookingEventTitle').textContent = eventTitle;
    document.getElementById('eventId').value = eventId;
    
    const modal = new bootstrap.Modal(modalElement);
    modal.show();
}

async function handleBookingSubmit(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const submitButton = this.querySelector('button[type="submit"]');
    const originalText = submitButton.textContent;
    
    // تعطيل الزر أثناء المعالجة
    submitButton.disabled = true;
    submitButton.textContent = 'جاري الحجز...';
    
    try {
        const response = await fetch('book_event.php', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            showAlert(result.message, 'success');
            const modal = bootstrap.Modal.getInstance(document.getElementById('bookingModal'));
            modal.hide();
            this.reset();
        } else {
            showAlert(result.message, 'danger');
        }
    } catch (error) {
        showAlert('حدث خطأ في الاتصال بالخادم', 'danger');
    } finally {
        submitButton.disabled = false;
        submitButton.textContent = originalText;
    }
}

// ==================== نموذج الاتصال ====================
function initializeContactForm() {
    const contactForm = document.getElementById('contactForm');
    if (!contactForm) return;
    
    contactForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        if (!validateContactForm()) {
            return;
        }
        
        // إرسال النموذج
        this.submit();
    });
}

function validateContactForm() {
    const name = document.getElementById('name').value.trim();
    const email = document.getElementById('email').value.trim();
    const message = document.getElementById('message').value.trim();
    
    // إعادة تعيين الصفوف غير صالحة
    document.querySelectorAll('.is-invalid').forEach(el => {
        el.classList.remove('is-invalid');
    });
    
    let isValid = true;
    
    if (!name) {
        document.getElementById('name').classList.add('is-invalid');
        isValid = false;
    }
    
    if (!email || !validateEmail(email)) {
        document.getElementById('email').classList.add('is-invalid');
        isValid = false;
    }
    
    if (!message) {
        document.getElementById('message').classList.add('is-invalid');
        isValid = false;
    }
    
    return isValid;
}

// ==================== نظام البحث والتصفية ====================
function initializeSearchFilter() {
    const searchInput = document.getElementById('searchInput');
    const categoryFilter = document.getElementById('categoryFilter');
    const dateFilter = document.getElementById('dateFilter');
    
    if (searchInput) {
        searchInput.addEventListener('input', filterEvents);
    }
    
    if (categoryFilter) {
        categoryFilter.addEventListener('change', filterEvents);
    }
    
    if (dateFilter) {
        dateFilter.addEventListener('change', filterEvents);
    }
}

function filterEvents() {
    const searchTerm = document.getElementById('searchInput')?.value.toLowerCase() || '';
    const category = document.getElementById('categoryFilter')?.value || '';
    const date = document.getElementById('dateFilter')?.value || '';
    
    const eventCards = document.querySelectorAll('.event-card');
    
    eventCards.forEach(card => {
        const title = card.querySelector('.card-title').textContent.toLowerCase();
        const cardCategory = card.getAttribute('data-category');
        const cardDate = card.getAttribute('data-date').split(' ')[0];
        
        const matchesSearch = title.includes(searchTerm);
        const matchesCategory = !category || cardCategory === category;
        const matchesDate = !date || cardDate === date;
        
        if (matchesSearch && matchesCategory && matchesDate) {
            card.style.display = 'block';
            card.classList.add('fade-in');
        } else {
            card.style.display = 'none';
        }
    });
}

// ==================== وظائف مساعدة ====================
function validateEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}

function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('ar-SA', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}

function showAlert(message, type) {
    // إزالة التنبيهات القديمة
    const oldAlerts = document.querySelectorAll('.custom-alert');
    oldAlerts.forEach(alert => alert.remove());
    
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} custom-alert alert-dismissible fade show`;
    alertDiv.style.position = 'fixed';
    alertDiv.style.top = '20px';
    alertDiv.style.right = '20px';
    alertDiv.style.zIndex = '1060';
    alertDiv.style.minWidth = '300px';
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(alertDiv);
    
    // إخفاء التنبيه تلقائياً بعد 5 ثوان
    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.remove();
        }
    }, 5000);
}

// ==================== إدارة حالة التحميل ====================
function showLoading() {
    let loadingDiv = document.getElementById('loadingSpinner');
    if (!loadingDiv) {
        loadingDiv = document.createElement('div');
        loadingDiv.id = 'loadingSpinner';
        loadingDiv.className = 'd-flex justify-content-center align-items-center';
        loadingDiv.style.position = 'fixed';
        loadingDiv.style.top = '0';
        loadingDiv.style.left = '0';
        loadingDiv.style.width = '100%';
        loadingDiv.style.height = '100%';
        loadingDiv.style.backgroundColor = 'rgba(0,0,0,0.5)';
        loadingDiv.style.zIndex = '9999';
        loadingDiv.innerHTML = `
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">جاري التحميل...</span>
            </div>
        `;
        document.body.appendChild(loadingDiv);
    }
}

function hideLoading() {
    const loadingDiv = document.getElementById('loadingSpinner');
    if (loadingDiv) {
        loadingDiv.remove();
    }
}

// ==================== تحسينات UX ====================
// إضافة تأثيرات عند التمرير
window.addEventListener('scroll', function() {
    const elements = document.querySelectorAll('.fade-in');
    elements.forEach(element => {
        const position = element.getBoundingClientRect();
        if (position.top < window.innerHeight - 100) {
            element.style.opacity = '1';
            element.style.transform = 'translateY(0)';
        }
    });
});

// تهيئة العناصر المتحركة
document.querySelectorAll('.fade-in').forEach(el => {
    el.style.opacity = '0';
    el.style.transform = 'translateY(20px)';
    el.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
});

console.log('✅ System initialized successfully');