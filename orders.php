<?php

include 'components/connect.php';

session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php'); // Redirect to login if not logged in
    exit();
}

$user_id = $_SESSION['user_id']; // Get the logged-in user's ID

// Handle order deletion (canceling order)
if (isset($_GET['delete']) && isset($_SESSION['user_id'])) {
    $order_id = $_GET['delete'];

    // Implement CSRF token to prevent CSRF attacks
    if (isset($_SESSION['csrf_token']) && $_SESSION['csrf_token'] === $_GET['csrf_token']) {
        try {
            // Check if the order's payment status is 'pending'
            $check_status = $conn->prepare("SELECT payment_status FROM `orders` WHERE id = ? AND user_id = ?");
            $check_status->execute([$order_id, $user_id]);
            $order = $check_status->fetch(PDO::FETCH_ASSOC);

            if ($order && $order['payment_status'] === 'pending') {
                // Delete the order from the database if payment status is 'pending'
                $delete_order = $conn->prepare("DELETE FROM `orders` WHERE id = ? AND user_id = ?");
                $delete_order->execute([$order_id, $user_id]);

                echo "<script>alert('Order canceled successfully'); window.location.href = 'orders.php';</script>";
            } else {
                // If the order is not pending, show a message that it cannot be canceled
                echo "<script>alert('This order cannot be canceled as it is not in pending status.'); window.location.href = 'orders.php';</script>";
            }
        } catch (PDOException $e) {
            echo "<script>alert('Error canceling order: " . $e->getMessage() . "'); window.location.href = 'orders.php';</script>";
        }
    } else {
        echo "<script>alert('Invalid request.'); window.location.href = 'orders.php';</script>";
    }
}


// Generate CSRF Token for secure requests
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32)); // Generate a new CSRF token
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Placed Orders</title>

    <!-- Font Awesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-800">

<?php include 'components/user_header.php'; ?>

<!-- Placed Orders Section -->
<section class="py-12">

    <div class="container mx-auto px-4">
        <h1 class="text-4xl font-semibold text-center mb-8">Your Placed Orders</h1>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8">
            <?php
            // Fetch only orders placed by the logged-in user with product and variant details
            $select_orders = $conn->prepare("SELECT o.*, p.name AS product_name, pv.size, pv.color, pv.price, 
                                                u.name AS user_name, u.email AS user_email, u.number AS user_number 
                                                FROM `orders` o
                                                LEFT JOIN `products` p ON o.product_id = p.id
                                                LEFT JOIN `product_variants` pv ON o.variants_id = pv.id
                                                LEFT JOIN `users` u ON o.user_id = u.id
                                                WHERE o.user_id = ?");
            $select_orders->execute([$user_id]);

            if ($select_orders->rowCount() > 0) {
                while ($fetch_orders = $select_orders->fetch(PDO::FETCH_ASSOC)) {
            ?>
            <div class="bg-white p-6 rounded-lg shadow-md hover:shadow-xl transition duration-300">
                <p><strong>Order ID:</strong> <span class="font-medium"><?= $fetch_orders['id']; ?></span></p>
                <p><strong>Placed On:</strong> <span class="font-medium"><?= $fetch_orders['placed_on']; ?></span></p>
                <p><strong>Product Name:</strong> <span class="font-medium"><?= $fetch_orders['product_name']; ?></span></p>
                <p><strong>Variant:</strong> <span class="font-medium"><?= $fetch_orders['size']; ?>, <?= $fetch_orders['color']; ?></span></p>
                <p><strong>Price:</strong> <span class="font-medium">$<?= $fetch_orders['price']; ?>/-</span></p>
                <p><strong>Total Products:</strong> <span class="font-medium"><?= $fetch_orders['total_products']; ?></span></p>
                <p><strong>Total Price:</strong> <span class="font-medium">$<?= $fetch_orders['total_price']; ?>/-</span></p>
                <p><strong>Payment Method:</strong> <span class="font-medium"><?= $fetch_orders['method']; ?></span></p>
                <p><strong>Payment Status:</strong> <span class="font-medium"><?= ucfirst($fetch_orders['payment_status']); ?></span></p>

                <!-- Update Payment Status -->
                <form action="" method="POST" class="mt-4">
                    <input type="hidden" name="order_id" value="<?= $fetch_orders['id']; ?>">
                    <div class="flex justify-between items-center">
                        <a href="orders.php?delete=<?= $fetch_orders['id']; ?>&csrf_token=<?= $_SESSION['csrf_token']; ?>" class="text-red-600 hover:text-red-500" onclick="return confirm('Are you sure you want to Canceled this order?');">Canceled Order </a>
                    </div>
                </form>
            </div>
            <?php
                }
            } else {
                echo '<p class="col-span-full text-center text-gray-500">You have not placed any orders yet!</p>';
            }
            ?>
        </div>
    </div>

</section>


</body>
</html>
