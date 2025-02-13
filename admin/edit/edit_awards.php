<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: ../login.php');
    exit();
}

include '../../db_connect.php';

// Initialize variables
$error = '';
$successMessage = '';
$award = [];

// Get the award ID from the query string
$award_id = isset($_GET['id']) ? intval($_GET['id']) : null;

if (!$award_id) {
    header('Location: manage_awards.php');
    exit();
}

// Fetch the award details
$stmt = $conn->prepare("SELECT * FROM awards WHERE id = ?");
$stmt->bind_param("i", $award_id);
$stmt->execute();
$award = $stmt->get_result()->fetch_assoc();

if (!$award) {
    header('Location: manage_awards.php');
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate form fields
    $title = isset($_POST['title']) ? trim($_POST['title']) : '';
    $description = isset($_POST['description']) ? trim($_POST['description']) : '';
    $date_received = isset($_POST['date_received']) ? trim($_POST['date_received']) : '';

    // Validate required fields
    if (empty($title)) {
        $error = "Title is required.";
    } elseif (empty($date_received)) {
        $error = "Date received is required.";
    } else {
        // Handle image uploads
        $imagePaths = explode(',', $award['image_url']); // Existing images
        if (isset($_FILES['images']) && !empty($_FILES['images']['name'][0])) {
            $uploadDir = '../../uploads/'; // Define the upload directory
            $relativePath = 'uploads/'; // Path to store in the database

            // Loop through each uploaded file
            foreach ($_FILES['images']['tmp_name'] as $key => $tmp_name) {
                $fileName = basename($_FILES['images']['name'][$key]);
                $fileTmpPath = $_FILES['images']['tmp_name'][$key];
                $fileSize = $_FILES['images']['size'][$key];
                $fileType = $_FILES['images']['type'][$key];
                $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION)); // Get file extension

                // Validate file type and extension
                $allowedFileTypes = ['image/jpeg', 'image/png', 'image/gif'];
                $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif']; // Allowed image extensions

                if (!in_array($fileType, $allowedFileTypes) || !in_array($fileExtension, $allowedExtensions)) {
                    $error = "Only JPG, PNG, and GIF images are allowed.";
                    continue; // Skip invalid files
                }

                // Generate a unique name for the file to avoid overwriting
                $uniqueFileName = uniqid() . '_' . $fileName; // Add a unique ID to the filename
                $destination = $uploadDir . $uniqueFileName;
                $dbFilePath = $relativePath . $uniqueFileName; // Relative path for the database

                // Move uploaded file to the server directory
                if (move_uploaded_file($fileTmpPath, $destination)) {
                    $imagePaths[] = $dbFilePath; // Add the file path to the array
                } else {
                    $error = "Error moving the uploaded file: $fileName.";
                }
            }
        }

        // Handle file upload (certificate)
        $filePath = $award['file_url']; // Keep the existing file path
        if (isset($_FILES['file']) && !empty($_FILES['file']['name'])) {
            $uploadDir = '../../uploads/'; // Define the upload directory
            $relativePath = 'uploads/'; // Path to store in the database

            $fileName = basename($_FILES['file']['name']);
            $fileTmpPath = $_FILES['file']['tmp_name'];
            $fileSize = $_FILES['file']['size'];
            $fileType = $_FILES['file']['type'];
            $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION)); // Get file extension

            // Validate file type and extension
            $allowedFileTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
            $allowedExtensions = ['pdf', 'doc', 'docx']; // Allowed file extensions

            if (!in_array($fileType, $allowedFileTypes) || !in_array($fileExtension, $allowedExtensions)) {
                $error = "Only PDF, DOC, and DOCX files are allowed.";
            } else {
                // Generate a unique name for the file to avoid overwriting
                $uniqueFileName = uniqid() . '_' . $fileName; // Add a unique ID to the filename
                $destination = $uploadDir . $uniqueFileName;
                $dbFilePath = $relativePath . $uniqueFileName; // Relative path for the database

                // Move uploaded file to the server directory
                if (move_uploaded_file($fileTmpPath, $destination)) {
                    $filePath = $dbFilePath; // Save the new file path
                } else {
                    $error = "Error moving the uploaded file: $fileName.";
                }
            }
        }

        // If no errors, update the award details in the database
        if (empty($error)) {
            $imageUrls = implode(',', $imagePaths); // Convert array to comma-separated string

            $query = "UPDATE awards SET title = ?, description = ?, date_received = ?, image_url = ?, file_url = ? WHERE id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("sssssi", $title, $description, $date_received, $imageUrls, $filePath, $award_id);

            if ($stmt->execute()) {
                $successMessage = "Award updated successfully!";
            } else {
                $error = "Error updating award in the database.";
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
    <title>Edit Award</title>
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
            <h1 class="text-center">Edit Award</h1>

            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>

            <?php if (!empty($successMessage)): ?>
                <div class="alert alert-success"><?php echo $successMessage; ?></div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data">
                <div class="form-group mb-3">
                    <label for="title">Title:</label>
                    <input type="text" id="title" name="title" class="form-control" value="<?php echo htmlspecialchars($award['title']); ?>" required>
                </div>
                <div class="form-group mb-3">
                    <label for="description">Description:</label>
                    <textarea id="description" name="description" class="form-control" rows="4" required><?php echo htmlspecialchars($award['description']); ?></textarea>
                </div>
                <div class="form-group mb-3">
                    <label for="date_received">Date Received:</label>
                    <input type="date" id="date_received" name="date_received" class="form-control" value="<?php echo htmlspecialchars($award['date_received']); ?>" required>
                </div>
                <div class="form-group mb-3">
                    <label for="images">Upload Images (JPG, PNG, GIF):</label>
                    <input type="file" id="images" name="images[]" class="form-control" accept=".jpg,.jpeg,.png,.gif" multiple>
                    <small class="text-muted">Current Images: <?php echo htmlspecialchars($award['image_url']); ?></small>
                </div>
                <div class="form-group mb-3">
                    <label for="file">Upload Certificate (PDF, DOC, DOCX):</label>
                    <input type="file" id="file" name="file" class="form-control" accept=".pdf,.doc,.docx">
                    <small class="text-muted">Current File: <?php echo htmlspecialchars($award['file_url']); ?></small>
                </div>
                <button type="submit" class="btn btn-primary">Save Changes</button>
                <a href="../manage/manage_awards.php" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../includes/script.js"></script>
</body>
</html>