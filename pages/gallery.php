<?php
// Include database connection
include '../db_connect.php';

// Pagination settings
$itemsPerPage = 6; // Number of gallery entries per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1; // Current page number
$offset = ($page - 1) * $itemsPerPage; // Offset for SQL query

// Fetch total number of gallery entries
$totalQuery = "SELECT COUNT(*) AS total FROM gallery";
$totalResult = $conn->query($totalQuery);
$totalItems = $totalResult->fetch_assoc()['total'];

// Fetch gallery entries for the current page
$query = "SELECT * FROM gallery ORDER BY uploaded_date DESC LIMIT $offset, $itemsPerPage";
$result = $conn->query($query);

// Calculate total pages
$totalPages = ceil($totalItems / $itemsPerPage);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gallery</title>
    <!-- Bootstrap and CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" rel="stylesheet">
    <link rel="icon" href="../images/logo.png" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .card { height: 100%; }
        .card-img-top { height: 200px; object-fit: cover; }
        .hover-icon { opacity: 0; transition: opacity 0.3s ease; position: absolute; top: 90%; left: 90%; transform: translate(-50%, -50%); }
        .card:hover .hover-icon { opacity: 1; }
    </style>
</head>
<body>
    <div class="container">
        <!-- Banner Image -->
        <div class="banner">
            <img src="../images/banner.jpeg" alt="Gallery Banner" class="img-fluid w-100 shadow-sm">
        </div>
        <div class="header logo d-flex flex-row">
            <hr class="bg-light">
            <!-- Breadcrumb -->
            <div aria-label="breadcrumb" class="shadow header w-100">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="../index.html" class="text-decoration-none">Home</a> /</li>
                    <li class="breadcrumb-item active" aria-current="page">Gallery</li>
                </ol>
            </div>
        </div>
        <div class="container border">
            <h1 class="text-center m-4">Gallery</h1>
            <div class="row row-cols-1 row-cols-md-3 g-4">
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo '
                        <div class="col">
                            <div class="card h-100 shadow-sm">
                                <img src="../' . $row['image_url'] . '" class="card-img-top" alt="' . $row['title'] . '">
                                <div class="card-body">
                                    <h5 class="card-title">' . $row['title'] . '</h5>
                                    <p class="card-text">' . $row['description'] . '</p>
                                    <p class="card-text"><small class="text-body-secondary">Uploaded: ' . $row['uploaded_date'] . '</small></p>
                                </div>
                                <div class="hover-icon">
                                    <i class="fa fa-arrow-right fa-lg" style="color: #002079;"></i>
                                </div>
                            </div>
                        </div>';
                    }
                } else {
                    echo '<hr><div class="alert alert-danger text-center shadow mt-3 w-100"> No gallery entries available.</div>';
                }
                ?>
            </div>
            <!-- Pagination -->
            <nav aria-label="Page navigation" class="mt-4">
                <ul class="pagination justify-content-center">
                    <?php if ($page > 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?php echo $page - 1; ?>">Previous</a>
                        </li>
                    <?php else: ?>
                        <li class="page-item disabled">
                            <a class="page-link">Previous</a>
                        </li>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                        </li>
                    <?php endfor; ?>

                    <?php if ($page < $totalPages): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?php echo $page + 1; ?>">Next</a>
                        </li>
                    <?php else: ?>
                        <li class="page-item disabled">
                            <a class="page-link">Next</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
        <!-- Footer -->
        <footer class="footer shadow mt-3 w-100">
            <div class="container">
                <div class="row">
                    <div class="col-md-4">
                        <div class="logo text-center">
                            <h5><a href="../index.html" class="navbar-brand text-white fw-bold">Dr. Abdullah Al-Swidi</a></h5>
                            <p style="font-size: 15px; margin-top: 10px;">Driving Excellence and Innovation in Sustainability</p>
                        </div>
                    </div>
                    <div class="col-md-4 text-center mt-2">
                        <p id="visitor-counter" class="text-white fw-bold" style="font-size: 14px;">
                            Visitors: <br>
                            <span id="visitor-count" class="animated-counter">0</span>
                        </p>
                    </div>
                    <div class="col-md-4 mt-2">
                        <div id="google_translate_element" class="translate-dropdown"></div>
                    </div>
                </div>
                <hr class="bg-light">
                <div class="text-center">
                    <p class="mb-0 small">&copy; 2024 <a href="#" class="text-white text-decoration-none">Dr. Abdullah Al-Swidi</a>. All rights reserved.</p>
                </div>
            </div>
        </footer>
    </div>
    <script type="text/javascript" src="https://translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>
    <script src="../js/counter.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script type="text/javascript">
        function googleTranslateElementInit() {
            new google.translate.TranslateElement(
                {pageLanguage: 'en', includedLanguages: 'en,ar', layout: google.translate.TranslateElement.InlineLayout.SIMPLE},
                'google_translate_element'
            );
        }
    </script>
</body>
</html>