<?php
// assign_images_to_events.php - لتوزيع الصور على الأحداث
include 'db.php';

function assignImagesToEvents()
{
    global $pdo;

    // صور لكل تصنيف
    $categoryImages = [
        'تعليمية' => ['education_1.jpg', 'education_2.jpg', 'education_3.jpg'],
        'رياضة' => ['sports_1.jpg', 'sports_2.jpg', 'sports_3.jpg'],
        'موسيقى' => ['music_1.jpg', 'music_2.jpg', 'music_3.jpg'],
        'ثقافة' => ['culture_1.jpg', 'culture_2.jpg', 'culture_3.jpg'],
        'عائلية' => ['family_1.jpg', 'family_2.jpg', 'family_3.jpg'],
        'ترفيهية' => ['entertainment_1.jpg', 'entertainment_2.jpg', 'entertainment_3.jpg']
    ];

    // جلب جميع الأحداث
    $events = $pdo->query("SELECT id, title, category FROM events WHERE image = 'default-event.jpg'")->fetchAll();

    $updated = 0;

    foreach ($events as $event) {
        $category = $event['category'];

        if (isset($categoryImages[$category])) {
            // اختيار صورة عشوائية من التصنيف
            $images = $categoryImages[$category];
            $randomImage = $images[array_rand($images)];

            // تحديث الصورة في قاعدة البيانات
            $stmt = $pdo->prepare("UPDATE events SET image = ? WHERE id = ?");
            $stmt->execute([$randomImage, $event['id']]);

            $updated++;
            echo "<p style='color: green;'>✅ {$event['title']} - {$randomImage}</p>";
        }
    }

    echo "<hr><h3>نتائج التحديث:</h3>";
    echo "<p>✅ تم تحديث: $updated حدث</p>";
    echo "<p>📊 إجمالي الأحداث: " . count($events) . " حدث</p>";
}

// تنفيذ التحديث
assignImagesToEvents();
