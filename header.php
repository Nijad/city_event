<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>دليل فعاليات المدينة</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="index.php">
            <strong>🏙️ دليل الفعاليات</strong>
        </a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link" href="index.php">الرئيسية</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="events.php">الفعاليات</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="about.php">عن الدليل</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="contact.php">اتصل بنا</a>
                </li>
                <?php if(isset($_SESSION['admin'])): ?>
                <li class="nav-item">
                    <a class="nav-link text-warning" href="admin/dashboard.php">لوحة التحكم</a>
                </li>
                <?php endif; ?>
            </ul>
            
            <div class="d-flex align-items-center">
                <button id="themeToggle" class="theme-toggle btn btn-outline-light">🌙</button>
            </div>
        </div>
    </div>
</nav>