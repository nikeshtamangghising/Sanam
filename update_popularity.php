<?php
// Include the external database connection file
include 'components/connect.php'; // Assuming this file contains the connection to the database

// Function to update popularity in the database
function updatePopularity($productId, $popularity) {
    global $conn;  // Use the global $conn variable for database connection

    $sql = "UPDATE products SET popularity = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(1, $popularity, PDO::PARAM_INT);  // Bind the popularity value
    $stmt->bindParam(2, $productId, PDO::PARAM_INT);   // Bind the productId value
    $stmt->execute();
}

// Function to calculate popularity based on order counts
function calculatePopularity($orderCount) {
    return $orderCount;  // You can modify this if you need a more complex formula
}

// Fetch product ids and their order counts, then update popularity
function updateProductsPopularity() {
    global $conn;  // Use the global $conn variable for database connection

    // Query to get product ids and their order counts
    $sql = "
        SELECT p.id, COUNT(o.product_id) AS order_count
        FROM products p
        LEFT JOIN orders o ON p.id = o.product_id
        GROUP BY p.id";

    $stmt = $conn->query($sql); // PDO::query method returns a PDOStatement object

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $popularity = calculatePopularity($row['order_count']);
        updatePopularity($row['id'], $popularity);  // Update the popularity in the database
    }
}

// Call the function to update popularity
updateProductsPopularity();
echo "Popularity update completed.";
?>
