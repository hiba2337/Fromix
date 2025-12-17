<?php
require_once 'config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(false, 'طريقة طلب غير صحيحة');
}

if (!isLoggedIn()) {
    jsonResponse(false, 'يجب تسجيل الدخول');
}

$data = json_decode(file_get_contents('php://input'), true);
$cartId = isset($data['cart_id']) ? (int)$data['cart_id'] : 0;

if ($cartId <= 0) {
    jsonResponse(false, 'معرف غير صحيح');
}

try {
    $userId = $_SESSION['user_id'];
    
    // التحقق من ملكية العنصر
    $stmt = $pdo->prepare("SELECT id FROM panier WHERE id = ? AND user_id = ?");
    $stmt->execute([$cartId, $userId]);
    
    if (!$stmt->fetch()) {
        jsonResponse(false, 'العنصر غير موجود');
    }
    
    // الحذف
    $stmt = $pdo->prepare("DELETE FROM panier WHERE id = ? AND user_id = ?");
    $stmt->execute([$cartId, $userId]);
    
    // عدد العناصر المتبقية
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM panier WHERE user_id = ?");
    $stmt->execute([$userId]);
    $count = $stmt->fetch()['count'];
    
    logActivity('REMOVE_FROM_CART', 'panier', $cartId);
    
    jsonResponse(true, 'تم الحذف بنجاح', ['count' => $count]);
    
} catch(PDOException $e) {
    jsonResponse(false, 'خطأ: ' . $e->getMessage());
}
?>