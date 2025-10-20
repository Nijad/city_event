<?php include 'db.php'; ?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>عن الدليل - دليل فعاليات المدينة</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
    <?php include 'header.php'; ?>

    <main class="container py-5">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <h1 class="text-center mb-4">عن دليل فعاليات المدينة</h1>
                
                <section class="mb-5">
                    <h2>رؤيتنا</h2>
                    <p>نهدف إلى إنشاء منصة شاملة تجمع جميع الفعاليات والأنشطة في المدينة في مكان واحد، مما يسهل على السكان والزوار اكتشاف واستكشاف ما تقدمه المدينة من فرص ترفيهية وثقافية وتعليمية.</p>
                </section>

                <section class="mb-5">
                    <h2>فريق العمل</h2>
                    <div class="row">
                        <div class="col-md-6 text-center mb-6">
                            <div class="team-member">
                                <img src="assets/img/team1.jpg" class="rounded-circle mb-3" width="150" height="150" alt="فريق العمل">
                                <h5>د. باسل الخطيب</h5>
                                <p class="text-muted">المشرف على المشروع</p>
                            </div>
                        </div>
                        <div class="col-md-6 text-center mb-6">
                            <div class="team-member">
                                <img src="assets/img/team2.jpg" class="rounded-circle mb-3" width="150" height="150" alt="فريق العمل">
                                <h5>فريق التطوير</h5>
                                <p class="text-muted">نجاد الخوري</p>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="mb-5">
                    <h2>سياسات تقديم الفعاليات</h2>
                    <div class="card">
                        <div class="card-body">
                            <h5>شروط إضافة الفعاليات:</h5>
                            <ul>
                                <li>يجب أن تكون الفعالية قانونية ومرخصة</li>
                                <li>يجب تقديم معلومات دقيقة وكاملة عن الفعالية</li>
                                <li>يجب أن تكون الفعالية مفتوحة للجمهور أو وفق شروط واضحة</li>
                                <li>يحق للإدارة حذف أي فعالية لا تلتزم بالشروط</li>
                            </ul>
                            
                            <h5>لإضافة فعالية جديدة:</h5>
                            <p>يمكنك التواصل معنا عبر <a href="contact.php">صفحة الاتصال</a> أو تسجيل الدخول إلى لوحة التحكم إذا كنت مشرفاً.</p>
                        </div>
                    </div>
                </section>

                <section class="mb-5">
                    <h2>شركاؤنا</h2>
                    <div class="row text-center">
                        <div class="col-md-3 mb-3">
                            <div class="partner-logo p-3 border rounded">
                                <h6>بلدية المدينة</h6>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="partner-logo p-3 border rounded">
                                <h6>المركز الثقافي</h6>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="partner-logo p-3 border rounded">
                                <h6>نادي الرياضة</h6>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="partner-logo p-3 border rounded">
                                <h6>جمعية الفنون</h6>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </main>

    <?php include 'footer.php'; ?>
</body>
</html>