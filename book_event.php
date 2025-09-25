<?php
include 'db.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $event_id = $_POST['event_id'] ?? '';
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $tickets = $_POST['tickets'] ?? 1;
    
    // التحقق من البيانات
    if (empty($event_id) || empty($name) || empty($email) || empty($phone)) {
        echo json_encode(['success' => false, 'message' => 'جميع الحقول مطلوبة']);
        exit;
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'البريد الإلكتروني غير صحيح']);
        exit;
    }
    
    try {
        // التحقق من وجود الفعالية
        $stmt = $pdo->prepare("SELECT id FROM events WHERE id = ?");
        $stmt->execute([$event_id]);
        $event = $stmt->fetch();
        
        if (!$event) {
            echo json_encode(['success' => false, 'message' => 'الفعالية غير موجودة']);
            exit;
        }
        
        // إدخال الحجز
        $stmt = $pdo->prepare("INSERT INTO bookings (event_id, name, email, phone, tickets) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$event_id, $name, $email, $phone, $tickets]);
        
        echo json_encode(['success' => true, 'message' => 'تم الحجز بنجاح! سنتواصل معك قريباً.']);
        
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'خطأ في الحجز: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'طريقة غير مسموحة']);
}
?>