<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: ../login.php');
    exit();
}

include '../../db_connect.php';

// Pagination settings
$imagesPerPage = 5; // Number of images per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1; // Current page number
$offset = ($page - 1) * $imagesPerPage; // Offset for SQL query

// Fetch total number of images
$totalImagesQuery = "SELECT COUNT(*) AS total FROM gallery";
$totalImagesResult = $conn->query($totalImagesQuery);
$totalImages = $totalImagesResult->fetch_assoc()['total'];

// Fetch images for the current page
$query = "SELECT * FROM gallery LIMIT $offset, $imagesPerPage";
$result = $conn->query($query);

// Calculate total pages
$totalPages = ceil($totalImages / $imagesPerPage);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Gallery</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" rel="stylesheet">
    <link rel="icon" href="../../images/logo.png" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../includes/style.css">
</head>
<body>
     <!-- Sidebar -->
     <div class="sidebar" id="sidebar">
        <h4 class="text-center"> Dashboard</h4>
        <hr class="bg-light">
        <a href="manage_g_articles.php"><i class="fa fa-book me-2"></i> General Articles</a>
        <a href="manage_s_articles.php"><i class="fa-brands fa-google-scholar me-2"></i> Scientific Articles</a>
        <a href="manage_consultancy.php"><i class="fa fa-briefcase me-2"></i> Consultancy</a>
        <a href="manage_training_programs.php"><i class="fa fa-chalkboard-teacher me-2"></i> Training Programs</a>
        <a href="manage_teaching_materials.php"><i class="fa fa-graduation-cap me-2"></i> Teaching Materials</a>
        <a href="manage_awards.php"><i class="fa fa-award me-2"></i>Awards</a>
        <a href="manage_gallery.php"><i class="fa fa-images me-2"></i> Gallery</a>
        <a href="manage_events.php"><i class="fa fa-calendar-alt me-2"></i> Events</a>
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
        <h1 class="text-center">Manage Gallery</h1>
        <div class="d-flex justify-content-start mb-3">
            <a href="../add/add_gallery.php" class="btn btn-success"><i class="fa fa-plus me-2"></i>Add New Image</a>
        </div>
        <div class="row">
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "
                    <div class='col-md-4 mb-4'>
                        <div class='card'>
                            <img src='../../{$row['image_url']}' class='card-img-top' alt='{$row['title']}'>
                            <div class='card-body'>
                                <h5 class='card-title'>{$row['title']}</h5>
                                <p class='card-text'>{$row['description']}</p>
                                <div class='d-flex justify-content-between'>
                                    <a href='../edit/edit_gallery.php?id={$row['id']}' class='btn btn-primary btn-sm'>
                                        <i class='fa fa-edit'></i> Edit
                                    </a>
                                    <a href='../delete/delete_gallery.php?id={$row['id']}' class='btn btn-danger btn-sm' onclick='return confirm(\"Are you sure you want to delete this image?\")'>
                                        <i class='fa fa-trash'></i> Delete
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>";
                }
            } else {
                echo "<div class='col-12 text-center text-danger'>No images found in the gallery.</div>";
            }
            ?>
        </div>

        <!-- Pagination -->
        <nav aria-label="Page navigation">
            <ul class="pagination">
                <?php if ($page > 1): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?php echo $page - 1; ?>" aria-label="Previous">
                            <span aria-hidden="true">&laquo;</span>
                        </a>
                    </li>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                        <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                    </li>
                <?php endfor; ?>

                <?php if ($page < $totalPages): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?php echo $page + 1; ?>" aria-label="Next">
                            <span aria-hidden="true">&raquo;</span>
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../includes/script.js"></script>
</body>
</html>