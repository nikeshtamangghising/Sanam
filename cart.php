<?php
include 'components/connect.php'; // Include the database connection file
session_start();

// Ensure the user is logged in, else redirect
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
} else {
    header('location:home.php');
    exit;
}

// Handle delete action for individual cart items
if (isset($_POST['delete'])) {
    $cart_id = $_POST['cart_id'];
    $delete_cart_item = $conn->prepare("DELETE FROM `cart` WHERE id = ?");
    $delete_cart_item->execute([$cart_id]);
    $_SESSION['message'] = 'Cart item deleted!';
    header('Location: cart.php');
    exit;
}

// Handle delete action for all cart items
if (isset($_POST['delete_all'])) {
    $delete_cart_item = $conn->prepare("DELETE FROM `cart` WHERE user_id = ?");
    $delete_cart_item->execute([$user_id]);
    $_SESSION['message'] = 'All items deleted from cart!';
    header('Location: cart.php');
    exit;
}

// Handle updating cart item quantity
if (isset($_POST['update_qty'])) {
    $cart_id = $_POST['cart_id'];
    $qty = filter_var($_POST['qty'], FILTER_SANITIZE_NUMBER_INT);
    $update_qty = $conn->prepare("UPDATE `cart` SET quantity = ? WHERE id = ?");
    $update_qty->execute([$qty, $cart_id]);
    $_SESSION['message'] = 'Cart quantity updated';
    header('Location: cart.php');
    exit;
}

// Fetch cart items for the logged-in user
$select_cart = $conn->prepare("SELECT c.*, p.name, p.price, p.image FROM `cart` c JOIN `products` p ON c.product_id = p.id WHERE c.user_id = ?");
$select_cart->execute([$user_id]);
$cart_items = $select_cart->fetchAll(PDO::FETCH_ASSOC);

// Initialize grand total
$grand_total = 0;

// Calculate grand total
foreach ($cart_items as $item) {
    $grand_total += $item['price'] * $item['quantity'];
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Cart</title>

   <!-- Font Awesome CDN link -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- TailwindCSS CDN -->
   <script src="https://cdn.tailwindcss.com"></script>

   <!-- Custom CSS file link -->
   <link rel="stylesheet" href="css/style.css">
</head>
<body class="flex flex-col min-h-screen">

<!-- Header Section -->
<?php include 'components/user_header.php'; ?>

<!-- Main Content Section -->
<section class="py-10 px-4 flex-grow bg-background">
    <h2 class="text-4xl font-bold mb-12 text-center">Your Shopping Cart</h2>

    <?php if (!empty($_SESSION['message'])): ?>
        <div class="text-center mb-4 text-green-600"><?= $_SESSION['message']; unset($_SESSION['message']); ?></div>
    <?php endif; ?>

    <?php if (!empty($cart_items)): ?>
        <div class="space-y-8">
            <?php foreach ($cart_items as $item): ?>
                <div class="bg-white p-10 rounded-xl shadow-2xl flex items-center justify-between space-x-8 hover:shadow-3xl transition-shadow duration-300 ease-in-out transform hover:scale-105">
                    <div class="flex items-center space-x-8">
                        <img src="uploaded_img/<?php echo $item['image']; ?>" alt="Product Image" class="w-40 h-40 object-cover rounded-xl shadow-lg">
                        <div class="flex flex-col space-y-4">
                            <h3 class="font-semibold text-2xl text-gray-800"><?php echo $item['name']; ?></h3>
                            <p class="text-lg text-gray-600">Rs. <?php echo $item['price']; ?></p>
                            <form method="POST" action="cart.php" class="mt-4">
                                <input type="hidden" name="cart_id" value="<?php echo $item['id']; ?>">
                                <input type="number" name="qty" value="<?php echo $item['quantity']; ?>" min="1" class="w-16 p-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" required>
                                <button type="submit" name="update_qty" class="bg-blue-600 text-white py-3 px-6 rounded-lg mt-4 hover:bg-blue-700 transition duration-300 ease-in-out">Update</button>
                            </form>
                        </div>
                    </div>
                    <form method="POST" action="cart.php" class="ml-4">
                        <input type="hidden" name="cart_id" value="<?php echo $item['id']; ?>">
                        <button type="submit" name="delete" class="text-red-600 text-xl hover:text-red-800 font-semibold">Delete</button>
                    </form>
                </div>
            <?php endforeach; ?>

            <div class="flex justify-between items-center mt-8">
                <span class="font-semibold text-2xl">Total:</span>
                <span class="font-bold text-3xl text-green-600">Rs. <?php echo $grand_total; ?></span>
            </div>

            <div class="flex justify-between mt-12 space-x-4">
                <form method="POST" action="cart.php" class="w-full">
                    <button type="submit" name="delete_all" class="bg-red-600 text-white py-3 px-6 rounded-lg w-full hover:bg-red-700 transition duration-300 ease-in-out">Clear All Cart Items</button>
                </form>
                <a href="checkout.php" class="bg-green-600 text-white py-3 px-6 rounded-lg w-full text-center hover:bg-green-700 transition duration-300 ease-in-out">Proceed to Checkout</a>
            </div>
        </div>
    <?php else: ?>
        <div class="text-center">
            <p class="text-2xl text-gray-600">Your cart is empty!</p>
            <a href="home.php" class="mt-4 text-blue-600 text-xl">Continue Shopping</a>
        </div>
    <?php endif; ?>
</section>

<?php include 'components/footer.php'; ?>

</body>
</html>
