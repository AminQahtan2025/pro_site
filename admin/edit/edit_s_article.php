<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: ../login.php');
    exit();
}

include '../../db_connect.php';

// Fetch the article data
if (isset($_GET['id'])) {
    $id = intval($_GET['id']); // Securely fetch the article ID

    $query = "SELECT * FROM s_articles WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $article = $result->fetch_assoc();
    } else {
        $_SESSION['message'] = "Article not found.";
        header('Location: ../manage/manage_s_articles.php');
        exit();
    }
} else {
    header('Location: ../manage/manage_s_articles.php');
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $author = $_POST['author'];
    $pdf_url = $article['pdf_url']; // Retain the existing file by default

    // Handle file upload
    if (isset($_FILES['pdf_file']) && $_FILES['pdf_file']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../../uploads/';
        $fileName = basename($_FILES['pdf_file']['name']);
        $targetFilePath = $uploadDir . $fileName;

        // Check if file is a PDF
        $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));
        if ($fileType !== 'pdf') {
            $error = "Only PDF files are allowed.";
        } else {
            // Delete the old file
            if (file_exists($uploadDir . $pdf_url)) {
                unlink($uploadDir . $pdf_url);
            }

            // Upload the new file
            if (move_uploaded_file($_FILES['pdf_file']['tmp_name'], $targetFilePath)) {
                $pdf_url = $fileName; // Update the database with the new file name
            } else {
                $error = "Failed to upload the file.";
            }
        }
    }

    if (!isset($error)) {
        // Update query
        $updateQuery = "UPDATE s_articles SET title = ?, author = ?, pdf_url = ? WHERE id = ?";
        $updateStmt = $conn->prepare($updateQuery);
        $updateStmt->bind_param("sssi", $title, $author, $pdf_url, $id);

        if ($updateStmt->execute()) {
            $_SESSION['message'] = "Article updated successfully!";
            header('Location: ../manage/manage_s_articles.php');
            exit();
        } else {
            $error = "Failed to update the article. Please try again.";
        }

        $updateStmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Scientific Article</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" rel="stylesheet">
    <link rel="icon" href="/images/logo.png" type="image/x-icon">
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
            <h1 class="text-center">Edit Article</h1>

            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="title" class="form-label">Title</label>
                    <input type="text" class="form-control" id="title" name="title" value="<?php echo htmlspecialchars($article['title']); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="author" class="form-label">Author</label>
                    <input type="text" class="form-control" id="author" name="author" value="<?php echo htmlspecialchars($article['author']); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="pdf_file" class="form-label">Upload PDF</label>
                    <input type="file" class="form-control" id="pdf_file" name="pdf_file" accept=".pdf">
                </div>
                <button type="submit" class="btn btn-primary">Save Changes</button>
                <a href="manage_general_articles.php" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../includes/script.js"></script>
</body>
</html>
