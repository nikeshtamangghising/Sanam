<?php
include 'components/connect.php';

session_start();

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
} else {
    $user_id = '';
    header('location:home.php');
    exit;
}

if (isset($_POST['submit'])) {
    // Sanitize POST inputs to prevent undefined array key errors and possible XSS attacks
    $name = isset($_POST['name']) ? filter_var($_POST['name'], FILTER_SANITIZE_STRING) : '';
    $number = isset($_POST['number']) ? filter_var($_POST['number'], FILTER_SANITIZE_STRING) : '';
    $email = isset($_POST['email']) ? filter_var($_POST['email'], FILTER_SANITIZE_EMAIL) : '';
    $method = isset($_POST['method']) ? filter_var($_POST['method'], FILTER_SANITIZE_STRING) : '';
    $address = isset($_POST['address']) ? filter_var($_POST['address'], FILTER_SANITIZE_STRING) : '';
    $total_products = isset($_POST['total_products']) ? $_POST['total_products'] : '';
    $total_price = isset($_POST['total_price']) ? $_POST['total_price'] : '';

    $check_cart = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ?");
    $check_cart->execute([$user_id]);

    if ($check_cart->rowCount() > 0) {
        if ($address == '') {
            $message[] = 'Please add your address!';
        } else {
            $placed_on = date('Y-m-d H:i:s');
            $payment_status = 'pending';
            $created_at = date('Y-m-d H:i:s');
            $updated_at = date('Y-m-d H:i:s');

            // Insert order into the database
            try {
                $insert_order = $conn->prepare("INSERT INTO `orders`(user_id, name, number, email, method, address, total_products, total_price, placed_on, payment_status, created_at, updated_at) VALUES(?,?,?,?,?,?,?,?,?,?,?,?)");
                $insert_order->execute([$user_id, $name, $number, $email, $method, $address, $total_products, $total_price, $placed_on, $payment_status, $created_at, $updated_at]);

                // Delete cart items after placing the order
                $delete_cart = $conn->prepare("DELETE FROM `cart` WHERE user_id = ?");
                $delete_cart->execute([$user_id]);

                // Success message and redirect after 2 seconds
                $message[] = 'Order placed successfully! Redirecting to home page.';
                header("refresh:2; url=home.php");
            } catch (PDOException $e) {
                // Error handling if there is any issue with the database query
                $message[] = 'Error occurred while placing your order. Please try again later.';
            }
        }
    } else {
        $message[] = 'Your cart is empty';
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>

    <!-- Font Awesome CDN link -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

    <!-- TailwindCSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Custom CSS file link -->
    <link rel="stylesheet" href="css/style.css">

</head>

<body class="bg-gray-100">

    <!-- Header Section -->
    <?php include 'components/user_header.php'; ?>

    <!-- Heading Section -->
    <div class="bg-blue-600 text-white py-6">
        <h3 class="text-3xl text-center font-semibold">Checkout</h3>
        <p class="text-center text-lg mt-2"><a href="home.php" class="underline hover:text-gray-300">Home</a> <span> / Checkout</span></p>
    </div>

    <section class="max-w-7xl mx-auto p-6">

        <h1 class="text-3xl font-semibold mb-8">Order Summary</h1>

        <!-- Display Messages -->
        <?php if (!empty($message)) {
            foreach ($message as $msg) {
                echo "<p class='text-center text-lg font-semibold text-green-500'>$msg</p>";
            }
        } ?>

        <form action="" method="post" class="space-y-8">

            <!-- Cart Items Section -->
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h3 class="text-xl font-semibold mb-4">Cart Items</h3>
                <div class="space-y-4">
                    <?php
                    $grand_total = 0;
                    $cart_items[] = '';
                    $select_cart = $conn->prepare("SELECT c.*, p.name AS product_name, p.price AS product_price, p.image AS product_image FROM `cart` c INNER JOIN `products` p ON c.product_id = p.id WHERE c.user_id = ?");
                    $select_cart->execute([$user_id]);
                    if ($select_cart->rowCount() > 0) {
                        while ($fetch_cart = $select_cart->fetch(PDO::FETCH_ASSOC)) {
                            $product_name = $fetch_cart['product_name'] ?? 'Unknown Product';
                            $product_price = $fetch_cart['product_price'] ?? 0;
                            $product_quantity = $fetch_cart['quantity'] ?? 1;
                            $product_image = $fetch_cart['product_image'] ?? 'default_image.jpg';

                            $cart_items[] = $product_name . ' (Rs. ' . $product_price . ' x ' . $product_quantity . ') - ';
                            $total_products = implode($cart_items);
                            $grand_total += ($product_price * $product_quantity);
                    ?>
                            <div class="flex items-center space-x-4">
                                <img src="images/<?= htmlspecialchars($product_image); ?>" alt="<?= htmlspecialchars($product_name); ?>" class="w-16 h-16 object-cover">
                                <div class="flex-1">
                                    <p class="font-medium"><?= htmlspecialchars($product_name); ?></p>
                                    <p class="text-gray-600">Rs. <?= htmlspecialchars($product_price); ?> x <?= htmlspecialchars($product_quantity); ?></p>
                                </div>
                                <div class="space-x-2">
                                    <a href="update_cart.php?product_id=<?= $fetch_cart['product_id']; ?>" class="text-blue-600 hover:text-blue-400">Update</a>
                                    <a href="delete_cart.php?product_id=<?= $fetch_cart['product_id']; ?>" class="text-red-600 hover:text-red-400">Delete</a>
                                </div>
                            </div>
                    <?php
                        }
                    } else {
                        echo '<p class="empty text-red-500">Your cart is empty!</p>';
                    }
                    ?>
                </div>
                <p class="mt-4 text-xl font-semibold text-right">Grand Total: <span class="text-green-600">Rs. <?= $grand_total; ?></span></p>
                <a href="cart.php" class="block text-center text-blue-600 mt-4 hover:text-blue-400">View Cart</a>
            </div>

            <input type="hidden" name="total_products" value="<?= $total_products; ?>">
            <input type="hidden" name="total_price" value="<?= $grand_total; ?>">

            <!-- User Info Section -->
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h3 class="text-xl font-semibold mb-4">Your Info</h3>
                <?php
                $select_profile = $conn->prepare("SELECT * FROM `users` WHERE id = ?");
                $select_profile->execute([$user_id]);
                $fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);
                ?>
                <p class="flex items-center space-x-2 text-lg">
                    <i class="fas fa-user text-gray-600"></i>
                    <span><?= htmlspecialchars($fetch_profile['name']); ?></span>
                </p>
                <p class="flex items-center space-x-2 text-lg">
                    <i class="fas fa-phone text-gray-600"></i>
                    <span><?= htmlspecialchars($fetch_profile['number']); ?></span>
                </p>
                <p class="flex items-center space-x-2 text-lg">
                    <i class="fas fa-envelope text-gray-600"></i>
                    <span><?= htmlspecialchars($fetch_profile['email']); ?></span>
                </p>

                <h3 class="text-xl font-semibold mt-8 mb-4">Delivery Address</h3>
                <p class="flex items-center space-x-2 text-lg">
                    <i class="fas fa-map-marker-alt text-gray-600"></i>
                    <span><?php if ($fetch_profile['address'] == '') {
                                echo 'Please enter your address';
                            } else {
                                echo htmlspecialchars($fetch_profile['address']);
                            } ?></span>
                </p>

                <!-- Payment Method -->
                <div class="mt-6">
                    <label for="method" class="block text-lg font-medium">Payment Method:</label>
                    <select name="method" id="method" class="w-full px-4 py-2 border border-gray-300 rounded-md">
                        <option value="Cash On Delivery">Cash On Delivery</option>
                        <option value="Credit Card">Credit Card</option>
                        <option value="PayPal">PayPal</option>
                    </select>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-center">
                <button type="submit" name="submit" class="bg-blue-600 text-white py-2 px-6 rounded-md hover:bg-blue-500 mt-6">Place Order</button>
            </div>

        </form>
    </section>

</body>

</html>
