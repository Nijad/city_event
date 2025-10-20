<?php include 'db.php'; ?>
<?php include 'functions/images.php'; ?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>دليل فعاليات المدينة</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
    <?php include 'header.php'; ?>
    
    <main class="container-fluid p-0">
        <!-- سلايدر الفعاليات البارزة -->
        <section class="featured-events-section py-5 bg-light">
            <div class="container">
                <h2 class="text-center mb-5 section-title">فعاليات بارزة هذا الأسبوع</h2>
                
                <?php
                // جلب الفعاليات البارزة (الفعاليات القادمة خلال الأسبوع القادم)
                $stmt = $pdo->prepare("SELECT * FROM events WHERE event_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY) AND status = 'active' ORDER BY event_date ASC LIMIT 5");
                $stmt->execute();
                $featured_events = $stmt->fetchAll();
                
                if ($featured_events) {
                ?>
                <div id="featuredEventsCarousel" class="carousel slide" data-bs-ride="carousel">
                    <div class="carousel-indicators">
                        <?php foreach ($featured_events as $index => $event): ?>
                            <button type="button" data-bs-target="#featuredEventsCarousel" 
                                    data-bs-slide-to="<?= $index ?>" 
                                    class="<?= $index === 0 ? 'active' : '' ?>" 
                                    aria-current="<?= $index === 0 ? 'true' : 'false' ?>" 
                                    aria-label="Slide <?= $index + 1 ?>"></button>
                        <?php endforeach; ?>
                    </div>
                    
                    <div class="carousel-inner rounded-3">
                        <?php foreach ($featured_events as $index => $event): ?>
                            <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>">
                                <div class="row align-items-center">
                                    <div class="col-md-6">
                                        <!-- <img src="<?= $event['image'] ?: 'assets/img/default-event.jpg' ?>" 
                                            class="d-block w-100 rounded-3" 
                                            alt="<?= htmlspecialchars($event['title']) ?>"
                                            style="height: 400px; object-fit: cover;"> -->

                                            <img src="<?= getEventImage($event['image']) ?>" 
                                                class="card-img-top" 
                                                alt="<?= $event['title'] ?>"
                                                style="height: 400px; object-fit: cover;"
                                                onerror="this.src='assets/img/default-event.jpg'">
                                    </div>
                                    <div class="col-md-6">
                                        <div class="carousel-content p-4">
                                            <h3 class="text-primary"><?= htmlspecialchars($event['title']) ?></h3>
                                            <p class="lead"><?= htmlspecialchars(substr($event['description'], 0, 150)) ?>...</p>
                                            <div class="event-info mb-3">
                                                <p class="mb-1"><strong>📅 التاريخ:</strong> <?= date('Y-m-d H:i', strtotime($event['event_date'])) ?></p>
                                                <p class="mb-1"><strong>📍 المكان:</strong> <?= htmlspecialchars($event['location']) ?></p>
                                                <p class="mb-1"><strong>🏷️ التصنيف:</strong> <?= htmlspecialchars($event['category']) ?></p>
                                            </div>
                                            <div class="carousel-buttons">
                                                <a href="event.php?id=<?= $event['id'] ?>" class="btn btn-primary me-2">عرض التفاصيل</a>
                                                <button class="btn btn-success book-event" 
                                                        data-event-id="<?= $event['id'] ?>" 
                                                        data-event-title="<?= htmlspecialchars($event['title']) ?>">
                                                    احجز الآن
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <button class="carousel-control-prev" type="button" data-bs-target="#featuredEventsCarousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">السابق</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#featuredEventsCarousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">التالي</span>
                    </button>
                </div>
                <?php } else { ?>
                    <div class="text-center py-5">
                        <div class="alert alert-info">
                            <h4>لا توجد فعاليات بارزة هذا الأسبوع</h4>
                            <p>يمكنك تصفح جميع الفعاليات المتاحة</p>
                            <a href="events.php" class="btn btn-primary">عرض جميع الفعاليات</a>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </section>

        <!-- باقي المحتوى يبقى كما هو -->
        <section class="categories py-5">
            <div class="container">
                <h2 class="text-center mb-4 section-title">تصفح الفعاليات حسب التصنيف</h2>
                <div class="row text-center">
                    <div class="col-md-3 mb-3">
                        <a href="events.php?category=ثقافة" class="btn btn-outline-primary btn-lg w-100 category-btn">
                            🎭 ثقافة
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="events.php?category=رياضة" class="btn btn-outline-primary btn-lg w-100 category-btn">
                            ⚽ رياضة
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="events.php?category=موسيقى" class="btn btn-outline-primary btn-lg w-100 category-btn">
                            🎵 موسيقى
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="events.php?category=عائلية" class="btn btn-outline-primary btn-lg w-100 category-btn">
                            👨‍👩‍👧‍👦 عائلية
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <section class="latest-events py-5 bg-light">
            <div class="container">
                <h2 class="text-center mb-4 section-title">أحدث الفعاليات</h2>
                <div class="row" id="eventsGrid">
                    <!-- سيتم ملؤها عبر JavaScript -->
                </div>
                <div class="text-center mt-4">
                    <a href="events.php" class="btn btn-primary btn-lg">عرض جميع الفعاليات</a>
                </div>
            </div>
        </section>
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
                            <select name="tickets" class="form-select" style="direction: ltr;" required>
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

    <!-- page-specific scripts are loaded in footer.php (shared). -->
</body>
</html>