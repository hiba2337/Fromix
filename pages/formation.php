<!--formation.html-->
<?php 
  session_start();
if (!isset($_SESSION['user_id'])) {
   header("Location: ../index.html");
exit();

}
include "config.php";
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
  <form class="filter-course">

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
<div class="box ">
  <div class="image">

    <img src="../assets/images/courses/course01.png" alt="">
  </div>
  <div class="info">
    <h3 class="title">Introduction to Artificial Intelligence</h3>
    <p>Explore how machines learn, think, and make decisions using real-world AI models.
     
    </p>
    <p class="level"><strong>Level:</strong> Intermediate</p>
    <p class="duration"><strong>Duration:</strong> 3 Weeks</p>
  </div>
  <div class="course-footer">
    <a href="fromation-details.php">View Details</a>
   <div class="rating">
    <div class="rating-value">

      <span class="stars">
        <i class="fa-solid fa-star"></i>
        <i class="fa-solid fa-star"></i>
        <i class="fa-solid fa-star"></i>
        <i class="fa-regular fa-star"></i>
        <i class="fa-regular fa-star"></i>
      </span>
      <span class="star-value">3.6</span>
   
    </div>
    <div class="price">12000DA</div>
     
  </div>
   
</div>
   
</div>

<div class="box ">
  <img src="../assets/images/courses/course03.png" alt="">
  <div class="info">
    <h3 class="title">Digital Marketing Strategy</h3>
    <p>Discover how to promote brands effectively using SEO, content marketing, and social media.</p>
    <p class="level"><strong>Level:</strong> All Levels</p>
    <p class="duration"><strong>Duration:</strong> 6 Weeks</p>
  </div>
  <div class="course-footer">
      <a href="fromation-details.php">View Details</a>
   
      <div class="rating">
        <div class="rating-value">

          <span class="stars">
            <i class="fa-solid fa-star"></i>
            <i class="fa-solid fa-star"></i>
            <i class="fa-solid fa-star-half-stroke"></i>
            <i class="fa-regular fa-star"></i>
            <i class="fa-regular fa-star"></i>
          </span>
          <span class="star-value">2.8</span>
        </div>
         <div class="price">20000DA</div>
      </div>
  </div>
</div>

<!-- 
<div class="box ">
  <img src="../assets/images/courses/course04.png" alt="">
  <div class="info">
    <h3 class="title">Public Speaking and Communication Skills</h3>
    <p>Overcome stage fear and improve your verbal and non-verbal communication.</p>
    <p class="level"><strong>Level:</strong> All Levels</p>
    <p class="duration"><strong>Duration:</strong> 4 Weeks</p>
  </div>
  <div class="course-footer">
      <a href="#">View Details</a>
      <div class="rating">
        <div class="rating-value">

          <span class="stars">
            <i class="fa-solid fa-star"></i>
            <i class="fa-solid fa-star"></i>
            <i class="fa-solid fa-star"></i>
            <i class="fa-solid fa-star-half-stroke"></i>
            <i class="fa-regular fa-star"></i>
          </span>
          <span class="star-value">3.9</span>
        </div>
          <div class="price">5000DA</div>
      </div>
  
  </div>
</div> -->


 <!-- <div class="box"> 
  <img src="../assets/images/courses/course05.png" alt="">
  <div class="info">
    <h3 class="title">Entrepreneurship & Business Innovation</h3>
    <p>Turn your ideas into reality by learning the fundamentals of startup creation and innovation.</p>
    <p class="level"><strong>Level:</strong> Intermediate</p>
    <p class="duration"><strong>Duration:</strong> 7 Weeks</p>
  </div>
  <div class="course-footer">
    <a href="#">View Details</a>
 
    <div class="rating">
      <div class="rating-value">
        <span class="stars">
          <i class="fa-solid fa-star"></i>
          <i class="fa-solid fa-star"></i>
          <i class="fa-solid fa-star"></i>
          <i class="fa-solid fa-star"></i>
          <i class="fa-regular fa-star"></i>
        </span>
        <span class="star-value">4.5</span>
  
  </div>
        <div class="price">15000DA</div>
    </div>
  </div>
</div> -->


<!-- <div class="box ">
  <img src="../assets/images/courses/course06.png" alt="">
  <div class="info">
    <h3 class="title">Personal Development and Productivity</h3>
    <p>Learn how to manage your time, stay focused, and achieve your goals efficiently.</p>
    <p class="level"><strong>Level:</strong> All Levels</p>
    <p class="duration"><strong>Duration:</strong> 4 Weeks</p>
  </div>
  <div class="course-footer">
    <a href="#">View Details</a>

    <div class="rating">
      <div class="rating-value">

        <span class="stars">
          <i class="fa-solid fa-star"></i>
          <i class="fa-solid fa-star"></i>
          <i class="fa-solid fa-star"></i>
          <i class="fa-regular fa-star"></i>
          <i class="fa-regular fa-star"></i>
        </span>
        <span class="star-value">3.5</span>
      </div>
        <div class="price"> 5500DA</div>
    </div>
  </div>
</div> -->

<!-- <div class="box ">
  <div class="image">

    <img src="../assets/images/courses/course02.png" alt="">
  </div>
  <div class="info">
    <h3 class="title">Graphic Design Essentials</h3>
    <p>Develop your creativity and learn to design with Photoshop and Illustrator. Understand layout.</p>
    <p class="level"><strong>Level:</strong> Beginner</p>
    <p class="duration"><strong>Duration:</strong> 5 Weeks</p>
  </div>
  <div class="course-footer">
  <a href="#">View Details</a>
   
  <div class="rating">
    <div class="rating-value">

      <span class="stars">
        <i class="fa-solid fa-star"></i>
        <i class="fa-solid fa-star"></i>
        <i class="fa-solid fa-star"></i>
        <i class="fa-regular fa-star"></i>
        <i class="fa-regular fa-star"></i>
      </span>
      <span class="star-value">3.6</span>
    </div>

    <div class="price">18000DA</div>
  </div>
  </div>
</div>  -->


    <!-- </div>
    <div class="number-pages">
      <a href="#" class="left">></a>
      <div class="one">1</div>
      <div class="two">2</div>
      <div class="three">3</div>
    </div> -->
</main>
   </div> 








 
 
</body>
</html>