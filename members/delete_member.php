<?php
require_once "../config/config.php";

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "DELETE FROM Member WHERE member_id = $id";

    if ($conn->query($sql) === TRUE) {
        header("Location: view_members.php");
        exit;
    } else {
        echo "Error deleting record: " . $conn->error;
    }
} else {
    echo "ID not provided!";
}
?>