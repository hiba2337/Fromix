

<?php 
session_start();

?>

<!--login.html-->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
 
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <!-- font awsome -->
<script src="https://kit.fontawesome.com/f14d152ebc.js" crossorigin="anonymous"></script>
  <!-- google font -->
      <link rel="preconnect" href="https://fonts.googleapis.com">
      <!-- font family -->
<link rel="stylesheet" href="../assets/css/normilze.css">
    <link rel="stylesheet" href="../assets/css/pages/login.css">
   <title> Formix | Log in</title>
      
</head>
<body>
 <div class="container">
    <div class="form">
<div class="text">
    <h2>
WELCOME BACK
    </h2>
    <p>welcome back ! please inter your details</p>
    </div>
    <?php 
$error_msg = "";


if (isset($_POST["send"])){

    $email = $_POST["email"];
    $pass  = $_POST["password"];
    require_once "config.php";

  
    if (empty($email) || empty($pass)) {
        $error_msg = "Please enter both email and password!";
    } else {

        $sql = "SELECT * FROM users WHERE email ='$email'";
        $result = mysqli_query( $conn ,$sql);

        if (mysqli_num_rows( $result) > 0){

            $user = mysqli_fetch_assoc($result );
if ($user){

    if (password_verify($pass , $user["password"])){
 $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_email'] = $user['email'];
header("Location: dashboard.php");           
exit();

    } else {
        $error_msg = "Incorrect password!";
    }
}

        } else {
            $error_msg = "Email not found!";
        }
    }
}

// mysqli_close($conn);
?>
   <form action="login.php" method="post" autocomplete="off">
    
        <label for="email">Email</label>
        <input type="email" name="email" id="email" placeholder="Enter Your Email">
    
  
        <label for="password">Password</label>
        <input type="password" name="password" id="password" placeholder="Enter Your Password" >
        <?php if (!empty($error_msg)): ?>
    <div class="error" style="color:red; margin-bottom:10px;">
        <?= $error_msg ?>
    </div>
<?php endif; ?>
   <input type="submit" name="send" id="send" value="sing-in">

   </form>
<div class="sing-in">
    <p>Don’t have an account ?</p>
    <a href="sign-up.html">sign-up</a>
</div>

 
    
</div>
<div class="img">
    
    </div>

 </div>   
</body>
</html>



