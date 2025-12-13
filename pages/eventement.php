<?php
include "config.php";
session_start();
require_once "../assets/qr-code/phpqrcode/qrlib.php";


$success_message = "";
$error_message = "";
$qr_file = "";

if (isset($_POST["register_submit"])) {

    $evenement_id = $_POST["evenement_id"];
    $fullname = $_POST["fullname"];
    $email = $_POST["email"];
    $phone = $_POST["phone"] ?? '';
    $horaire = $_POST["session"];
    $note = $_POST["notes"] ?? '';

    $sql_user = "SELECT * FROM users WHERE email='$email'";
    $result_user = mysqli_query($conn, $sql_user);

    if (mysqli_num_rows($result_user) > 0) {
        $user = mysqli_fetch_assoc($result_user);
        $user_id = $user['id'];

        $check = "SELECT * FROM inscriptions_evenements 
                  WHERE user_id='$user_id' AND evenement_id='$evenement_id'";
        $res_check = mysqli_query($conn, $check);

        if (mysqli_num_rows($res_check) > 0) {
            $error_message = "You are already registered for this event!";
        } else {
        
            $qr_file = "assets/qrcodes/qr_" . time() . ".png";
            QRcode::png("Event: $evenement_id, Name: $fullname, Email: $email, Session: $horaire", "../" . $qr_file, QR_ECLEVEL_L, 4);

            $stmt = "INSERT INTO inscriptions_evenements (user_id, evenement_id, horaire, note, qr_code) 
                     VALUES ('$user_id', '$evenement_id', '$horaire', '$note', '$qr_file')";

            $result = mysqli_query($conn, $stmt);

            if ($result) {
                $success_message = "Registration successful! 🎉";
            } else {
                $error_message = "Error: " . mysqli_error($conn);
            }
        }

    } else {
        $error_message = "User not found!";
    }
}


$search = $_GET['search'] ?? "";

if (!isset($_GET["search"]) || empty($search)) {
    $sql = "SELECT * FROM evenements ORDER BY date_event ASC";
} else {
    $clean = mysqli_real_escape_string($conn, $search);
    $sql = "SELECT * FROM evenements 
            WHERE titre LIKE '%$clean%' 
               OR description LIKE '%$clean%' 
               OR lieu = '$clean'
            ORDER BY date_event ASC";
}

$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Formix | Events</title>
<link rel="stylesheet" href="../assets/css/pages/evets.css">
<script src="https://kit.fontawesome.com/f14d152ebc.js" crossorigin="anonymous"></script>

</head>
<body>



    <div id="toast" class="toast">
        <p>Registration completed successfully!</p>
        <?php if($success_message && $qr_file && file_exists("../" . $qr_file)): ?>
            <p>Your QR Code:</p>
          <img src="../<?= $qr_file ?>" alt="QR Code">
              <a href="../<?= $qr_file ?>" download="QR_Event_<?= $evenement_id ?>.png" class="download-btn">Download QR Code</a>

        <?php endif; ?>
    </div>
</div>
<div class="events">
    <aside class="side-bar">
        <img src="../assets/images/logo/sidebar.png" alt="" class="logo">
        <ul>
            <li><a href="dashboard.php"><i class="fa-regular fa-chart-bar fa-fw"></i><span>Dashboard</span></a></li>
            <li><a href="formation.php"><i class="fa-solid fa-graduation-cap fa-fw"></i><span>Courses</span></a></li>
            <li><a href="eventement.php"><i class="fa-solid fa-calendar-days"></i><span>Events</span></a></li>
            <li><a href="panier.php"><i class="fa-solid fa-cart-shopping"></i><span>Cart</span></a></li>
            <li><a href="Blog.php"><i class="fa-solid fa-pen-nib"></i><span>Blog</span></a></li>
            <li><a href="contact.php"><i class="fa-solid fa-phone"></i><span>Contact</span></a></li>
            <li><a href="../index.html"><i class="fa-solid fa-right-from-bracket"></i><span>Log Out</span></a></li>
        </ul>
    </aside>

    <main class="event-content">
        <header>
            <div class="main-title"><h2>Events</h2></div>

            <!-- Search Box -->
            <form class="search-box" method="get" action="eventement.php">
                <input type="text" name="search" placeholder="Search Events..." value="<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>">
                <button type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
            </form>
        </header>

        <div class="content">
            <?php if(mysqli_num_rows($result) > 0): ?>
                <?php while($row = mysqli_fetch_assoc($result)):
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
                <?php endwhile; ?>
            <?php else: ?>
                <?php if(!empty($search)): ?>
                    <p>No results found for "<?php echo htmlspecialchars($search); ?>"</p>
                <?php else: ?>
                    <p>No events available.</p>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </main>
</div>

<div id="modal" class="modal hidden">
    <div class="modal-content">
        <span id="close">&times;</span>
        <h2>Register for <span class="modal-title"></span></h2>
        <div class="message">
            <?php if($error_message): ?>
                <p style="color:red;"><?= $error_message ?></p>
            <?php endif; ?>
        </div>

        <form id="registerForm" method="post" action="eventement.php">
            <input type="hidden" name="evenement_id" id="evenement_id">
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
document.addEventListener("DOMContentLoaded", function() {
    const buttons = document.querySelectorAll('.btn-register');
    const modal = document.getElementById('modal');
    const modalTitle = document.querySelector('.modal-title');
    const evenementInput = document.getElementById('evenement_id');
    const closeBtn = document.getElementById('close');
    const toast = document.getElementById('toast');

    buttons.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            modal.classList.remove('hidden');
            modalTitle.textContent = this.getAttribute('data-event-title');
            evenementInput.value = this.getAttribute('data-event-id');
        });
    });

    closeBtn.addEventListener('click', () => modal.classList.add('hidden'));

    <?php if (!empty($success_message)): ?>
        modal.classList.add('hidden');
        toast.style.display = "block";
        setTimeout(() => { toast.style.display = "none"; }, 20000);
    <?php endif; ?>

    <?php if (!empty($error_message)): ?>
        modal.classList.remove('hidden');
    <?php endif; ?>
});
</script>

</body>
</html>
