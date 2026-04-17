<?php
//Adapted from Lesson 09

// Sessions allow us to store login information across multiple pages.
session_start();
require "includes/connect.php";
 
// Variable to store an error message if login fails
$error = "";

// Check if the form was submitted using the POST method
// This ensures the login logic only runs after the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Retrieve the username or email from the form
    // trim() removes any extra spaces from the beginning or end
    $usernameOrEmail = trim($_POST['username_or_email'] ?? '');

    // Retrieve the password from the form
    $password = $_POST['password'] ?? '';

    // Basic validation: make sure both fields were filled in
    if ($usernameOrEmail === '' || $password === '') {

        // Store an error message if either field is empty
        $error = "Username/email and password are required.";

    } else {

        // SQL query to find a user with the matching username OR email
        // LIMIT 1 ensures only one user record is returned
        $sql = "SELECT id, username, email, password FROM users WHERE username = :login OR email = :login LIMIT 1"; 

        // Prepare the SQL statement using PDO
        $stmt = $pdo->prepare($sql); 

        // Bind the user input to the :login parameter
        $stmt->bindParam(':login', $usernameOrEmail); 
        
        // Execute the query
        $stmt->execute(); 

        // Fetch the matching user as an associative array
        $user = $stmt->fetch(PDO::FETCH_ASSOC); 

        // Check two conditions:
        // 1. A user record was found
        // 2. The entered password matches the stored hashed password
        if ($user && password_verify($password, $user['password'])) {

            // Regenerate the session ID for security
            // This helps prevent session fixation attacks
            session_regenerate_id(true);
           
            // Store user information in session variables
            // These variables indicate the user is now logged in
            $_SESSION['user_id'] = $user['id']; 
            $_SESSION['username'] = $user['username']; 

            // Redirect the user to index.php
            header("Location: index.php");  
           
            // Stop the script immediately after redirecting
            exit; 

        } else {

            // If login fails, display an error message
            $error = "Invalid credentials. Please try again!"; 
        }
    }
}
?>

<main>
    <h2>Login</h2>

    <!-- If there is an error message, display it in a Bootstrap alert -->
    <?php if ($error !== ""): ?>
        <div>
            <!-- htmlspecialchars prevents malicious code from being displayed -->
            <?= htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <!-- Login form -->
    <form method="post">

        <!-- Username or email input -->
        <label for="username_or_email">Username or Email</label>
        <input
            type="text"
            id="username_or_email"
            name="username_or_email"
            required
        >
        <br>
        <!-- Password input -->
        <label for="password">Password</label>
        <input
            type="password"
            id="password"
            name="password"
            required
        >
        <br>
        <!-- Submit button -->
        <button type="submit">Login</button>

        <!-- Link to registration page -->
        <a href="register.php">Create Account</a>
    </form>
</main>
