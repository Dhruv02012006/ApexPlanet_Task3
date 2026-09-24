<?php
session_start();
require_once "config.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

/* ---------------- SEARCH ---------------- */

$search = "";

if (isset($_GET["search"])) {
    $search = trim($_GET["search"]);
}

/* ---------------- PAGINATION ---------------- */

$posts_per_page = 5;

$page = isset($_GET["page"]) ? (int)$_GET["page"] : 1;

if ($page < 1) {
    $page = 1;
}

$offset = ($page - 1) * $posts_per_page;


/* ---------------- COUNT POSTS ---------------- */

if ($search != "") {

    $search_term = "%" . $search . "%";

    $count_stmt = $conn->prepare(
        "SELECT COUNT(*) AS total
         FROM posts
         WHERE title LIKE ? OR content LIKE ?"
    );

    $count_stmt->bind_param("ss", $search_term, $search_term);
    $count_stmt->execute();

    $count_result = $count_stmt->get_result();
    $total_posts = $count_result->fetch_assoc()["total"];

    $count_stmt->close();

} else {

    $count_result = $conn->query(
        "SELECT COUNT(*) AS total FROM posts"
    );

    $total_posts = $count_result->fetch_assoc()["total"];
}


/* ---------------- TOTAL PAGES ---------------- */

$total_pages = ceil($total_posts / $posts_per_page);


/* ---------------- GET POSTS ---------------- */

if ($search != "") {

    $search_term = "%" . $search . "%";

    $stmt = $conn->prepare(
        "SELECT *
         FROM posts
         WHERE title LIKE ? OR content LIKE ?
         ORDER BY created_at DESC
         LIMIT ? OFFSET ?"
    );

    $stmt->bind_param(
        "ssii",
        $search_term,
        $search_term,
        $posts_per_page,
        $offset
    );

    $stmt->execute();

    $result = $stmt->get_result();

} else {

    $stmt = $conn->prepare(
        "SELECT *
         FROM posts
         ORDER BY created_at DESC
         LIMIT ? OFFSET ?"
    );

    $stmt->bind_param(
        "ii",
        $posts_per_page,
        $offset
    );

    $stmt->execute();

    $result = $stmt->get_result();
}
?>

<!DOCTYPE html>
<html>

<head>

    <title>My Blog - Task 3</title>

    <link rel="stylesheet" href="style.css">

</head>

<body>

<div class="container">

    <div class="card">

        <h1>My Blog</h1>

        <p>
            Welcome,
            <strong>
                <?php echo htmlspecialchars($_SESSION["username"]); ?>
            </strong>
        </p>

        <?php if ($_SESSION["role"] === "admin"): ?>
    <a href="create.php">Create New Post</a>
<?php endif; ?>

<a href="logout.php">Logout</a>

    </div>


    <!-- SEARCH -->

    <div class="card">

        <h2>Search Posts</h2>

        <form method="GET" action="index.php">

            <input
                type="text"
                name="search"
                placeholder="Search by title or content..."
                value="<?php echo htmlspecialchars($search); ?>"
            >

            <button type="submit">
                Search
            </button>

            <?php if ($search != ""): ?>

                <a href="index.php">
                    Clear Search
                </a>

            <?php endif; ?>

        </form>

    </div>


    <!-- POSTS -->

    <div class="card">

        <h2>Posts</h2>

        <?php if ($result->num_rows > 0): ?>

            <?php while ($post = $result->fetch_assoc()): ?>

                <div class="post">

                    <h3>
                        <?php echo htmlspecialchars($post["title"]); ?>
                    </h3>

                    <p>
                        <?php
                        echo nl2br(
                            htmlspecialchars($post["content"])
                        );
                        ?>
                    </p>

                    <small>
                        Created:
                        <?php echo htmlspecialchars($post["created_at"]); ?>
                    </small>

                    <br><br>

                    <a href="edit.php?id=<?php echo $post["id"]; ?>">
                        Edit
                    </a>

                    <a
                        href="delete.php?id=<?php echo $post["id"]; ?>"
                        onclick="return confirm('Are you sure you want to delete this post?');"
                    >
                        Delete
                    </a>

                </div>

                <hr>

            <?php endwhile; ?>

        <?php else: ?>

            <p>
                No posts found.
            </p>

        <?php endif; ?>

    </div>


    <!-- PAGINATION -->

    <?php if ($total_pages > 1): ?>

        <div class="pagination">

            <?php if ($page > 1): ?>

                <a
                    href="index.php?page=<?php echo $page - 1; ?>&search=<?php echo urlencode($search); ?>"
                >
                    ← Previous
                </a>

            <?php endif; ?>


            <?php for ($i = 1; $i <= $total_pages; $i++): ?>

                <a
                    href="index.php?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>"
                    class="<?php echo ($i == $page) ? 'active' : ''; ?>"
                >
                    <?php echo $i; ?>
                </a>

            <?php endfor; ?>


            <?php if ($page < $total_pages): ?>

                <a
                    href="index.php?page=<?php echo $page + 1; ?>&search=<?php echo urlencode($search); ?>"
                >
                    Next →
                </a>

            <?php endif; ?>

        </div>

    <?php endif; ?>

</div>

</body>

</html>