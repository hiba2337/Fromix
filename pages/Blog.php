<?php
require_once "config.php";
session_start();
$search = $_GET['search-blog'] ?? "";
if (!isset($_GET["search-btn"]) || empty($search)) {
  $sql = "SELECT * FROM blog_posts ORDER BY date_post DESC";

} else {
    $clean = mysqli_real_escape_string($conn, $search);
    $sql = "SELECT * FROM blog_posts 
            WHERE titre LIKE '%$clean%' 
            ORDER BY date_post ASC";
}
$result = mysqli_query($conn, $sql);


?>
<?php
if(isset($_POST["Subs"])){
  $email = $_POST["email"];
  $userid = $_SESSION["user_id"];
 $sql = "INSERT IGNORE INTO blog_subscribers (user_id, email) VALUES ('$userid', '$email')";
    mysqli_query($conn, $sql);
    
  }
  mysqli_close($conn);
  ?>
<!--blog.html-->
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
<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@200..1000&family=Open+Sans:ital,wght@0,300..800;1,300..800&family=Work+Sans:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">

<!-- title -->
<title>Formix  |  Blog</title>

<!-- style -->
<link rel="stylesheet" href="../assets/css/pages/blog.css">
 <style>
  img{
    width: 100px;
  }
 </style>

</head>
<body>
    <div class="blog">
        <div class="side-bar">

           <img src="../assets/images/logo/sidebar.png" alt="" class="logo">


            <ul>
                <li>
            <a  href="dashboard.php">
              <i class="fa-regular fa-chart-bar fa-fw"></i>
              <span>Dashboard</span>
            </a>
               </li>
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
                       <a  href="contact.html">
        <i class="fa-solid fa-phone"></i>


              <span>Contact</span>
            </a>
                     </li>
               
                <li>
                       <a  href="../index.html">
<i class="fa-solid fa-right-from-bracket"></i>



              <span>Log Out</span>
            </a>
                </li>
               
                
            </ul>
        </div>
 <div class="blog-content">
  <!-- start head -->
    <div class="head">
<div class="main-title">
  <h2>Blog</h2>
</div>

  <form class="search-box" method="get" action="Blog.php">
    <input type="text" placeholder="Search.." name="search-blog" id="sr-blog">
    <button type="submit" id="sub-sr-blog" name="search-btn"><i class="fa-solid fa-magnifying-glass"></i></button>
  </form>
   </div>   
      <!-- end head -->

       <!-- start content -->
<div class="content">
 <?php if(mysqli_num_rows($result) > 0): ?>
  <?php while($row = mysqli_fetch_assoc($result)):
     $formatted_date = date("d M", strtotime($row['date_post']));
                ?>
                <div class="blog-card">
          <img src="<?php echo $row['image']; ?>" alt="">
        <div class="card-content">
          <h3><?php echo $row['titre']; ?></h3>
          
            <a href="#" class="btn"> Learn More </a>
          
          
        </div>
      </div> 
        <?php endwhile; ?>
          <?php else: ?>
             <?php if(!empty($search)): ?>
                    <p>No results found for "<?php echo htmlspecialchars($search); ?>"</p>
                <?php else: ?>
                    <p>No events available.</p>
                <?php endif; ?>
            <?php endif; ?>
  </div> 
 
  
      
  <!-- start footer -->
   <div class="footer">
    <div class="text">
      <div class="title">
  
        <h3>Stay Update</h3>
      </div>
      <p>Subscribe to our newsletter to get the latest news, events, and articles delivered straight to your inbox</p>
    </div>
    
    <form class="subs" method="post" action="Blog.php">
      <input type="email" name="email" id="" placeholder="Email">
      <input type="submit" value="Subscribe" name="Subs">
    </form>
  </div> 
  <!-- end footer -->
</div>
<!-- end content -->

</div> 
</div>
</body>
</html>