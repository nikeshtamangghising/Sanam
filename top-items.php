<?php
include 'components/connect.php';

$top_items_query = $conn->prepare("SELECT id, name, price, meta_description, image FROM `products` ORDER BY rating DESC LIMIT 8");
$top_items_query->execute();
$top_items = $top_items_query->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($top_items);
?>
