<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: ../login.php');
    exit();
}

include '../../db_connect.php';

// Pagination settings
$awardsPerPage = 5; // Number of awards per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1; // Current page number
$offset = ($page - 1) * $awardsPerPage; // Offset for SQL query

// Fetch total number of awards
$totalAwardsQuery = "SELECT COUNT(*) AS total FROM awards";
$totalAwardsResult = $conn->query($totalAwardsQuery);
$totalAwards = $totalAwardsResult->fetch_assoc()['total'];

// Fetch awards for the current page
$query = "SELECT * FROM awards LIMIT $offset, $awardsPerPage";
$result = $conn->query($query);

// Calculate total pages
$totalPages = ceil($totalAwards / $awardsPerPage);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Awards</title>
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
        <h1 class="text-center">Manage Awards</h1>
        <div class="d-flex justify-content-start mb-3">
            <a href="../add/add_award.php" class="btn btn-success"><i class="fa fa-plus me-2"></i>Add New Award</a>
        </div>
        <table class="table table-striped table-bordered table-hover w-100">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Description</th>
                    <th>Date Received</th>

                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        // Truncate the description to 100 characters
                        $description = $row['description'];
                        $truncatedDescription = strlen($description) > 100 ? substr($description, 0, 100) . '...' : $description;
                        echo "
                        <tr>
                            <td>{$row['id']}</td>
                            <td>{$row['title']}</td>
                            <td>
                                {$truncatedDescription}
                                <a href='../../pages/view/award-viewer.php?id={$row['id']}' class='text-primary'>Read More</a>
                            </td>
                            <td>{$row['date_received']}</td>
                            <td>
                                <a href='../edit/edit_award.php?id={$row['id']}' class='btn btn-primary btn-sm'>
                                    <i class='fa fa-edit'></i> Edit
                                </a>
                                <a href='../delete/delete_award.php?id={$row['id']}' class='btn btn-danger btn-sm' onclick='return confirm(\"Are you sure you want to delete this award?\")'>
                                    <i class='fa fa-trash'></i> Delete
                                </a>
                            </td>
                        </tr>";
                    }
                } else {
                    echo "<tr><td colspan='8' class='text-center text-danger'>No awards found.</td></tr>";
                }
                ?>
            </tbody>
        </table>

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