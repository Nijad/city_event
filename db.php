<?php
session_start();

$host = getenv('DB_HOST') ?: 'localhost:3306';
$dbname = getenv('DB_NAME') ?: 'city_events';
$username = getenv('DB_USER') ?: 'city_events_user';
$password = getenv('DB_PASS') ?: 'StrongPassword123!';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// إنشاء الجداول إذا لم تكن موجودة
function createTables($pdo)
{
    $tables = [
        "CREATE TABLE IF NOT EXISTS events (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            description TEXT,
            category VARCHAR(100),
            location VARCHAR(255),
            event_date DATETIME,
            image VARCHAR(255),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )",

        "CREATE TABLE IF NOT EXISTS admin (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )",

        "CREATE TABLE IF NOT EXISTS bookings (
            id INT AUTO_INCREMENT PRIMARY KEY,
            event_id INT,
            name VARCHAR(100) NOT NULL,
            email VARCHAR(100) NOT NULL,
            phone VARCHAR(20),
            tickets INT DEFAULT 1,
            booking_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE
        )",

        "INSERT IGNORE INTO admin (username, password) VALUES ('admin', 'admin')"
    ];

    foreach ($tables as $sql) {
        $pdo->exec($sql);
    }
}

// استدعاء الدالة لإنشاء الجداول
createTables($pdo);
