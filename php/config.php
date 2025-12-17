<?php
/**
 * Configuration File - Fromix Platform
 * Version 2.0 - Updated for new database structure
 */

// بدء الجلسة
session_start();

// إعدادات قاعدة البيانات
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'formix_training_platform_v2'); // القاعدة الجديدة

// إعدادات الموقع
define('SITE_URL', 'http://localhost/Fromix');
define('SITE_NAME', 'Fromix Training Platform');

// الاتصال بقاعدة البيانات باستخدام PDO
try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
} catch(PDOException $e) {
    die("خطأ في الاتصال بقاعدة البيانات: " . $e->getMessage());
}

// الاتصال بقاعدة البيانات باستخدام MySQLi (للتوافق مع الكود القديم)
$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if (!$conn) {
    die("فشل الاتصال: " . mysqli_connect_error());
}
mysqli_set_charset($conn, "utf8mb4");

/**
 * دالة للتحقق من تسجيل الدخول
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

/**
 * دالة للحصول على معلومات المستخدم الحالي
 */
function getCurrentUser() {
    if (!isLoggedIn()) {
        return null;
    }
    
    global $pdo;
    $stmt = $pdo->prepare("
        SELECT u.id, u.nom, u.prenom, u.email, r.nom_role as role 
        FROM users u
        LEFT JOIN roles r ON u.role_id = r.id
        WHERE u.id = ?
    ");
    $stmt->execute([$_SESSION['user_id']]);
    return $stmt->fetch();
}

/**
 * دالة لحماية الصفحات (تتطلب تسجيل دخول)
 */
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: ' . SITE_URL . '/pages/login.php');
        exit;
    }
}

/**
 * دالة للتحقق من الدور
 */
function hasRole($role) {
    if (!isLoggedIn()) {
        return false;
    }
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === $role;
}

/**
 * دالة تنظيف البيانات المدخلة
 */
function cleanInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

/**
 * دالة لإرسال استجابة JSON
 */
function jsonResponse($success, $message, $data = null) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'data' => $data
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

/**
 * دالة لتسجيل الأنشطة
 */
function logActivity($action_type, $table_name = null, $record_id = null, $description = null) {
    if (!isLoggedIn()) {
        return;
    }
    
    global $pdo;
    
    $ip = $_SERVER['REMOTE_ADDR'] ?? null;
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? null;
    
    $stmt = $pdo->prepare("
        INSERT INTO activity_logs 
        (user_id, action_type, table_name, record_id, description, ip_address, user_agent) 
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");
    
    $stmt->execute([
        $_SESSION['user_id'],
        $action_type,
        $table_name,
        $record_id,
        $description,
        $ip,
        $user_agent
    ]);
}

/**
 * دالة لإرسال إشعار
 */
function sendNotification($user_id, $type, $titre, $message, $lien = null) {
    global $pdo;
    
    $stmt = $pdo->prepare("
        INSERT INTO notifications (user_id, type, titre, message, lien) 
        VALUES (?, ?, ?, ?, ?)
    ");
    
    $stmt->execute([$user_id, $type, $titre, $message, $lien]);
}

/**
 * دالة لتوليد slug من نص
 */
function generateSlug($text) {
    // تحويل للأحرف الصغيرة
    $text = strtolower($text);
    // إزالة الأحرف الخاصة
    $text = preg_replace('/[^a-z0-9\s-]/', '', $text);
    // تحويل المسافات إلى شرطات
    $text = preg_replace('/[\s-]+/', '-', $text);
    // إزالة الشرطات من البداية والنهاية
    $text = trim($text, '-');
    
    return $text;
}
?>
