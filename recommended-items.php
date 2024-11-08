<?php
include 'components/connect.php';

$recommended_items_query = $conn->prepare("SELECT id, name, price, meta_description, image FROM `products` WHERE category = 'recommended' LIMIT 8");
$recommended_items_query->execute();
$recommended_items = $recommended_items_query->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($recommended_items);
?>
