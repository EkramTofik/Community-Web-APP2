<?php
require_once 'includes/db_connect.php';
require_once 'includes/functions.php';

if (!is_logged_in()) {
    header('Location: login.php');
    exit;
}

// Fetch ads
$stmt = $pdo->query('SELECT a.*, u.full_name FROM ads a JOIN users u ON a.created_by = u.id ORDER BY a.created_at DESC');
$ads = $stmt->fetchAll();

// Handle ad submission
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_ad'])) {
    $title = sanitize($_POST['title']);
    $description = sanitize($_POST['description']);
    $category = sanitize($_POST['category']);
    $contact_info = sanitize($_POST['contact_info']);
    $created_by = $_SESSION['user_id'];

    // Validate inputs
    if (empty($title)) {
        $errors[] = 'Title is required.';
    }
    if (empty($description)) {
        $errors[] = 'Description is required.';
    }
    if (empty($category)) {
        $errors[] = 'Category is required.';
    }
    if (empty($contact_info)) {
        $errors[] = 'Contact information is required.';
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

    // Insert ad if no errors
    if (empty($errors)) {
        $stmt = $pdo->prepare('INSERT INTO ads (title, description, category, contact_info, image, created_by) VALUES (?, ?, ?, ?, ?, ?)');
        $stmt->execute([$title, $description, $category, $contact_info, $image_path, $created_by]);
        header('Location: ads.php');
        exit;
    }
}

// Handle ad deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_ad'])) {
    $ad_id = (int)$_POST['ad_id'];
    $user_id = $_SESSION['user_id'];

    // Verify ownership
    $stmt = $pdo->prepare('SELECT image, created_by FROM ads WHERE id = ?');
    $stmt->execute([$ad_id]);
    $ad = $stmt->fetch();

    if ($ad && $ad['created_by'] == $user_id) {
        // Delete image file if exists
        if ($ad['image'] && file_exists($ad['image'])) {
            unlink($ad['image']);
        }
        // Delete ad
        $stmt = $pdo->prepare('DELETE FROM ads WHERE id = ?');
        $stmt->execute([$ad_id]);
    }
    header('Location: ads.php');
    exit;
}
?>

<?php include 'includes/header.php'; ?>
<div class="container">
    <section class="ads">
        <h2>Advertisements</h2>
        <button class="create-ad-button">Create Ad</button>
        
        <!-- Ad Form (Hidden by Default) -->
        <div class="add-ad" style="display: none;">
            <h3>Create a New Ad</h3>
            <?php if (!empty($errors)): ?>
                <ul class="errors">
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="add_ad" value="1">
                <label>
                    Title:
                    <input type="text" name="title" value="<?php echo isset($_POST['title']) ? htmlspecialchars($_POST['title']) : ''; ?>" required>
                </label>
                <label>
                    Description:
                    <textarea name="description" required><?php echo isset($_POST['description']) ? htmlspecialchars($_POST['description']) : ''; ?></textarea>
                </label>
                <label>
                    Category:
                    <select name="category" required>
                        <option value="" disabled selected>Select a category</option>
                        <option value="For Sale" <?php echo isset($_POST['category']) && $_POST['category'] === 'For Sale' ? 'selected' : ''; ?>>For Sale</option>
                        <option value="Services" <?php echo isset($_POST['category']) && $_POST['category'] === 'Services' ? 'selected' : ''; ?>>Services</option>
                        <option value="Housing" <?php echo isset($_POST['category']) && $_POST['category'] === 'Housing' ? 'selected' : ''; ?>>Housing</option>
                        <option value="Jobs" <?php echo isset($_POST['category']) && $_POST['category'] === 'Jobs' ? 'selected' : ''; ?>>Jobs</option>
                        <option value="Events" <?php echo isset($_POST['category']) && $_POST['category'] === 'Events' ? 'selected' : ''; ?>>Events</option>
                        <option value="Other" <?php echo isset($_POST['category']) && $_POST['category'] === 'Other' ? 'selected' : ''; ?>>Other</option>
                    </select>
                </label>
                <label>
                    Contact Information (e.g., email, phone):
                    <input type="text" name="contact_info" value="<?php echo isset($_POST['contact_info']) ? htmlspecialchars($_POST['contact_info']) : ''; ?>" required>
                </label>
                <label>
                    Image (optional):
                    <input type="file" name="image" accept="image/jpeg,image/png,image/gif">
                </label>
                <button type="submit">Submit Ad</button>
            </form>
        </div>
        
        <!-- Ad List -->
        <div class="ad-cards">
            <?php if ($ads): ?>
                <?php foreach ($ads as $ad): ?>
                    <div class="ad-card">
                        <img src="<?php echo $ad['image'] ?: 'images/default_ad.jpg'; ?>" alt="<?php echo htmlspecialchars($ad['title']); ?>">
                        <h3><?php echo htmlspecialchars($ad['title']); ?></h3>
                        <p><strong>Category:</strong> <?php echo htmlspecialchars($ad['category']); ?></p>
                        <p><?php echo htmlspecialchars($ad['description']); ?></p>
                        <p><strong>Contact:</strong> <?php echo htmlspecialchars($ad['contact_info']); ?></p>
                        <p><strong>Posted by:</strong> <?php echo htmlspecialchars($ad['full_name']); ?></p>
                        <?php if ($ad['created_by'] == $_SESSION['user_id']): ?>
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="ad_id" value="<?php echo $ad['id']; ?>">
                                <button type="submit" name="delete_ad" class="delete-ad-button" onclick="return confirm('Are you sure you want to delete this ad?');">Delete</button>
                            </form>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No ads yet. Be the first to create one!</p>
            <?php endif; ?>
        </div>
    </section>
</div>
<?php include 'includes/footer.php'; ?>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const createButton = document.querySelector('.create-ad-button');
    const formContainer = document.querySelector('.add-ad');
    
    createButton.addEventListener('click', () => {
        formContainer.style.display = formContainer.style.display === 'none' ? 'block' : 'none';
    });
});
</script>
