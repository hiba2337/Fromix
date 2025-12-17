<?php
require_once 'config.php';

header('Content-Type: application/json');

$search = isset($_GET['q']) ? cleanInput($_GET['q']) : '';

if (empty($search)) {
    jsonResponse(false, 'يرجى إدخال كلمة البحث');
}

try {
    $stmt = $pdo->prepare("
        SELECT f.id, f.titre, f.description, f.prix, f.duree, f.niveau, f.image,
               c.nom as categorie_id, form.nom as instructor_name
        FROM formations f
        LEFT JOIN categories c ON f.categorie_id = c.id
        LEFT JOIN formateurs form ON f.formateur_id = form.id
        WHERE f.is_published = 1
        AND (f.titre LIKE ? OR f.description LIKE ? OR c.nom LIKE ?)
        ORDER BY f.created_at DESC
        LIMIT 20
    ");
    
    $searchTerm = "%{$search}%";
    $stmt->execute([$searchTerm, $searchTerm, $searchTerm]);
    $formations = $stmt->fetchAll();
    
    jsonResponse(true, 'تم البحث بنجاح', $formations);
    
} catch(PDOException $e) {
    jsonResponse(false, 'خطأ: ' . $e->getMessage());
}
?>