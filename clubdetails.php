<?php
require_once 'includes/db_connect.php';
require_once 'includes/functions.php';

if (!is_logged_in()) {
    header('Location: login.php');
    exit;
}

// Get club ID from URL
$club_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Fetch club details
$stmt = $pdo->prepare('SELECT * FROM clubs WHERE id = ?');
$stmt->execute([$club_id]);
$club = $stmt->fetch();

if (!$club) {
    // Redirect to clubs page if club not found
    header('Location: club.php');
    exit;
}

// Fetch club members
$stmt = $pdo->prepare('
    SELECT u.full_name 
    FROM club_memberships cm 
    JOIN users u ON cm.user_id = u.id 
    WHERE cm.club_id = ? 
    ORDER BY cm.joined_at DESC
');
$stmt->execute([$club_id]);
$members = $stmt->fetchAll(PDO::FETCH_COLUMN);
?>

<?php include 'includes/header.php'; ?>
<div class="container">
    <section class="club-details">
        <h2><?php echo htmlspecialchars($club['name']); ?></h2>
        <img src="<?php echo $club['image'] ?: 'images/default_club.jpg'; ?>" alt="<?php echo htmlspecialchars($club['name']); ?>" class="club-image">
        <p><strong>Tag:</strong> <?php echo htmlspecialchars($club['tag']); ?></p>
        <p><strong>Description:</strong> <?php echo htmlspecialchars($club['description'] ?: 'No description available.'); ?></p>
        <a href="<?php echo htmlspecialchars($club['telegram_link']); ?>" target="_blank" class="join-button">Join on Telegram</a>
        <section class="club-members">
            <h3>Members</h3>
            <?php if ($members): ?>
                <ul>
                    <?php foreach ($members as $member): ?>
                        <li><?php echo htmlspecialchars($member); ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>No members yet. Be the first to join!</p>
            <?php endif; ?>
        </section>
        <a href="club.php" class="back-link">Back to Clubs</a>
    </section>
</div>
<?php include 'includes/footer.php'; ?>