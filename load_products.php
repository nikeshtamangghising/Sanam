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
$sql = "
    SELECT 
        p.id AS product_id,
        p.name AS product_name,
        p.category,
        p.gender,
        p.stock_quantity AS product_stock,
        p.weight,
        p.slug,
        p.meta_description,
        p.seo_keywords,
        p.tags,
        p.image AS product_image,
        pv.id AS variant_id,
        pv.size,
        pv.color,
        pv.price,
        pv.stock_quantity AS variant_stock
    FROM products p
    LEFT JOIN product_variants pv ON p.id = pv.product_id
";


// Initialize filter conditions and parameters
$sql_conditions = [];
$params = [];

// Apply filters only if any filter is not 'all'
if ($category != 'all') {
    $sql_conditions[] = "p.category = :category";
    $params[':category'] = $category;
}
if ($gender != 'all') {
    $sql_conditions[] = "p.gender = :gender";
    $params[':gender'] = $gender;
}
if ($color != 'all') {
    $sql_conditions[] = "pv.color = :color";  // Assuming 'pv' is a joined table or alias
    $params[':color'] = $color;
}
if ($size != 'all') {
    $sql_conditions[] = "pv.size = :size";  // Assuming 'pv' is a joined table or alias
    $params[':size'] = $size;
}

// If any filters are applied, add the WHERE clause
if (count($sql_conditions) > 0) {
    $sql .= " WHERE ";  // Start the WHERE clause
    $sql .= implode(" OR ", $sql_conditions);  // Join all conditions with AND
}

// Apply sorting
switch ($sort_by) {
    case 'price_low':
        $sql .= " ORDER BY pv.price ASC";
        break;
    case 'price_high':
        $sql .= " ORDER BY pv.price DESC";
        break;
    case 'popular':
    default:
        $sql .= " ORDER BY p.popularity DESC"; // Default sort by popularity
        break;
}

try {
    // Print the final query to check it (for debugging purposes)
    // echo $sql;
    
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
