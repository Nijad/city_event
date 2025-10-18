<?php include 'db.php';
include 'functions/images.php';
$event_id = $_GET['id'] ?? 0;
$stmt = $pdo->prepare("SELECT * FROM events WHERE id = ?");
$stmt->execute([$event_id]);
$event = $stmt->fetch();

if (!$event) {
    header('Location: events.php');
    exit;
}

// جلب فعاليات ذات صلة
$related_stmt = $pdo->prepare("SELECT * FROM events WHERE category = ? AND id != ? LIMIT 3");
$related_stmt->execute([$event['category'], $event_id]);
$related_events = $related_stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $event['title'] ?> - دليل فعاليات المدينة</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/styles.css">
</head>

<body>
    <?php include 'header.php'; ?>

    <main class="container py-5">
        <div class="row">
            <div class="col-md-8">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.php">الرئيسية</a></li>
                        <li class="breadcrumb-item"><a href="events.php">الفعاليات</a></li>
                        <li class="breadcrumb-item active"><?= $event['title'] ?></li>
                    </ol>
                </nav>

                <article>
                    <h1 class="mb-3"><?= $event['title'] ?></h1>

                    <div class="row mb-4">
                        <img
                            src="<?= getEventImage($event['image']) ?>"
                            class="col-md-6 img-fluid rounded"
                            alt="<?= $event['title'] ?>"
                            onerror="this.src='assets/img/default-event.jpg'" />
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <h5>معلومات الفعالية</h5>
                                    <p><strong>📅 التاريخ:</strong> <?= date('Y-m-d H:i', strtotime($event['event_date'])) ?></p>
                                    <p><strong>📍 الموقع:</strong> <?= $event['location'] ?></p>
                                    <p><strong>🏷️ التصنيف:</strong> <?= $event['category'] ?></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <h3>وصف الفعالية</h3>
                        <p><?= nl2br($event['description']) ?></p>
                    </div>

                    <div class="d-flex gap-2 mb-4">
                        <button class="btn btn-success book-event"
                            data-event-id="<?= $event['id'] ?>"
                            data-event-title="<?= $event['title'] ?>">
                            🎫 احجز مقعدك الآن
                        </button>
                        <button class="btn btn-outline-primary" onclick="shareEvent()">
                            📤 مشاركة
                        </button>
                        <button class="btn btn-outline-secondary" onclick="addToCalendar()">
                            📅 إضافة إلى التقويم
                        </button>
                    </div>
                </article>
            </div>

            <div class="col-md-4">
                <!-- الفعاليات ذات الصلة -->
                <div class="card">
                    <div class="card-header">
                        <h5>فعاليات ذات صلة</h5>
                    </div>
                    <div class="card-body">
                        <?php if ($related_events): ?>
                            <?php foreach ($related_events as $related): ?>
                                <div class="mb-3">
                                    <h6><a href="event.php?id=<?= $related['id'] ?>"><?= $related['title'] ?></a></h6>
                                    <small class="text-muted"><?= date('Y-m-d', strtotime($related['event_date'])) ?></small>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p>لا توجد فعاليات ذات صلة</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php include 'footer.php'; ?>

    <!-- نموذج الحجز -->
    <div class="modal fade booking-modal" id="bookingModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">حجز فعالية: <span id="bookingEventTitle"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
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
                            <select name="tickets" class="form-select" required>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/main.js"></script>
    <script>
        function shareEvent() {
            if (navigator.share) {
                navigator.share({
                    title: <?= json_encode($event['title']) ?>,
                    text: <?= json_encode(substr($event['description'], 0, 100) . '...') ?>,
                    url: window.location.href
                });
            } else {
                alert('شارك الرابط: ' + window.location.href);
            }
        }

        function addToCalendar() {
            // Use server-provided event date safely as a JS string
            const eventDate = new Date(<?= json_encode($event['event_date']) ?>);

            // Format dates for Google Calendar: YYYYMMDDTHHMMSS (use ISO and strip non-digits)
            const iso = eventDate.toISOString();
            const when = iso.replace(/[-:]/g, '').split('.')[0];

            const title = <?= json_encode($event['title']) ?>;
            const details = <?= json_encode($event['description']) ?>;
            const location = <?= json_encode($event['location']) ?>;

            const calendarUrl = `https://calendar.google.com/calendar/render?action=TEMPLATE` +
                `&text=${encodeURIComponent(title)}` +
                `&dates=${when}/${when}` +
                `&details=${encodeURIComponent(details)}` +
                `&location=${encodeURIComponent(location)}`;

            window.open(calendarUrl, '_blank');
        }
    </script>
</body>

</html>