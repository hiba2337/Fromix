<?php
require_once 'config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(false, 'طريقة طلب غير صحيحة');
}

if (!isLoggedIn()) {
    jsonResponse(false, 'يجب تسجيل الدخول أولاً');
}

$data = json_decode(file_get_contents('php://input'), true);
$formationId = isset($data['formation_id']) ? (int)$data['formation_id'] : 0;

if ($formationId <= 0) {
    jsonResponse(false, 'معرف التكوين غير صحيح');
}

try {
    $userId = $_SESSION['user_id'];
    
    // التحقق من وجود التكوين
    $stmt = $pdo->prepare("SELECT id, titre, prix FROM formations WHERE id = ? AND is_published = 1");
    $stmt->execute([$formationId]);
    $formation = $stmt->fetch();
    
    if (!$formation) {
        jsonResponse(false, 'التكوين غير موجود');
    }
    
    // التحقق إذا كان موجود مسبقاً
    $stmt = $pdo->prepare("SELECT id FROM panier WHERE user_id = ? AND formation_id = ?");
    $stmt->execute([$userId, $formationId]);
    
    if ($stmt->fetch()) {
        jsonResponse(false, 'التكوين موجود مسبقاً في السلة');
    }
    
    // الإضافة
    $stmt = $pdo->prepare("INSERT INTO panier (user_id, formation_id) VALUES (?, ?)");
    $stmt->execute([$userId, $formationId]);
    
    // عدد العناصر
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM panier WHERE user_id = ?");
    $stmt->execute([$userId]);
    $count = $stmt->fetch()['count'];
    
    // تسجيل النشاط
    logActivity('ADD_TO_CART', 'panier', $pdo->lastInsertId(), "أضاف: {$formation['titre']}");
    
    jsonResponse(true, 'تمت الإضافة بنجاح', ['count' => $count]);
    
} catch(PDOException $e) {
    jsonResponse(false, 'خطأ: ' . $e->getMessage());
}
?>