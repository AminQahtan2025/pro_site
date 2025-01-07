<?php
// Database connection
$conn = new mysqli('localhost', 'root', '', 'Dr_db');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Collect form data
$title = $_POST['title'];
$description = $_POST['description'];
$pdf = $_FILES['pdf'];

// Upload PDF file
$targetDir = "uploads/";
$targetFile = $targetDir . basename($pdf['name']);
if (move_uploaded_file($pdf['tmp_name'], $targetFile)) {
    // Insert article details into database
    $sql = "INSERT INTO articles (title, description, published_date, pdf_url) 
            VALUES ('$title', '$description', NOW(), '$targetFile')";
    if ($conn->query($sql) === TRUE) {
        echo "Article uploaded successfully!";
        header("Location: general-articles.php");
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
} else {
    echo "Error uploading file.";
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Article</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center">Add New Article</h1>
        <form action="add-article.php" method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="title" class="form-label">Article Title</label>
                <input type="text" class="form-control" id="title" name="title" required>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
            </div>
            <div class="mb-3">
                <label for="pdf" class="form-label">Upload PDF</label>
                <input type="file" class="form-control" id="pdf" name="pdf" accept=".pdf" required>
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
    </div>
</body>
</html>
