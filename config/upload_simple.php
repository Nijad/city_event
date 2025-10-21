<?php
// config/upload_simple.php - إصدار مبسط بدون GD Library

class UploadConfig {
    const UPLOAD_DIR = '../assets/uploads/events/';
    const THUMB_DIR = '../assets/uploads/events/thumbs/';
    const MAX_FILE_SIZE = 5 * 1024 * 1024; // 5MB
    const ALLOWED_TYPES = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    
    public static function init() {
        // إنشاء المجلدات إذا لم تكن موجودة
        if (!is_dir(self::UPLOAD_DIR)) {
            mkdir(self::UPLOAD_DIR, 0755, true);
        }
        if (!is_dir(self::THUMB_DIR)) {
            mkdir(self::THUMB_DIR, 0755, true);
        }
        
        // إنشاء ملف .htaccess للحماية: امنع تنفيذ ملفات PHP لكن اسمح بالملفات الثابتة مثل الصور
        $htaccess_content = <<<HT
Options -Indexes
<FilesMatch "\.(php|php5|phtml|phar)$">
    <IfModule mod_authz_core.c>
        Require all denied
    </IfModule>
    <IfModule !mod_authz_core.c>
        Order deny,allow
        Deny from all
    </IfModule>
</FilesMatch>
HT;
        @file_put_contents(self::UPLOAD_DIR . '.htaccess', $htaccess_content);
        @file_put_contents(self::THUMB_DIR . '.htaccess', $htaccess_content);
    }
    
    public static function isAllowedType($filename) {
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        return in_array($extension, self::ALLOWED_TYPES);
    }
}

// تهيئة المجلدات
UploadConfig::init();

// دالة معالجة رفع الصورة (بدون إنشاء thumbnails معقدة)
function handleImageUpload($file) {
    $result = ['success' => false, 'filename' => '', 'error' => ''];
    
    // التحقق من نوع الملف
    if (!UploadConfig::isAllowedType($file['name'])) {
        $result['error'] = 'نوع الملف غير مسموح به. المسموح: ' . implode(', ', UploadConfig::ALLOWED_TYPES);
        return $result;
    }
    
    // التحقق من حجم الملف
    if ($file['size'] > UploadConfig::MAX_FILE_SIZE) {
        $result['error'] = 'حجم الملف كبير جداً. الحد الأقصى: 5MB';
        return $result;
    }
    
    // التحقق من أخطاء الرفع
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $error_messages = [
            UPLOAD_ERR_INI_SIZE => 'حجم الملف أكبر من المسموح به في الخادم',
            UPLOAD_ERR_FORM_SIZE => 'حجم الملف أكبر من المسموح به في النموذج',
            UPLOAD_ERR_PARTIAL => 'تم رفع جزء فقط من الملف',
            UPLOAD_ERR_NO_FILE => 'لم يتم اختيار ملف',
            UPLOAD_ERR_NO_TMP_DIR => 'مجلد التخزين المؤقت غير موجود',
            UPLOAD_ERR_CANT_WRITE => 'فشل في كتابة الملف على القرص',
            UPLOAD_ERR_EXTENSION => 'تم إيقاف الرفع بواسطة إضافة PHP'
        ];
        $result['error'] = $error_messages[$file['error']] ?? 'حدث خطأ غير معروف أثناء رفع الملف';
        return $result;
    }
    
    // إنشاء اسم فريد للملف
    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $filename = uniqid() . '_' . time() . '.' . $extension;
    $target_path = UploadConfig::UPLOAD_DIR . $filename;
    
    // نقل الملف إلى المجلد
    if (move_uploaded_file($file['tmp_name'], $target_path)) {
        // نسخ نفس الصورة كـ thumbnail (بدون resize)
        $thumb_path = UploadConfig::THUMB_DIR . $filename;
        copy($target_path, $thumb_path);
        
        $result['success'] = true;
        $result['filename'] = $filename;
    } else {
        $result['error'] = 'فشل في حفظ الملف على الخادم';
    }
    
    return $result;
}

// دالة حذف الصورة
if (!function_exists('deleteEventImage')) {
    function deleteEventImage($filename) {
        if (empty($filename) || $filename === 'default-event.jpg') {
            return true;
        }
        
        $image_path = UploadConfig::UPLOAD_DIR . $filename;
        $thumb_path = UploadConfig::THUMB_DIR . $filename;
        
        $success = true;
        
        if (file_exists($image_path)) {
            $success = $success && unlink($image_path);
        }
        
        if (file_exists($thumb_path)) {
            $success = $success && unlink($thumb_path);
        }
        
        return $success;
    }
}
?>