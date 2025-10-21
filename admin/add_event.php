<?php
session_start();
include '../db.php';
include '../config/upload.php';

if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}

$error_message = '';
$success_message = '';
$uploaded_image = 'default-event.jpg';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';
    $description = $_POST['description'] ?? '';
    $category = $_POST['category'] ?? '';
    $location = $_POST['location'] ?? '';
    $event_date = $_POST['event_date'] ?? '';
    
    // التحقق من البيانات الأساسية
    if (empty($title) || empty($description) || empty($category) || empty($location) || empty($event_date)) {
        $error_message = 'جميع الحقول مطلوبة';
    } else {
        // معالجة رفع الصورة
        if (isset($_FILES['event_image']) && $_FILES['event_image']['error'] === UPLOAD_ERR_OK) {
            $upload_result = handleImageUpload($_FILES['event_image']);
            if ($upload_result['success']) {
                $uploaded_image = $upload_result['filename'];
            } else {
                $error_message = $upload_result['error'];
            }
        }
        
        if (!$error_message) {
            try {
                $stmt = $pdo->prepare("INSERT INTO events (title, description, category, location, event_date, image) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([$title, $description, $category, $location, $event_date, $uploaded_image]);
                
                $success_message = 'تم إضافة الفعالية بنجاح!';
                $_POST = array(); // إعادة تعيين النموذج
                $uploaded_image = 'default-event.jpg';
                
            } catch (PDOException $e) {
                $error_message = 'حدث خطأ أثناء إضافة الفعالية: ' . $e->getMessage();
            }
        }
    }
}

// دالة معالجة رفع الصورة
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
        $result['error'] = 'حدث خطأ أثناء رفع الملف: ' . $file['error'];
        return $result;
    }
    
    // إنشاء اسم فريد للملف
    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $filename = uniqid() . '_' . time() . '.' . $extension;
    $target_path = UploadConfig::UPLOAD_DIR . $filename;
    
    // نقل الملف إلى المجلد
    if (move_uploaded_file($file['tmp_name'], $target_path)) {
        // إنشاء صورة مصغرة
        createThumbnail($target_path, UploadConfig::THUMB_DIR . $filename, UploadConfig::THUMB_WIDTH, UploadConfig::THUMB_HEIGHT);
        
        $result['success'] = true;
        $result['filename'] = $filename;
    } else {
        $result['error'] = 'فشل في حفظ الملف';
    }
    
    return $result;
}

