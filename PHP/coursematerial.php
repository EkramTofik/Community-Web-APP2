<?php
require_once 'includes/db_connect.php';
require_once 'includes/functions.php';

if (!is_logged_in()) {
    header('Location: login.php');
    exit;
}

// Fetch course materials
$stmt = $pdo->query('SELECT * FROM course_materials ORDER BY created_at DESC');
$materials = $stmt->fetchAll();

// Fetch comments and reactions
$comments = [];
$like_counts = [];
$dislike_counts = [];
$user_reactions = [];

foreach ($materials as $material) {
    $stmt = $pdo->prepare('SELECT c.*, u.username FROM comments c JOIN users u ON c.user_id = u.id WHERE c.material_id = ? ORDER BY c.created_at DESC');
    $stmt->execute([$material['id']]);
    $comments[$material['id']] = $stmt->fetchAll();

    // Fetch like/dislike counts and user reactions
    foreach ($comments[$material['id']] as $comment) {
        // Like count
        $stmt = $pdo->prepare('SELECT COUNT(*) as count FROM comment_reactions WHERE comment_id = ? AND reaction = "like"');
        $stmt->execute([$comment['id']]);
        $like_counts[$comment['id']] = $stmt->fetch()['count'];

        // Dislike count
        $stmt = $pdo->prepare('SELECT COUNT(*) as count FROM comment_reactions WHERE comment_id = ? AND reaction = "dislike"');
        $stmt->execute([$comment['id']]);
        $dislike_counts[$comment['id']] = $stmt->fetch()['count'];

        // User reaction
        if (is_logged_in()) {
            $stmt = $pdo->prepare('SELECT reaction FROM comment_reactions WHERE comment_id = ? AND user_id = ?');
            $stmt->execute([$comment['id'], $_SESSION['user_id']]);
            $user_reactions[$comment['id']] = $stmt->fetchColumn() ?: null;
        }
    }
}

// Handle comment submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment'])) {
    $material_id = (int)$_POST['material_id'];
    $comment = sanitize($_POST['comment']);
    $user_id = $_SESSION['user_id'];
    
    $stmt = $pdo->prepare('INSERT INTO comments (material_id, user_id, comment) VALUES (?, ?, ?)');
    $stmt->execute([$material_id, $user_id, $comment]);
    header('Location: coursematerial.php');
    exit;
}

// Handle file upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['material_file'])) {
    $title = sanitize($_POST['title']);
    $description = sanitize($_POST['description']);
    $upload = upload_file($_FILES['material_file']);
    
    if (isset($upload['error'])) {
        $error = $upload['error'];
    } else {
        $stmt = $pdo->prepare('INSERT INTO course_materials (title, description, file_path) VALUES (?, ?, ?)');
        $stmt->execute([$title, $description, $upload['path']]);
        header('Location: coursematerial.php');
        exit;
    }
}

// Handle like/dislike submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reaction'])) {
    $comment_id = (int)$_POST['comment_id'];
    $reaction = $_POST['reaction'] === 'like' ? 'like' : 'dislike';
    $user_id = $_SESSION['user_id'];

    // Check if user already reacted
    $stmt = $pdo->prepare('SELECT reaction FROM comment_reactions WHERE comment_id = ? AND user_id = ?');
    $stmt->execute([$comment_id, $user_id]);
    $existing_reaction = $stmt->fetchColumn();

    if ($existing_reaction) {
        // Update existing reaction if different
        if ($existing_reaction !== $reaction) {
            $stmt = $pdo->prepare('UPDATE comment_reactions SET reaction = ? WHERE comment_id = ? AND user_id = ?');
            $stmt->execute([$reaction, $comment_id, $user_id]);
        }
    } else {
        // Insert new reaction
        $stmt = $pdo->prepare('INSERT INTO comment_reactions (comment_id, user_id, reaction) VALUES (?, ?, ?)');
        $stmt->execute([$comment_id, $user_id, $reaction]);
    }

    header('Location: coursematerial.php');
    exit;
}
?>

<?php include 'includes/header.php'; ?>
<div class="container">
    <h2>Course Material Repository</h2>
    <p>Browse through a curated selection of lessons, complete with images and summaries.</p>
    
    <!-- File Upload Form -->
    <?php if (isset($error)): ?>
        <p style="color: red;"><?php echo $error; ?></p>
    <?php endif; ?>
    <form method="POST" enctype="multipart/form-data">
        <label>Title: <input type="text" name="title" required></label><br>
        <label>Description: <textarea name="description" required></textarea></label><br>
        <label>File: <input type="file" name="material_file" accept="image/*,application/pdf" required></label><br>
        <button type="submit">Upload Material</button>
    </form>

    <div class="material-list">
        <?php foreach ($materials as $material): ?>
            <div class="material-item">
                <h3><?php echo htmlspecialchars($material['title']); ?></h3>
                <p><?php echo htmlspecialchars($material['description']); ?></p>
                <?php if ($material['file_path']): ?>
                    <a href="<?php echo $material['file_path']; ?>" download>Download</a>
                <?php endif; ?>
                
                <!-- Comments -->
                <div class="discussion">
                    <h4>Discussion</h4>
                    <?php foreach ($comments[$material['id']] as $comment): ?>
                        <div class="comment">
                            <p><strong><?php echo htmlspecialchars($comment['username']); ?></strong> (<?php echo $comment['created_at']; ?>)</p>
                            <p><?php echo htmlspecialchars($comment['comment']); ?></p>
                            <div class="reactions">
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="comment_id" value="<?php echo $comment['id']; ?>">
                                    <input type="hidden" name="reaction" value="like">
                                    <button type="submit" <?php echo $user_reactions[$comment['id']] === 'like' ? 'disabled' : ''; ?>>
                                        Like (<?php echo $like_counts[$comment['id']]; ?>)
                                    </button>
                                </form>
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="comment_id" value="<?php echo $comment['id']; ?>">
                                    <input type="hidden" name="reaction" value="dislike">
                                    <button type="submit" <?php echo $user_reactions[$comment['id']] === 'dislike' ? 'disabled' : ''; ?>>
                                        Dislike (<?php echo $dislike_counts[$comment['id']]; ?>)
                                    </button>
                                </form>
                                <button>Share</button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    
                    <!-- Comment Form -->
                    <form method="POST">
                        <input type="hidden" name="material_id" value="<?php echo $material['id']; ?>">
                        <textarea name="comment" placeholder="Add Comment" required></textarea>
                        <button type="submit">Comment</button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
<?php include 'includes/footer.php'; ?>