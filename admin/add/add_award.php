<?php
// Include database connection
include '../../db_connect.php';

// Initialize error messages
$errorMessages = [];
$successMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate form fields
    $title = isset($_POST['title']) ? trim($_POST['title']) : '';
    $description = isset($_POST['description']) ? trim($_POST['description']) : '';
    $date_received = isset($_POST['date_received']) ? trim($_POST['date_received']) : '';

    // Validate required fields
    if (empty($title)) {
        $errorMessages[] = "Title is required.";
    }
    if (empty($date_received)) {
        $errorMessages[] = "Date received is required.";
    }

    // Handle multiple image uploads
    $imagePaths = [];
    $hasImages = false;
    if (!empty(array_filter($_FILES['images']['name']))) {
        $uploadDir = '../../uploads/';
        $relativePath = 'uploads/';

        foreach ($_FILES['images']['tmp_name'] as $key => $tmp_name) {
            $fileName = basename($_FILES['images']['name'][$key]);
            $fileTmpPath = $_FILES['images']['tmp_name'][$key];
            $fileSize = $_FILES['images']['size'][$key];
            $fileType = $_FILES['images']['type'][$key];
            $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

            $allowedFileTypes = ['image/jpeg', 'image/png', 'image/gif'];
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];

            if (!in_array($fileType, $allowedFileTypes) || !in_array($fileExtension, $allowedExtensions)) {
                $errorMessages[] = "Only JPG, PNG, and GIF images are allowed.";
                continue;
            }

            $uniqueFileName = uniqid() . '_' . $fileName;
            $destination = $uploadDir . $uniqueFileName;
            $dbFilePath = $relativePath . $uniqueFileName;

            if (move_uploaded_file($fileTmpPath, $destination)) {
                $imagePaths[] = $dbFilePath;
                $hasImages = true;
            } else {
                $errorMessages[] = "Error moving the uploaded file: $fileName.";
            }
        }
    }

    // Handle file upload (certificate)
    $filePath = '';
    $hasFile = false;
    if (isset($_FILES['file']) && !empty($_FILES['file']['name'])) {
        $uploadDir = '../../uploads/';
        $relativePath = 'uploads/';

        $fileName = basename($_FILES['file']['name']);
        $fileTmpPath = $_FILES['file']['tmp_name'];
        $fileSize = $_FILES['file']['size'];
        $fileType = $_FILES['file']['type'];
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        $allowedFileTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
        $allowedExtensions = ['pdf', 'doc', 'docx'];

        if (!in_array($fileType, $allowedFileTypes) || !in_array($fileExtension, $allowedExtensions)) {
            $errorMessages[] = "Only PDF, DOC, and DOCX files are allowed.";
        } else {
            $uniqueFileName = uniqid() . '_' . $fileName;
            $destination = $uploadDir . $uniqueFileName;
            $dbFilePath = $relativePath . $uniqueFileName;

            if (move_uploaded_file($fileTmpPath, $destination)) {
                $filePath = $dbFilePath;
                $hasFile = true;
            } else {
                $errorMessages[] = "Error moving the uploaded file: $fileName.";
            }
        }
    }

    // Ensure at least one of images or file is uploaded
    if (!$hasImages && !$hasFile) {
        $errorMessages[] = "You must upload at least an image or a file.";
    }

    // If no errors, save award details to the database
    if (empty($errorMessages)) {
        $imageUrls = implode(',', $imagePaths);

        $query = "INSERT INTO awards (title, description, date_received, image_url, file_url) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sssss", $title, $description, $date_received, $imageUrls, $filePath);

        if ($stmt->execute()) {
            $successMessage = "Award added successfully!";
        } else {
            $errorMessages[] = "Error saving award to the database.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Award</title>
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
        <h2 class="text-center mb-4">Add New Award</h2>
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
                    <label for="title">Title:</label>
                    <input type="text" id="title" name="title" class="form-control" required>
                </div>
                <div class="form-group mb-3">
                    <label for="description">Description:</label>
                    <textarea id="description" name="description" class="form-control"></textarea>
                </div>
                <div class="form-group mb-3">
                    <label for="date_received">Date Received:</label>
                    <input type="date" id="date_received" name="date_received" class="form-control" required>
                </div>
                <div class="form-group mb-3">
                    <label for="images">Upload Images (JPG, PNG, GIF):</label>
                    <input type="file" id="images" name="images[]" class="form-control" accept=".jpg,.jpeg,.png,.gif" multiple>
                </div>
                <div class="form-group mb-3">
                    <label for="file">Upload Certificate (PDF, DOC, DOCX):</label>
                    <input type="file" id="file" name="file" class="form-control" accept=".pdf,.doc,.docx">
                </div>
                <button type="submit" class="btn btn-primary">Add Award</button>
            </form>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../includes/script.js"></script>
</body>
</html>