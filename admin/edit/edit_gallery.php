<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: ../login.php');
    exit();
}

// Include database connection
include '../../db_connect.php';

// Initialize error messages
$errorMessages = [];
$successMessage = '';

// Fetch gallery entry details if ID is provided
$galleryId = isset($_GET['id']) ? intval($_GET['id']) : 0;
$galleryEntry = null;

if ($galleryId > 0) {
    $query = "SELECT * FROM gallery WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $galleryId);
    $stmt->execute();
    $result = $stmt->get_result();
    $galleryEntry = $result->fetch_assoc();
    $stmt->close();

    if (!$galleryEntry) {
        $errorMessages[] = "Gallery entry not found.";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate form fields
    $title = isset($_POST['title']) ? trim($_POST['title']) : '';
    $description = isset($_POST['description']) ? trim($_POST['description']) : '';

    // Validate required fields
    if (empty($title)) {
        $errorMessages[] = "Title is required.";
    }

    // Handle image upload if a new image is provided
    $imagePath = $galleryEntry['image_url']; // Keep the existing image by default

    if (isset($_FILES['image']) && !empty($_FILES['image']['name'])) {
        $uploadDir = '../../uploads/'; // Define the upload directory
        $relativePath = 'uploads/'; // Path to store in the database

        $fileName = basename($_FILES['image']['name']);
        $fileTmpPath = $_FILES['image']['tmp_name'];
        $fileSize = $_FILES['image']['size'];
        $fileType = $_FILES['image']['type'];
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION)); // Get file extension

        // Validate file type and extension
        $allowedFileTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif']; // Allowed image extensions

        if (!in_array($fileType, $allowedFileTypes) || !in_array($fileExtension, $allowedExtensions)) {
            $errorMessages[] = "Only JPG, PNG, and GIF images are allowed.";
        } else {
            // Generate a unique name for the file to avoid overwriting
            $uniqueFileName = uniqid() . '_' . $fileName; // Add a unique ID to the filename
            $destination = $uploadDir . $uniqueFileName;
            $dbFilePath = $relativePath . $uniqueFileName; // Relative path for the database

            // Move uploaded file to the server directory
            if (move_uploaded_file($fileTmpPath, $destination)) {
                // Delete the old image file if it exists
                if (file_exists('../../' . $galleryEntry['image_url'])) {
                    unlink('../../' . $galleryEntry['image_url']);
                }
                $imagePath = $dbFilePath; // Update the image path
            } else {
                $errorMessages[] = "Error moving the uploaded file: $fileName.";
            }
        }
    }

    // If no errors, update the gallery entry in the database
    if (empty($errorMessages)) {
        $query = "UPDATE gallery SET title = ?, description = ?, image_url = ? WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sssi", $title, $description, $imagePath, $galleryId);

        if ($stmt->execute()) {
            $successMessage = "Gallery entry updated successfully!";
        } else {
            $errorMessages[] = "Error updating gallery entry in the database.";
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modify Gallery Entry</title>
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
        <h2 class="text-center mb-4">Modify Gallery Entry</h2>
        <div class="container border p-4 rounded">
            <!-- Display Success Message -->
            <?php if (!empty($successMessage)): ?>
                <div class="alert alert-success"><?php echo $successMessage; ?></div>
            <?php endif; ?>
            
            <!-- Display Error Messages -->
            <?php if (!empty($errorMessages)): ?>
                <div class="alert alert-danger">
                    <ul>
                        <?php foreach ($errorMessages as $message): ?>
                            <li><?php echo htmlspecialchars($message); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <?php if ($galleryEntry): ?>
                <form method="POST" enctype="multipart/form-data">
                    <div class="form-group mb-3">
                        <label for="title">Title:</label>
                        <input type="text" id="title" name="title" class="form-control" value="<?php echo htmlspecialchars($galleryEntry['title']); ?>" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="description">Description:</label>
                        <textarea id="description" name="description" class="form-control" rows="4"><?php echo htmlspecialchars($galleryEntry['description']); ?></textarea>
                    </div>
                    <div class="form-group mb-3">
                        <label for="image">Current Image:</label>
                        <img src="../../<?php echo htmlspecialchars($galleryEntry['image_url']); ?>" alt="Gallery Image" class="img-fluid mb-2" style="max-width: 200px;">
                        <label for="image">Upload New Image (JPG, PNG, GIF):</label>
                        <input type="file" id="image" name="image" class="form-control" accept=".jpg,.jpeg,.png,.gif">
                    </div>
                    <button type="submit" class="btn btn-primary">Update Gallery Entry</button>
                </form>
            <?php else: ?>
                <div class="alert alert-warning">No gallery entry found.</div>
            <?php endif; ?>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../includes/script.js"></script>
</body>
</html>