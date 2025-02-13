<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: admin/login.php');
    exit();
}
include 'db_connect.php'; 

// Queries to fetch counts for each category
$generalArticlesCount = $conn->query("SELECT COUNT(*) as count FROM G_articles")->fetch_assoc()['count'];
$scientificArticlesCount = $conn->query("SELECT COUNT(*) as count FROM s_articles")->fetch_assoc()['count'];
$consultancyCount = $conn->query("SELECT COUNT(*) as count FROM consultancy")->fetch_assoc()['count'];
$trainingProgramsCount = $conn->query("SELECT COUNT(*) as count FROM training_programs")->fetch_assoc()['count'];
$teachingMaterialsCount = $conn->query("SELECT COUNT(*) as count FROM teaching_materials")->fetch_assoc()['count'];
$networkingCount = $conn->query("SELECT COUNT(*) as count FROM awards")->fetch_assoc()['count'];
$galleryCount = $conn->query("SELECT COUNT(*) as count FROM gallery")->fetch_assoc()['count'];
$eventsCount = $conn->query("SELECT COUNT(*) as count FROM events")->fetch_assoc()['count'];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" rel="stylesheet">
    <link rel="icon" href="images/logo.png" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="admin/includes/style.css">
    <!-- Include Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <h4 class="text-center"> Dashboard</h4>
        <hr class="bg-light">
        <a href="admin/manage/manage_g_articles.php"><i class="fa fa-book me-2"></i> General Articles</a>
        <a href="admin/manage/manage_s_articles.php"><i class="fa-brands fa-google-scholar me-2"></i> Scientific Articles</a>
        <a href="admin/manage/manage_consultancy.php"><i class="fa fa-briefcase me-2"></i> Consultancy</a>
        <a href="admin/manage/manage_training_programs.php"><i class="fa fa-chalkboard-teacher me-2"></i> Training Programs</a>
        <a href="admin/manage/manage_teaching_materials.php"><i class="fa fa-graduation-cap me-2"></i> Teaching Materials</a>
        <a href="admin/manage/manage_awards.php"><i class="fa fa-award me-2"></i>Awards</a>
        <a href="admin/manage/manage_gallery.php"><i class="fa fa-images me-2"></i> Gallery</a>
        <a href="admin/manage/manage_events.php"><i class="fa fa-calendar-alt me-2"></i> Events</a>
        <hr>
        <a href="admin/manage/manage_profile.php"><i class="fa fa-user-tie me-2"></i> Profile settings</a>
        <a href="admin/login.php"><i class="fa fa-sign-out-alt me-2"></i> Logout</a>
    </div>
    <!-- Main Content -->
    <div class="topbar" id="topbar">
        <button class="btn btn-outline-primary toggle-sidebar-btn" id="toggleSidebar" ><i class="fa fa-bars"></i></button>
        <h5>Welcome, Admin</h5>
    </div>
    
    <div class="content" id="content">
        <div class="row g-4">
            <!-- Cards for statistics -->
            <?php
            $categories = [
                'General Articles' => ['count' => $generalArticlesCount, 'icon' => 'fa fa-book'],
                'Scientific Articles' => ['count' => $scientificArticlesCount, 'icon' => 'fa-brands fa-google-scholar'],
                'Consultancy' => ['count' => $consultancyCount, 'icon' => 'fa fa-briefcase'],
                'Training Programs' => ['count' => $trainingProgramsCount, 'icon' => 'fa fa-chalkboard-teacher'],
                'Materials' => ['count' => $teachingMaterialsCount, 'icon' => 'fa fa-graduation-cap'],
                'Awards' => ['count' => $networkingCount, 'icon' => 'fa fa-award'],
                'Gallery' => ['count' => $galleryCount, 'icon' => 'fa fa-images'],
                'Events' => ['count' => $eventsCount, 'icon' => 'fa fa-calendar-alt'],
            ];

            foreach ($categories as $category => $data) {
                echo "
                <div class='col-md-3'>
                    <div class='card bg-light'>
                        <div class='row align-items-center'>
                            <!-- Icon Section -->
                            <div class='col-3'>
                                <div class='card-body'>
                                    <i class='{$data['icon']} fa-2x mb-3' style='color: #002079;'></i>
                                </div>
                            </div>
                            <!-- Text Section -->
                            <div class='col-9'>
                                <div class='card-body'>
                                    <h5 class='card-title'>$category</h5>
                                    <h5 class='card-text  mb-0 fw-bold text-center'>{$data['count']}</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>";
            }
            ?>
        </div>
</body>
</html>