<?php
include 'components/connect.php';

session_start();

// Check if the user is logged in
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

    // Check if cart is not empty
    $check_cart = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ?");
    $check_cart->execute([$user_id]);
    if ($check_cart->rowCount() > 0) {

        // Fetch user's profile details (address, name, email, number)
        $select_profile = $conn->prepare("SELECT * FROM `users` WHERE id = ?");
        $select_profile->execute([$user_id]);
        $fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);

        // Fetch the address, number, and other details
        $user_address = $fetch_profile['address'] ?? '';
        $user_name = $fetch_profile['name'] ?? '';
        $user_email = $fetch_profile['email'] ?? '';
        $user_number = $fetch_profile['number'] ?? '';

        // If the address is empty in the user's profile and not provided in the form
        if (empty($user_address) || empty($address)) {
            $message[] = 'Please add your address!';
        } else {
            // Prepare order details
            $placed_on = date('Y-m-d H:i:s');
            $payment_status = 'pending';
            $created_at = date('Y-m-d H:i:s');
            $updated_at = date('Y-m-d H:i:s');

            try {
                // Fetch cart items with product and variant details
                $select_cart = $conn->prepare("SELECT c.*, p.name AS product_name, pv.price AS variant_price, pv.size, pv.color
                                              FROM `cart` c 
                                              INNER JOIN `products` p ON c.product_id = p.id
                                              LEFT JOIN `product_variants` pv ON c.variant_id = pv.id
                                              WHERE c.user_id = ?");
                $select_cart->execute([$user_id]);

                // Initialize variables to store the total price and products list
                $grand_total = 0;  // Initialize grand total to 0
                $cart_items = '';
                $total_quantity = 0;  // Track total quantity

                while ($fetch_cart = $select_cart->fetch(PDO::FETCH_ASSOC)) {
                    $product_id = $fetch_cart['product_id'];
                    $product_name = $fetch_cart['product_name'];
                    $variant_price = $fetch_cart['variant_price'];
                    $variant_id = $fetch_cart['variant_id'];
                    $product_quantity = $fetch_cart['quantity'];
                    $size = $fetch_cart['size'];
                    $color = $fetch_cart['color'];

                    // Calculate the total price, using variant price if available
                    $price = $variant_price ?? 0;  // Use variant price if available
                    $cart_items .= $product_name . ' (' . $size . ' / ' . $color . ') (Rs. ' . $price . ' x ' . $product_quantity . ') - ';
                    $grand_total += ($price * $product_quantity); // Calculate total price
                    $total_quantity += $product_quantity;  // Track total quantity

                    // Insert into orders table with the quantity
                    $insert_order = $conn->prepare("INSERT INTO `orders`(user_id, product_id, variants_id, number, method, address, total_products, total_price, placed_on, payment_status, created_at, updated_at, name, email) 
                                                    VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
                    $insert_order->execute([$user_id, $product_id, $variant_id, $user_number, $method, $address, $total_quantity, $grand_total, $placed_on, $payment_status, $created_at, $updated_at, $user_name, $user_email]);
                }

                // Delete cart items after placing the order
                $delete_cart = $conn->prepare("DELETE FROM `cart` WHERE user_id = ?");
                $delete_cart->execute([$user_id]);

                // Success message
                $message[] = 'Order placed successfully! Redirecting to home page.';
                header("refresh:2; url=home.php");

            } catch (PDOException $e) {
                // Error handling
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

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">

    <?php include 'components/user_header.php'; ?>

    <div class="bg-blue-600 text-white py-6">
        <h3 class="text-3xl text-center font-semibold">Checkout</h3>
        <p class="text-center text-lg mt-2"><a href="home.php" class="underline hover:text-gray-300">Home</a> <span> / Checkout</span></p>
    </div>

    <section class="max-w-7xl mx-auto p-6">

        <h1 class="text-3xl font-semibold mb-8">Order Summary</h1>

        <?php if (!empty($message)) {
            foreach ($message as $msg) {
                echo "<p class='text-center text-lg font-semibold text-green-500'>$msg</p>";
            }
        } ?>

        <form action="" method="post" class="space-y-8">

            <div class="bg-white p-6 rounded-lg shadow-md">
                <h3 class="text-xl font-semibold mb-4">Cart Items</h3>
                <div class="space-y-4">
                    <?php
                    $select_cart = $conn->prepare("SELECT c.*, p.name AS product_name,p.image AS product_image, pv.price AS variant_price, pv.size, pv.color 
                                                  FROM `cart` c 
                                                  INNER JOIN `products` p ON c.product_id = p.id 
                                                  LEFT JOIN `product_variants` pv ON c.variant_id = pv.id 
                                                  WHERE c.user_id = ?");
                    $select_cart->execute([$user_id]);
                    if ($select_cart->rowCount() > 0) {
                        while ($fetch_cart = $select_cart->fetch(PDO::FETCH_ASSOC)) {
                            $product_name = $fetch_cart['product_name'];
                            $variant_price = $fetch_cart['variant_price'];
                            $product_quantity = $fetch_cart['quantity'];
                            $size = $fetch_cart['size'];
                            $color = $fetch_cart['color'];
                            $price = $variant_price ?? 0;
                    ?>
                            <div class="flex items-center space-x-4">
                                <img src="uploaded_img/<?= htmlspecialchars($fetch_cart['product_image']); ?>" alt="<?= htmlspecialchars($product_name); ?>" class="w-16 h-16 object-cover">
                                <div class="flex-1">
                                    <p class="font-medium"><?= htmlspecialchars($product_name); ?> (<?= htmlspecialchars($size); ?> / <?= htmlspecialchars($color); ?>)</p>
                                    <p class="text-gray-600">Rs. <?= htmlspecialchars($price); ?> x <?= htmlspecialchars($product_quantity); ?></p>
                                </div>
                            </div>
                    <?php
                        }
                    } else {
                        echo '<p class="empty text-red-500">Your cart is empty!</p>';
                    }
                    ?>
                </div>
                <p class="mt-4 text-xl font-semibold text-right">Grand Total: <span class="text-green-600">Rs. <?= isset($grand_total) ? $grand_total : 0; ?></span></p>
            </div>

            <div class="bg-white p-6 rounded-lg shadow-md">
                <h3 class="text-xl font-semibold mb-4">Your Info</h3>
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
                <div class="mt-6">
                    <label for="address" class="block text-lg font-medium">Shipping Address:</label>
                    <input type="text" name="address" id="address" class="w-full px-4 py-2 border border-gray-300 rounded-md" value="<?= htmlspecialchars($fetch_profile['address']); ?>" placeholder="Enter your address">
                </div>

                <!-- Payment Method Field -->
                <div class="mt-6">
                    <label for="method" class="block text-lg font-medium">Payment Method:</label>
                    <select name="method" id="method" class="w-full px-4 py-2 border border-gray-300 rounded-md">
                        <option value="Cash on Delivery">Cash on Delivery</option>
                        <option value="Credit Card">Credit Card</option>
                        <option value="Debit Card">Debit Card</option>
                    </select>
                </div>
            </div>

            <div class="mt-6">
                <button type="submit" name="submit" class="bg-blue-600 text-white py-2 px-6 rounded-md hover:bg-blue-500">Place Order</button>
            </div>
        </form>
    </section>

</body>

</html>