// دالة إنشاء صورة مصغرة
function createThumbnail($source_path, $thumb_path, $width, $height) {
    $source_info = getimagesize($source_path);
    if (!$source_info) return false;
    
    list($source_width, $source_height, $type) = $source_info;
    
    // تحديد نوع الصورة
    switch ($type) {
        case IMAGETYPE_JPEG:
            $source = imagecreatefromjpeg($source_path);
            break;
        case IMAGETYPE_PNG:
            $source = imagecreatefrompng($source_path);
            break;
        case IMAGETYPE_GIF:
            $source = imagecreatefromgif($source_path);
            break;
        case IMAGETYPE_WEBP:
            $source = imagecreatefromwebp($source_path);
            break;
        default:
            return false;
    }
    
    // حساب الأبعاد الجديدة مع الحفاظ على التناسب
    $aspect_ratio = $source_width / $source_height;
    $thumb_ratio = $width / $height;
    
    if ($aspect_ratio > $thumb_ratio) {
        $new_height = $height;
        $new_width = $height * $aspect_ratio;
    } else {
        $new_width = $width;
        $new_height = $width / $aspect_ratio;
    }
    
    $thumb = imagecreatetruecolor($width, $height);
    $white = imagecolorallocate($thumb, 255, 255, 255);
    imagefill($thumb, 0, 0, $white);
    
    // نسخ وتغيير حجم الصورة
    $x_offset = ($width - $new_width) / 2;
    $y_offset = ($height - $new_height) / 2;
    
    imagecopyresampled($thumb, $source, $x_offset, $y_offset, 0, 0, $new_width, $new_height, $source_width, $source_height);
    
    // حفظ الصورة المصغرة
    switch ($type) {
        case IMAGETYPE_JPEG:
            imagejpeg($thumb, $thumb_path, 85);
            break;
        case IMAGETYPE_PNG:
            imagepng($thumb, $thumb_path, 8);
            break;
        case IMAGETYPE_GIF:
            imagegif($thumb, $thumb_path);
            break;
        case IMAGETYPE_WEBP:
            imagewebp($thumb, $thumb_path, 85);
            break;
    }
    
    // تحرير الذاكرة
    imagedestroy($source);
    imagedestroy($thumb);
    
    return true;
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
    <style>
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
    </style>
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
                        <form method="POST" id="eventForm" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="title" class="form-label">عنوان الفعالية *</label>
                                        <input type="text" class="form-control" id="title" name="title" 
                                            value="<?= htmlspecialchars($_POST['title'] ?? '') ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="category" class="form-label">التصنيف *</label>
                                        <select class="form-select" style="direction: ltr;" id="category" name="category" required>
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
                                    rows="5" required><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="location" class="form-label">الموقع *</label>
                                        <input type="text" class="form-control" id="location" name="location" 
                                            value="<?= htmlspecialchars($_POST['location'] ?? '') ?>" required>
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

                            <!-- منطقة رفع الصورة -->
                            <div class="mb-3">
                                <label class="form-label">صورة الفعالية</label>
                                
                                <div class="upload-area" id="uploadArea">
                                    <input type="file" id="event_image" name="event_image" 
                                        accept=".jpg,.jpeg,.png,.gif,.webp" style="display: none;">
                                    
                                    <div id="uploadContent">
                                        <i class="fas fa-cloud-upload-alt" style="font-size: 3rem; color: #007bff;"></i>
                                        <h5>انقر لاختيار صورة أو اسحبها هنا</h5>
                                        <p class="text-muted">الحد الأقصى: 5MB | المسموح: JPG, PNG, GIF, WEBP</p>
                                    </div>
                                </div>
                                
                                <div class="image-preview" id="imagePreview">
                                    <img src="" alt="معاينة الصورة" id="previewImage">
                                    <div class="mt-2">
                                        <button type="button" class="btn btn-sm btn-danger" onclick="removeImage()">
                                            ❌ إزالة الصورة
                                        </button>
                                    </div>
                                </div>
                                
                                <small class="text-muted">إذا لم تختَر صورة، سيتم استخدام صورة افتراضية</small>
                            </div>

                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <button type="reset" class="btn btn-secondary" onclick="resetForm()">مسح النموذج</button>
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
        var s="";
        
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
                    s= e.target.result;
                    imagePreview.style.display = 'block';
                    uploadContent.innerHTML = '<p class="text-success">✓ تم اختيار الصورة بنجاح</p>';
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
                <i class="fas fa-cloud-upload-alt" style="font-size: 3rem; color: #007bff;"></i>
                <h5>انقر لاختيار صورة أو اسحبها هنا</h5>
                <p class="text-muted">الحد الأقصى: 5MB | المسموح: JPG, PNG, GIF, WEBP</p>
            `;
        }
        
        // إعادة تعيين النموذج
        function resetForm() {
            removeImage();
        }

        const fileInput2 = document.getElementById('event_image');

    fileInput2.addEventListener('change', (event) => {
        // This function will execute when a file is selected or changed
        const selectedFiles = event.target.files; // FileList object
        
        if (selectedFiles.length > 0) {
            const firstFile = selectedFiles[0];
            document.getElementById('nijad').src=URL.createObjectURL(firstFile);
            s=firstFile;
            console.log('File Name:', firstFile.name);
            console.log('File Size:', firstFile.size, 'bytes');
            console.log('File Type:', firstFile.type);
            // You can perform further actions with the selected files here
        } else {
            console.log('No file selected.');
        }
    });
        
        // معاينة الفعالية في الوقت الحقيقي
        document.getElementById('eventForm').addEventListener('input', function() {
            const title = document.getElementById('title').value || 'عنوان الفعالية';
            const description = document.getElementById('description').value || 'وصف الفعالية';
            const category = document.getElementById('category').value || 'التصنيف';
            const location = document.getElementById('location').value || 'الموقع';
            const eventDate = document.getElementById('event_date').value || '2024-01-01T00:00';
            
            const previewHTML = `
                <div class="card">
                    <img id="nijad" src="${s}" 
                    class="card-img-top" 
                    alt="${title}" 
                    style="height: 200px; object-fit: cover;"
                    onerror="this.src='../assets/img/default-event.jpg'">
                    <div class="card-body">
                        <h5 class="card-title">${title}</h5>
                        <p class="card-text">${description.substring(0, 100)}${description.length > 100 ? '...' : ''}</p>
                        <p class="text-muted">
                            <small>📅 ${new Date(eventDate).toLocaleDateString('ar-SY')}</small><br>
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