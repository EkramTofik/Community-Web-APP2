<?php
require_once 'includes/db_connect.php';
require_once 'includes/functions.php';

if (!is_logged_in()) {
    header('Location: login.php');
    exit;
}

// Fetch announcements
$stmt = $pdo->query('SELECT a.*, u.full_name FROM announcements a JOIN users u ON a.created_by = u.id ORDER BY a.created_at DESC');
$announcements = $stmt->fetchAll();

// Handle announcement submission
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_announcement'])) {
    $title = sanitize($_POST['title']);
    $description = sanitize($_POST['description']);
    $event_date = $_POST['event_date'];
    $google_form_link = sanitize($_POST['google_form_link']);
    $created_by = $_SESSION['user_id'];

    // Validate inputs
    if (empty($title)) {
        $errors[] = 'Title is required.';
    }
    if (empty($description)) {
        $errors[] = 'Description is required.';
    }
    if (empty($event_date)) {
        $errors[] = 'Event date and time are required.';
    }
    if (empty($google_form_link) || !filter_var($google_form_link, FILTER_VALIDATE_URL)) {
        $errors[] = 'Valid Google Form link is required.';
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

    // Insert announcement if no errors
    if (empty($errors)) {
        $stmt = $pdo->prepare('INSERT INTO announcements (title, description, event_date, google_form_link, image, created_by) VALUES (?, ?, ?, ?, ?, ?)');
        $stmt->execute([$title, $description, $event_date, $google_form_link, $image_path, $created_by]);
        header('Location: announcements.php');
        exit;
    }
}

// Handle announcement deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_announcement'])) {
    $announcement_id = (int)$_POST['announcement_id'];
    $user_id = $_SESSION['user_id'];

    // Verify ownership
    $stmt = $pdo->prepare('SELECT image, created_by FROM announcements WHERE id = ?');
    $stmt->execute([$announcement_id]);
    $announcement = $stmt->fetch();

    if ($announcement && $announcement['created_by'] == $user_id) {
        // Delete image file if exists
        if ($announcement['image'] && file_exists($announcement['image'])) {
            unlink($announcement['image']);
        }
        // Delete announcement
        $stmt = $pdo->prepare('DELETE FROM announcements WHERE id = ?');
        $stmt->execute([$announcement_id]);
    }
    header('Location: announcements.php');
    exit;
}
?>

<?php include 'includes/header.php'; ?>
<div class="container">
    <section class="announcements">
        <h2>Announcements</h2>
        <button class="create-announcement-button">Create Announcement</button>
        
        <!-- Announcement Form (Hidden by Default) -->
        <div class="add-announcement" style="display: none;">
            <h3>Create a New Announcement</h3>
            <?php if (!empty($errors)): ?>
                <ul class="errors">
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="add_announcement" value="1">
                <label>
                    Title:
                    <input type="text" name="title" value="<?php echo isset($_POST['title']) ? htmlspecialchars($_POST['title']) : ''; ?>" required>
                </label>
                <label>
                    Description:
                    <textarea name="description" required><?php echo isset($_POST['description']) ? htmlspecialchars($_POST['description']) : ''; ?></textarea>
                </label>
                <label>
                    Event Date and Time:
                    <input type="datetime-local" name="event_date" value="<?php echo isset($_POST['event_date']) ? htmlspecialchars($_POST['event_date']) : ''; ?>" required>
                </label>
                <label>
                    Google Form Link:
                    <input type="url" name="google_form_link" value="<?php echo isset($_POST['google_form_link']) ? htmlspecialchars($_POST['google_form_link']) : ''; ?>" required>
                </label>
                <label>
                    Image (optional):
                    <input type="file" name="image" accept="image/jpeg,image/png,image/gif">
                </label>
                <button type="submit">Submit Announcement</button>
            </form>
        </div>
        
        <!-- Announcement List -->
        <div class="announcement-cards">
            <?php if ($announcements): ?>
                <?php foreach ($announcements as $announcement): ?>
                    <div class="announcement-card">
                        <img src="<?php echo $announcement['image'] ?: 'images/default_announcement.jpg'; ?>" alt="<?php echo htmlspecialchars($announcement['title']); ?>">
                        <h3><?php echo htmlspecialchars($announcement['title']); ?></h3>
                        <p><?php echo htmlspecialchars($announcement['description']); ?></p>
                        <p><strong>Event Date:</strong> <?php echo date('F j, Y, g:i A', strtotime($announcement['event_date'])); ?></p>
                        <p><strong>Posted by:</strong> <?php echo htmlspecialchars($announcement['full_name']); ?></p>
                        <a href="<?php echo htmlspecialchars($announcement['google_form_link']); ?>" target="_blank" class="get-tickets-button">Get Tickets</a>
                        <?php if ($announcement['created_by'] == $_SESSION['user_id']): ?>
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="announcement_id" value="<?php echo $announcement['id']; ?>">
                                <button type="submit" name="delete_announcement" class="delete-announcement-button" onclick="return confirm('Are you sure you want to delete this announcement?');">Delete</button>
                            </form>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No announcements yet. Be the first to create one!</p>
            <?php endif; ?>
        </div>
    </section>
</div>
<?php include 'includes/footer.php'; ?>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const createButton = document.querySelector('.create-announcement-button');
    const formContainer = document.querySelector('.add-announcement');
    
    createButton.addEventListener('click', () => {
        formContainer.style.display = formContainer.style.display === 'none' ? 'block' : 'none';
    });
});
</script>