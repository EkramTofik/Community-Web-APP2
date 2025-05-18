<?php
// Start session (if not already started)
session_start();

// Include database connection file
include("connection.php"); // Make sure this file contains your database connection code

// Function to get user role (replace with your actual user role retrieval logic)
function getUserRole() {
    if (isset($_SESSION['role'])) {
        return $_SESSION['role'];
    } else {
        return 'guest'; // Default role if user is not logged in
    }
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login_form.php"); // Redirect to your login page
    exit();
}

// Get user role
$userRole = getUserRole();

// Fetch ads from the database
$sql = "SELECT * FROM ads"; // Replace "ads" with your actual ads table name
$result = mysqli_query($conn, $sql);
$ads = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Free result from memory
mysqli_free_result($result);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Campus Connect</title>
    <link rel="stylesheet" href="../../Community-Web-APP2/css/ads.css">
    <link rel="stylesheet" href="../../Community-Web-APP2/css/sidebar.css">
    <link rel="stylesheet" href="../../Community-Web-APP2/css/footer.css">
    <style>
        /* Style for the plus icon */
        .add-icon {
            position: fixed; /* Fix it to the corner */
            bottom: 20px;
            right: 20px;
            background-color: #007bff; /* Blue color */
            color: white;
            border-radius: 50%; /* Make it round */
            width: 50px; /* Adjust size as needed */
            height: 50px;
            text-align: center;
            line-height: 50px; /* Vertically center the icon */
            font-size: 24px; /* Adjust icon size */
            cursor: pointer; /* Show a pointer cursor on hover */
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2); /* Add some shadow for better visibility */
            z-index: 1000; /* Ensure it's above other elements */
        }

        .add-icon:hover {
            background-color: #0056b3; /* Darker blue on hover */
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <div>
            <div class="logo">WelcomeHub</div>
            <nav>
                <ul>
                    <li><span><img src="../images/home.png" width="18px" height="18px"></span><a href="homepage.html">Home</a></li>
                    <li> <span><img src="../images/group.png" width="20px" height="20px"></span>  <a href="club.html">Clubs</a></li>
                    <li> <span><img src="../images/promotion.png" width="20px" height="20px"></span>  <a href="announcement.html">Announcement</a></li>
                    <li> <span><img src="../images/fabric.png" width="20px" height="20px"></span>  <a href="coursematerial.html" >Materials</a></li>
                    <li  class="active"> <span><img src="../images/cart.png" width="20px" height="20px"></span>  <a href="ads.html">Ads</a></li>
                </ul>
            </nav>
        </div>
        <div class="profile">
            <img src="https://via.placeholder.com/40" alt="User">
            <div>
                <div class="name">Amanda</div>
                <a href="profile.html">View profile</a>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="container">
            <section class="job-listings">
                <h2>Advertisements</h2>
                <div class="job-cards">
                    <?php if (count($ads) > 0): ?>
                        <?php foreach ($ads as $ad): ?>
                            <div class="job-card">
                                <img src="<?php echo htmlspecialchars($ad['image_url']); ?>" alt="Ad Image">
                                <h3><?php echo htmlspecialchars($ad['title']); ?></h3>
                                <p><?php echo htmlspecialchars($ad['description']); ?></p>
                                </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>No advertisements available yet.</p>
                    <?php endif; ?>
                </div>
            </section>

            <section class="community-section">
                <h2>Join the community</h2>
                <p>Connect with fellow students, share experiences, and create unforgettable events. Become a part of our vibrant community today!</p>
                <button>Join Us Now</button>
            </section>
        </div>

        <footer>
            <div class="container">
                <div class="newsletter">
                    <input type="email" placeholder="Subscribe to our newsletter">
                    <button>Subscribe</button>
                </div>

                <div class="links">
                    <a href="homepage.html">Home</a>
                    <a href="club.html">Clubs</a>
                    <a href="announcement.html" >Announcement</a>
                    <a href="coursematerial.html" >Materials</a>
                    <a href="ads.html" >Ads</a>
                </div>
            <p>&copy; 2024 Brand, Inc. - <a href="#">Privacy</a> - <a href="#">Terms</a> - <a href="#">Sitemap</a></p>

            </div>
        </footer>

        <?php if ($userRole == 'staff'): ?>
            <a href="add_ads.php" class="add-icon">+</a>
        <?php endif; ?>
    </div>
</body>
</html>
