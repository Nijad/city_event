<?php
include 'db.php';
header('Content-Type: application/json');

try {
    $category = $_GET['category'] ?? '';
    $date = $_GET['date'] ?? '';
    
    $sql = "SELECT * FROM events WHERE event_date >= CURDATE()";
    $params = [];
    
    if (!empty($category)) {
        $sql .= " AND category = ?";
        $params[] = $category;
    }
    
    if (!empty($date)) {
        $sql .= " AND DATE(event_date) = ?";
        $params[] = $date;
    }
    
    $sql .= " ORDER BY event_date ASC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $events = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($events);
    
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>