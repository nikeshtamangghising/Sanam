<?php
include 'components/connect.php';

$category = isset($_GET['category']) ? $_GET['category'] : '';
$category_query = $conn->prepare("SELECT id, name, price, meta_description, image FROM `products` WHERE category = ? LIMIT 8");
$category_query->execute([$category]);
$category_items = $category_query->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($category_items);
?>
