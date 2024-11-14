<?php

include 'components/connect.php';

session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php'); // Redirect to login if not logged in
    exit();
}

$user_id = $_SESSION['user_id']; // Get the logged-in user's ID

// Handle payment status update
if (isset($_POST['update_payment'])) {
    $order_id = $_POST['order_id'];
    $payment_status = $_POST['payment_status'];

    // Validate the payment status input
    if (empty($payment_status)) {
        echo "<script>alert('Please select a payment status');</script>";
    } else {
        try {
            // Update the payment status for the order
            $update_payment = $conn->prepare("UPDATE `orders` SET payment_status = ? WHERE id = ? AND user_id = ?");
            $update_payment->execute([$payment_status, $order_id, $user_id]);

            echo "<script>alert('Payment status updated successfully'); window.location.href = 'placed_orders.php';</script>";
        } catch (PDOException $e) {
            echo "<script>alert('Error updating payment status: " . $e->getMessage() . "'); window.location.href = 'placed_orders.php';</script>";
        }
    }
}

// Handle order deletion
if (isset($_GET['delete']) && isset($_SESSION['user_id'])) {
    $order_id = $_GET['delete'];

    // Implement CSRF token to prevent CSRF attacks
    if (isset($_SESSION['csrf_token']) && $_SESSION['csrf_token'] === $_GET['csrf_token']) {
        try {
            // Delete the order from the database
            $delete_order = $conn->prepare("DELETE FROM `orders` WHERE id = ? AND user_id = ?");
            $delete_order->execute([$order_id, $user_id]);

            echo "<script>alert('Order deleted successfully'); window.location.href = 'placed_orders.php';</script>";
        } catch (PDOException $e) {
            echo "<script>alert('Error deleting order: " . $e->getMessage() . "'); window.location.href = 'placed_orders.php';</script>";
        }
    } else {
        echo "<script>alert('Invalid request.'); window.location.href = 'placed_orders.php';</script>";
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
            // Fetch only orders placed by the logged-in user
            $select_orders = $conn->prepare("SELECT * FROM `orders` WHERE user_id = ?");
            $select_orders->execute([$user_id]);

            if ($select_orders->rowCount() > 0) {
                while ($fetch_orders = $select_orders->fetch(PDO::FETCH_ASSOC)) {
            ?>
            <div class="bg-white p-6 rounded-lg shadow-md hover:shadow-xl transition duration-300">
                <p><strong>Order ID:</strong> <span class="font-medium"><?= $fetch_orders['id']; ?></span></p>
                <p><strong>Placed On:</strong> <span class="font-medium"><?= $fetch_orders['placed_on']; ?></span></p>
                <p><strong>Name:</strong> <span class="font-medium"><?= $fetch_orders['name']; ?></span></p>
                <p><strong>Email:</strong> <span class="font-medium"><?= $fetch_orders['email']; ?></span></p>
                <p><strong>Phone:</strong> <span class="font-medium"><?= $fetch_orders['number']; ?></span></p>
                <p><strong>Address:</strong> <span class="font-medium"><?= $fetch_orders['address']; ?></span></p>
                <p><strong>Total Products:</strong> <span class="font-medium"><?= $fetch_orders['total_products']; ?></span></p>
                <p><strong>Total Price:</strong> <span class="font-medium">$<?= $fetch_orders['total_price']; ?>/-</span></p>
                <p><strong>Payment Method:</strong> <span class="font-medium"><?= $fetch_orders['method']; ?></span></p>
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

<!-- Custom JS -->
<script src="../js/admin_script.js"></script>

</body>
</html>
