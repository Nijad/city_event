<?php
session_start();
include '../db.php';

if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}

// جلب جميع الحجوزات
$bookings = $pdo->query("
    SELECT b.*, e.title as event_title, e.event_date 
    FROM bookings b 
    LEFT JOIN events e ON b.event_id = e.id 
    ORDER BY b.booking_date DESC
")->fetchAll();

// معالجة حذف الحجز
if (isset($_GET['delete'])) {
    $booking_id = $_GET['delete'];

    try {
        $stmt = $pdo->prepare("DELETE FROM bookings WHERE id = ?");
        $stmt->execute([$booking_id]);

        header('Location: bookings.php?success=تم حذف الحجز بنجاح');
        exit;
    } catch (PDOException $e) {
        header('Location: bookings.php?error=حدث خطأ أثناء حذف الحجز');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إدارة الحجوزات - لوحة التحكم</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <?php include 'sidebar.php'; ?>

            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">إدارة الحجوزات</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <a href="dashboard.php" class="btn btn-outline-secondary">← العودة للرئيسية</a>
                    </div>
                </div>

                <?php if (isset($_GET['success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show">
                        <?= $_GET['success'] ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if (isset($_GET['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show">
                        <?= $_GET['error'] ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <div class="card shadow">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">قائمة الحجوزات</h5>
                        <span class="badge bg-primary">إجمالي الحجوزات: <?= count($bookings) ?></span>
                    </div>
                    <div class="card-body">
                        <?php if ($bookings): ?>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>المستخدم</th>
                                            <th>الفعالية</th>
                                            <th>معلومات الاتصال</th>
                                            <th>التذاكر</th>
                                            <th>تاريخ الحجز</th>
                                            <th>الإجراءات</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($bookings as $booking): ?>
                                            <tr>
                                                <td><?= $booking['id'] ?></td>
                                                <td>
                                                    <strong><?= $booking['name'] ?></strong>
                                                </td>
                                                <td>
                                                    <a href="../event.php?id=<?= $booking['event_id'] ?>" target="_blank">
                                                        <?= $booking['event_title'] ?>
                                                    </a>
                                                    <br>
                                                    <small class="text-muted">
                                                        📅 <?= date('Y-m-d', strtotime($booking['event_date'])) ?>
                                                    </small>
                                                </td>
                                                <td>
                                                    📧 <?= $booking['email'] ?>
                                                    <br>
                                                    📞 <?= $booking['phone'] ?>
                                                </td>
                                                <td>
                                                    <span class="badge bg-success"><?= $booking['tickets'] ?> تذاكر</span>
                                                </td>
                                                <td>
                                                    <?= date('Y-m-d H:i', strtotime($booking['booking_date'])) ?>
                                                    <br>
                                                    <small class="text-muted">
                                                        منذ <?= time_elapsed_string($booking['booking_date']) ?>
                                                    </small>
                                                </td>
                                                <td>
                                                    <div class="btn-group btn-group-sm">
                                                        <button type="button" class="btn btn-outline-info"
                                                            onclick="showBookingDetails(<?= htmlspecialchars(json_encode($booking)) ?>)"
                                                            style="border-radius: 0 10px 10px 0;">
                                                            📋 التفاصيل
                                                        </button>
                                                        <button onclick="confirmDelete(<?= $booking['id'] ?>)"
                                                            class="btn btn-outline-danger"
                                                            style="border-radius: 10px 0 0 10px;">
                                                            🗑️ حذف
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-5">
                                <h5 class="text-muted">لا توجد حجوزات</h5>
                                <p class="text-muted">لم يتم إجراء أي حجوزات حتى الآن</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Modal لعرض تفاصيل الحجز -->
    <div class="modal fade" id="bookingDetailsModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">تفاصيل الحجز</h5>
                    <button type="button" class="btn-close m-0" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="bookingDetailsContent">
                    <!-- سيتم ملؤها بالجافاسكريبت -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function showBookingDetails(booking) {
            const content = `
            <div class="row">
                <div class="col-6">
                    <strong>الاسم:</strong>
                </div>
                <div class="col-6">
                    ${booking.name}
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-6">
                    <strong>البريد الإلكتروني:</strong>
                </div>
                <div class="col-6">
                    ${booking.email}
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-6">
                    <strong>الهاتف:</strong>
                </div>
                <div class="col-6">
                    ${booking.phone}
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-6">
                    <strong>عدد التذاكر:</strong>
                </div>
                <div class="col-6">
                    ${booking.tickets}
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-6">
                    <strong>الفعالية:</strong>
                </div>
                <div class="col-6">
                    ${booking.event_title}
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-6">
                    <strong>تاريخ الحجز:</strong>
                </div>
                <div class="col-6">
                    ${new Date(booking.booking_date).toLocaleString('ar-SA')}
                </div>
            </div>
        `;

            document.getElementById('bookingDetailsContent').innerHTML = content;
            new bootstrap.Modal(document.getElementById('bookingDetailsModal')).show();
        }

        function confirmDelete(bookingId) {
            if (confirm('هل أنت متأكد من حذف هذا الحجز؟')) {
                window.location.href = 'bookings.php?delete=' + bookingId;
            }
        }
    </script>
</body>

</html>

<?php
// دالة مساعدة لعرض الوقت المنقضي
function time_elapsed_string($datetime, $full = false)
{
    $now = new DateTime;
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);

    $diff->w = floor($diff->d / 7);
    $diff->d -= $diff->w * 7;

    $string = array(
        'y' => 'سنة',
        'm' => 'شهر',
        'w' => 'أسبوع',
        'd' => 'يوم',
        'h' => 'ساعة',
        'i' => 'دقيقة',
        's' => 'ثانية',
    );

    foreach ($string as $k => &$v) {
        if ($diff->$k) {
            $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? '' : '');
        } else {
            unset($string[$k]);
        }
    }

    if (!$full) $string = array_slice($string, 0, 1);
    return $string ? 'منذ ' . implode(', ', $string) : 'الآن';
}
?>