<?php
include '../db.php';
header('Content-Type: application/json');

try {
    // جلب الفعاليات البارزة (الأسبوع القادم)
    $stmt = $pdo->prepare("SELECT * FROM events WHERE event_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY) AND status = 'active' ORDER BY event_date ASC LIMIT 5");
    $stmt->execute();
    $events = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // تحسين البيانات للإرجاع
    foreach ($events as &$event) {
        $event['event_date'] = date('Y-m-d H:i', strtotime($event['event_date']));
        $event['image'] = $event['image'] ?: 'assets/img/default-event.jpg';
    }
    
    echo json_encode($events);
    
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>