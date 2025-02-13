<?php
// Include database connection
include '../../db_connect.php';

// Initialize error messages
$errorMessages = [];
$successMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate form fields
    $service_name = isset($_POST['service_name']) ? trim($_POST['service_name']) : '';
    $description = isset($_POST['description']) ? trim($_POST['description']) : '';
    $link = isset($_POST['link']) ? trim($_POST['link']) : '';

    // Validate service name and description
    if (empty($service_name)) {
        $errorMessages[] = "Service name is required.";
    }

    // Handle multiple image uploads
    if (isset($_FILES['images']) && !empty($_FILES['images']['name'][0])) {
        $uploadDir = '../../uploads/'; // Define the upload directory
        $relativePath = 'uploads/'; // Path to store in the database
        $imagePaths = []; // Array to store uploaded image paths

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
                $errorMessages[] = "Only JPG, PNG, and GIF images are allowed.";
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
                $errorMessages[] = "Error moving the uploaded file: $fileName.";
            }
        }

        // If no errors, save consultancy details to the database
        if (empty($errorMessages)) {
            $imageUrls = implode(',', $imagePaths); // Convert array to comma-separated string

            $query = "INSERT INTO consultancy (service_name, description, image_url, link) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("ssss", $service_name, $description, $imageUrls, $link);

            if ($stmt->execute()) {
                $successMessage = "Consultancy added successfully!";
            } else {
                $errorMessages[] = "Error saving consultancy to the database.";
            }
        }
    } else {
        $errorMessages[] = "No images uploaded.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Consultancy</title>
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
        <h2 class="text-center mb-4">Add New Consultancy</h2>
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
            <form method="POST" enctype="multipart/form-data">
    <div class="form-group mb-3">
        <label for="service_name">Service Name:</label>
        <input type="text" id="service_name" name="service_name" class="form-control" required>
    </div>
    <div class="form-group mb-3">
        <label for="description">Description:</label>
        <textarea id="description" name="description" class="form-control"></textarea>
    </div>
    <div class="form-group mb-3">
        <label for="link">Link:</label>
        <input type="url" id="link" name="link" class="form-control" placeholder="https://example.com">
    </div>
    <div class="form-group mb-3">
        <label for="images">Upload Images (JPG, PNG, GIF):</label>
        <input type="file" id="images" name="images[]" class="form-control" accept=".jpg,.jpeg,.png,.gif" multiple required>
    </div>
    <button type="submit" class="btn btn-primary">Add Consultancy</button>
</form>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../includes/script.js"></script>
</body>
</html>