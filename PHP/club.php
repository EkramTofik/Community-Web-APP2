<?php
require_once 'includes/db_connect.php';
require_once 'includes/functions.php';

if (!is_logged_in()) {
    header('Location: login.php');
    exit;
}

// Fetch clubs
$stmt = $pdo->query('SELECT * FROM clubs ORDER BY created_at DESC');
$clubs = $stmt->fetchAll();

// Handle club join
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['club_id'])) {
    $club_id = (int)$_POST['club_id'];
    $user_id = $_SESSION['user_id'];
    
    // Check if user is already a member
    $stmt = $pdo->prepare('SELECT * FROM club_memberships WHERE user_id = ? AND club_id = ?');
    $stmt->execute([$user_id, $club_id]);
    if (!$stmt->fetch()) {
        $stmt = $pdo->prepare('INSERT INTO club_memberships (user_id, club_id) VALUES (?, ?)');
        $stmt->execute([$user_id, $club_id]);
    }
    header('Location: club.php');
    exit;
}

// Handle new club submission
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_club'])) {
    $name = sanitize($_POST['name']);
    $tag = sanitize($_POST['tag']);
    $description = sanitize($_POST['description']);
    $telegram_link = sanitize($_POST['telegram_link']);
    
    // Validate inputs
    if (empty($name)) {
        $errors[] = 'Club name is required.';
    }
    if (empty($tag)) {
        $errors[] = 'Club tag is required.';
    }
    if (empty($description)) {
        $errors[] = 'Description is required.';
    }
    if (empty($telegram_link) || !filter_var($telegram_link, FILTER_VALIDATE_URL)) {
        $errors[] = 'Valid Telegram link is required.';
    }
    
    // Handle image upload
    $image_path = null;
    if (isset($_FILES['image']) && $_FILES['image']['size'] > 0) {
        $upload = upload_file($_FILES['image'], ['image/jpeg', 'image/png', 'image/gif']);
        if (isset($upload['error'])) {
            $errors[] = $upload['error'];
        } else {
            $image_path = $upload['path'];
        }
    }
    
    // Insert club if no errors
    if (empty($errors)) {
        $stmt = $pdo->prepare('INSERT INTO clubs (name, tag, description, telegram_link, image) VALUES (?, ?, ?, ?, ?)');
        $stmt->execute([$name, $tag, $description, $telegram_link, $image_path]);
        header('Location: club.php');
        exit;
    }
}
?>

<?php include 'includes/header.php'; ?>
<div class="container">
    <div class="header">
        <div class="search-bar">
            <input type="text" placeholder="Search...">
        </div>
    </div>
    <section class="job-listings">
        <h2>Clubs And Communities</h2>
        
        <!-- Add Club Form -->
        <div class="add-club">
            <h3>Add a New Club</h3>
            <?php if (!empty($errors)): ?>
                <ul class="errors">
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="add_club" value="1">
                <label>
                    Club Name:
                    <input type="text" name="name" value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>" required>
                </label>
                <label>
                    Tag:
                    <input type="text" name="tag" value="<?php echo isset($_POST['tag']) ? htmlspecialchars($_POST['tag']) : ''; ?>" required>
                </label>
                <label>
                    Description:
                    <textarea name="description" required><?php echo isset($_POST['description']) ? htmlspecialchars($_POST['description']) : ''; ?></textarea>
                </label>
                <label>
                    Telegram Link:
                    <input type="url" name="telegram_link" value="<?php echo isset($_POST['telegram_link']) ? htmlspecialchars($_POST['telegram_link']) : ''; ?>" required>
                </label>
                <label>
                    Image (optional):
                    <input type="file" name="image" accept="image/jpeg,image/png,image/gif">
                </label>
                <button type="submit">Create Club</button>
            </form>
        </div>
        
        <h4>Clubs</h4>
        <div class="job-cards">
            <?php foreach ($clubs as $club): ?>
                <div class="job-card">
                    <img src="<?php echo $club['image'] ?: 'images/default_club.jpg'; ?>" alt="<?php echo htmlspecialchars($club['name']); ?>">
                    <h3><?php echo htmlspecialchars($club['name']); ?></h3>
                    <form method="POST" style="display: inline;">
                        <input type="hidden" name="club_id" value="<?php echo $club['id']; ?>">
                        <button type="submit" class="join-club-button">Join Club</button>
                    </form>
                    <?php if ($club['telegram_link']): ?>
                        <a href="<?php echo htmlspecialchars($club['telegram_link']); ?>" target="_blank" class="join-button">Join on Telegram</a>
                    <?php else: ?>
                        <span class="no-telegram">No Telegram link</span>
                    <?php endif; ?>
                    <a href="clubdetails.php?id=<?php echo $club['id']; ?>" class="details-link"><?php echo htmlspecialchars($club['tag']); ?></a>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
</div>
<?php include 'includes/footer.php'; ?>
<script>
document.addEventListener("DOMContentLoaded", () => {
    const searchInput = document.querySelector(".search-bar input");
    const jobCards = document.querySelectorAll(".job-card");

    searchInput.addEventListener("input", () => {
        const filter = searchInput.value.toLowerCase();
        jobCards.forEach(card => {
            const title = card.querySelector("h3").textContent.toLowerCase();
            const link = card.querySelector(".details-link").textContent.toLowerCase();
            card.style.display = (title.includes(filter) || link.includes(filter)) ? "block" : "none";
        });
    });
});
</script>