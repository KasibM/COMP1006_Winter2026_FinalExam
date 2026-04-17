<?php
//require database connection script and login check
require "auth.php";
require "includes/connect.php";


//check if GET
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    die('Invalid request');
}

//get + sanitise values
$id = trim(filter_input(INPUT_GET,'id',FILTER_SANITIZE_SPECIAL_CHARS));


//validation

$errors = [];

if($id === null || $id === ''){
    $errors[] = "ID is required.";
}

//if errors stop before inserting into the database
if (!empty($errors)) { ?>
    <?php echo "Failed to remove data due to the following errors:\n";
    foreach ($errors as $error) : ?>
        <li><?php echo $error; ?> </li>
    <?php endforeach;
    //stop the script from executing  
    exit;
}


//build query with named placeholder 
$sql = "DELETE from images WHERE id = :id and username = :username";

//prepare the query
$stmt = $pdo->prepare($sql);

//map named placeholder to data
//e.g. $stmt -> bindParam(":first_name", firstName);

$stmt -> bindParam(':id', $id);
$stmt -> bindParam(':username', $_SESSION["username"]);

//execute the query
$stmt -> execute();

//close connection
$_pdo = null;

//send back to index
header("Location: index.php");
exit;
?>

 
