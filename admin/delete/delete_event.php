<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: ../login.php');
    exit();
}

include '../../db_connect.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']); 
    $fetchQuery = "SELECT image_url FROM events WHERE id = ?";
    $fetchStmt = $conn->prepare($fetchQuery);
    $fetchStmt->bind_param("i", $id);
    $fetchStmt->execute();
    $result = $fetchStmt->get_result();

    if ($result->num_rows === 1) {
        $consultancy = $result->fetch_assoc();
        $filePath = $consultancy['image_url'];

        // Delete the consultation from the database
        $deleteQuery = "DELETE FROM events WHERE id = ?";
        $deleteStmt = $conn->prepare($deleteQuery);
        $deleteStmt->bind_param("i", $id);

        if ($deleteStmt->execute()) {
            // Check if the file exists and delete it
            if (!empty($filePath) && file_exists("../../" . $filePath)) {
                unlink("../../" . $filePath);
            }
            $_SESSION['message'] = "Event and its associated image deleted successfully!";
        } else {
            $_SESSION['message'] = "Failed to delete the event from the database.";
        }
        $deleteStmt->close();
    } else {
        $_SESSION['message'] = "Event not found.";
    }

    $fetchStmt->close();
    $conn->close();

    // Redirect back to the consultancy management page
    header('Location: ../manage/manage_events.php');
    exit();
} else {
    header('Location: ../manage/manage_events.php');
    exit();
}
?>