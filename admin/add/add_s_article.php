<?php
// Include database connection
include '../../db_connect.php';

// Initialize error messages
$errorMessages = [];
$successMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate form fields
    $title = isset($_POST['title']) ? trim($_POST['title']) : '';
    $author = isset($_POST['author']) ? trim($_POST['author']) : 'Dr. Abdullah Al-Swidi';
    $description = isset($_POST['description']) ? trim($_POST['description']) : '';
    $url = isset($_POST['url']) ? trim($_POST['url']) : '';

    // Validate title and description
    if (empty($title)) {
        $errorMessages[] = "Title is required.";
    }

    // Check if a file is uploaded
    if (isset($_FILES['pdf']) && $_FILES['pdf']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['pdf']['tmp_name'];
        $fileName = basename($_FILES['pdf']['name']);
        $fileSize = $_FILES['pdf']['size'];
        $fileType = $_FILES['pdf']['type'];
        $uploadDir = '../../uploads/'; // Define the upload directory
        $relativePath = 'uploads/'; // Path to store in the database

        // Validate file type (e.g., PDF)
        $allowedFileTypes = ['application/pdf'];
        if (!in_array($fileType, $allowedFileTypes)) {
            $errorMessages[] = "Only PDF files are allowed.";
        } else {
            // Generate a unique name for the file to avoid overwriting
            $uniqueFileName = $fileName;
            $destination = $uploadDir . $uniqueFileName;
            $dbFilePath = $relativePath . $uniqueFileName; // Relative path for the database

            // Move uploaded file to the server directory
            if (move_uploaded_file($fileTmpPath, $destination)) {
                // Save article details to the database
                $query = "INSERT INTO s_articles (title, author, description,url, pdf_url) VALUES (?, ?,?, ?, ?)";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("sssss", $title, $author, $description,$url, $dbFilePath);

                if ($stmt->execute()) {
                    $successMessage = "Article added successfully!";
                } else {
                    $errorMessages[] = "Error saving article to the database.";
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
    <title>Add New Scientific Article</title>
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
        <h2 class="text-center mb-4">Add New Scientific Article</h2>
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

            <form action="" method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="title" class="form-label">Title</label>
                    <input type="text" name="title" id="title" class="form-control" value="<?php echo htmlspecialchars($title ?? ''); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="author" class="form-label">Author</label>
                    <input type="text" name="author" id="author" class="form-control" value="<?php echo htmlspecialchars($author ?? ''); ?>">
                </div>
                <div class="mb-3">
                    <label for="description" class="form-label">Description (Optional)</label>
                    <textarea name="description" id="description" class="form-control" rows="4"><?php echo htmlspecialchars($description ?? ''); ?></textarea>
                </div>
                <div class="mb-3">
                    <label for="url" class="form-label">URL Link</label>
                    <input type="text" name="url" id="url" class="form-control" value="<?php echo htmlspecialchars($url ?? ''); ?>">
                </div>
                <div class="mb-3">
                    <label for="pdf" class="form-label">Upload PDF</label>
                    <input type="file" name="pdf" id="pdf" class="form-control" accept="application/pdf" required>
                </div>
                <button type="submit" class="btn btn-primary">Add Article</button>
            </form>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../includes/script.js"></script>
</body>
</html>
