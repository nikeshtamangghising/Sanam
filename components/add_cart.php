<?php
include 'connect.php'; // Include database connection

session_start();
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit();
}

$user_id = $_SESSION['user_id'];
$data = json_decode(file_get_contents('php://input'), true);

if (isset($data['product_id']) && isset($data['quantity'])) {
    $product_id = filter_var($data['product_id'], FILTER_SANITIZE_NUMBER_INT);
    $quantity = filter_var($data['quantity'], FILTER_SANITIZE_NUMBER_INT);

    if ($quantity <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid quantity']);
        exit();
    }

    // Check if product exists
    $select_product = $conn->prepare("SELECT * FROM `products` WHERE id = ?");
    $select_product->execute([$product_id]);
    $product = $select_product->fetch(PDO::FETCH_ASSOC);

    if ($product) {
        // Check if product is already in cart
        $check_cart = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ? AND product_id = ?");
        $check_cart->execute([$user_id, $product_id]);

        if ($check_cart->rowCount() > 0) {
            // Update quantity if product is already in the cart
            $update_cart = $conn->prepare("UPDATE `cart` SET quantity = quantity + ? WHERE user_id = ? AND product_id = ?");
            $update_cart->execute([$quantity, $user_id, $product_id]);
        } else {
            // Insert product into cart if it's not already there
            $insert_cart = $conn->prepare("INSERT INTO `cart` (user_id, product_id, quantity) VALUES (?, ?, ?)");
            $insert_cart->execute([$user_id, $product_id, $quantity]);
        }

        // Fetch and return the updated cart count
        $cart_count_query = $conn->prepare("SELECT COUNT(*) AS cart_count FROM `cart` WHERE user_id = ?");
        $cart_count_query->execute([$user_id]);
        $cart_count = $cart_count_query->fetch(PDO::FETCH_ASSOC)['cart_count'];

        echo json_encode(['success' => true, 'message' => 'Product added to cart', 'cart_count' => $cart_count]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Product not found']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Product ID and quantity are required']);
}
?>
