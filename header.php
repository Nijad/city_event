<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>دليل فعاليات المدينة</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/styles.css">
    
    <!-- تحميل التفضيل قبل تحميل CSS -->
    <script>
        // تحميل التفضيل فوراً لمنع الوميض
        (function() {
            const savedTheme = localStorage.getItem('theme') || 'light';
            document.documentElement.setAttribute('data-theme', savedTheme);
        })();
    </script>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container">
        <a class="navbar-brand" href="index.php">
            <strong>🏙️ دليل الفعاليات</strong>
        </a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item"><a class="nav-link" href="info.php">nijad_203834</a></li>
                <li class="nav-item"><a class="nav-link" href="index.php">الرئيسية</a></li>
                <li class="nav-item"><a class="nav-link" href="events.php">الفعاليات</a></li>
                <li class="nav-item"><a class="nav-link" href="about.php">عن الدليل</a></li>
                <li class="nav-item"><a class="nav-link" href="contact.php">اتصل بنا</a></li>
                <?php if(isset($_SESSION['admin'])): ?>
                <li class="nav-item"><a class="nav-link text-warning" href="admin/dashboard.php">لوحة التحكم</a></li>
                <?php endif; ?>
            </ul>
            
            <div class="d-flex align-items-center">
                <button id="themeToggle" class="btn theme-toggle-btn" type="button">
                    <span id="themeIcon">🌙</span>
                </button>
            </div>
        </div>
    </div>
</nav>

<!-- Script فوري للوضع الليلي -->
<script>
// تهيئة فورية عند تحميل الصفحة
document.addEventListener('DOMContentLoaded', function() {
    console.log('=== بدء تهيئة الوضع الليلي ===');
    
    const themeToggle = document.getElementById('themeToggle');
    const themeIcon = document.getElementById('themeIcon');
    
    if (!themeToggle || !themeIcon) {
        console.error('❌ عناصر الوضع الليلي غير موجودة!');
        return;
    }
    
    console.log('✅ عناصر الوضع الليلي موجودة');
    
    // الحصول على الوضع الحالي
    let currentTheme = localStorage.getItem('theme');
    if (!currentTheme) {
        currentTheme = 'light';
        localStorage.setItem('theme', currentTheme);
    }
    
    console.log('🎯 الوضع الحالي:', currentTheme);
    
    // تطبيق الوضع الحالي
    applyTheme(currentTheme);
    
    // إضافة مستمع الحدث
    themeToggle.addEventListener('click', function(event) {
        event.preventDefault();
        event.stopPropagation();
        
        console.log('🖱️ تم النقر على زر الوضع الليلي');
        
        // تبديل الوضع
        currentTheme = currentTheme === 'light' ? 'dark' : 'light';
        console.log('🔄 الوضع الجديد:', currentTheme);
        
        // تطبيق التغييرات
        applyTheme(currentTheme);
        localStorage.setItem('theme', currentTheme);
    });
    
    function applyTheme(theme) {
        console.log('🎨 تطبيق الوضع:', theme);
        
        // تغيير سمة HTML
        document.documentElement.setAttribute('data-theme', theme);
        
        // تحديث الأيقونة
        themeIcon.textContent = theme === 'light' ? '🌙' : '☀️';
        themeIcon.title = theme === 'light' ? 'تفعيل الوضع الليلي' : 'تفعيل الوضع النهاري';
        
        // إضافة class للمساعدة في التصحيح
        document.body.classList.remove('theme-light', 'theme-dark');
        document.body.classList.add('theme-' + theme);
        
        console.log('✅ تم تطبيق الوضع بنجاح');
    }
    
    // اختبار الوظيفة بعد التحميل
    setTimeout(() => {
        console.log('🧪 اختبار الوضع الليلي:', {
            theme: currentTheme,
            htmlTheme: document.documentElement.getAttribute('data-theme'),
            localStorage: localStorage.getItem('theme'),
            icon: themeIcon.textContent
        });
    }, 100);
});

// أيضًا جعل الوظيفة متاحة globally للاختبار
window.toggleTheme = function() {
    const currentTheme = localStorage.getItem('theme') || 'light';
    const newTheme = currentTheme === 'light' ? 'dark' : 'light';
    localStorage.setItem('theme', newTheme);
    document.documentElement.setAttribute('data-theme', newTheme);
    
    const themeIcon = document.getElementById('themeIcon');
    if (themeIcon) {
        themeIcon.textContent = newTheme === 'light' ? '🌙' : '☀️';
    }
    
    console.log('🔧 تم تبديل الوضع يدويًا إلى:', newTheme);
};
</script>