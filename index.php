<?php require "auth.php" ?>
<?php require "includes/connect.php" ?>
<?php
    // Build query 
    $sql = "SELECT id, title, image_path
            FROM images 
            WHERE username = :username
            ORDER BY created_at";

    // Prepare the query
    $stmt = $pdo->prepare($sql);

    // Map named placeholder to data
    // e.g. $stmt -> bindParam(":first_name", firstName);
    $stmt -> bindParam(':username', $_SESSION["username"]);

    // Execute the query
    $stmt -> execute();

    // Fetch query results
    $images = $stmt->fetchALL();


?>
<main>
<h1>Image Gallery</h1>
<br>
<a href="add.php">Add Image</a>
<br>
<a href="logout.php">Logout</a>
<br>

<table class="table table-bordered mt-3">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Image</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($images as $image): ?>
                    <tr>
                        <td>
                            <?= htmlspecialchars($image['id']);?>
                        </td>
                        <td>
                            <?= htmlspecialchars($image['title']);?>
                        </td>
                        <td>
                            <img width = 150px height = auto src = "<?= htmlspecialchars($image['image_path'])?>" />
                        </td>
                        
                        <td>
                            <a
                                href="delete?id=<?= urlencode($image['id']); ?>"
                                onclick="return confirm('Please confirm deletion of this image.');">
                                Delete
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>





</main>