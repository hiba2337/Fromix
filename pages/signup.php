
<?php
session_start();
include "config.php";
$error_msg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name     = $_POST['name'] ;
    $email    = $_POST['email'] ;
    $pass     = $_POST['pass'] ;
    $phone    = $_POST['phone'] ;
    $role_id  = $_POST['role_id'] ;

    if (empty($name) || empty($email) || empty($pass) || empty($role_id)) {
        $error_msg = "All fields marked with * are required!";
    } else {
        $check_sql = "SELECT id FROM users WHERE email='$email' LIMIT 1";
        $check_result = mysqli_query($conn, $check_sql);

        if (mysqli_num_rows($check_result) > 0) {
            $error_msg = "Email already exists!";
        } else {
            $hashed_pass = password_hash($pass, PASSWORD_DEFAULT);

            $insert_sql = "INSERT INTO users (nom, email, password, telephone , role_id)
                           VALUES ('$name', '$email', '$hashed_pass', '$phone', '$role_id')";

            if (mysqli_query($conn, $insert_sql)) {
          $_SESSION['user_id'] = mysqli_insert_id($conn);
    $_SESSION['user_name'] = $name;
    $_SESSION['user_email'] = $email;
header("Location: dashboard.php");           
exit();
            } else {
                $error_msg = "Database error: " . mysqli_error($conn);
            }
        }
    }
}

mysqli_close($conn);
?>


<!--sign-up.html-->

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> Formix / Sign up</title>
    <!-- font awsome -->
<script src="https://kit.fontawesome.com/f14d152ebc.js" crossorigin="anonymous"></script>
  <!-- google font -->
      <link rel="preconnect" href="https://fonts.googleapis.com">
      <!-- font family -->
<link rel="stylesheet" href="../assets/css/normilze.css">
    <link rel="stylesheet" href="../assets/css/pages/sign-up.css">
</head>
<body>
 <div class="container">
    <div class="form">
<div class="text">
    <h2>
 Creat Account
    </h2>
    <p>Fields marked with (*) are required.</p>
</div>
<form action="signup.php" method="post" autocomplete="off">
 
    <label for="name">Username</label>
    <input type="text" name="name" id="name" placeholder="Enter Your Name *" required>
    
    <label for="email">Email</label>
    <input type="email" name="email" id="email" placeholder="Enter Your Email *" required>
     
    <label for="pass">Password</label>
    <input type="password" name="pass" id="pass" placeholder="Enter Your Password *" required>
   
    <label for="phone">Your Phone</label>
    <input type="text" name="phone" id="phone" placeholder="Enter Your Phone">

    <label>Role</label>
<div class="role-options">
    <div>
        <input type="radio" name="role_id" id="student" value="1" required>
        <label for="student">Student</label>
    </div>
    <div>
        <input type="radio" name="role_id" id="trainer" value="2">
        <label for="trainer">Trainer</label>
    </div>
</div>
<?php if (!empty($error_msg)): ?>
    <div class="error" style="color:red; margin-bottom:10px;">
        <?= $error_msg ?>
    </div>
<?php endif; ?>


    <input type="submit" name="creat" id="send" value="Sign-up">
</form>


<div class="sing-in">
    <p>Already have an account ?</p>
    <a href="login.html">sing-in</a>
</div>
</div>
    <div class="img">

    </div>
 </div>   
</body>
</html>


