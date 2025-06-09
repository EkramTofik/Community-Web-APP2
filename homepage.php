<?php
require_once 'includes/db_connect.php';
require_once 'includes/functions.php';

if (!is_logged_in()) {
    header('Location: login.php');
    exit;
}

// Fetch announcements
$stmt = $pdo->query('SELECT * FROM announcements ORDER BY created_at DESC LIMIT 1');
$featured = $stmt->fetch();

// Fetch clubs
$stmt = $pdo->query('SELECT * FROM clubs ORDER BY created_at DESC LIMIT 6');
$clubs = $stmt->fetchAll();
?>
<head>
    <link rel="stylesheet" href="css/homepage2.css">

</head>
<body>
<?php include 'includes/header.php'; ?>
<div class="container">
    <section class="hero">
        <h1>Welcome to Campus Connect</h1>
        <p>Your Gateway to University Life</p>
        <button>Join us now</button>
        <button><a href="login.php">Login</a></button>
    </section>

    <section class="university-fair">
        <img src="<?php echo $featured['image'] ?: 'images/default.jpg'; ?>" alt="University Fair">
        <h2><?php echo htmlspecialchars($featured['title']); ?></h2>
        <p><?php echo htmlspecialchars($featured['description']); ?></p>
        <button>Contact</button>
        <button>Follow</button>
    </section>

    <section class="upcoming-events">
        <h2>Upcoming Events</h2>
        <div class="event-cards">
            <?php foreach ($clubs as $club): ?>
                <div class="event-card">
                    <h3><?php echo htmlspecialchars($club['name']); ?></h3>
                    <p>Location: Red Carpet</p>
                    <p>Time: Jan 2nd, 8:pm</p>
                    <a href="#"><?php echo htmlspecialchars($club['tag']); ?></a>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
</div>
<?php include 'includes/footer.php'; ?>
</body>