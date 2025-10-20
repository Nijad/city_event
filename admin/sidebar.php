<?php
// ملف الشريط الجانبي الذي يتم تضمينه في جميع صفحات لوحة التحكم
?>
<nav class="col-md-3 col-lg-2 d-md-block sidebar collapse bg-dark">
    <div class="position-sticky pt-3">
        <div class="text-center text-white p-3 border-bottom">
            <h5>🚀 لوحة التحكم</h5>
            <small>مرحباً، <?= $_SESSION['admin']['username'] ?? 'زائر' ?></small>
            <br>
            <small>دليل فعاليات المدينة</small>
        </div>
        
        <ul class="nav flex-column mt-3">
            <li class="nav-item">
                <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : '' ?>" 
                    href="dashboard.php">
                    📊 الرئيسية
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'events.php' ? 'active' : '' ?>" 
                    href="events.php">
                    🎪 إدارة الفعاليات
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'bookings.php' ? 'active' : '' ?>" 
                    href="bookings.php">
                    📋 إدارة الحجوزات
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'add_event.php' ? 'active' : '' ?>" 
                    href="add_event.php">
                    ➕ إضافة فعالية
                </a>
            </li>
            <li class="nav-item mt-4 border-top pt-3">
                <a class="nav-link text-warning" href="../index.php" target="_blank">
                    👁️ معاينة الموقع
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-danger" href="logout.php">
                    🚪 تسجيل الخروج
                </a>
            </li>
        </ul>
        
        <!-- إحصائيات سريعة -->
        <div class="mt-4 p-3 border-top">
            <small class="text-white d-block">إحصائيات سريعة:</small>
            <small class="text-white">
                <?php
                if (isset($pdo)) {
                    $total_events = $pdo->query("SELECT COUNT(*) FROM events")->fetchColumn();
                    $total_bookings = $pdo->query("SELECT COUNT(*) FROM bookings")->fetchColumn();
                    echo "الفعاليات: $total_events<br>الحجوزات: $total_bookings";
                }
                ?>
            </small>
        </div>
    </div>
</nav>