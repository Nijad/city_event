<?php
// config/upload.php - إعدادات رفع الملفات

class UploadConfig {
    const UPLOAD_DIR = '../assets/uploads/events/';
    const THUMB_DIR = '../assets/uploads/events/thumbs/';
    const MAX_FILE_SIZE = 5 * 1024 * 1024; // 5MB
    const ALLOWED_TYPES = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    const THUMB_WIDTH = 300;
    const THUMB_HEIGHT = 200;
    
    public static function init() {
        // إنشاء المجلدات إذا لم تكن موجودة
        if (!is_dir(self::UPLOAD_DIR)) {
            mkdir(self::UPLOAD_DIR, 0755, true);
        }
        if (!is_dir(self::THUMB_DIR)) {
            mkdir(self::THUMB_DIR, 0755, true);
        }
        
        // إنشاء ملف .htaccess للحماية
        $htaccess_content = "Order deny,allow\nDeny from all";
        file_put_contents(self::UPLOAD_DIR . '.htaccess', $htaccess_content);
        file_put_contents(self::THUMB_DIR . '.htaccess', $htaccess_content);
    }
    
    public static function isAllowedType($filename) {
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        return in_array($extension, self::ALLOWED_TYPES);
    }
}

// تهيئة المجلدات عند تضمين الملف
UploadConfig::init();
?>