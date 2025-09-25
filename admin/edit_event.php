<?php
session_start();
include '../db.php';

if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}

$error_message = '';
$success_message = '';

// جلب بيانات الفعالية الحالية
$event_id = $_GET['id'] ?? 0;
$stmt = $pdo->prepare("SELECT * FROM events WHERE id = ?");
$stmt->execute([$event_id]);
$event = $stmt->fetch();

if (!$event) {
    header('Location: events.php?error=الفعالية غير موجودة');
    exit;
}

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
            $stmt = $pdo->prepare("UPDATE events SET title = ?, description = ?, category = ?, location = ?, event_date = ?, image = ? WHERE id = ?");
            $stmt->execute([$title, $description, $category, $location, $event_date, $image, $event_id]);
            
            $success_message = 'تم تحديث الفعالية بنجاح!';
            // تحديث بيانات الفعالية لعرضها في النموذج
            $event = array_merge($event, $_POST);
            
        } catch (PDOException $e) {
            $error_message = 'حدث خطأ أثناء تحديث الفعالية: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تعديل فعالية - لوحة التحكم</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <?php include 'sidebar.php'; ?>

            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">تعديل الفعالية</h1>
                    <div>
                        <a href="events.php" class="btn btn-secondary">← العودة للقائمة</a>
                        <a href="../event.php?id=<?= $event['id'] ?>" target="_blank" class="btn btn-outline-primary">
                            👁️ معاينة
                        </a>
                    </div>
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
                                               value="<?= htmlspecialchars($event['title']) ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="category" class="form-label">التصنيف *</label>
                                        <select class="form-select" id="category" name="category" required>
                                            <option value="">اختر التصنيف</option>
                                            <option value="ثقافة" <?= $event['category'] == 'ثقافة' ? 'selected' : '' ?>>ثقافة</option>
                                            <option value="رياضة" <?= $event['category'] == 'رياضة' ? 'selected' : '' ?>>رياضة</option>
                                            <option value="موسيقى" <?= $event['category'] == 'موسيقى' ? 'selected' : '' ?>>موسيقى</option>
                                            <option value="عائلية" <?= $event['category'] == 'عائلية' ? 'selected' : '' ?>>عائلية</option>
                                            <option value="تعليمية" <?= $event['category'] == 'تعليمية' ? 'selected' : '' ?>>تعليمية</option>
                                            <option value="ترفيهية" <?= $event['category'] == 'ترفيهية' ? 'selected' : '' ?>>ترفيهية</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">وصف الفعالية *</label>
                                <textarea class="form-control" id="description" name="description" 
                                          rows="5" required><?= htmlspecialchars($event['description']) ?></textarea>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="location" class="form-label">الموقع *</label>
                                        <input type="text" class="form-control" id="location" name="location" 
                                               value="<?= htmlspecialchars($event['location']) ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="event_date" class="form-label">تاريخ ووقت الفعالية *</label>
                                        <input type="datetime-local" class="form-control" id="event_date" 
                                               name="event_date" value="<?= date('Y-m-d\TH:i', strtotime($event['event_date'])) ?>" required>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="image" class="form-label">رابط الصورة</label>
                                <input type="url" class="form-control" id="image" name="image" 
                                       value="<?= htmlspecialchars($event['image']) ?>" 
                                       placeholder="https://example.com/image.jpg">
                                <small class="text-muted">يمكن ترك هذا الحقل فارغاً لاستخدام صورة افتراضية</small>
                            </div>

                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <a href="events.php" class="btn btn-secondary">إلغاء</a>
                                <button type="submit" class="btn btn-primary">تحديث الفعالية</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- إحصائيات الفعالية -->
                <div class="row mt-4">
                    <div class="col-md-4">
                        <div class="card text-white bg-primary">
                            <div class="card-body">
                                <h5 class="card-title">عدد الحجوزات</h5>
                                <p class="card-text display-6">
                                    <?= $pdo->query("SELECT COUNT(*) FROM bookings WHERE event_id = {$event['id']}")->fetchColumn() ?>
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card text-white bg-success">
                            <div class="card-body">
                                <h5 class="card-title">الحجوزات النشطة</h5>
                                <p class="card-text display-6">
                                    <?= $pdo->query("SELECT COUNT(*) FROM bookings WHERE event_id = {$event['id']}")->fetchColumn() ?>
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card text-white bg-info">
                            <div class="card-body">
                                <h5 class="card-title">تاريخ الإنشاء</h5>
                                <p class="card-text">
                                    <?= date('Y-m-d', strtotime($event['created_at'])) ?>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>