<!--formation.html-->
<?php 
  session_start();
if (!isset($_SESSION['user_id'])) {
   header("Location: ../index.html");
exit();

}
include "config.php";
$where = [];

if (!empty($_GET['category'])) {
    $category = mysqli_real_escape_string($conn, $_GET['category']);
    $where[] = "f.categorie = '$category'";
}

if (!empty($_GET['level'])) {
    $level = mysqli_real_escape_string($conn, $_GET['level']);
    $where[] = "f.level = '$level'";
}

if (!empty($_GET['duration'])) {
    if ($_GET['duration'] == 'less4') {
        $where[] = "f.duration < 4";
    } elseif ($_GET['duration'] == 'more8') {
        $where[] = "f.duration > 8";
    } else {
        $d = (int) $_GET['duration'];
        $where[] = "f.duration = $d";
    }
}

if (!empty($_GET['price'])) {
    if ($_GET['price'] == 'free') {
        $where[] = "f.price = 0";
    }
}

/* ================= SQL ================= */
$sql = "
SELECT 
    f.*,
    ROUND(AVG(fr.rating),1) AS avg_rating
FROM formations f
LEFT JOIN formation_ratings fr 
    ON f.id = fr.formation_id
";

if (!empty($where)) {
    $sql .= " WHERE " . implode(" AND ", $where);
}

$sql .= " GROUP BY f.id";

$result = mysqli_query($conn, $sql);

?>


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
    <!-- style -->
    <link rel="stylesheet" href="../assets/css/pages/formation.css">
<title>Formix | Courses</title>

 <style>
        img{
            max-width: 100%;
            width: 300px;
        }
    </style></head>
<body>
    <div class="courses">
      <aside class="side-bar">

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
        
<main class="courses-content">
    <header >
     <div class="main-title">
       <h2>Courses</h2>
     </div>
     
    <div class="filter-box">
  <form class="filter-course" method="get" action="formation.php">

    <div class="filter-item">
      <i class="fa-solid fa-layer-group"></i>
      <select name="category" id="category">
        <option value="">Category</option>
        <option value="web">Web Development</option>
        <option value="design">UI/UX Design</option>
        <option value="data">Data Science</option>
        <option value="security">Cyber Security</option>
        <option value="Marketing">Marketing</option>
        <option value="Design">Design</option>
        <option value="Artificial Intelligence">AI</option>
      </select>
    </div>

    <div class="filter-item">
      <i class="fa-solid fa-chart-line"></i>
      <select name="level" id="duration">
        <option value="">Level</option>
        <option value="beginner">Beginner</option>
        <option value="intermediate">Intermediate</option>
        <option value="advanced">Advanced</option>
      </select>
    </div>

    <div class="filter-item">
      <i class="fa-solid fa-clock"></i>
<select name="duration" id="duration">
  <option value=""> Duration</option>
  <option value="less4">Less than 4 Weeks</option>
  <option value="4">4 Weeks</option>
  <option value="6">6 Weeks</option>
  <option value="8">8 Weeks</option>
  <option value="more8">More than 8 Weeks</option>
</select>

    </div>

    <div class="filter-item">
      <i class="fa-solid fa-dollar-sign"></i>
      <select name="price" id="price">
        <option value="">Price</option>
        <option value="low">Low to High</option>
        <option value="high">High to Low</option>
        <option value="free">Free</option>
      </select>
    </div>

    <button type="submit" class="btn-filter">Filter</button>
  </form>
</div>


           </header> 
           <div class="content">
<?php while ($row = mysqli_fetch_assoc($result)) : ?>

<div class="box">
    <div class="image">
        <img src="../assets/images/courses/<?php echo $row['image']; ?>" alt="">
    </div>

    <div class="info">
        <h3 class="title"><?php echo $row['titre']; ?></h3>
        <p><?php echo $row['description']; ?></p>

        <p class="level">
            <strong>Level:</strong> <?php echo $row['level']; ?>
        </p>

        <p class="duration">
            <strong>Duration:</strong> <?php echo $row['duration']; ?> Weeks
        </p>
    </div>

    <div class="course-footer">
        <a href="fromation-details.php?id=<?php echo $row['id']; ?>"  target="_blank">
            View Details
        </a>

        <div class="rating">
            <div class="rating-value">
                <span class="stars">
                    <?php
                    $rating = $row['avg_rating'] ?? 0;
                    $stars = round($rating);

                    for ($i = 1; $i <= 5; $i++) {
                        if ($i <= $stars) {
                            echo '<i class="fa-solid fa-star"></i>';
                        } else {
                            echo '<i class="fa-regular fa-star"></i>';
                        }
                    }
                    ?>
                </span>
                <span class="star-value">
                    <?php echo $rating > 0 ? $rating : 'No rating'; ?>
                </span>
            </div>

            <div class="price">
                <?php echo $row['price']; ?> DA
            </div>
        </div>
    </div>
</div>

<?php endwhile; ?>



      </div>
 



</main>
   </div> 








 
 
</body>
</html>