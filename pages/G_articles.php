<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>General Articles</title>
    <!-- Bootstrap and CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" rel="stylesheet">
    <link rel="icon" href="../images/logo.png" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .card { height: 200px; }
        .hover-icon { opacity: 0; transition: opacity 0.3s ease; position: absolute; top: 90%; left: 90%; transform: translate(-50%, -50%); }
    </style>
</head>
<body>
    <div class="container">
        <!-- Banner Image -->
        <div class="banner">
            <img src="../images/banner.jpeg" alt="General Articles Banner" class="img-fluid w-100 shadow-sm">
        </div>

        <div class="container border">
            <h1 class="text-center m-4">General Articles</h1>
            <div class="row g-3">
                <?php
                // Database connection
                $conn = new mysqli('localhost', 'root', '', 'articles_db');
                if ($conn->connect_error) {
                    die("Connection failed: " . $conn->connect_error);
                }

                // Fetch articles
                $sql = "SELECT id, title, description, published_date, pdf_url FROM articles";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo '
                        <div class="col-md-6">
                            <a href="../articles/article-viewer.php?id=' . $row['id'] . '" class="text-decoration-none card-link" style="color: black;">
                                <div class="card mb-3 shadow-sm h-wrap">
                                    <div class="row g-0 align-items-center">
                                        <div class="col-2 text-center p-3">
                                            <i class="fa-regular fa-newspaper fa-4x" style="color: #002079;"></i>
                                        </div>
                                        <div class="col-10 position-relative">
                                            <div class="card-body">
                                                <h5 class="card-title">' . $row['title'] . '</h5>
                                                <p class="card-text">' . $row['description'] . '</p>
                                                <p class="card-text"><small class="text-body-secondary">Published: ' . $row['published_date'] . '</small></p>
                                            </div>
                                            <div class="hover-icon position-absolute translate-middle-y pe-3">
                                                <i class="fa fa-arrow-right fa-lg" style="color: #002079;"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>';
                    }
                } else {
                    echo '<p class="text-center">No articles available.</p>';
                }

                $conn->close();
                ?>
            </div>
        </div>
    </div>

<!-- Footer -->
<footer class="footer shadow mt-3 w-100">
    <div class="container">
        <div class="row">
            <div class="col-md-4">
                <div class="logo text-center">
                    <h5><a href="#" class="navbar-brand text-white fw-bold">Dr. Abdullah Al-Swidi</a></h5>
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
            <p class="mb-0 small">&copy; 2024 <a href="#"
                    class="text-white text-decoration-none">Dr. Abdullah Al-Swidi</a>. All rights reserved.</p>
        </div>
    </div>
</footer>

</div>
</main>
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