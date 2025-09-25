<?php
session_start();
include '../db.php';

// إذا كان المستخدم مسجل الدخول بالفعل، توجيهه للوحة التحكم
if (isset($_SESSION['admin'])) {
    header('Location: dashboard.php');
    exit;
}

$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        $error_message = 'يرجى إدخال اسم المستخدم وكلمة المرور';
    } else {
        // التحقق من بيانات الدخول
        $stmt = $pdo->prepare("SELECT * FROM admin WHERE username = ?");
        $stmt->execute([$username]);
        $admin = $stmt->fetch();
        
        if ($admin && $password === $admin['password']) { // في الواقع يجب استخدام password_verify()
            $_SESSION['admin'] = [
                'id' => $admin['id'],
                'username' => $admin['username'],
                'login_time' => time()
            ];
            header('Location: dashboard.php');
            exit;
        } else {
            $error_message = 'اسم المستخدم أو كلمة المرور غير صحيحة';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل الدخول - لوحة التحكم</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/styles.css">
    <style>
        .login-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .login-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            width: 100%;
            max-width: 400px;
        }
        .login-header {
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
            color: white;
            border-radius: 15px 15px 0 0;
            padding: 30px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <h2>🚀 لوحة التحكم</h2>
                <p class="mb-0">دليل فعاليات المدينة</p>
            </div>
            
            <div class="card-body p-4">
                <?php if ($error_message): ?>
                    <div class="alert alert-danger alert-dismissible fade show">
                        <?= $error_message ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                
                <form method="POST" id="loginForm">
                    <div class="mb-3">
                        <label for="username" class="form-label">اسم المستخدم</label>
                        <input type="text" class="form-control" id="username" name="username" 
                               value="<?= $_POST['username'] ?? '' ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="password" class="form-label">كلمة المرور</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-100 py-2">تسجيل الدخول</button>
                </form>
                
                <div class="text-center mt-3">
                    <small class="text-muted">بيانات الدخول الافتراضية: admin / admin</small>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const username = document.getElementById('username').value;
            const password = document.getElementById('password').value;
            
            if (!username || !password) {
                e.preventDefault();
                alert('يرجى ملء جميع الحقول');
            }
        });
    </script>
</body>
</html>