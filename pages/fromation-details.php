


<?php 
  session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.html");
exit();

}
include "config.php";



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

      <h2 class="title-course" id="title-course">Introduction to Artificial Intelligence</h2> 

      <img src="../assets/images/courses/courseai.png" alt="">
  </div>
  <div class="course-main">

      <div class="details">
<div class="instructor">


<img src="../assets/images/testimonials/testi3.png" alt="Formateur">


<h3>Dr. Yacine Benali</h3>
<span class="">Expert in Artificial Intelligence and Machine Learning</span>
<p class="descr-instr">Expert in Artificial Intelligence with 10+ years of experience in machine learning and data science. 
 She has worked with multiple international projects and loves teaching AI in an interactive and practical way.</p>
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
<li>  Understanding core AI concepts and terminology</li>
<li>  Applying basic machine learning techniques</li>
<li> Working with datasets and AI models</li>
<li> Solving real-world problems using AI approaches</li>
<li>Critical thinking about AI ethics and societal impact</li>
<li>Building simple AI projects from scratch</li>
</ul>
</div>
<div class="planing">
<h2>Planning Dynamique</h2>
<ul>
<li>
<h3><span>Week 1 :</span> Introduction</h3>
  <p>Overview of Artificial Intelligence, history, key concepts, and applications.</p>
</li>
<li>
<h3><span>Week 2 :</span> Machine Learning Basics</h3>
  <p>Introduction to supervised and unsupervised learning, data preprocessing, and simple algorithms.</p>
</li>
<li>
<h3><span>Week 3 :</span> Week 3: AI Models & Tools</h3>
  <p>Hands-on experience with AI frameworks and building simple predictive models.</p>
</li>
<li>
<h3><span>Week 4 :</span> Final Project</h3>
  <p>Apply your knowledge to a real-world AI project, test and evaluate your model, and present results.</p>
</li>
</ul>
</div>
      </div>
      <div class="card-details">
<div class="price">
<div class="price-values">
<span class="price-af">12000DA</span>
<span class="price-bef">16800DA</span>
</div>
<div class="percentag">-40%</div>
</div>

<div class="info">
<p class="level"><strong>Level:</strong>  Intermediate</p>
<p class="duration"><strong>Duration:</strong> 3 Weeks</p>
<p class="seats"><strong>Available Seats:</strong> 25</p>
<p class="partner"><strong>Partner:</strong> OpenClassrooms </p>
<p class="category"><strong>Catégorie:</strong> AI</p>
</div>
<div class="course-actions">
<button class="btn-add">Add to Cart</button>
<!-- <button class="btn-enroll">Enroll Now </button> -->
<button class="btn-enroll"> <a href="paiement.html" target="_blank">Enroll Now</a></button>
</div>
      </div>
  </div>
  <!-- Add Review Section -->
<div class="add-review">
  <h2>Leave a Review</h2>

  <form action="#" method="post" class="review-form">

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

    <button type="submit" class="btn-submit">Submit Review</button>

  </form>
</div>
<div class="all-comment">
<h4>All Comment</h4>
  <div class="comment">
    
    <div class="comment-header">
      <div class="user">

        <img src="../assets/images/testimonials/testi6.png" alt="User" class="user-img" style="width:50px; height:50px; border-radius:50%;">
        <strong class="user-name">Narimane Mezazga</strong>
      </div>

      <div class="comment-rating">
        <i class="fa-solid fa-star"></i>
        <i class="fa-solid fa-star"></i>
        <i class="fa-solid fa-star"></i>
        <i class="fa-solid fa-star"></i>
        <i class="fa-regular fa-star"></i>
      </div>
    </div>
    <p class="comment-text">
      Cours très instructif ! Le formateur explique très clairement et les exemples pratiques sont excellents.
    </p>
    <span class="comment-date">17 Décembre 2025</span>
  </div>
  <div class="comment">
    
    <div class="comment-header">
      <div class="user">

        <img src="../assets/images/testimonials/testi1.png" alt="User" class="user-img" style="width:50px; height:50px; border-radius:50%;">
        <strong class="user-name">Narimane Mezazga</strong>
      </div>
      <div class="comment-rating">
        <i class="fa-solid fa-star"></i>
        <i class="fa-solid fa-star"></i>
        <i class="fa-solid fa-star"></i>
        <i class="fa-regular fa-star"></i>
        <i class="fa-regular fa-star"></i>
      </div>

    </div>
    <p class="comment-text">
      Cours très instructif ! Le formateur explique très clairement et les exemples pratiques sont excellents.
    </p>
    <span class="comment-date">17 Décembre 2025</span>
  </div>
</div>

</div>  
    </div>
    
<!-- Testimonials Section -->

</body>
</html>