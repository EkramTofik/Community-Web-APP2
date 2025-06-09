<?php
require_once 'includes/db_connect.php';
require_once 'includes/functions.php';

if (!is_logged_in()) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare('SELECT * FROM users WHERE id = ?');
$stmt->execute([$user_id]);
$user = $stmt->fetch();

// Fetch contributions
$stmt = $pdo->prepare('SELECT * FROM contributions WHERE user_id = ? ORDER BY created_at DESC');
$stmt->execute([$user_id]);
$contributions = $stmt->fetchAll();

// Handle contribution upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['contribution_file'])) {
    $title = sanitize($_POST['title']);
    $description = sanitize($_POST['description']);
    $upload = upload_file($_FILES['contribution_file']);
    
    if (isset($upload['error'])) {
        $error = $upload['error'];
    } else {
        $stmt = $pdo->prepare('INSERT INTO contributions (user_id, title, description, file_path) VALUES (?, ?, ?, ?)');
        $stmt->execute([$user_id, $title, $description, $upload['path']]);
        header('Location: profile.php');
        exit;
    }
}
?>

<?php include 'includes/header.php'; ?>
<div class="container">
<section class="profile-header">
    <img src="images/Avatar2.avif" alt="User">
    <h2><?php echo htmlspecialchars($user['full_name']); ?></h2>
    <p>University of Example</p>
    <p>Course: Computer Science</p>
    <button>Contact</button>
    <form method="POST" action="logout.php" style="display: inline;">
        <button type="submit">Logout</button>
    </form>
</section>


    <!-- Contribution Upload Form -->
    <?php if (isset($error)): ?>
        <p style="color: red;"><?php echo $error; ?></p>
    <?php endif; ?>
    <form method="POST" enctype="multipart/form-data">
        <label>Title: <input type="text" name="title" required></label><br>
        <label>Description: <textarea name="description" required></textarea></label><br>
        <label>File: <input type="file" name="contribution_file" accept="image/*,application/pdf" required></label><br>
        <button type="submit">Upload Contribution</button>
    </form>

    <section class="user-contributions">
        <h2>User Contributions</h2>
        <div class="posts">
            <?php foreach ($contributions as $contribution): ?>
                <div class="post">
                    <h3><?php echo htmlspecialchars($contribution['title']); ?></h3>
                    <p><?php echo htmlspecialchars($contribution['description']); ?></p>
                    <p>Uploaded on <?php echo date('M d, Y', strtotime($contribution['created_at'])); ?></p>
                    <?php if ($contribution['file_path']): ?>
                        <a href="<?php echo $contribution['file_path']; ?>" download>Download</a>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="user-achievements">
        <h2>User Achievements</h2>
        <ul>
            <li>Best Photographer 2023 – Awarded for excellence in nature photography.</li>
            <li>Top Contributor – Recognized for consistent contributions in club activities.</li>
            <li>Workshop Leader – Led multiple workshops on creative writing and digital art.</li>
        </ul>
    </section>
</div>
<?php include 'includes/footer.php'; ?>