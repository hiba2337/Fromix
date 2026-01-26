<?php
session_start();
include "config.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if (isset($_GET['remove_id'])) {
    $remove_id = intval($_GET['remove_id']);
    mysqli_query($conn, "DELETE FROM panier WHERE id=$remove_id AND user_id=$user_id");
    header("Location: panier.php");
    exit();
}

if (isset($_GET['clear_cart'])) {
    mysqli_query($conn, "DELETE FROM panier WHERE user_id=$user_id");
    header("Location: panier.php");
    exit();
}

$sql = "
SELECT 
    p.id AS panier_id,
    f.titre,
    f.price,
    f.image,
    f.duration,
    f.level,
    fo.nom AS instructeur
FROM panier p
JOIN formations f ON p.formation_id = f.id
JOIN formateurs fo ON f.id_formateur = fo.id
WHERE p.user_id = $user_id
";

$result = mysqli_query($conn, $sql);

$cart_items = [];
$total = 0;

while ($row = mysqli_fetch_assoc($result)) {
    $cart_items[] = $row;
    $total += $row['price'];
}

$nombre_elements = count($cart_items);
$tax = $total * 0.19;
$grand_total = $total + $tax;
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Formix | Cart</title>
    <link rel="stylesheet" href="../assets/css/pages/panier.css">
</head>
<body>

<div class="cart">
<main class="main-content">

<h2>Cart (<?= $nombre_elements ?>)</h2>

<?php if ($nombre_elements == 0): ?>

    <div class="empty-cart">
        <h2>Votre panier est vide</h2>
        <a href="formation.php">Explorer les formations</a>
    </div>

<?php else: ?>

<div class="cart-items">

<?php foreach ($cart_items as $item): ?>
    <div class="cart-item">

        <img src="../uploads/<?= $item['image'] ?>" width="120">

        <div>
            <h3><?= $item['titre'] ?></h3>
            <p>Par <?= $item['instructeur'] ?></p>
            <p><?= $item['duration'] ?> h | <?= $item['level'] ?></p>
        </div>

        <div>
            <strong><?= number_format($item['price'],0) ?> DA</strong>
            <br>
            <a href="panier.php?remove_id=<?= $item['panier_id'] ?>">🗑 Supprimer</a>
        </div>

    </div>
<?php endforeach; ?>

</div>

<div class="order-summary">
    <p>Sous-total : <?= number_format($total,0) ?> DA</p>
    <p>TVA (19%) : <?= number_format($tax,0) ?> DA</p>
    <h3>Total : <?= number_format($grand_total,0) ?> DA</h3>

    <a href="panier.php?clear_cart=1">Vider le panier</a>
    <br><br>
    <a href="paiement.php">Procéder au paiement</a>
</div>

<?php endif; ?>

</main>
</div>

</body>
</html>
