// وظائف خاصة بصفحة الفعاليات
document.addEventListener('DOMContentLoaded', function() {
    initializeEventFilters();
    initializeEventActions();
});

function initializeEventFilters() {
    const searchInput = document.getElementById('searchInput');
    const categoryFilter = document.getElementById('categoryFilter');
    const dateFilter = document.getElementById('dateFilter');
    
    if (searchInput) {
        searchInput.addEventListener('input', debounce(filterEvents, 300));
    }
    
    if (categoryFilter) {
        categoryFilter.addEventListener('change', filterEvents);
    }
    
    if (dateFilter) {
        dateFilter.addEventListener('change', filterEvents);
    }
}

function initializeEventActions() {
    // تهيئة أزرار المشاركة
    const shareButtons = document.querySelectorAll('.share-btn');
    shareButtons.forEach(btn => {
        btn.addEventListener('click', shareEvent);
    });
    
    // تهيئة أزرار التقويم
    const calendarButtons = document.querySelectorAll('.calendar-btn');
    calendarButtons.forEach(btn => {
        btn.addEventListener('click', addToCalendar);
    });
}

function filterEvents() {
    const searchTerm = document.getElementById('searchInput')?.value.toLowerCase() || '';
    const category = document.getElementById('categoryFilter')?.value || '';
    const date = document.getElementById('dateFilter')?.value || '';
    
    const eventCards = document.querySelectorAll('.event-card');
    let visibleCount = 0;
    
    eventCards.forEach(card => {
        const title = card.querySelector('.card-title')?.textContent.toLowerCase() || '';
        const description = card.querySelector('.card-text')?.textContent.toLowerCase() || '';
        const cardCategory = card.getAttribute('data-category') || '';
        const cardDate = card.getAttribute('data-date')?.split(' ')[0] || '';
        
        const matchesSearch = title.includes(searchTerm) || description.includes(searchTerm);
        const matchesCategory = !category || cardCategory === category;
        const matchesDate = !date || cardDate === date;
        
        if (matchesSearch && matchesCategory && matchesDate) {
            card.style.display = 'block';
            visibleCount++;
        } else {
            card.style.display = 'none';
        }
    });
    
    // عرض رسالة إذا لم توجد نتائج
    const noResults = document.getElementById('noResults');
    if (!noResults && visibleCount === 0) {
        const eventsList = document.getElementById('eventsList');
        const message = document.createElement('div');
        message.id = 'noResults';
        message.className = 'col-12 text-center py-5';
        message.innerHTML = `
            <div class="alert alert-info">
                <h4>لا توجد نتائج</h4>
                <p>لم نعثر على فعاليات تطابق معايير البحث الخاصة بك.</p>
                <button onclick="clearFilters()" class="btn btn-primary">مسح الفلاتر</button>
            </div>
        `;
        eventsList.appendChild(message);
    } else if (noResults && visibleCount > 0) {
        noResults.remove();
    }
}

function clearFilters() {
    document.getElementById('searchInput').value = '';
    document.getElementById('categoryFilter').value = '';
    document.getElementById('dateFilter').value = '';
    filterEvents();
}

function shareEvent(event) {
    const eventCard = event.target.closest('.event-card');
    const eventId = eventCard.getAttribute('data-event-id');
    const eventTitle = eventCard.querySelector('.card-title').textContent;
    const eventUrl = `${window.location.origin}/event.php?id=${eventId}`;
    
    if (navigator.share) {
        navigator.share({
            title: eventTitle,
            text: 'تفضل بمشاهدة هذه الفعالية المميزة',
            url: eventUrl
        });
    } else {
        // نسخ الرابط إلى الحافظة
        navigator.clipboard.writeText(eventUrl).then(() => {
            showAlert('تم نسخ رابط الفعالية إلى الحافظة', 'success');
        });
    }
}

function addToCalendar(event) {
    const eventCard = event.target.closest('.event-card');
    const eventDate = eventCard.getAttribute('data-event-date');
    const eventTitle = eventCard.querySelector('.card-title').textContent;
    const eventLocation = eventCard.querySelector('[data-location]')?.getAttribute('data-location') || '';
    
    // إنشاء رابط تقويم Google
    const startDate = new Date(eventDate).toISOString().replace(/-|:|\.\d+/g, '');
    const endDate = new Date(new Date(eventDate).getTime() + 2 * 60 * 60 * 1000).toISOString().replace(/-|:|\.\d+/g, '');
    
    const calendarUrl = `https://calendar.google.com/calendar/render?action=TEMPLATE&dates=${startDate}/${endDate}&text=${encodeURIComponent(eventTitle)}&location=${encodeURIComponent(eventLocation)}`;
    
    window.open(calendarUrl, '_blank');
}

// وظيفة Debounce لتحسين الأداء
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

// تحميل المزيد من الفعاليات (للتطوير المستقبلي)
function loadMoreEvents() {
    const currentCount = document.querySelectorAll('.event-card:not([style*="display: none"])').length;
    
    // محاكاة تحميل المزيد من البيانات
    showLoading();
    
    setTimeout(() => {
        // هنا سيتم جلب البيانات من الخادم
        hideLoading();
        showAlert('تم تحميل المزيد من الفعاليات', 'success');
    }, 1000);
}