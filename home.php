<?php
include 'components/connect.php';

session_start();
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : '';

// Process add to cart
if (isset($_POST['add_to_cart'])) {
    $product_id = $_POST['product_id'];
    $quantity = $_POST['quantity'];

    if ($product_id && $quantity > 0) {
        // Fetch product details
        $select_product = $conn->prepare("SELECT * FROM `products` WHERE id = ?");
        $select_product->execute([$product_id]);
        $product = $select_product->fetch(PDO::FETCH_ASSOC);

        // Check if the product is already in the cart
        $check_cart = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ? AND product_id = ?");
        $check_cart->execute([$user_id, $product_id]);

        if ($check_cart->rowCount() > 0) {
            // Update the quantity if the product is already in the cart
            $update_cart = $conn->prepare("UPDATE `cart` SET quantity = quantity + ? WHERE user_id = ? AND product_id = ?");
            $update_cart->execute([$quantity, $user_id, $product_id]);
            $message[] = 'Product quantity updated in cart!';
        } else {
            // Insert new product into the cart
            $insert_cart = $conn->prepare("INSERT INTO `cart` (user_id, product_id, quantity) VALUES (?, ?, ?)");
            $insert_cart->execute([$user_id, $product_id, $quantity]);
            $message[] = 'Product added to cart!';
        }
    } else {
        $message[] = 'Invalid product or quantity!';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<?php include 'components/user_header.php'; ?>

<div class="p-6 bg-background">
    <!-- Display Top Picks Section -->
    <h2 class="text-2xl font-bold mb-4">Top Picks</h2>
    <div id="top-items" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6 mb-6"></div>
    <button onclick="loadItems('top-items.php', 'top-items')" class="text-primary underline">View More</button>

    <!-- Display Recommended Items Section -->
    <h2 class="text-2xl font-bold mb-4 mt-6">Recommended for You</h2>
    <div id="recommended-items" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6 mb-6"></div>
    <button onclick="loadItems('recommended-items.php', 'recommended-items')" class="text-primary underline">View More</button>

    <!-- Display Other Sections (e.g., Sale Items) -->
    <h2 class="text-2xl font-bold mb-4 mt-6">Sale Items</h2>
    <div id="sale-items" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6 mb-6"></div>
    <button onclick="loadItems('sale-items.php', 'sale-items')" class="text-primary underline">View More</button>
</div>

<script>
    // Function to load items dynamically from the API and display them
    function loadItems(apiUrl, containerId) {
        fetch(apiUrl)
            .then(response => response.json())
            .then(data => renderItems(data, containerId))
            .catch(error => console.error('Error fetching items:', error));
    }

    // Function to render the fetched items inside the specified container
    function renderItems(items, containerId) {
        const container = document.getElementById(containerId);
        container.innerHTML = items.map(item => `
            <div class="bg-card p-4 rounded-lg shadow-lg">
                <img src="uploaded_img/${item.image}" alt="${item.name}" class="w-full h-48 object-cover rounded-lg mb-2" />
                <h2 class="font-semibold">${item.name}</h2>
                <p class="text-muted-foreground">${item.meta_description}</p>
                <p class="font-bold">Rs.${item.price}</p>
                <form action="home.php" method="POST" class="add-to-cart-form">
                    <input type="hidden" name="product_id" value="${item.id}" />
                    <input type="number" name="quantity" value="1" min="1" class="w-12 p-2 border border-border rounded" />
                    <button type="submit" name="add_to_cart" class="bg-blue-600 text-white py-2 px-4 rounded mt-2">Add to Cart</button>
                </form>
            </div>
        `).join('');
    }

    // Load items for the default sections
    loadItems('top-items.php', 'top-items');
    loadItems('recommended-items.php', 'recommended-items');
    loadItems('sale-items.php', 'sale-items');
</script>

<?php include 'components/footer.php'; ?>

</body>
</html>
