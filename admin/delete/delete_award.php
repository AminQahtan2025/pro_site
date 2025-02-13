<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: ../login.php');
    exit();
}

include '../../db_connect.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']); // Get the award ID securely

    // Fetch the image URLs and file URL of the award
    $fetchQuery = "SELECT image_url, file_url FROM awards WHERE id = ?";
    $fetchStmt = $conn->prepare($fetchQuery);
    $fetchStmt->bind_param("i", $id);
    $fetchStmt->execute();
    $result = $fetchStmt->get_result();

    if ($result->num_rows === 1) {
        $award = $result->fetch_assoc();
        $imageUrls = explode(',', $award['image_url']); // Split image URLs into an array
        $filePath = $award['file_url']; // Get the certificate file path

        // Delete the award from the database
        $deleteQuery = "DELETE FROM awards WHERE id = ?";
        $deleteStmt = $conn->prepare($deleteQuery);
        $deleteStmt->bind_param("i", $id);

        if ($deleteStmt->execute()) {
            // Delete associated images from the upload folder
            foreach ($imageUrls as $imagePath) {
                if (!empty($imagePath) && file_exists("../../" . $imagePath)) {
                    unlink("../../" . $imagePath); // Delete the image file
                }
            }

            // Delete the certificate file from the upload folder
            if (!empty($filePath) && file_exists("../../" . $filePath)) {
                unlink("../../" . $filePath); // Delete the certificate file
            }

            $_SESSION['message'] = "Award and its associated files deleted successfully!";
        } else {
            $_SESSION['message'] = "Failed to delete the award from the database.";
        }
        $deleteStmt->close();
    } else {
        $_SESSION['message'] = "Award not found.";
    }

    $fetchStmt->close();
    $conn->close();

    // Redirect back to the awards management page
    header('Location: ../manage/manage_awards.php');
    exit();
} else {
    header('Location: ../manage/manage_awards.php');
    exit();
}
?>