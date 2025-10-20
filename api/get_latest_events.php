<?php

header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../functions/images.php'; // إذا تملك دالة getEventImage

try {
    // آخر 4 إضافات بناءً على id تنازلي (أو created_at إذا متوفر)
    $stmt = $pdo->prepare("SELECT id, title, description, image, event_date, location, category FROM events WHERE status = 'active' ORDER BY id DESC LIMIT 4");
    $stmt->execute();
    $events = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Normalize image path if helper موجود
    foreach ($events as &$e) {
        if (function_exists('getEventImage')) {
            $e['image'] = getEventImage($e['image']);
        } else {
            $e['image'] = $e['image'] ? $e['image'] : 'assets/img/default-event.jpg';
        }
    }

    echo json_encode($events, JSON_UNESCAPED_UNICODE);
} catch (Exception $ex) {
    http_response_code(500);
    echo json_encode(['error' => 'Server error']);
}
?>