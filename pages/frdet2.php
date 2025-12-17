<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.html");
    exit();
}

include "config.php";

// التأكد من وجود id الدورة في الرابط
if (!isset($_GET['id'])) {
    echo "Course ID is missing!";
    exit();
}

$course_id = intval($_GET['id']);

// ===== جلب بيانات الدورة مع المدرب والشريك =====
$sql = "SELECT f.*, fr.nom AS formateur_nom, fr.description AS formateur_desc, fr.photo AS formateur_photo, 
        fr.id AS formateur_id, p.nom AS partenaire_nom, p.logo AS partenaire_logo
        FROM formations f
        LEFT JOIN formateurs fr ON f.id_formateur = fr.id
        LEFT JOIN partenaires p ON f.partenaire_id = p.id
        WHERE f.id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $course_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Course not found!";
    exit();
}

$course = $result->fetch_assoc();

// ===== جلب المهارات =====
$skills = [];
$skill_stmt = $conn->prepare("SELECT skill FROM formation_skills WHERE formation_id = ?");
$skill_stmt->bind_param("i", $course_id);
$skill_stmt->execute();
$skill_result = $skill_stmt->get_result();
while ($row = $skill_result->fetch_assoc()) {
    $skills[] = $row['skill'];
}

// ===== جلب خطة الدورة =====
$planning = [];
$plan_stmt = $conn->prepare("SELECT week_title, week_description FROM formation_plan WHERE formation_id = ?");
$plan_stmt->bind_param("i", $course_id);
$plan_stmt->execute();
$plan_result = $plan_stmt->get_result();
while ($row = $plan_result->fetch_assoc()) {
    $planning[] = $row;
}

// ===== جلب روابط المدرب الاجتماعية =====
$social_links = [];
if ($course['formateur_id']) {
    $social_stmt = $conn->prepare("SELECT type, url FROM formateur_social_links WHERE formateur_id = ?");
    $social_stmt->bind_param("i", $course['formateur_id']);
    $social_stmt->execute();
    $social_result = $social_stmt->get_result();
    while ($row = $social_result->fetch_assoc()) {
        $social_links[$row['type']] = $row['url'];
    }
}

