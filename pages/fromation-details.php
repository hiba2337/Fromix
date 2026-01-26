

<?php
session_start();
include "config.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.html");
    exit();
}

if (!isset($_GET['id'])) {
    die("Course not found");
}

$formation_id = (int) $_GET['id'];
$sql = "
SELECT 
    f.*,
    fo.nom AS formateur_nom,
    fo.description AS formateur_desc,
    fo.photo AS formateur_photo,
    fo.specialite AS formateur_specia,
    p.nom AS partenaire_nom
FROM formations f
LEFT JOIN formateurs fo ON f.id_formateur = fo.id
LEFT JOIN partenaires p ON f.partenaire_id = p.id
WHERE f.id = $formation_id
";

$result = mysqli_query($conn, $sql);
$formation = mysqli_fetch_assoc($result);
$discount_percentage = null;
if (!empty($formation['price_before']) && $formation['price_before'] > $formation['price']) {
    $discount_percentage = round((($formation['price_before'] - $formation['price']) / $formation['price_before']) * 100);
}

if (!$formation) {
    die("Course not found");
}
$social_sql = "
SELECT * FROM formateur_social_links
WHERE formateur_id = {$formation['id_formateur']}
";
$socials = mysqli_query($conn, $social_sql);
$skills_sql = "
SELECT skill FROM formation_skills
WHERE formation_id = $formation_id
";
$skills = mysqli_query($conn, $skills_sql);
$plan_sql = "
SELECT * FROM formation_plan
WHERE formation_id = $formation_id
ORDER BY id ASC
";
$plan = mysqli_query($conn, $plan_sql);
$reviews_sql = "
SELECT 
    fr.rating,
    fr.commentaire,
    fr.date_creation,
    u.nom AS user_name
FROM formation_ratings fr
JOIN users u ON fr.user_id = u.id
WHERE fr.formation_id = $formation_id
ORDER BY fr.date_creation DESC
";

$reviews = mysqli_query($conn, $reviews_sql);
$avg_sql = "
SELECT ROUND(AVG(rating),1) AS avg_rating
FROM formation_ratings
WHERE formation_id = $formation_id
";
$avg_result = mysqli_query($conn, $avg_sql);
$avg = mysqli_fetch_assoc($avg_result);
$avg_rating = $avg['avg_rating'] ?? 0;




/* ===== ADD REVIEW ===== */
if (isset($_POST['submit-rating'])) {

    $user_id = $_SESSION['user_id'];
    $rating = (int) $_POST['rating'];
    $comment = trim($_POST['comment']);

    if ($rating >= 1 && $rating <= 5 && !empty($comment)) {

        $comment = mysqli_real_escape_string($conn, $comment);

     
        
       

        
    }
}
// add to cart

 $user_id = $_SESSION['user_id'];
if (isset($_POST['add_to_cart'])) {

    $formation_id = (int) $_POST['formation_id'];

    //  التحقق: هل الدورة موجودة في السلة؟
    $check = mysqli_query(
        $conn,
        "SELECT id FROM panier 
         WHERE user_id = $user_id AND formation_id = $formation_id"
    );

    if (mysqli_num_rows($check) == 0) {

        // إضافة الدورة
        $insert = mysqli_query(
            $conn,
            "INSERT INTO panier (user_id, formation_id)
             VALUES ($user_id, $formation_id)"
        );

        if ($insert) {
            $success = "Course added to cart successfully ✅";
        } else {
            $error = "Error while adding to cart ❌";
        }

    } else {
        $error = "This course is already in your cart ⚠️";
    }
}

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
     <link rel="stylesheet" href="../assets/css/pages/from-details.css">
     <style>
        img{
            width: 100px;
        }
     </style>
    <title>Formix | Courses_details</title>
</head>
<body>
  <?php if (!empty($success)): ?>
    <p   class="succ-cart"><?php echo $success; ?></p>
<?php endif; ?>

<?php if (!empty($error)): ?>
    <p class="error-cart"><?php echo $error; ?></p>
<?php endif; ?>

    <div class="cours-details" style="display:flex; gap:30px">
        <aside class="side-bar">
            <h3 class="logo">  <img src="../assets/images/logo/sidebar.png" alt=""></h3>
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
        
          
<div class="container" style="margin-right: 30px;">

  <div class="head">


<h2 class="title-course" id="title-course"><?php echo $formation['titre']; ?></h2> 
<img src="../assets/images/courses/<?php echo $formation['image']; ?>">

      
  </div>
  <div class="course-main">

      <div class="details">
<div class="instructor">
<img src="../assets/images/formateurs/<?php echo $formation['formateur_photo']; ?>">

