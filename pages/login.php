<?php
session_start();

// إذا كان مسجل دخول بالفعل، انتقل للدشبورد
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

require_once('../php/config.php');

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = cleanInput($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($email) || empty($password)) {
        $error = 'يرجى إدخال البريد الإلكتروني وكلمة المرور';
    } else {
        $stmt = $pdo->prepare("
            SELECT u.id, u.nom, u.prenom, u.email, u.password, r.nom_role 
            FROM users u
            LEFT JOIN roles r ON u.role_id = r.id
            WHERE u.email = ? AND u.is_active = 1
        ");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if (!$user) {
            $error = 'البريد الإلكتروني أو كلمة المرور غير صحيحة';
        } else {
            // للتجربة فقط: password123 للجميع
            // في الإنتاج: استخدم password_verify($password, $user['password'])
            if ($password === 'password123') {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['prenom'] . ' ' . $user['nom'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_role'] = $user['nom_role'];
                
                // تسجيل النشاط
                logActivity('LOGIN', 'users', $user['id'], 'تسجيل دخول ناجح');
                
                // تحديث آخر دخول
                $stmt = $pdo->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
                $stmt->execute([$user['id']]);
                
                header("Location: dashboard.php");
                exit();
            } else {
                $error = 'البريد الإلكتروني أو كلمة المرور غير صحيحة';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/pages/login.css">
    <title>Fromix | Connexion</title>
</head>
<body>
    <div class="login-container">
        <div class="login-left">
            <img src="../assets/images/logo/sidebar.png" alt="Fromix Logo">
            <h1>Bienvenue sur Fromix</h1>
            <p>Connectez-vous pour accéder à vos formations</p>
        </div>
        
        <div class="login-right">
            <form method="POST" class="login-form">
                <h2>Connexion</h2>
                
                <?php if ($error): ?>
                <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>
                
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required 
                           value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label for="password">Mot de passe</label>
                    <input type="password" id="password" name="password" required>
                </div>
                
                <button type="submit" class="btn-login">Se connecter</button>
                
                <p class="signup-link">
                    Pas encore de compte? <a href="sign-up.php">S'inscrire</a>
                </p>
                
                <div class="test-accounts">
                    <p><strong>Comptes de test:</strong></p>
                    <p>Email: admin@fromix.dz | Password: password123</p>
                    <p>Email: nadia.boudiaf@gmail.com | Password: password123</p>
                </div>
            </form>
        </div>
    </div>
</body>
</html>