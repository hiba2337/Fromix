<?php
session_start();
include "config.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id'])) {
    die("Course not found");
}

$user_id = $_SESSION['user_id'];
$formation_id = (int) $_GET['id'];


$sql = "SELECT titre, price FROM formations WHERE id = $formation_id";
$result = mysqli_query($conn, $sql);
$formation = mysqli_fetch_assoc($result);

if (!$formation) {
    die("Course not found");
}


if (isset($_POST['submit_payment'])) {

    $amount = (float) $_POST['amount'];

    $receipt = $_FILES['receipt'];
    $receipt_name = time() . "_" . basename($receipt['name']);
    $upload_dir = "../assets/uploads/paiements/";

    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    if (move_uploaded_file($receipt['tmp_name'], $upload_dir . $receipt_name)) {

        $insert = mysqli_query($conn, "
            INSERT INTO paiements (user_id, formation_id, montant, preuve, statut)
            VALUES ($user_id, $formation_id, $amount, '$receipt_name', 'en attente')
        ");

        if ($insert) {
            $success = " Paiement envoyé avec succès. En attente de confirmation.";
        } else {
            $error = " Erreur lors de l'enregistrement du paiement.";
        }

    } else {
        $error = " Erreur lors de l'upload du reçu.";
    }
}
?>


<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<script src="https://kit.fontawesome.com/f14d152ebc.js" crossorigin="anonymous"></script>
<link rel="stylesheet" href="../assets/css/pages/paiement.css">
<title>Paiement | <?php echo $formation['titre']; ?></title>
</head>
<body>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Paiement | <?php echo $formation['titre']; ?></title>
<link rel="stylesheet" href="../assets/css/pages/paiement.css">
</head>
<body>

<div class="payment-container">
    <h2>Paiement – <?php echo $formation['titre']; ?></h2>

    <?php if (!empty($success)) echo "<p style='color:green'>$success</p>"; ?>
    <?php if (!empty($error)) echo "<p style='color:red'>$error</p>"; ?>

    <div class="bank-info">
        <p><strong>Montant :</strong> <?php echo $formation['price']; ?> DA</p>
        <p><strong>Banque :</strong> BNA</p>
        <p><strong>Compte :</strong> 123 456 789</p>
    </div>

    <form method="post" enctype="multipart/form-data">
        <input type="hidden" name="amount" value="<?php echo $formation['price']; ?>">

        <label>Preuve de paiement</label>
        <input type="file" name="receipt" required>

        <button type="submit" name="submit_payment">Envoyer le paiement</button>
    </form>
</div>

</body>
</html>
