<?php
session_start();
include '../db.php';

if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}

// جلب جميع الفعاليات
$events = $pdo->query("SELECT * FROM events ORDER BY event_date DESC")->fetchAll();

// معالجة الحذف
if (isset($_GET['delete'])) {
    $event_id = $_GET['delete'];
    
    try {
        $stmt = $pdo->prepare("DELETE FROM events WHERE id = ?");
        $stmt->execute([$event_id]);
        
        header('Location: events.php?success=تم حذف الفعالية بنجاح');
        exit;
    } catch (PDOException $e) {
        header('Location: events.php?error=حدث خطأ أثناء الحذف');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إدارة الفعاليات - لوحة التحكم</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <?php include 'sidebar.php'; ?>

            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">إدارة الفعاليات</h1>
                    <a href="add_event.php" class="btn btn-success">➕ إضافة فعالية جديدة</a>
                </div>

                <?php if (isset($_GET['success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show">
                        <?= $_GET['success'] ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if (isset($_GET['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show">
                        <?= $_GET['error'] ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <div class="card shadow">
                    <div class="card-header">
                        <h5 class="mb-0">قائمة الفعاليات</h5>
                    </div>
                    <div class="card-body">
                        <?php if ($events): ?>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>الصورة</th>
                                            <th>العنوان</th>
                                            <th>التصنيف</th>
                                            <th>الموقع</th>
                                            <th>التاريخ</th>
                                            <th>الإجراءات</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($events as $event): ?>
                                            <tr>
                                                <td><?= $event['id'] ?></td>
                                                <td>
                                                    <img src="<?= $event['image'] ?>" 
                                                         alt="<?= $event['title'] ?>" 
                                                         style="width: 60px; height: 40px; object-fit: cover;" 
                                                         class="rounded">
                                                </td>
                                                <td>
                                                    <strong><?= $event['title'] ?></strong>
                                                    <br>
                                                    <small class="text-muted">
                                                        <?= substr($event['description'], 0, 50) ?>...
                                                    </small>
                                                </td>
                                                <td>
                                                    <span class="badge bg-primary"><?= $event['category'] ?></span>
                                                </td>
                                                <td><?= $event['location'] ?></td>
                                                <td>
                                                    <?= date('Y-m-d H:i', strtotime($event['event_date'])) ?>
                                                    <br>
                                                    <small class="text-<?= 
                                                        strtotime($event['event_date']) > time() ? 'success' : 'danger' 
                                                    ?>">
                                                        <?= strtotime($event['event_date']) > time() ? 'قادمة' : 'منتهية' ?>
                                                    </small>
                                                </td>
                                                <td>
                                                    <div class="btn-group btn-group-sm">
                                                        <a href="../event.php?id=<?= $event['id'] ?>" 
                                                           class="btn btn-outline-primary" target="_blank">
                                                            👁️ عرض
                                                        </a>
                                                        <a href="edit_event.php?id=<?= $event['id'] ?>" 
                                                           class="btn btn-outline-warning">
                                                            ✏️ تعديل
                                                        </a>
                                                        <button onclick="confirmDelete(<?= $event['id'] ?>)" 
                                                                class="btn btn-outline-danger">
                                                            🗑️ حذف
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-5">
                                <h5 class="text-muted">لا توجد فعاليات</h5>
                                <p class="text-muted">ابدأ بإضافة فعالية جديدة</p>
                                <a href="add_event.php" class="btn btn-primary">إضافة فعالية</a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    function confirmDelete(eventId) {
        if (confirm('هل أنت متأكد من حذف هذه الفعالية؟ سيتم حذف جميع الحجوزات المرتبطة بها أيضاً.')) {
            window.location.href = 'events.php?delete=' + eventId;
        }
    }
    </script>
</body>
</html>