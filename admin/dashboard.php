<?php
session_start();
include '../db.php';

// التحقق من تسجيل الدخول
if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}

// إحصائيات سريعة
$stats = [
    'total_events' => $pdo->query("SELECT COUNT(*) FROM events")->fetchColumn(),
    'upcoming_events' => $pdo->query("SELECT COUNT(*) FROM events WHERE event_date >= CURDATE()")->fetchColumn(),
    'total_bookings' => $pdo->query("SELECT COUNT(*) FROM bookings")->fetchColumn(),
    'today_bookings' => $pdo->query("SELECT COUNT(*) FROM bookings WHERE DATE(booking_date) = CURDATE()")->fetchColumn()
];

// الفعاليات القادمة
$upcoming_events = $pdo->query("SELECT * FROM events WHERE event_date >= CURDATE() ORDER BY event_date ASC LIMIT 5")->fetchAll();

// آخر الحجوزات
$recent_bookings = $pdo->query("
    SELECT b.*, e.title as event_title 
    FROM bookings b 
    LEFT JOIN events e ON b.event_id = e.id 
    ORDER BY b.booking_date DESC 
    LIMIT 5
")->fetchAll();
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>لوحة التحكم - الرئيسية</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/styles.css">
    <style>
        .stat-card {
            border-radius: 10px;
            transition: transform 0.3s ease;
        }
        .stat-card:hover {
            transform: translateY(-5px);
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- الشريط الجانبي -->
            <?php include 'sidebar.php'; ?>
            <!-- المحتوى الرئيسي -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <!-- شريط الأدوات -->
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">الرئيسية</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <span class="text-muted">آخر دخول: <?= date('Y-m-d H:i') ?></span>
                    </div>
                </div>

                <!-- الإحصائيات -->
                <div class="row mb-4">
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card stat-card border-left-primary shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                            إجمالي الفعاليات
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                                            <?= $stats['total_events'] ?>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-calendar fa-2x text-gray-300">🎪</i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card stat-card border-left-success shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                            فعاليات قادمة
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                                            <?= $stats['upcoming_events'] ?>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-calendar-check fa-2x text-gray-300">📅</i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card stat-card border-left-info shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                            إجمالي الحجوزات
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                                            <?= $stats['total_bookings'] ?>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-ticket-alt fa-2x text-gray-300">🎫</i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card stat-card border-left-warning shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                            حجوزات اليوم
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                                            <?= $stats['today_bookings'] ?>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-users fa-2x text-gray-300">👥</i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- الفعاليات القادمة -->
                    <div class="col-lg-6 mb-4">
                        <div class="card shadow">
                            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                                <h6 class="m-0 font-weight-bold text-primary">الفعاليات القادمة</h6>
                                <a href="events.php" class="btn btn-sm btn-primary">عرض الكل</a>
                            </div>
                            <div class="card-body">
                                <?php if ($upcoming_events): ?>
                                    <div class="list-group list-group-flush">
                                        <?php foreach ($upcoming_events as $event): ?>
                                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                                <div>
                                                    <h6 class="mb-1"><?= $event['title'] ?></h6>
                                                    <small class="text-muted">
                                                        📅 <?= date('Y-m-d', strtotime($event['event_date'])) ?> 
                                                        | 📍 <?= $event['location'] ?>
                                                    </small>
                                                </div>
                                                <a href="edit_event.php?id=<?= $event['id'] ?>" class="btn btn-sm btn-outline-primary">تعديل</a>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php else: ?>
                                    <p class="text-muted text-center">لا توجد فعاليات قادمة</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- آخر الحجوزات -->
                    <div class="col-lg-6 mb-4">
                        <div class="card shadow">
                            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                                <h6 class="m-0 font-weight-bold text-success">آخر الحجوزات</h6>
                                <a href="bookings.php" class="btn btn-sm btn-success">عرض الكل</a>
                            </div>
                            <div class="card-body">
                                <?php if ($recent_bookings): ?>
                                    <div class="list-group list-group-flush">
                                        <?php foreach ($recent_bookings as $booking): ?>
                                            <div class="list-group-item">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <h6 class="mb-1"><?= $booking['name'] ?></h6>
                                                        <small class="text-muted">
                                                            📧 <?= $booking['email'] ?> | 
                                                            📞 <?= $booking['phone'] ?> |
                                                            🎫 <?= $booking['tickets'] ?> تذاكر
                                                        </small>
                                                        <br>
                                                        <small>لفعالية: <?= $booking['event_title'] ?></small>
                                                    </div>
                                                    <small><?= date('Y-m-d', strtotime($booking['booking_date'])) ?></small>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php else: ?>
                                    <p class="text-muted text-center">لا توجد حجوزات حديثة</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>