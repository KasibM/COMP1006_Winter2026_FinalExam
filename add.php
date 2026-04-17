<?php // Adapted from Lesson 10
// Need to be logged in to associate the added picture to an account
require "auth.php";

// Connect to the database
require "includes/connect.php";


// Array for validation errors
$errors = [];

// Success message
$success = "";

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Get and sanitize form values
    $username = $_SESSION['username'];
    $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_SPECIAL_CHARS);
    
    // This will store the image path for the database
    $imagePath = null;

    // Validate description
    if ($title === '') {
        $errors[] = "Image title is required.";
    }

    //From Lesson 10
    //check whether a file was uploaded
    if (isset($_FILES['image_path']) && $_FILES['image_path']['error'] !== UPLOAD_ERR_NO_FILE) {
        //make sure upload completed successfully 
        if ($_FILES['image_path']['error'] !== UPLOAD_ERR_OK) {
            $errors[] = "There was a problem uploading your file!";
        } else {
            //only allow a few file types 
            $allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/jpg'];
            //detect the real MIME type of the file 
            $detectedType = mime_content_type($_FILES['image_path']['tmp_name']);
            if (!in_array($detectedType, $allowedTypes, true)) {
                $errors[] = "Only jpeg, png, webp, jpg allowed";
            } else {
                //build the file name and move it to where we want it to go (uploads)
                //get the file extension 
                $extension = pathinfo($_FILES['image_path']['name'], PATHINFO_EXTENSION);
                //create a unique filename so uploaded files don't overwrite 
                $safeFilename = uniqid('image_', true) . '.' . strtolower($extension);
                //build the full server path where the file will be stored 
                $destination = __DIR__ . '/uploads/' . $safeFilename;
                if (move_uploaded_file($_FILES['image_path']['tmp_name'], $destination)) {
                    //save the relative path to the database
                    $imagePath = 'uploads/' . $safeFilename; 
                } else {
                    $errors[] = "Document uploaded failed!"; 
                }
            }
        }
    }

    //Add Code Here 

    // If there are no errors, insert the product into the database
    if (empty($errors)) {
        $sql = "INSERT INTO images (username, title, image_path)
                VALUES (:username, :title, :image_path)";
        // Prepare and bind
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':image_path', $imagePath);
        // Execute stmt
        $stmt->execute();
        // Success message
        $success = "Image added successfully!";
    }
}
?>

<main>
    <h1>Add Image</h1>

    <?php if (!empty($errors)): ?>
        <div>
            <h3>Please fix the following:</h3>
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php if ($success !== ""): ?>
        <div>
            <?= htmlspecialchars($success); ?>
        </div>
    <?php endif; ?>
    <!--enctype="multipart/form-data" required for uploads, will not send properly if not included -->
    <form method="post" enctype="multipart/form-data">
        <label for="title">Image Title</label>
        <input
            type="text"
            id="title"
            name="title"
            required
        >

        <label for="product_image">Image</label>
        <input
            type="file"
            id="image_path"
            name="image_path"
            accept=".jpg,.jpeg,.png,.webp"
        >

        <button type="submit">Add Image</button>
    </form>
</main>

<a href="index.php">View All</a>