<h3><?php echo $formation['formateur_nom']; ?></h3>
<span class=""><?php echo $formation['formateur_specia']; ?></span>
<p class="descr-instr"><?php echo $formation['formateur_desc']; ?></p>
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
<div class="description">
  <h3>Description:</h3>
  <p class="descr-cours">Explore how machines learn, think, and make decisions using real-world AI models. 
This course will give you hands-on experience with AI tools and techniques, 
preparing you for more advanced studies in machine learning and data science.</p>

</div>
<div class="skills">
 <h3>Skills You Will Gain</h3>
<ul>

<?php while ($sk = mysqli_fetch_assoc($skills)) : ?>
    <li><?php echo $sk['skill']; ?></li>
<?php endwhile; ?>

</ul>
</div>
<div class="planing">
<h2>Planning Dynamique</h2>

<ul>
<?php while ($pl = mysqli_fetch_assoc($plan)) : ?>
    <li>
        <h3><?php echo $pl['week_title']; ?></h3>
        <p><?php echo $pl['week_description']; ?></p>
    </li>
<?php endwhile; ?>
</ul>


</div>
      </div>
    <div class="card-details">
    <div class="price">
        <div class="price-values">
            <span class="price-af"><?php echo $formation['price']; ?>DA</span>
            <?php if (!empty($formation['price_before'])): ?>
            <span class="price-bef"><?php echo $formation['price_before']; ?>DA</span>
            <?php endif; ?>
        </div>
        <?php if ($discount_percentage): ?>
        <div class="percentag">-<?php echo $discount_percentage; ?>%</div>
        <?php endif; ?>
    </div>

    <div class="info">
        <p class="level"><strong>Level:</strong> <?php echo $formation['level']; ?></p>
        <p class="duration"><strong>Duration:</strong> <?php echo $formation['duration']; ?> Weeks</p>
        <p class="seats"><strong>Available Seats:</strong> <?php echo $formation['available_seats']; ?></p>
        <p class="partner"><strong>Partner:</strong> <?php echo $formation['partenaire_nom']; ?></p>
        <p class="category"><strong>Catégorie:</strong> <?php echo $formation['categorie']; ?></p>
    </div>
<form method="post">
   <div class="course-actions">
    
     <input type="hidden" name="formation_id" value="<?php echo $formation['id']; ?>">
     <button type="submit" name="add_to_cart" class="btn-add"> Add to Cart</button>
        <button class="btn-enroll" >
         <a href="paiement.php?id=<?php echo $formation['id']; ?>" class="btn-enroll">
  Enroll Now
</a>

</button>
    </div>
</form>

   
</div>
  </div>
  <!-- Add Review Section -->
<div class="add-review">
  <h2>Leave a Review</h2>

<form method="post" class="review-form"  action="fromation-details.php">

  <div class="rating">
  <span>Rating:</span>
   <div class="stars">
        <input type="radio" name="rating" id="star5" value="5">
        <label for="star5"><i class="fa-solid fa-star"></i></label>

        <input type="radio" name="rating" id="star4" value="4">
        <label for="star4"><i class="fa-solid fa-star"></i></label>

        <input type="radio" name="rating" id="star3" value="3">
        <label for="star3"><i class="fa-solid fa-star"></i></label>

        <input type="radio" name="rating" id="star2" value="2">
        <label for="star2"><i class="fa-solid fa-star"></i></label>

        <input type="radio" name="rating" id="star1" value="1">
        <label for="star1"><i class="fa-solid fa-star"></i></label>
      </div>
</div>


    <div class="form-group">
      <label>Your Comment</label>
      <textarea name="comment" placeholder="Write your experience with this course..." required></textarea>
    </div>

    <button type="submit" class="btn-submit" name="submit-rating">Submit Review</button>

  </form>
</div>
<div class="all-comment">
<h4>All Comment</h4>
<?php while ($r = mysqli_fetch_assoc($reviews)) : ?>

  <div class="comment">
    
    <div class="comment-header">
      <div class="user">

        <img src="../assets/images/testimonials/testi6.png" alt="User" class="user-img" style="width:50px; height:50px; border-radius:50%;">
   <strong><?php echo $r['user_name']; ?></strong>  
      </div>

      <div class="comment-rating">
        <?php
    for ($i=1; $i<=5; $i++) {
        echo $i <= $r['rating']
            ? '<i class="fa-solid fa-star"></i>'
            : '<i class="fa-regular fa-star"></i>';
    }
    ?>
      </div>
    </div>
     <p  class="comment-text"><?php echo $r['commentaire']; ?></p>
    
    <span class="comment-date"><?php echo $r['date_creation']; ?></span>
  </div>
 <?php endwhile; ?>
</div>

</div>  
    </div>
    
<!-- Testimonials Section -->

</body>
</html>