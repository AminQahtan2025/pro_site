<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: ../login.php');
    exit();
}

include '../../db_connect.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']); // Get the article ID securely

    // Fetch the file path of the article
    $fetchQuery = "SELECT file_url FROM training_programs WHERE id = ?";
    $fetchStmt = $conn->prepare($fetchQuery);
    $fetchStmt->bind_param("i", $id);
    $fetchStmt->execute();
    $result = $fetchStmt->get_result();

    if ($result->num_rows === 1) {
        $article = $result->fetch_assoc();
        $filePath = $article['file_url'];

        // Delete the article from the database
        $deleteQuery = "DELETE FROM training_programs WHERE id = ?";
        $deleteStmt = $conn->prepare($deleteQuery);
        $deleteStmt->bind_param("i", $id);

        if ($deleteStmt->execute()) {
            // Check if the file exists and delete it
            if (!empty($filePath) && file_exists("../../" . $filePath)) {
                unlink("../../" . $filePath);
            }
            $_SESSION['message'] = "Training program and its file deleted successfully!";
        } else {
            $_SESSION['message'] = "Failed to delete the Training program  from the database.";
        }
        $deleteStmt->close();
    } else {
        $_SESSION['message'] = "Training program not found.";
    }

    $fetchStmt->close();
    $conn->close();

    // Redirect back to the articles management page
    header('Location: ../manage/manage_training_programs.php');
    exit();
} else {
    header('Location: ../manage/manage_training_programs.php');
    exit();
}
?>
