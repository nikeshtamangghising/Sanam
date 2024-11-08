<?php
include 'connect.php';  // Include the database connection file

// Check if the user is logged in
session_start();
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
} else {
    // Redirect to home page if the user is not logged in
    header('location:home.php');
    exit();
}

// Check if product_id is received from the form
if (isset($_POST['product_id'])) {
    // Sanitize and assign the values from the form
    $product_id = filter_var($_POST['product_id'], FILTER_SANITIZE_NUMBER_INT);
    $variant_id = isset($_POST['variant_id']) ? filter_var($_POST['variant_id'], FILTER_SANITIZE_NUMBER_INT) : null;

    // Fetch product details
    $select_product = $conn->prepare("SELECT * FROM `products` WHERE id = ?");
    $select_product->execute([$product_id]);
    $product = $select_product->fetch(PDO::FETCH_ASSOC);

    if ($product) {
        // If a variant is selected, fetch the variant details
        if ($variant_id) {
            $select_variant = $conn->prepare("SELECT * FROM `product_variants` WHERE id = ? AND product_id = ?");
            $select_variant->execute([$variant_id, $product_id]);
            $variant = $select_variant->fetch(PDO::FETCH_ASSOC);

            // If the variant exists, use its price and stock quantity
            if ($variant) {
                $price = $variant['price'];
                $stock_quantity = $variant['stock_quantity'];
            } else {
                // If variant does not exist, return an error
                $message[] = 'Variant not found!';
                header('location:product_page.php?id=' . $product_id);  // Redirect back to the product page
                exit();
            }
        } else {
            // If no variant selected, use the base product details
            $price = $product['price'];
            $stock_quantity = $product['stock_quantity'];
        }

        // Check if the product (with variant if applicable) already exists in the cart
        $check_cart = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ? AND product_id = ? AND variant_id = ?");
        $check_cart->execute([$user_id, $product_id, $variant_id]);

        if ($check_cart->rowCount() > 0) {
            // If the item is already in the cart, update the quantity
            $update_cart = $conn->prepare("UPDATE `cart` SET quantity = quantity + 1 WHERE user_id = ? AND product_id = ? AND variant_id = ?");
            $update_cart->execute([$user_id, $product_id, $variant_id]);
            $message[] = 'Quantity updated in cart!';
        } else {
            // If the item is not in the cart, insert it into the cart
            $insert_cart = $conn->prepare("INSERT INTO `cart` (user_id, product_id, quantity, image, variant_id) VALUES (?, ?, ?, ?, ?)");
            $insert_cart->execute([$user_id, $product['id'], 1, $product['image'], $variant_id]);
            $message[] = 'Product added to cart!';
        }

        // Redirect to the cart page after adding the item
        header('location:cart.php');
        exit();
    } else {
        // If the product doesn't exist
        $message[] = 'Product not found!';
        header('location:home.php');  // Redirect back to the home page
        exit();
    }
}
?>
