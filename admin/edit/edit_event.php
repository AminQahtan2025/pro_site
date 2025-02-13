<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: ../login.php');
    exit();
}

include '../../db_connect.php';

// Initialize variables
$error = '';
$event = [];

// Get the event ID from the query string
$event_id = isset($_GET['id']) ? intval($_GET['id']) : null;

if ($event_id) {
    // Fetch the event details
    $stmt = $conn->prepare("SELECT * FROM events WHERE id = ?");
    $stmt->bind_param("i", $event_id);
    $stmt->execute();
    $event = $stmt->get_result()->fetch_assoc();

    if (!$event) {
        $error = "Event not found.";
    }
} else {
    $error = "Invalid Event ID.";
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $event_name = isset($_POST['event_name']) ? trim($_POST['event_name']) : '';
    $description = isset($_POST['description']) ? trim($_POST['description']) : '';
    $event_date = isset($_POST['event_date']) ? trim($_POST['event_date']) : '';
    $location = isset($_POST['location']) ? trim($_POST['location']) : '';
    $link = isset($_POST['link']) ? trim($_POST['link']) : '';

    // Validate required fields
    if (empty($event_name) || empty($event_date) || empty($location)) {
        $error = "Event name, date, and location are required.";
    } else {
        // Handle image upload
        $image_url = $event['image_url']; // Keep the existing image by default
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = '../../uploads/';
            $relativePath = 'uploads/';
            $fileName = basename($_FILES['image']['name']);
            $fileTmpPath = $_FILES['image']['tmp_name'];
            $fileType = $_FILES['image']['type'];
            $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

            // Validate file type and extension
            $allowedFileTypes = ['image/jpeg', 'image/png', 'image/gif'];
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];

            if (in_array($fileType, $allowedFileTypes) && in_array($fileExtension, $allowedExtensions)) {
                // Generate a unique name for the file
                $uniqueFileName = uniqid() . '_' . $fileName;
                $destination = $uploadDir . $uniqueFileName;
                $dbFilePath = $relativePath . $uniqueFileName;

                // Move uploaded file to the server directory
                if (move_uploaded_file($fileTmpPath, $destination)) {
                    // Delete the old image if it exists
                    if (!empty($event['image_url']) && file_exists("../../" . $event['image_url'])) {
                        unlink("../../" . $event['image_url']);
                    }
                    $image_url = $dbFilePath;
                } else {
                    $error = "Error uploading the image.";
                }
            } else {
                $error = "Only JPG, PNG, and GIF images are allowed.";
            }
        }

        // Update the event in the database
        if (empty($error)) {
            $query = "UPDATE events SET event_name = ?, description = ?, event_date = ?, location = ?, image_url = ?, link = ? WHERE id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("ssssssi", $event_name, $description, $event_date, $location, $image_url, $link, $event_id);

            if ($stmt->execute()) {
                header('Location: ../manage/manage_events.php');
                exit();
            } else {
                $error = "Error updating the event.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Event</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" rel="stylesheet">
    <link rel="icon" href="../../images/logo.png" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../css/style.css">
    <link rel="stylesheet" href="../includes/style.css">
</head>
<body>
     <!-- Sidebar -->
     <div class="sidebar" id="sidebar">
        <h4 class="text-center"> Dashboard</h4>
        <hr class="bg-light">
        <a href="../manage/manage_g_articles.php"><i class="fa fa-book me-2"></i> General Articles</a>
        <a href="../manage/manage_s_articles.php"><i class="fa-brands fa-google-scholar me-2"></i> Scientific Articles</a>
        <a href="../manage/manage_consultancy.php"><i class="fa fa-briefcase me-2"></i> Consultancy</a>
        <a href="../manage/manage_training_programs.php"><i class="fa fa-chalkboard-teacher me-2"></i> Training Programs</a>
        <a href="../manage/manage_teaching_materials.php"><i class="fa fa-graduation-cap me-2"></i> Teaching Materials</a>
        <a href="../manage/manage_awards.php"><i class="fa fa-award me-2"></i>Awards</a>
        <a href="../manage/manage_gallery.php"><i class="fa fa-images me-2"></i> Gallery</a>
        <a href="../manage/manage_events.php"><i class="fa fa-calendar-alt me-2"></i> Events</a>
        <hr>
        <a href="#"><i class="fa fa-user-tie me-2"></i> Profile settings</a>
        <a href="../login.php"><i class="fa fa-sign-out-alt me-2"></i> Logout</a>
    </div>
    <!-- Main Content -->
    <div class="topbar" id="topbar">
        <button class="btn btn-outline-primary toggle-sidebar-btn" id="toggleSidebar" ><i class="fa fa-bars"></i></button>
        <h5>Welcome, Admin</h5>
        <a href="../../admin_dash.php"><button class="btn btn-outline-primary"><i class="fa fa-home"></i></button></a>
    </div>
    <div class="content" id="content">
        <div class="container">
            <h1 class="text-center">Edit Event</h1>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data">
                <div class="form-group mb-3">
                    <label for="event_name">Event Name:</label>
                    <input type="text" id="event_name" name="event_name" class="form-control" value="<?php echo htmlspecialchars($event['event_name']); ?>" required>
                </div>
                <div class="form-group mb-3">
                    <label for="description">Description:</label>
                    <textarea id="description" name="description" class="form-control" rows="4" required><?php echo htmlspecialchars($event['description']); ?></textarea>
                </div>
                <div class="form-group mb-3">
                    <label for="event_date">Event Date:</label>
                    <input type="date" id="event_date" name="event_date" class="form-control" value="<?php echo htmlspecialchars($event['event_date']); ?>" required>
                </div>
                <div class="form-group mb-3">
                    <label for="location">Location:</label>
                    <input type="text" id="location" name="location" class="form-control" value="<?php echo htmlspecialchars($event['location']); ?>" required>
                </div>
                <div class="form-group mb-3">
                    <label for="link">Link:</label>
                    <input type="url" id="link" name="link" class="form-control" value="<?php echo htmlspecialchars($event['link']); ?>">
                </div>
                <div class="form-group mb-3">
                    <label for="image">Upload Image (JPG, PNG, GIF):</label>
                    <input type="file" id="image" name="image" class="form-control" accept=".jpg,.jpeg,.png,.gif">
                    <small class="text-muted">Current Image: <?php echo htmlspecialchars($event['image_url']); ?></small>
                </div>
                <button type="submit" class="btn btn-primary">Save Changes</button>
                <a href="../manage/manage_events.php" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../includes/script.js"></script>
</body>
</html>