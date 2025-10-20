<?php include 'db.php';
include 'functions/images.php';
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>الفعاليات - دليل فعاليات المدينة</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
    <?php include 'header.php'; ?>

    <main class="container py-5">
        <h1 class="text-center mb-4">الفعاليات</h1>
        
        <!-- شريط البحث والتصفية -->
        <div class="row mb-4">
            <div class="col-md-6">
                <input type="text" id="searchInput" class="form-control" placeholder="ابحث في الفعاليات...">
            </div>
            <div class="col-md-3">
                <select id="categoryFilter" class="form-select" style="direction: ltr;">
                    <option value="">جميع التصنيفات</option>
                    <option value="ثقافة">ثقافة</option>
                    <option value="رياضة">رياضة</option>
                    <option value="موسيقى">موسيقى</option>
                    <option value="عائلية">عائلية</option>
                </select>
            </div>
            <div class="col-md-3">
                <input type="date" id="dateFilter" class="form-control">
            </div>
        </div>

        <!-- قائمة الفعاليات -->
        <div class="row" id="eventsList">
            <?php
            $category = $_GET['category'] ?? '';
            $sql = "SELECT * FROM events WHERE event_date >= CURDATE()";
            
            if ($category) {
                $sql .= " AND category = ?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$category]);
            } else {
                $stmt = $pdo->query($sql);
            }
            
            $events = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if ($events) {
                foreach ($events as $event) {
                    echo "
                        <div class='col-md-4 mb-4 event-card' data-category='{$event['category']}' data-date='{$event['event_date']}'>
                            <div class='card h-100'>
                            <img src='" . getEventImage($event['image']) . 
                            "' class='card-img-top' alt='" . 
                            htmlspecialchars($event['title'], ENT_QUOTES) . 
                            "' onerror=\"this.src='assets/img/default-event.jpg'\" style='height: 200px; object-fit: cover;'>

                            <div class='card-body'>
                                <h5 class='card-title'>{$event['title']}</h5>
                                <p class='card-text'>" . substr($event['description'], 0, 100) . "...</p>
                                <p class='text-muted'>
                                    <small>📅 " . date('Y-m-d', strtotime($event['event_date'])) . "</small><br>
                                    <small>📍 {$event['location']}</small><br>
                                    <small>🏷️ {$event['category']}</small>
                                </p>
                            </div>
                            <div class='card-footer'>
                                <a href='event.php?id={$event['id']}' class='btn btn-primary'>التفاصيل</a>
                                <button class='btn btn-success book-event' 
                                        data-event-id='{$event['id']}' 
                                        data-event-title='{$event['title']}'>
                                    احجز الآن
                                </button>
                            </div>
                        </div>
                    </div>";
                }
            } else {
                echo "<div class='col-12'><p class='text-center'>لا توجد فعاليات متاحة حالياً.</p></div>";
            }
            ?>
        </div>
    </main>

    <?php include 'footer.php'; ?>

    <!-- نموذج الحجز -->
    <div class="modal fade booking-modal" id="bookingModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">حجز فعالية: <span id="bookingEventTitle"></span></h5>
                    <button type="button" class="btn-close m-0" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="bookingForm">
                        <input type="hidden" name="event_id" id="eventId">
                        <div class="mb-3">
                            <label class="form-label">الاسم الكامل</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">البريد الإلكتروني</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">رقم الهاتف</label>
                            <input type="tel" name="phone" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">عدد التذاكر</label>
                            <select name="tickets" class="form-select" style="direction:ltr;" required>
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                                <option value="4">4</option>
                                <option value="5">5</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">تأكيد الحجز</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- page-specific script -->
    <script src="assets/js/events.js"></script>
</body>
</html>