<?php
session_start();
include '../db.php';
include '../functions/images.php';

if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}

$event_id = $_GET['id'] ?? 0;

if ($event_id) {
    try {
        // جلب اسم الصورة قبل الحذف
        $stmt = $pdo->prepare("SELECT image FROM events WHERE id = ?");
        $stmt->execute([$event_id]);
        $event = $stmt->fetch();
        
        if ($event) {
            // حذف الصورة من الخادم
            deleteEventImage($event['image']);
            
            // حذف الفعالية من قاعدة البيانات
            $stmt = $pdo->prepare("DELETE FROM events WHERE id = ?");
            $stmt->execute([$event_id]);
            
            header('Location: events.php?success=تم حذف الفعالية بنجاح');
        } else {
            header('Location: events.php?error=الفعالية غير موجودة');
        }
    } catch (PDOException $e) {
        header('Location: events.php?error=حدث خطأ أثناء الحذف: ' . $e->getMessage());
    }
} else {
    header('Location: events.php?error=معرف الفعالية غير صحيح');
}
exit;
?>