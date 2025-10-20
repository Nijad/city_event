<?php
// functions/images.php - وظائف مساعدة للصور

function getEventImage($filename, $admin = false) {
    // web-relative default paths
    $default_path = $admin ? '../assets/img/default-event.jpg' : 'assets/img/default-event.jpg';

    if (empty($filename) || $filename === 'default-event.jpg') {
        return $default_path;
    }

    // web-relative paths to return
    $web_image_path = $admin ? '../assets/uploads/events/' . $filename : 'assets/uploads/events/' . $filename;
    $web_thumb_path = $admin ? '../assets/uploads/events/thumbs/' . $filename : 'assets/uploads/events/thumbs/' . $filename;

    // filesystem paths for file_exists checks (normalize using __DIR__)
    $baseDir = dirname(__DIR__); // functions/.. => project root
    $fs_image_path = $baseDir . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'events' . DIRECTORY_SEPARATOR . $filename;
    $fs_thumb_path = $baseDir . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'events' . DIRECTORY_SEPARATOR . 'thumbs' . DIRECTORY_SEPARATOR . $filename;

    // إذا كانت الصورة موجودة فعلياً في النظام، أرجع المسار الويب
    if (file_exists($fs_image_path)) {
        return $web_image_path;
    }

    // إذا لم توجد الصورة الأصلية ولكن يوجد مصغرة، استخدمها
    if (file_exists($fs_thumb_path)) {
        return $web_thumb_path;
    }

    return $default_path;
}

function getEventThumbnail($filename, $admin = false) {
    if (empty($filename) || $filename === 'default-event.jpg') {
        return $admin ? '../assets/img/default-event.jpg' : 'assets/img/default-event.jpg';
    }

    $web_thumb_path = $admin ? '../assets/uploads/events/thumbs/' . $filename : 'assets/uploads/events/thumbs/' . $filename;

    $baseDir = dirname(__DIR__);
    $fs_thumb_path = $baseDir . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'events' . DIRECTORY_SEPARATOR . 'thumbs' . DIRECTORY_SEPARATOR . $filename;

    if (file_exists($fs_thumb_path)) {
        return $web_thumb_path;
    }

    // fall back to the full image (getEventImage already uses filesystem checks)
    return getEventImage($filename, $admin);
}

// دالة لحذف صورة الفعالية
// function deleteEventImage($filename, $admin = false) {
//     if (empty($filename) || $filename === 'default-event.jpg') {
//         return true;
//     }
    
//     $image_path = $admin? '../assets/uploads/events/' . $filename : 'assets/uploads/events/' . $filename;
//     $thumb_path = $admin? '../assets/uploads/events/thumbs/' . $filename :'assets/uploads/events/thumbs/' . $filename;
    
//     $success = true;
    
//     if (file_exists($image_path)) {
//         $success = $success && unlink($image_path);
//     }
    
//     if (file_exists($thumb_path)) {
//         $success = $success && unlink($thumb_path);
//     }
    
//     return $success;
// }
?>