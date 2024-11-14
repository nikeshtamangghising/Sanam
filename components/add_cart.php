<?php
include 'connect.php'; // Include database connection

session_start();
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit();
}

$user_id = $_SESSION['user_id'];
$data = json_decode(file_get_contents('php://input'), true);

// Validate inputs
if (isset($data['product_id'], $data['quantity'], $data['variant_id'])) {
    $product_id = filter_var($data['product_id'], FILTER_SANITIZE_NUMBER_INT);
    $quantity = filter_var($data['quantity'], FILTER_SANITIZE_NUMBER_INT);
    $variant_id = filter_var($data['variant_id'], FILTER_SANITIZE_NUMBER_INT);

    // Validate quantity
    if ($quantity <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid quantity']);
        exit();
    }

    // Check if product exists and fetch the variant details
    $select_product = $conn->prepare("SELECT * FROM `products` WHERE id = ?");
    $select_product->execute([$product_id]);
    $product = $select_product->fetch(PDO::FETCH_ASSOC);

    if ($product) {
        // Check if variant exists
        $select_variant = $conn->prepare("SELECT * FROM `product_variants` WHERE id = ? AND product_id = ?");
        $select_variant->execute([$variant_id, $product_id]);
        $variant = $select_variant->fetch(PDO::FETCH_ASSOC);

        if ($variant) {
            // Check stock availability for the selected variant
            if ($variant['stock_quantity'] < $quantity) {
                echo json_encode(['success' => false, 'message' => 'Insufficient stock']);
                exit();
            }

            // Check if the product with the selected variant already exists in the cart
            $check_cart = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ? AND product_id = ? AND variant_id = ?");
            $check_cart->execute([$user_id, $product_id, $variant_id]);

            if ($check_cart->rowCount() > 0) {
                // Update quantity if the product is already in the cart
                $update_cart = $conn->prepare("UPDATE `cart` SET quantity = quantity + ? WHERE user_id = ? AND product_id = ? AND variant_id = ?");
                $update_cart->execute([$quantity, $user_id, $product_id, $variant_id]);
            } else {
                // Insert new product into the cart if it's not already there
                $insert_cart = $conn->prepare("INSERT INTO `cart` (user_id, product_id, quantity, variant_id) VALUES (?, ?, ?, ?)");
                $insert_cart->execute([$user_id, $product_id, $quantity, $variant_id]);
            }

            // Fetch and return the updated cart count
            $cart_count_query = $conn->prepare("SELECT COUNT(*) AS cart_count FROM `cart` WHERE user_id = ?");
            $cart_count_query->execute([$user_id]);
            $cart_count = $cart_count_query->fetch(PDO::FETCH_ASSOC)['cart_count'];

            echo json_encode(['success' => true, 'message' => 'Product added to cart', 'cart_count' => $cart_count]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Variant not found']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Product not found']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Product ID, quantity, and variant ID are required']);
}
?>
