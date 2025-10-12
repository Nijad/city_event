<?php
// debug_booking.php - وضع في المجلد الرئيسي
include 'db.php';

echo "<h3>🔍 فحص نظام الحجوزات</h3>";

// فحص جدول الحجوزات
try {
    $stmt = $pdo->query("SHOW TABLES LIKE 'bookings'");
    $table_exists = $stmt->fetch();

    if ($table_exists) {
        echo "<p style='color: green;'>✅ جدول الحجوزات موجود</p>";

        // فحص هيكل الجدول
        $stmt = $pdo->query("DESCRIBE bookings");
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo "<h4>هيكل جدول الحجوزات:</h4>";
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th></tr>";
        foreach ($columns as $column) {
            echo "<tr>";
            echo "<td>{$column['Field']}</td>";
            echo "<td>{$column['Type']}</td>";
            echo "<td>{$column['Null']}</td>";
            echo "<td>{$column['Key']}</td>";
            echo "</tr>";
        }
        echo "</table>";

        // فحص عدد الحجوزات
        $count = $pdo->query("SELECT COUNT(*) FROM bookings")->fetchColumn();
        echo "<p>عدد الحجوزات الحالية: <strong>{$count}</strong></p>";
    } else {
        echo "<p style='color: red;'>❌ جدول الحجوزات غير موجود</p>";
    }
} catch (PDOException $e) {
    echo "<p style='color: red;'>❌ خطأ في قاعدة البيانات: " . $e->getMessage() . "</p>";
}

// فحص جدول الفعاليات
try {
    $stmt = $pdo->query("SELECT id, title FROM events LIMIT 5");
    $events = $stmt->fetchAll();

    echo "<h4>الفعاليات المتاحة:</h4>";
    if ($events) {
        echo "<ul>";
        foreach ($events as $event) {
            echo "<li>ID: {$event['id']} - {$event['title']}</li>";
        }
        echo "</ul>";
    } else {
        echo "<p style='color: orange;'>⚠️ لا توجد فعاليات</p>";
    }
} catch (PDOException $e) {
    echo "<p style='color: red;'>❌ خطأ في جلب الفعاليات: " . $e->getMessage() . "</p>";
}

// فحص إعدادات PHP
echo "<h4>إعدادات PHP:</h4>";
echo "<ul>";
echo "<li>max_file_uploads: " . ini_get('max_file_uploads') . "</li>";
echo "<li>post_max_size: " . ini_get('post_max_size') . "</li>";
echo "<li>upload_max_filesize: " . ini_get('upload_max_filesize') . "</li>";
echo "<li>memory_limit: " . ini_get('memory_limit') . "</li>";
echo "</ul>";

// فحص مجلدات التحميل
echo "<h4>المجلدات والصلاحيات:</h4>";
$folders = ['assets/uploads/events', 'assets/uploads/events/thumbs'];
foreach ($folders as $folder) {
    if (is_dir($folder)) {
        if (is_writable($folder)) {
            echo "<p style='color: green;'>✅ {$folder} - قابل للكتابة</p>";
        } else {
            echo "<p style='color: red;'>❌ {$folder} - غير قابل للكتابة</p>";
        }
    } else {
        echo "<p style='color: red;'>❌ {$folder} - غير موجود</p>";
    }
}
