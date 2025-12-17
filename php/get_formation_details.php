<?php
require_once 'config.php';

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

try {
    // بناء الاستعلام الأساسي
    $sql = "SELECT f.id, f.titre, f.description, f.prix, f.duree, f.niveau, 
                   f.image, f.places_disponibles,
                   c.nom as categorie_id,
                   CONCAT(form.nom, ' ', form.prenom) as instructor_name
            FROM formations f
            LEFT JOIN categories c ON f.categorie_id = c.id
            LEFT JOIN formateurs form ON f.formateur_id = form.id
            WHERE f.is_published = 1";
    
    $params = [];
    
    // تطبيق الفلاتر
    if (isset($_GET['category']) && !empty($_GET['category'])) {
        $sql .= " AND f.categorie_id = ?";
        $params[] = $_GET['category'];
    }
    
    if (isset($_GET['level']) && !empty($_GET['level'])) {
        $sql .= " AND f.niveau = ?";
        $params[] = $_GET['level'];
    }
    
    // ترتيب حسب السعر
    if (isset($_GET['price']) && $_GET['price'] === 'asc') {
        $sql .= " ORDER BY f.prix ASC";
    } elseif (isset($_GET['price']) && $_GET['price'] === 'desc') {
        $sql .= " ORDER BY f.prix DESC";
    } else {
        $sql .= " ORDER BY f.created_at DESC";
    }
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $formations = $stmt->fetchAll();
    
    jsonResponse(true, 'تم جلب التكوينات بنجاح', $formations);
    
} catch(PDOException $e) {
    jsonResponse(false, 'خطأ في جلب البيانات: ' . $e->getMessage());
}
?>