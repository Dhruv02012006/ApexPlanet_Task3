<?php
session_start();
require_once "config.php";

/* Admin access only */
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "admin") {
    header("Location: index.php");
    exit();
}

/* Check post ID */
if (!isset($_GET["id"])) {
    header("Location: index.php");
    exit();
}

$id = intval($_GET["id"]);

/* Prepared statement */
$stmt = $conn->prepare("DELETE FROM posts WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    header("Location: index.php");
    exit();
} else {
    echo "Error deleting post.";
}

$stmt->close();
?>