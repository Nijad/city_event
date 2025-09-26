<?php
// functions/images.php - وظائف مساعدة للصور

function getEventImage($filename, $admin = false) {
    $default_path = $admin ? '../assets/img/default-event.jpg' : 'assets/img/default-event.jpg';
    if (empty($filename) || $filename === 'default-event.jpg') {
        return $default_path;
    }
    
    $image_path = 'assets/uploads/events/' . $filename;
    $thumb_path = 'assets/uploads/events/thumbs/' . $filename;
    if($admin){
        $image_path = '../' . $image_path;
        $thumb_path = '../' . $thumb_path;
    }
    // التحقق من وجود الصورة
    if (file_exists($image_path)) {
        return $image_path;
    } else {
        return $default_path;
    }
}

function getEventThumbnail($filename, $admin = false) {
    if (empty($filename) || $filename === 'default-event.jpg') {
        return $admin? '../assets/img/default-event.jpg': 'assets/img/default-event.jpg';
    }
    
    $thumb_path = $admin?'../assets/uploads/events/thumbs/' . $filename: 'assets/uploads/events/thumbs/' . $filename;
    
    // إذا كانت الصورة المصغرة موجودة، استخدمها
    if (file_exists($thumb_path)) {
        return $thumb_path;
    } else {
        // وإلا استخدم الصورة الأصلية
        return getEventImage($filename);
    }
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