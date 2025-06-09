<?php require_once 'includes/functions.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WelcomeHub</title>
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/footer.css">
    <link rel="stylesheet" href="css/club2.css">
    <link rel="stylesheet" href="css/announcement2.css">
    <link rel="stylesheet" href="css/coursematerial2.css">
    <link rel="stylesheet" href="css/ads2.css">
    <link rel="stylesheet" href="css/profile2.css">
    <link rel="stylesheet" href="css/register.css">


</head>
<body>
    <div class="sidebar">
        <div>
            <div class="logo">WelcomeHub</div>
            <nav>
                <ul>
                    <li><span><img src="images/home.png" width="18" height="18"></span><a href="homepage.php">Home</a></li>
                    <li><span><img src="images/group.png" width="20" height="20"></span><a href="club.php">Clubs</a></li>
                    <li><span><img src="images/promotion.png" width="20" height="20"></span><a href="announcement.php">Announcement</a></li>
                    <li><span><img src="images/fabric.png" width="20" height="20"></span><a href="coursematerial.php">Materials</a></li>
                    <li><span><img src="images/cart.png" width="20" height="20"></span><a href="ads.php">Ads</a></li>
            </nav>
        </div>
        <div class="profile">
            <img src="images/Avatar2.avif" alt="User">
            <div>
                <div class="name"><?php echo is_logged_in() ? $_SESSION['username'] : 'Guest'; ?></div>
                <a href="<?php echo is_logged_in() ? 'profile.php' : 'login.php'; ?>">View profile</a>
            </div>
        </div>
    </div>
    <div class="content">