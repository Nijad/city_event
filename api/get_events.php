<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}

include '../db.php';

$stmt = $pdo->query("
    SELECT b.*, e.title as event_title 
    FROM bookings b 
    LEFT JOIN events e ON b.event_id = e.id 
    ORDER BY b.booking_date DESC
");
$bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>إدارة الحجوزات</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>إدارة الحجوزات</h2>
        <a href="dashboard.php" class="btn btn-secondary">العودة للفعاليات</a>
        
        <table class="table table-bordered mt-3">
            <thead>
                <tr>
                    <th>#</th>
                    <th>الفعالية</th>
                    <th>الاسم</th>
                    <th>البريد</th>
                    <th>الهاتف</th>
                    <th>عدد التذاكر</th>
                    <th>تاريخ الحجز</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($bookings as $booking): ?>
                <tr>
                    <td><?= $booking['id'] ?></td>
                    <td><?= $booking['event_title'] ?></td>
                    <td><?= $booking['name'] ?></td>
                    <td><?= $booking['email'] ?></td>
                    <td><?= $booking['phone'] ?></td>
                    <td><?= $booking['tickets'] ?></td>
                    <td><?= $booking['booking_date'] ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>