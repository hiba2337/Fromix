<?php
require_once 'config.php';

$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
$cart_items = [];
$total = 0;

// للزوار - من Session
if (!$user_id) {
    if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
        $ids = implode(',', $_SESSION['cart']);
        $sql = "SELECT * FROM courses WHERE id IN ($ids)";
        $result = mysqli_query($conn, $sql);
        
        while ($row = mysqli_fetch_assoc($result)) {
            $cart_items[] = $row;
            $total += $row['price'];
        }
    }
} else {
    // للمستخدمين المسجلين - من DB
    $sql = "SELECT c.*, p.date_ajout 
            FROM panier p
            INNER JOIN courses c ON p.course_id = c.id
            WHERE p.user_id = $user_id";
    
    $result = mysqli_query($conn, $sql);
    
    while ($row = mysqli_fetch_assoc($result)) {
        $cart_items[] = $row;
        $total += $row['price'];
    }
}

echo json_encode([
    'success' => true,
    'items' => $cart_items,
    'total' => $total,
    'count' => count($cart_items)
]);

mysqli_close($conn);
?>