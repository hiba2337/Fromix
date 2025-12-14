<?php 
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.html");
    exit();
}
$message_succ = "";
include "config.php";
if (isset($_POST["send"])){

  $userid = $_SESSION['user_id'];
  $fname  = $_POST["firstname"];
  $lname  = $_POST["lastname"];
  $email  = $_POST["email"];
  $phone  = $_POST["phone"];
  $message = $_POST["message"];
  
  $sql = "INSERT INTO contact_messages (user_id, firstname, lastname, email, phone, message)
          VALUES ('$userid', '$fname', '$lname', '$email', '$phone', '$message')";
  
  $result = mysqli_query($conn, $sql);
  
  if ($result) {
     $message_succ= "Message sent successfully!";
  } else {
      echo "Error: " . mysqli_error($conn);
  }
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
     <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
<!-- font awsome -->
<script src="https://kit.fontawesome.com/f14d152ebc.js" crossorigin="anonymous"></script>
  <!-- google font -->
      <link rel="preconnect" href="https://fonts.googleapis.com">
      <!-- font family -->
<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@200..1000&family=Open+Sans:ital,wght@0,300..800;1,300..800&family=Work+Sans:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <!-- style -->
    <link rel="stylesheet" href="../assets/css/pages/contact.css">
<title>Formix | Contact</title>

 </head>
<body>
  <?php 
if (!empty($message_succ)) {
    echo "<p class='success-message'>$message_succ</p>";
}
?>
  <div class="contact-page">

    <aside class="side-bar">
             <img src="../assets/images/logo/sidebar.png" alt="" class="logo">
 
             <ul>
                 <li>
             <a  href="dashboard.php">
               <i class="fa-regular fa-chart-bar fa-fw"></i>
               <span>Dashboard</span>
             </a>
                 <li>
             <a  href="formation.php">
               <i class="fa-solid fa-graduation-cap fa-fw"></i>
               <span>Courses</span>
             </a>
           </li>
                 <li>
                        <a  href="eventement.php">
              <i class="fa-solid fa-calendar-days"></i>
               <span>Events</span>
             </a>
                 </li>
                 <li>
                        <a  href="panier.php">
             <i class="fa-solid fa-cart-shopping"></i>
               <span>Cart</span>
             </a>
                 </li>
                 <li>
                        <a  href="Blog.php">
            <i class="fa-solid fa-pen-nib"></i>
 
               <span>Blog</span>
             </a>
                 </li>
                
                 <li>
                        <a  href="contact.php">
         <i class="fa-solid fa-phone"></i>
 
 
               <span>Contact</span>
             </a>
                 </li>
                
                 <li>
                        <a  href="logout.php">
 <i class="fa-solid fa-right-from-bracket"></i>
 
 
 
               <span>Log Out</span>
             </a>
                 </li>
                
                 
             </ul>
         </aside>
      <div class="contact" id="contact">
     <div class="image">
         <div class="img">
             <img src="../assets/images/landing/contact.png" alt="" >
         </div>
         <div class="text">
        <h3>Formix</h3>
        <p>Need help choosing the right course or facing a technical issue? Our support team is here for you 24/7.</p>
         </div>
        <ul class="social">
             <li>
               <a href="#" class="facebook">
                 <i class="fab fa-facebook-f"></i>
               </a>
             </li>
             <li>
               <a href="#" class="twitter">
                 <i class="fab fa-twitter"></i>
               </a>
             </li>
             <li>
               <a href="#" class="instagram">
             <i class="fa-brands fa-instagram"></i>
               </a>
             </li>
          
           </ul>
     </div>
 
     <div class="request">
         <div class="content">
 
           <h2>Get IN TOUCH</h2>
           <p>24/7 will answer your questions and problems</p>
           <form action="contact.php" method="post">
   <div class="name-input">
 
     <input type="text" placeholder="First Name" name="firstname">
     <input type="text" placeholder="Last Name" name="lastname">
   </div>
           <input type="email" placeholder="Email" name="email">

             <input type="text" placeholder=" Phone" name="phone">
              <textarea class="input" placeholder="Descirbe Your issue..." name="message"></textarea>
                 <input type="submit" value="Send" name="send" />
           </form>
         </div>
       </div>
 </div> 
  </div>
</body>
</html>