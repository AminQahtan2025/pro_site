<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: ../login.php');
    exit();
}

include '../../db_connect.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']); // Get the consultation ID securely

    // Fetch the file path of the consultation image
    $fetchQuery = "SELECT image_url FROM gallery WHERE id = ?";
    $fetchStmt = $conn->prepare($fetchQuery);
    $fetchStmt->bind_param("i", $id);
    $fetchStmt->execute();
    $result = $fetchStmt->get_result();

    if ($result->num_rows === 1) {
        $consultancy = $result->fetch_assoc();
        $filePath = $consultancy['image_url'];

        // Delete the consultation from the database
        $deleteQuery = "DELETE FROM gallery WHERE id = ?";
        $deleteStmt = $conn->prepare($deleteQuery);
        $deleteStmt->bind_param("i", $id);

        if ($deleteStmt->execute()) {
            // Check if the file exists and delete it
            if (!empty($filePath) && file_exists("../../" . $filePath)) {
                unlink("../../" . $filePath);
            }
            $_SESSION['message'] = "gallery and its associated image deleted successfully!";
        } else {
            $_SESSION['message'] = "Failed to delete the gallery from the database.";
        }
        $deleteStmt->close();
    } else {
        $_SESSION['message'] = "gallery not found.";
    }

    $fetchStmt->close();
    $conn->close();

    // Redirect back to the consultancy management page
    header('Location: ../manage/manage_gallery.php');
    exit();
} else {
    header('Location: ../manage/manage_gallery.php');
    exit();
}
?>