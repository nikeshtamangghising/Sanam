<?php
$db_name = 'mysql:host=localhost;dbname=project';
$user_name = 'root';
$user_password = '';

try {
    // Create a PDO instance
    $conn = new PDO($db_name, $user_name, $user_password);
    // Set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    // If there's an error, show the error message
    die("Connection failed: " . $e->getMessage());
}
?>
