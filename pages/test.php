
<?php
include "config.php";
session_start();
require_once "../assets/phpqrcode/qrlib.php";


?>



<!--evetement.html-->
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
 <link rel="stylesheet" href="../assets/css/pages/evets.css">
<title>Formix | Events</title>
      <style>
        img{
            max-width: 100%;
            width: 300px;
        }
    </style>
</head>
<body>
   <div class="events">
      <aside class="side-bar">

           <img src="../assets/images/logo/sidebar.png" alt="" class="logo">


            <ul>
                <li>
            <a  href="dashboard.html">
              <i class="fa-regular fa-chart-bar fa-fw"></i>
              <span>Dashboard</span>
            </a>
            </li>
                <li>
            <a  href="formation.html">
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
                       <a  href="panier.html">
            <i class="fa-solid fa-cart-shopping"></i>
              <span>Cart</span>
            </a>
                </li>
               <li>
                       <a  href="Blog.html">
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
      </aside>
        <main class="event-content">
     
         <!-- start head -->
           <header >
     <div class="main-title">
       <h2>Events</h2>
     </div>
     
       <form class="search-box">
         <input type="text" placeholder="Search Events...">
         <button type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
       </form>
           </header>  
        
          <!-- end head -->

       <?php 

$sql = "SELECT * FROM evenements ORDER BY date_event ASC";
$result = mysqli_query($conn, $sql);
?>

<div class="content">
<?php
if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {

        $formatted_date = date("d M", strtotime($row['date_event']));
        ?>

        <div class="card-event">
            <div class="image">
                <img src="../assets/images/events/<?php echo $row['image']; ?>" alt="<?php echo $row['titre']; ?>">
                <span class="date"><?php echo $formatted_date; ?></span>
            </div>
            <div class="info">
                <h3><?php echo $row['titre']; ?></h3>
                <div class="time">
                    <span><i class="fa-regular fa-clock"></i> <?php echo $row['heure_debut']; ?> - <?php echo $row['heure_fin']; ?></span>
                    <span><i class="fa-solid fa-location-dot"></i> <?php echo $row['lieu']; ?></span>
                </div>
                <p><?php echo $row['description']; ?></p>
<a href="#" class="btn-register" data-event-id="<?php echo $row['id']; ?>" data-event-title="<?php echo $row['titre']; ?>">Register Now</a>
            </div>
        </div>

        <?php
    }
} else {
    echo "<p>No events available.</p>";
}
?>
</div>

     
         <!-- start footer -->
    <footer >
  <div class="text">
    <div class="title">

      <h3>Stay Update</h3>
    </div>
    <p>Subscribe to our newsletter to get the latest news, events, and articles delivered straight to your inbox</p>
  </div>
  <div class="subs">
    <input type="email" name="email" id="" placeholder="Email">
    <input type="submit" value="Subscribe" name="Subs">
  </div>
</footer> 
     <!-- end footer -->
     
        </main>
     



   </div> 
     <div id="modal" class="modal hidden">
  <div class="modal-content">
    <span id="close">&times;</span>
    <h2 >Register for <span class="modal-title">Event</span></h2>
    <form id="registerForm" method="post" action="evenement.php">
      <label for="fullname">Full Name</label>
      <input type="text" id="fullname" name="fullname" placeholder="Enter your full name" required>

      <label for="email">Email</label>
      <input type="email" id="email" name="email" placeholder="Enter your email" required>

      <label for="phone">Phone (Optional)</label>
      <input type="tel" id="phone" name="phone" placeholder="Enter your phone number">

      <label for="session">Choose Session</label>
      <select id="session" name="session">
        <option value="morning">Morning</option>
        <option value="afternoon">Afternoon</option>
        <option value="evening">Evening</option>
      </select>

      <label for="notes">Notes (Optional)</label>
      <textarea id="notes" name="notes" placeholder="Any special requests"></textarea>

<button type="submit" name="register_submit" class="submit-btn">Submit</button>
    </form>
  </div>
</div>


<script>
const buttons = document.querySelectorAll('.btn-register');
const modal = document.getElementById('modal');
const modalTitle = document.querySelector('.modal-title');
const evenementInput = document.getElementById('evenement_id');
const closeBtn = document.getElementById('close');

buttons.forEach(btn => {
    btn.addEventListener('click', function(e) {
        e.preventDefault();
        modal.classList.remove('hidden');
        modalTitle.textContent = this.dataset.eventTitle;
        evenementInput.value = this.dataset.eventId;
    });
});

closeBtn.addEventListener('click', () => modal.classList.add('hidden'));
window.addEventListener('click', e => { if (e.target === modal) modal.classList.add('hidden'); });
</script>

</body>
</html>