<?php include 'db.php'; ?>
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
        <section class="hero-section">
            <div class="container">
                <h2 class="text-center mb-4">فعاليات بارزة هذا الأسبوع</h2>
                <div id="eventsSlider" class="carousel slide" data-bs-ride="carousel">
                    <div class="carousel-inner" id="sliderContent">
                        <!-- سيتم ملؤها عبر JavaScript -->
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#eventsSlider" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">السابق</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#eventsSlider" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">التالي</span>
                    </button>
                </div>
            </div>
        </section>

        <!-- التصنيفات السريعة -->
        <section class="categories py-5">
            <div class="container">
                <h2 class="text-center mb-4">تصفح الفعاليات حسب التصنيف</h2>
                <div class="row text-center">
                    <div class="col-md-3 mb-3">
                        <a href="events.php?category=ثقافة" class="btn btn-outline-primary btn-lg w-100">ثقافة</a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="events.php?category=رياضة" class="btn btn-outline-primary btn-lg w-100">رياضة</a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="events.php?category=موسيقى" class="btn btn-outline-primary btn-lg w-100">موسيقى</a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="events.php?category=عائلية" class="btn btn-outline-primary btn-lg w-100">عائلية</a>
                    </div>
                </div>
            </div>
        </section>

        <!-- أحدث الفعاليات -->
        <section class="latest-events py-5 bg-light">
            <div class="container">
                <h2 class="text-center mb-4">أحدث الفعاليات</h2>
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
    
    <!-- زر العودة للأعلى -->
    <button id="scrollToTop" class="scroll-to-top">↑</button>

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
</body>
</html>