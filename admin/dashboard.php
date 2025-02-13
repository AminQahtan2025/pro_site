<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit();
}

include '../db_connect.php'; // Include database connection

// Fetch articles and users from the database
$articlesQuery = "SELECT * FROM articles";
$usersQuery = "SELECT * FROM users";

$articlesResult = $conn->query($articlesQuery);
$usersResult = $conn->query($usersQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container mt-4">
        <h1 class="text-center">Admin Dashboard</h1>

        <!-- Navigation Links -->
        <div class="mb-4">
            <a href="add_article.php" class="btn btn-success">Add New Article</a>
            <a href="logout.php" class="btn btn-danger">Logout</a>
        </div>

        <!-- Articles Management Section -->
        <div class="mb-5">
            <h2>Manage Articles</h2>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Title</th>
                        <th>Published Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($article = $articlesResult->fetch_assoc()): ?>
                    <tr>
                        <td><?= $article['id'] ?></td>
                        <td><?= $article['title'] ?></td>
                        <td><?= $article['published_date'] ?></td>
                        <td>
                            <a href="edit_article.php?id=<?= $article['id'] ?>" class="btn btn-primary btn-sm">Edit</a>
                            <a href="delete_article.php?id=<?= $article['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?');">Delete</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <!-- Users Management Section -->
        <div class="mb-5">
            <h2>Manage Users</h2>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($user = $usersResult->fetch_assoc()): ?>
                    <tr>
                        <td><?= $user['id'] ?></td>
                        <td><?= $user['name'] ?></td>
                        <td><?= $user['email'] ?></td>
                        <td>
                            <a href="edit_user.php?id=<?= $user['id'] ?>" class="btn btn-primary btn-sm">Edit</a>
                            <a href="delete_user.php?id=<?= $user['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?');">Delete</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <!-- Other Management Sections -->
        <div>
            <h2>Other Features</h2>
            <p>Expand this section with additional site management features (e.g., event management, feedback, etc.)</p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