// ===== جلب تقييمات الدورة =====
$ratings = [];
$rating_stmt = $conn->prepare("SELECT r.rating, r.commentaire, r.date_creation, u.nom AS user_nom, u.photo AS user_photo
                               FROM formation_ratings r
                               JOIN users u ON r.user_id = u.id
                               WHERE r.formation_id = ?
                               ORDER BY r.date_creation DESC");
$rating_stmt->bind_param("i", $course_id);
$rating_stmt->execute();
$rating_result = $rating_stmt->get_result();
while ($row = $rating_result->fetch_assoc()) {
    $ratings[] = $row;
}
// ===== حفظ التقييم الجديد =====
if (isset($_POST['submit_review'])) {
    $user_id = $_SESSION['user_id'];
    $formation_id = intval($_POST['formation_id']);
    $rating = floatval($_POST['rating']);
    $comment = trim($_POST['comment']);

    if ($rating >= 1 && $rating <= 5 && !empty($comment)) {
        $insert_stmt = $conn->prepare("INSERT INTO formation_ratings (formation_id, user_id, rating, commentaire) VALUES (?, ?, ?, ?)");
        $insert_stmt->bind_param("iids", $formation_id, $user_id, $rating, $comment);
        $insert_stmt->execute();

        // إعادة تحميل الصفحة لتحديث التعليقات بعد الإضافة
        header("Location: course-details.php?id=" . $formation_id);
        exit();
    } else {
        echo "<script>alert('Please provide a valid rating and comment.');</script>";
    }
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<script src="https://kit.fontawesome.com/f14d152ebc.js" crossorigin="anonymous"></script>
<link rel="stylesheet" href="../assets/css/pages/from-details.css">
<title>Formix | <?php echo htmlspecialchars($course['titre']); ?></title>
</head>
<body>

<div class="cours-details" style="display:flex; gap:30px">
    <aside class="side-bar">
        <h3 class="logo"><img src="../assets/images/logo/sidebar.png" alt=""></h3>
        <ul>
            <li><a href="dashboard.php"><i class="fa-regular fa-chart-bar fa-fw"></i><span>Dashboard</span></a></li>
            <li><a href="formation.php"><i class="fa-solid fa-graduation-cap fa-fw"></i><span>Courses</span></a></li>
            <li><a href="eventement.php"><i class="fa-solid fa-calendar-days"></i><span>Events</span></a></li>
            <li><a href="panier.php"><i class="fa-solid fa-cart-shopping"></i><span>Cart</span></a></li>
            <li><a href="Blog.php"><i class="fa-solid fa-pen-nib"></i><span>Blog</span></a></li>
            <li><a href="contact.php"><i class="fa-solid fa-phone"></i><span>Contact</span></a></li>
            <li><a href="logout.php"><i class="fa-solid fa-right-from-bracket"></i><span>Log Out</span></a></li>
        </ul>
    </aside>

    <div class="container" style="margin-right: 30px;">
        <div class="head">
            <h2 class="title-course"><?php echo htmlspecialchars($course['titre']); ?></h2> 
            <img src="../assets/images/courses/<?php echo $course['image']; ?>" alt="">
        </div>

        <div class="course-main">
            <div class="details">
                <div class="instructor">
                    <img src="../assets/images/testimonials/<?php echo $course['formateur_photo']; ?>" alt="Formateur">
                    <h3><?php echo htmlspecialchars($course['formateur_nom']); ?></h3>
                    <p class="descr-instr"><?php echo htmlspecialchars($course['formateur_desc']); ?></p>
                    <ul class="social">
                        <?php foreach ($social_links as $type => $url) {
                            echo "<li><a href='$url' target='_blank'><i class='fab fa-$type'></i></a></li>";
                        } ?>
                    </ul>
                </div>

                <div class="description">
                    <h3>Description:</h3>
                    <p class="descr-cours"><?php echo htmlspecialchars($course['description']); ?></p>
                </div>

                <div class="skills">
                    <h3>Skills You Will Gain</h3>
                    <ul>
                        <?php foreach ($skills as $skill) {
                            echo "<li>" . htmlspecialchars($skill) . "</li>";
                        } ?>
                    </ul>
                </div>

                <div class="planing">
                    <h2>Planning Dynamique</h2>
                    <ul>
                        <?php foreach ($planning as $week) {
                            echo "<li><h3><span>{$week['week_title']} :</span></h3><p>{$week['week_description']}</p></li>";
                        } ?>
                    </ul>
                </div>
            </div>

            <div class="card-details">
                <div class="price">
                    <div class="price-values">
                        <span class="price-af"><?php echo $course['price']; ?>DA</span>
                        <span class="price-bef"><?php echo $course['budget']; ?>DA</span>
                    </div>
                    <div class="percentag">
                        <?php echo intval((($course['budget'] - $course['price']) / $course['budget']) * 100); ?>%
                    </div>
                </div>

                <div class="info">
                    <p class="level"><strong>Level:</strong> <?php echo $course['level']; ?></p>
                    <p class="duration"><strong>Duration:</strong> <?php echo $course['duration']; ?> Weeks</p>
                    <p class="seats"><strong>Available Seats:</strong> <?php echo $course['available_seats']; ?></p>
                    <p class="partner"><strong>Partner:</strong> <?php echo htmlspecialchars($course['partenaire_nom']); ?></p>
                    <p class="category"><strong>Catégorie:</strong> <?php echo htmlspecialchars($course['categorie']); ?></p>
                </div>

                <div class="course-actions">
                    <button class="btn-add">Add to Cart</button>
                    <button class="btn-enroll"><a href="paiement.html" target="_blank">Enroll Now</a></button>
                </div>
            </div>
        </div>

        <div class="add-review">
  <h2>Leave a Review</h2>
  <form action="" method="post" class="review-form">
    <input type="hidden" name="formation_id" value="<?php echo $course['id']; ?>">
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

    <button type="submit" name="submit_review" class="btn-submit">Submit Review</button>
  </form>
</div>

        <div class="all-comment">
            <h4>All Reviews</h4>
            <?php foreach ($ratings as $rating) { ?>
                <div class="comment">
                    <div class="comment-header">
                        <div class="user">
                            <img src="../assets/images/testimonials/<?php echo $rating['user_photo']; ?>" class="user-img" style="width:50px; height:50px; border-radius:50%;">
                            <strong class="user-name"><?php echo htmlspecialchars($rating['user_nom']); ?></strong>
                        </div>
                        <div class="comment-rating">
                            <?php
                            $fullStars = floor($rating['rating']);
                            $emptyStars = 5 - $fullStars;
                            for ($i = 0; $i < $fullStars; $i++) echo "<i class='fa-solid fa-star'></i>";
                            for ($i = 0; $i < $emptyStars; $i++) echo "<i class='fa-regular fa-star'></i>";
                            ?>
                        </div>
                    </div>
                    <p class="comment-text"><?php echo htmlspecialchars($rating['commentaire']); ?></p>
                    <span class="comment-date"><?php echo date("d M Y", strtotime($rating['date_creation'])); ?></span>
                </div>
            <?php } ?>
        </div>

    </div>
</div>

</body>
</html>