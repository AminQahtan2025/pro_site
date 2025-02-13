<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: ../login.php');
    exit();
}

include '../../db_connect.php';

$articleId = $_GET['article_id'];

// Fetch comments and ratings for the article
$query = "SELECT * FROM g_article_comments WHERE article_id = $articleId";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Comments</title>
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
<body>
    <div class="container mt-5">
        <h1 class="text-center">Manage Comments and Ratings</h1>
        <a href="manage_g_articles.php" class="btn btn-secondary mb-3">Back to Articles</a>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Comment</th>
                    <th>Rating</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "
                        <tr>
                            <td>{$row['id']}</td>
                            <td>{$row['comment']}</td>
                            <td>{$row['rating']}</td>
                            <td>{$row['created_at']}</td>
                            <td>
                                <a href='delete_comment.php?id={$row['id']}' class='btn btn-danger btn-sm' onclick='return confirm(\"Are you sure you want to delete this comment?\")'>
                                    <i class='fa fa-trash'></i> Delete
                                </a>
                            </td>
                        </tr>";
                    }
                } else {
                    echo "<tr><td colspan='5' class='text-center'>No comments found.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>