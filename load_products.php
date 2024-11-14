<?php
include 'components/connect.php';
error_reporting(E_ALL);  // Report all errors
ini_set('display_errors', 1);  // Display errors on the screen

header('Content-Type: application/json');

session_start();
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : '';

// Get the filter data from POST request
$category = $_POST['category'] ?? 'all';
$gender = $_POST['gender'] ?? 'all';
$color = $_POST['color'] ?? 'all';
$size = $_POST['size'] ?? 'all';
$sort_by = $_POST['sort_by'] ?? 'popular';

// Build the SQL query based on filters
$sql = "SELECT * FROM products p";

// Apply filters
$sql_conditions = [];
$params = [];

if ($category != 'all') {
    $sql_conditions[] = "p.category = :category";
    $params[':category'] = $category;
}
if ($gender != 'all') {
    $sql_conditions[] = "p.gender = :gender";
    $params[':gender'] = $gender;
}
if ($color != 'all') {
    $sql_conditions[] = "p.color = :color";
    $params[':color'] = $color;
}
if ($size != 'all') {
    $sql_conditions[] = "p.size = :size";
    $params[':size'] = $size;
}

if (count($sql_conditions) > 0) {
    $sql .= " WHERE " . implode(" AND ", $sql_conditions);
}

// Apply sorting
switch ($sort_by) {
    case 'price_low':
        $sql .= " ORDER BY p.price ASC";
        break;
    case 'price_high':
        $sql .= " ORDER BY p.price DESC";
        break;
    case 'popular':
    default:
        $sql .= " ORDER BY p.popularity DESC"; // Assuming you have a 'popularity' column
        break;
}

try {
    $stmt = $conn->prepare($sql);
    
    // Bind parameters dynamically
    foreach ($params as $key => $value) {
        $stmt->bindParam($key, $value);
    }

    $stmt->execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Return the products as JSON
    echo json_encode($products);
} catch (PDOException $e) {
    // If there's an error, show the error message and ensure it doesn't break the JSON format
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>
