<?php
session_start();
include '../db.php';
include '../config/upload_simple.php'; // استخدام الإصدار المبسط
include '../functions/images.php'; // وظائف الصور

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
    $current_image = $event['image'];
    $new_image = $current_image;
    
    // التحقق من البيانات الأساسية
    if (empty($title) || empty($description) || empty($category) || empty($location) || empty($event_date)) {
        $error_message = 'جميع الحقول مطلوبة';
    } else {
        // معالجة رفع الصورة الجديدة إذا تم رفعها
        if (isset($_FILES['event_image']) && $_FILES['event_image']['error'] === UPLOAD_ERR_OK) {
            $upload_result = handleImageUpload($_FILES['event_image']);
            if ($upload_result['success']) {
                $new_image = $upload_result['filename'];
                
                // حذف الصورة القديمة إذا لم تكن الصورة الافتراضية
                if ($current_image !== 'default-event.jpg') {
                    deleteEventImage($current_image);
                }
            } else {
                $error_message = $upload_result['error'];
            }
        }
        
        if (!$error_message) {
            try {
                $stmt = $pdo->prepare("UPDATE events SET title = ?, description = ?, category = ?, location = ?, event_date = ?, image = ? WHERE id = ?");
                $stmt->execute([$title, $description, $category, $location, $event_date, $new_image, $event_id]);
                
                $success_message = 'تم تحديث الفعالية بنجاح!';
                
                // تحديث بيانات الفعالية لعرضها في النموذج
                $event['title'] = $title;
                $event['description'] = $description;
                $event['category'] = $category;
                $event['location'] = $location;
                $event['event_date'] = $event_date;
                $event['image'] = $new_image;
                
            } catch (PDOException $e) {
                $error_message = 'حدث خطأ أثناء تحديث الفعالية: ' . $e->getMessage();
            }
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
    <style>
        .current-image {
            max-width: 300px;
            max-height: 200px;
            border-radius: 5px;
            margin-bottom: 10px;
            border: 2px solid #dee2e6;
        }
        .image-preview {
            max-width: 300px;
            max-height: 200px;
            border: 2px dashed #ddd;
            border-radius: 5px;
            padding: 10px;
            margin-top: 10px;
            display: none;
        }
        .image-preview img {
            max-width: 100%;
            max-height: 180px;
        }
        .upload-area {
            border: 2px dashed #007bff;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            background: #f8f9fa;
        }
        .upload-area:hover {
            background: #e9ecef;
            border-color: #0056b3;
        }
        .upload-area.dragover {
            background: #d1ecf1;
            border-color: #17a2b8;
        }
        .fa-cloud-upload-alt {
            color: #007bff;
            font-size: 3rem !important;
            margin-bottom: 10px;
        }
    </style>
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
                        <form method="POST" id="eventForm" enctype="multipart/form-data">
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

                            <!-- الصورة الحالية -->
                            <div class="mb-3">
                                <label class="form-label">الصورة الحالية</label>
                                <div>
                                    <?php
                                    $current_image_path = getEventImage($event['image']);
                                    ?>
                                    <img src="<?= $current_image_path ?>" 
                                         alt="الصورة الحالية" class="current-image"
                                         onerror="this.src='../assets/img/default-event.jpg'">
                                    <br>
                                    <small class="text-muted">الصورة الحالية للفعالية</small>
                                </div>
                            </div>

                            <!-- منطقة رفع الصورة الجديدة -->
                            <div class="mb-3">
                                <label class="form-label">تغيير الصورة (اختياري)</label>
                                
                                <div class="upload-area" id="uploadArea">
                                    <input type="file" id="event_image" name="event_image" 
                                           accept=".jpg,.jpeg,.png,.gif,.webp" style="display: none;">
                                    
                                    <div id="uploadContent">
                                        <i class="fas fa-cloud-upload-alt"></i>
                                        <h5>انقر لاختيار صورة جديدة أو اسحبها هنا</h5>
                                        <p class="text-muted">الحد الأقصى: 5MB | المسموح: JPG, PNG, GIF, WEBP</p>
                                    </div>
                                </div>
                                
                                <div class="image-preview" id="imagePreview">
                                    <img src="" alt="معاينة الصورة الجديدة" id="previewImage">
                                    <div class="mt-2">
                                        <button type="button" class="btn btn-sm btn-danger" onclick="removeImage()">
                                            ❌ إلغاء التغيير
                                        </button>
                                    </div>
                                </div>
                                
                                <small class="text-muted">اترك هذا الحقل فارغاً للحفاظ على الصورة الحالية</small>
                            </div>

                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <a href="events.php" class="btn btn-secondary">إلغاء</a>
                                <button type="submit" class="btn btn-primary">تحديث الفعالية</button>
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
                        <div id="eventPreview">
                            <div class="card">
                                <img src="<?= $current_image_path ?>" class="card-img-top" alt="<?= htmlspecialchars($event['title']) ?>" 
                                     style="height: 200px; object-fit: cover;"
                                     onerror="this.src='../assets/img/default-event.jpg'">
                                <div class="card-body">
                                    <h5 class="card-title"><?= htmlspecialchars($event['title']) ?></h5>
                                    <p class="card-text"><?= mb_substr($event['description'], 0, 100) . (mb_strlen($event['description']) > 100 ? '...' : '') ?></p>
                                    <p class="text-muted">
                                        <small>📅 <?= date('Y-m-d H:i', strtotime($event['event_date'])) ?></small><br>
                                        <small>📍 <?= htmlspecialchars($event['location']) ?></small><br>
                                        <small>🏷️ <?= htmlspecialchars($event['category']) ?></small>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // إدارة رفع الصورة
        const uploadArea = document.getElementById('uploadArea');
        const fileInput = document.getElementById('event_image');
        const imagePreview = document.getElementById('imagePreview');
        const previewImage = document.getElementById('previewImage');
        const uploadContent = document.getElementById('uploadContent');
        
        // النقر على منطقة الرفع
        uploadArea.addEventListener('click', () => fileInput.click());
        
        // تغيير الملف المختار
        fileInput.addEventListener('change', function(e) {
            if (this.files && this.files[0]) {
                const file = this.files[0];
                
                // التحقق من نوع الملف
                const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
                if (!allowedTypes.includes(file.type)) {
                    alert('نوع الملف غير مسموح به. المسموح: JPG, PNG, GIF, WEBP');
                    this.value = '';
                    return;
                }
                
                // التحقق من حجم الملف (5MB)
                if (file.size > 5 * 1024 * 1024) {
                    alert('حجم الملف كبير جداً. الحد الأقصى: 5MB');
                    this.value = '';
                    return;
                }
                
                // عرض المعاينة
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImage.src = e.target.result;
                    imagePreview.style.display = 'block';
                    uploadContent.innerHTML = '<p class="text-success">✓ تم اختيار الصورة بنجاح</p>';
                    
                    // تحديث المعاينة
                    updatePreview();
                };
                reader.readAsDataURL(file);
            }
        });
        
        // سحب وإفلات الملفات
        uploadArea.addEventListener('dragover', function(e) {
            e.preventDefault();
            this.classList.add('dragover');
        });
        
        uploadArea.addEventListener('dragleave', function(e) {
            e.preventDefault();
            this.classList.remove('dragover');
        });
        
        uploadArea.addEventListener('drop', function(e) {
            e.preventDefault();
            this.classList.remove('dragover');
            
            if (e.dataTransfer.files && e.dataTransfer.files[0]) {
                fileInput.files = e.dataTransfer.files;
                fileInput.dispatchEvent(new Event('change'));
            }
        });
        
        // إزالة الصورة
        function removeImage() {
            fileInput.value = '';
            imagePreview.style.display = 'none';
            uploadContent.innerHTML = `
                <i class="fas fa-cloud-upload-alt"></i>
                <h5>انقر لاختيار صورة جديدة أو اسحبها هنا</h5>
                <p class="text-muted">الحد الأقصى: 5MB | المسموح: JPG, PNG, GIF, WEBP</p>
            `;
            updatePreview();
        }
        
        // تحديث معاينة الفعالية في الوقت الحقيقي
        function updatePreview() {
            const title = document.getElementById('title').value || '<?= htmlspecialchars($event['title']) ?>';
            const description = document.getElementById('description').value || '<?= htmlspecialchars($event['description']) ?>';
            const category = document.getElementById('category').value || '<?= htmlspecialchars($event['category']) ?>';
            const location = document.getElementById('location').value || '<?= htmlspecialchars($event['location']) ?>';
            const eventDate = document.getElementById('event_date').value || '<?= date('Y-m-d\TH:i', strtotime($event['event_date'])) ?>';
            const imageSrc = previewImage.src || '<?= $current_image_path ?>';
            
            const previewHTML = `
                <div class="card">
                    <img src="${imageSrc}" class="card-img-top" alt="${title}" style="height: 200px; object-fit: cover;"
                         onerror="this.src='../assets/img/default-event.jpg'">
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
        }
        
        // إضافة مستمعات الأحداث للحقول النصية
        document.getElementById('title').addEventListener('input', updatePreview);
        document.getElementById('description').addEventListener('input', updatePreview);
        document.getElementById('category').addEventListener('change', updatePreview);
        document.getElementById('location').addEventListener('input', updatePreview);
        document.getElementById('event_date').addEventListener('change', updatePreview);
        
        // التهيئة الأولية
        document.addEventListener('DOMContentLoaded', function() {
            updatePreview();
        });
    </script>
    
    <!-- رابط Font Awesome للأيقونات -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</body>
</html>