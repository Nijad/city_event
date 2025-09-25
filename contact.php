<?php include 'db.php';

$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $message = $_POST['message'] ?? '';
    
    if (empty($name) || empty($email) || empty($message)) {
        $error_message = 'يرجى ملء جميع الحقول المطلوبة';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = 'البريد الإلكتروني غير صحيح';
    } else {
        // حفظ الرسالة في قاعدة البيانات (اختياري)
        try {
            $stmt = $pdo->prepare("INSERT INTO contact_messages (name, email, message, created_at) VALUES (?, ?, ?, NOW())");
            $stmt->execute([$name, $email, $message]);
            $success_message = 'شكراً لك! تم إرسال رسالتك بنجاح وسنتواصل معك قريباً.';
            
            // إعادة تعيين الحقول
            $_POST = array();
        } catch (PDOException $e) {
            $error_message = 'حدث خطأ أثناء إرسال الرسالة: ' . $e->getMessage();
        }
    }
}

// إنشاء جدول رسائل الاتصال إذا لم يكن موجوداً
$pdo->exec("CREATE TABLE IF NOT EXISTS contact_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>اتصل بنا - دليل فعاليات المدينة</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
    <?php include 'header.php'; ?>

    <main class="container py-5">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <h1 class="text-center mb-4">اتصل بنا</h1>
                
                <?php if ($success_message): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?= $success_message ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                
                <?php if ($error_message): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?= $error_message ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <div class="row">
                    <div class="col-md-6">
                        <form id="contactForm" method="POST" novalidate>
                            <div class="mb-3">
                                <label for="name" class="form-label">الاسم الكامل *</label>
                                <input type="text" class="form-control" id="name" name="name" 
                                       value="<?= $_POST['name'] ?? '' ?>" required>
                                <div class="invalid-feedback">يرجى إدخال الاسم</div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="email" class="form-label">البريد الإلكتروني *</label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="<?= $_POST['email'] ?? '' ?>" required>
                                <div class="invalid-feedback">يرجى إدخال بريد إلكتروني صحيح</div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="message" class="form-label">الرسالة *</label>
                                <textarea class="form-control" id="message" name="message" rows="5" required><?= $_POST['message'] ?? '' ?></textarea>
                                <div class="invalid-feedback">يرجى كتابة رسالتك</div>
                            </div>
                            
                            <button type="submit" class="btn btn-primary w-100">إرسال الرسالة</button>
                        </form>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="card h-100">
                            <div class="card-body">
                                <h5 class="card-title">معلومات التواصل</h5>
                                
                                <div class="mb-3">
                                    <h6>📧 البريد الإلكتروني</h6>
                                    <p>info@cityevents.com<br>support@cityevents.com</p>
                                </div>
                                
                                <div class="mb-3">
                                    <h6>📞 الهاتف</h6>
                                    <p>+966 12 345 6789<br>+966 98 765 4321</p>
                                </div>
                                
                                <div class="mb-3">
                                    <h6>🕒 أوقات العمل</h6>
                                    <p>الأحد - الخميس: 8:00 ص - 5:00 م<br>الجمعة والسبت: إجازة</p>
                                </div>
                                
                                <div class="mb-3">
                                    <h6>🌐 وسائل التواصل الاجتماعي</h6>
                                    <div class="d-flex gap-2">
                                        <a href="#" class="btn btn-outline-primary btn-sm">Twitter</a>
                                        <a href="#" class="btn btn-outline-primary btn-sm">Facebook</a>
                                        <a href="#" class="btn btn-outline-primary btn-sm">Instagram</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php include 'footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/main.js"></script>
    <script src="assets/js/contact.js"></script>
</body>
</html>