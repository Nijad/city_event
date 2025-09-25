<?php
session_start();
include '../db.php';

if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}

$error_message = '';
$success_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';
    $description = $_POST['description'] ?? '';
    $category = $_POST['category'] ?? '';
    $location = $_POST['location'] ?? '';
    $event_date = $_POST['event_date'] ?? '';
    $image = $_POST['image'] ?? '';
    
    // التحقق من البيانات
    if (empty($title) || empty($description) || empty($category) || empty($location) || empty($event_date)) {
        $error_message = 'جميع الحقول مطلوبة';
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO events (title, description, category, location, event_date, image) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$title, $description, $category, $location, $event_date, $image]);
            
            $success_message = 'تم إضافة الفعالية بنجاح!';
            $_POST = array(); // إعادة تعيين النموذج
            
        } catch (PDOException $e) {
            $error_message = 'حدث خطأ أثناء إضافة الفعالية: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إضافة فعالية - لوحة التحكم</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <?php include 'sidebar.php'; ?>

            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">إضافة فعالية جديدة</h1>
                    <a href="events.php" class="btn btn-secondary">← العودة للقائمة</a>
                </div>

                <?php if ($error_message): ?>
                    <div class="alert alert-danger alert-dismissible fade show">
                        <?= $error_message ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if ($success_message): ?>
                    <div class="alert alert-success alert-dismissible fade show">
                        <?= $success_message ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <div class="card shadow">
                    <div class="card-body">
                        <form method="POST" id="eventForm">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="title" class="form-label">عنوان الفعالية *</label>
                                        <input type="text" class="form-control" id="title" name="title" 
                                               value="<?= $_POST['title'] ?? '' ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="category" class="form-label">التصنيف *</label>
                                        <select class="form-select" id="category" name="category" required>
                                            <option value="">اختر التصنيف</option>
                                            <option value="ثقافة" <?= ($_POST['category'] ?? '') == 'ثقافة' ? 'selected' : '' ?>>ثقافة</option>
                                            <option value="رياضة" <?= ($_POST['category'] ?? '') == 'رياضة' ? 'selected' : '' ?>>رياضة</option>
                                            <option value="موسيقى" <?= ($_POST['category'] ?? '') == 'موسيقى' ? 'selected' : '' ?>>موسيقى</option>
                                            <option value="عائلية" <?= ($_POST['category'] ?? '') == 'عائلية' ? 'selected' : '' ?>>عائلية</option>
                                            <option value="تعليمية" <?= ($_POST['category'] ?? '') == 'تعليمية' ? 'selected' : '' ?>>تعليمية</option>
                                            <option value="ترفيهية" <?= ($_POST['category'] ?? '') == 'ترفيهية' ? 'selected' : '' ?>>ترفيهية</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">وصف الفعالية *</label>
                                <textarea class="form-control" id="description" name="description" 
                                          rows="5" required><?= $_POST['description'] ?? '' ?></textarea>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="location" class="form-label">الموقع *</label>
                                        <input type="text" class="form-control" id="location" name="location" 
                                               value="<?= $_POST['location'] ?? '' ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="event_date" class="form-label">تاريخ ووقت الفعالية *</label>
                                        <input type="datetime-local" class="form-control" id="event_date" 
                                               name="event_date" value="<?= $_POST['event_date'] ?? '' ?>" required>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="image" class="form-label">رابط الصورة</label>
                                <input type="url" class="form-control" id="image" name="image" 
                                       value="<?= $_POST['image'] ?? '' ?>" 
                                       placeholder="https://example.com/image.jpg">
                                <small class="text-muted">يمكن ترك هذا الحقل فارغاً لاستخدام صورة افتراضية</small>
                            </div>

                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <button type="reset" class="btn btn-secondary">مسح النموذج</button>
                                <button type="submit" class="btn btn-primary">إضافة الفعالية</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- معاينة الفعالية -->
                <div class="card shadow mt-4">
                    <div class="card-header">
                        <h5 class="mb-0">معاينة الفعالية</h5>
                    </div>
                    <div class="card-body">
                        <div id="eventPreview" class="text-muted">
                            سيظهر هنا معاينة للفعالية بعد تعبئة النموذج
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // معاينة الفعالية في الوقت الحقيقي
        document.getElementById('eventForm').addEventListener('input', function() {
            const title = document.getElementById('title').value || 'عنوان الفعالية';
            const description = document.getElementById('description').value || 'وصف الفعالية';
            const category = document.getElementById('category').value || 'التصنيف';
            const location = document.getElementById('location').value || 'الموقع';
            const eventDate = document.getElementById('event_date').value || '2024-01-01T00:00';
            const image = document.getElementById('image').value || '../assets/img/default-event.jpg';
            
            const previewHTML = `
                <div class="card">
                    <img src="${image}" class="card-img-top" alt="${title}" style="height: 200px; object-fit: cover;">
                    <div class="card-body">
                        <h5 class="card-title">${title}</h5>
                        <p class="card-text">${description.substring(0, 100)}${description.length > 100 ? '...' : ''}</p>
                        <p class="text-muted">
                            <small>📅 ${new Date(eventDate).toLocaleDateString('ar-SA')}</small><br>
                            <small>📍 ${location}</small><br>
                            <small>🏷️ ${category}</small>
                        </p>
                    </div>
                </div>
            `;
            
            document.getElementById('eventPreview').innerHTML = previewHTML;
        });
    </script>
</body>
</html>