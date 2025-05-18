<?php
session_start();

// Include database connection file
include("connection.php");

// Function to get user role
function getUserRole() {
    if (isset($_SESSION['role'])) {
        return $_SESSION['role'];
    } else {
        return 'guest';
    }
}

$userRole = getUserRole();

// Redirect non-admin users
if ($userRole != 'admin') {
    header("Location: ads.php"); // Redirect to the ads page
    exit();
}

// Initialize variables for form data and errors
$title = $description = $image_url = "";
$title_err = $description_err = $image_url_err = "";
$success = false;

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate title
    if (empty(trim($_POST["title"]))) {
        $title_err = "Title is required";
    } else {
        $title = trim($_POST["title"]);
    }

    // Validate description
    if (empty(trim($_POST["description"]))) {
        $description_err = "Description is required";
    } else {
        $description = trim($_POST["description"]);
    }

    // Validate image URL (basic validation)
    if (!empty($_POST["image_url"])) {
        $image_url = trim($_POST["image_url"]);
        if (!filter_var($image_url, FILTER_VALIDATE_URL)) {
            $image_url_err = "Invalid URL format";
        }
    } else {
        $image_url_err = "Image URL is required"; //make image required
    }

    // If there are no errors, insert data into the database
    if (empty($title_err) && empty($description_err) && empty($image_url_err)) {
        $sql = "INSERT INTO ads (title, description, image_url, user_id) VALUES (?, ?, ?, ?)"; //added user_id

        if ($stmt = mysqli_prepare($conn, $sql)) {
             // Bind parameters 
            mysqli_stmt_bind_param($stmt, "sssi", $param_title, $param_description, $param_image_url, $param_user_id); //bind user_id

            // Set parameters
            $param_title = $title;
            $param_description = $description;
            $param_image_url = $image_url;
            $param_user_id = $_SESSION['user_id']; //added user_id from session

            // Attempt to execute the prepared statement
            if (mysqli_stmt_execute($stmt)) {
                $success = true;
                // Redirect to ads page after successful submission
                header("Location: ads.php");
                exit();
            } else {
                echo "Something went wrong. Please try again later.";
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }
    }
    mysqli_close($conn);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Advertisement</title>
    <link rel="stylesheet" href="../../Community-Web-APP2/css/ads.css">
    <link rel="stylesheet" href="../../Community-Web-APP2/css/sidebar.css">
    <link rel="stylesheet" href="../../Community-Web-APP2/css/footer.css">
    <style>
        .form-container {
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            background-color: #f4f4f4;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        .form-control {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }

        .form-control:focus {
            outline: none;
            border-color: #007bff;
        }

        .btn-primary {
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .btn-primary:hover {
            background-color: #0056b3;
        }

        .text-danger {
            color: red;
        }
        .success-message {
            background-color: #d4edda;
            color: #155724;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #c3e6cb;
            border-radius: 4px;
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
            <div class="form-container">
                <h2>Add Advertisement</h2>
                <?php if ($success): ?>
                    <div class="success-message">
                        Advertisement added successfully!
                    </div>
                <?php endif; ?>
                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                    <div class="form-group">
                        <label for="title">Title:</label>
                        <input type="text" class="form-control" id="title" name="title" value="<?php echo htmlspecialchars($title); ?>">
                        <span class="text-danger"><?php echo $title_err; ?></span>
                    </div>
                    <div class="form-group">
                        <label for="description">Description:</label>
                        <textarea class="form-control" id="description" name="description" rows="5"><?php echo htmlspecialchars($description); ?></textarea>
                        <span class="text-danger"><?php echo $description_err; ?></span>
                    </div>
                    <div class="form-group">
                        <label for="image_url">Image URL:</label>
                        <input type="text" class="form-control" id="image_url" name="image_url" value="<?php echo htmlspecialchars($image_url); ?>">
                        <span class="text-danger"><?php echo $image_url_err; ?></span>
                    </div>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
