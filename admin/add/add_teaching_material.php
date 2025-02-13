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
    $category = isset($_POST['category']) ? trim($_POST['category']) : '';

    // Validate title and description
    if (empty($title)) {
        $errorMessages[] = "Title is required.";
    }

    if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['file']['tmp_name'];
        $fileName = basename($_FILES['file']['name']);
        $fileSize = $_FILES['file']['size'];
        $fileType = $_FILES['file']['type'];
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION)); // Get file extension
        $uploadDir = '../../uploads/'; // Define the upload directory
        $relativePath = 'uploads/'; // Path to store in the database

        // Validate file type and extension
        $allowedFileTypes = [
            'application/pdf',
            'application/msword',
            'text/plain',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
        ];
        $allowedExtensions = ['pdf', 'doc', 'docx', 'txt']; // Allowed file extensions

        if (!in_array($fileType, $allowedFileTypes) || !in_array($fileExtension, $allowedExtensions)) {
            $errorMessages[] = "Only DOCX, PDF, and TXT files are allowed.";
        } else {
            // Generate a unique name for the file to avoid overwriting
            $uniqueFileName = uniqid() . '_' . $fileName; // Add a unique ID to the filename
            $destination = $uploadDir . $uniqueFileName;
            $dbFilePath = $relativePath . $uniqueFileName; // Relative path for the database

            // Move uploaded file to the server directory
            if (move_uploaded_file($fileTmpPath, $destination)) {
                // Save teaching material details to the database
                $query = "INSERT INTO teaching_materials (title, description, file_url, category, created_at) VALUES (?, ?, ?, ?, NOW())";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("ssss", $title, $description, $dbFilePath, $category);

                if ($stmt->execute()) {
                    $successMessage = "Teaching material added successfully!";
                } else {
                    $errorMessages[] = "Error saving teaching material to the database.";
                }
            } else {
                $errorMessages[] = "Error moving the uploaded file.";
            }
        }
    } else {
        $errorMessages[] = "Error uploading the file.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Teaching Material</title>
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
        <h2 class="text-center mb-4">Add New Teaching Material</h2>
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
                    <label for="category">Category:</label>
                    <input type="text" id="category" name="category" class="form-control" placeholder="e.g., Mathematics, Science">
                </div>
                <div class="form-group mb-3">
                    <label for="file">Upload File (PDF, DOCX, TXT):</label>
                    <input type="file" id="file" name="file" class="form-control" accept=".pdf,.doc,.docx,.txt" required>
                </div>
                <button type="submit" class="btn btn-primary">Add Teaching Material</button>
            </form>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../includes/script.js"></script>
</body>
</html>