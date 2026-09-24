<?php
session_start();
require_once "config.php";

/* Admin access only */
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "admin") {
    header("Location: index.php");
    exit();
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    /* Get and clean form data */
    $title = trim($_POST["title"] ?? "");
    $content = trim($_POST["content"] ?? "");

    /* Server-side validation */
    if ($title === "" || $content === "") {

        $message = "Title and content are required.";

    } elseif (strlen($title) > 255) {

        $message = "Title must not exceed 255 characters.";

    } else {

        /* Prepared statement */
        $stmt = $conn->prepare(
            "INSERT INTO posts (title, content) VALUES (?, ?)"
        );

        $stmt->bind_param("ss", $title, $content);

        if ($stmt->execute()) {
            header("Location: index.php");
            exit();
        } else {
            $message = "Error creating post.";
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Create Post</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>

<div class="container">

    <div class="card">

        <h2>Create New Post</h2>

        <?php if ($message != ""): ?>
            <p><?php echo htmlspecialchars($message); ?></p>
        <?php endif; ?>

        <form method="POST" onsubmit="return validateForm()">

            <label>Title:</label><br>

            <input
                type="text"
                name="title"
                id="title"
                required
            >

            <br><br>

            <label>Content:</label><br>

            <textarea
                name="content"
                id="content"
                rows="8"
                cols="50"
                required
            ></textarea>

            <br><br>

            <button type="submit">Create Post</button>

        </form>

        <br>

        <a href="index.php">Back to Home</a>

    </div>

</div>

<script>

function validateForm() {

    const title = document.getElementById("title").value.trim();
    const content = document.getElementById("content").value.trim();

    if (title === "") {
        alert("Please enter a title.");
        return false;
    }

    if (title.length > 255) {
        alert("Title must not exceed 255 characters.");
        return false;
    }

    if (content === "") {
        alert("Please enter content.");
        return false;
    }

    return true;
}

</script>

</body>
</html>