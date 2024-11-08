<?php

include '../components/connect.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
    header('location:admin_login.php');
}

if (isset($_POST['add_product'])) {
    // Sanitize input fields
    $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
    $price = filter_var($_POST['price'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    $category = filter_var($_POST['category'], FILTER_SANITIZE_STRING);
    $color = filter_var($_POST['color'], FILTER_SANITIZE_STRING);
    $gender = filter_var($_POST['gender'], FILTER_SANITIZE_STRING);
    $tags = filter_var($_POST['tags'], FILTER_SANITIZE_STRING);
    $rating = filter_var($_POST['rating'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    $stock_quantity = filter_var($_POST['stock_quantity'], FILTER_SANITIZE_NUMBER_INT);
    $weight = filter_var($_POST['weight'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    $shipping_cost = filter_var($_POST['shipping_cost'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    $slug = filter_var($_POST['slug'], FILTER_SANITIZE_STRING);
    $meta_description = filter_var($_POST['meta_description'], FILTER_SANITIZE_STRING);
    $seo_keywords = filter_var($_POST['seo_keywords'], FILTER_SANITIZE_STRING);
    
    // Handle image upload
    $image = $_FILES['image']['name'];
    $image_tmp_name = $_FILES['image']['tmp_name'];
    $image_folder = '../uploaded_img/'.$image;
    $image_size = $_FILES['image']['size'];
    
    // Check if product name already exists
    $select_products = $conn->prepare("SELECT * FROM `products` WHERE name = ?");
    $select_products->execute([$name]);
    
    if ($select_products->rowCount() > 0) {
        $message[] = 'Product name already exists!';
    } else {
        // Check image size
        if ($image_size > 2000000) {
            $message[] = 'Image size is too large';
        } else {
            move_uploaded_file($image_tmp_name, $image_folder);

            // Insert product into products table
            $insert_product = $conn->prepare("INSERT INTO `products` (name, category, color, gender, rating, stock_quantity, weight, shipping_cost, slug, meta_description, seo_keywords, tags, price, image) 
            VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
            $insert_product->execute([$name, $category, $color, $gender, $rating, $stock_quantity, $weight, $shipping_cost, $slug, $meta_description, $seo_keywords, $tags, $price, $image]);
            
            // Get the last inserted product ID
            $product_id = $conn->lastInsertId();

            // Insert product variants
            if (isset($_POST['variant_sizes']) && isset($_POST['variant_stocks'])) {
                $variant_sizes = $_POST['variant_sizes'];
                $variant_stocks = $_POST['variant_stocks'];

                foreach ($variant_sizes as $index => $size) {
                    $stock = $variant_stocks[$index];

                    // Insert variant into product_variants table
                    $insert_variant = $conn->prepare("INSERT INTO `product_variants` (product_id, size, stock_quantity) VALUES (?, ?, ?)");
                    $insert_variant->execute([$product_id, $size, $stock]);
                }
            }

            $message[] = 'New product added with variants!';
        }
    }
}

if (isset($_GET['delete'])) {

    $delete_id = $_GET['delete'];
    $delete_product_image = $conn->prepare("SELECT * FROM `products` WHERE id = ?");
    $delete_product_image->execute([$delete_id]);
    $fetch_delete_image = $delete_product_image->fetch(PDO::FETCH_ASSOC);
    unlink('../uploaded_img/'.$fetch_delete_image['image']);
    $delete_product = $conn->prepare("DELETE FROM `products` WHERE id = ?");
    $delete_product->execute([$delete_id]);
    $delete_cart = $conn->prepare("DELETE FROM `cart` WHERE product_id = ?");
    $delete_cart->execute([$delete_id]);
    header('location:products.php');
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Products</title>

   <!-- Font Awesome CDN -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- Tailwind CSS CDN -->
   <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 text-gray-800">

<?php include '../components/admin_header.php'; ?>

<!-- Add Products Section -->
<section class="py-12">
   <div class="container mx-auto px-4">
      <h3 class="text-3xl font-semibold text-center mb-8">Add New Product</h3>
      <form action="" method="POST" enctype="multipart/form-data" class="max-w-lg mx-auto bg-white p-8 rounded-lg shadow-md">
         <input type="text" name="name" class="w-full p-4 mb-4 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Enter product name" maxlength="100" required>
         <input type="number" name="price" class="w-full p-4 mb-4 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Enter product price" min="0" max="9999999999" required onkeypress="if(this.value.length == 10) return false;">
         <select name="category" class="w-full p-4 mb-4 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            <option value="" disabled selected>Select category --</option>
            <option value="t-shirts">T-Shirts</option>
            <option value="dresses">Dresses</option>
            <option value="jeans">Jeans</option>
            <option value="jackets">Jackets</option>
            <option value="accessories">Accessories</option>
         </select>
         <input type="text" name="color" class="w-full p-4 mb-4 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Enter color" maxlength="50">
         <select name="gender" class="w-full p-4 mb-4 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            <option value="male">Male</option>
            <option value="female">Female</option>
            <option value="child">Child</option>
         </select>
         <input type="text" name="tags" class="w-full p-4 mb-4 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Enter product tags (comma separated)" maxlength="255">
         <input type="number" name="rating" class="w-full p-4 mb-4 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Enter product rating (1-5)" min="1" max="5" step="0.1">
         <input type="number" name="stock_quantity" class="w-full p-4 mb-4 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Enter stock quantity" min="0">
         <input type="number" name="weight" class="w-full p-4 mb-4 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Enter product weight" min="0" step="0.1">
         <input type="number" name="shipping_cost" class="w-full p-4 mb-4 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Enter shipping cost" min="0">
         <input type="text" name="slug" class="w-full p-4 mb-4 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Enter SEO slug" maxlength="100">
         <textarea name="meta_description" class="w-full p-4 mb-4 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Enter product meta description" maxlength="255"></textarea>
         <input type="text" name="seo_keywords" class="w-full p-4 mb-4 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Enter SEO keywords (comma separated)" maxlength="255">
         <input type="file" name="image" class="w-full p-4 mb-4 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
         
         <!-- Product Variants Section -->
         <h3 class="text-xl font-semibold mb-4">Product Variants</h3>
         <div class="variant-inputs">
            <div class="variant-group mb-4">
                <input type="text" name="variant_sizes[]" class="w-full p-4 border border-gray-300 rounded-lg mb-2" placeholder="Enter variant size (e.g., S, M, L)" required>
                <input type="number" name="variant_stocks[]" class="w-full p-4 border border-gray-300 rounded-lg mb-2" placeholder="Enter stock for this size" required min="0">
            </div>
         </div>
         
         <button type="button" onclick="addVariant()" class="mb-4 p-2 bg-blue-500 text-white rounded-md">Add Another Variant</button>

         <!-- Submit Button -->
         <button type="submit" name="add_product" class="w-full p-4 bg-blue-500 text-white rounded-lg hover:bg-blue-600 focus:outline-none">Add Product</button>
      </form>
   </div>
</section>
<!-- Show Products -->
<section class="py-12">
   <div class="container mx-auto px-4">
      <h3 class="text-3xl font-semibold text-center mb-8">All Products</h3>
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
         <?php
         $select_products = $conn->prepare("SELECT * FROM `products` LIMIT 10");
         $select_products->execute();
         while ($product = $select_products->fetch(PDO::FETCH_ASSOC)) {
         ?>
            <div class="bg-white p-6 rounded-lg shadow-lg hover:shadow-xl transition-all">
               <img src="../uploaded_img/<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>" class="w-full h-48 object-cover rounded-lg mb-4">
               <h4 class="text-xl font-semibold"><?php echo $product['name']; ?></h4>
               <p class="text-gray-600">$<?php echo $product['price']; ?></p>
               <p class="text-sm text-gray-500">Category: <?php echo ucfirst($product['category']); ?></p>
               <p class="text-sm text-gray-500">Color: <?php echo ucfirst($product['color']); ?></p>
               <p class="text-sm text-gray-500">Gender: <?php echo ucfirst($product['gender']); ?></p>
               <div class="mt-4 flex justify-between">
                  <a href="update_product.php?id=<?php echo $product['id']; ?>" class="text-blue-500 hover:underline">Update</a>
                  <a href="?delete_id=<?php echo $product['id']; ?>" class="text-red-500 hover:underline" onclick="return confirm('Are you sure you want to delete this product?')">Delete</a>
               </div>
            </div>
         <?php } ?>
      </div>
   </div>
</section>

<script>
function addVariant() {
    var variantGroup = document.createElement('div');
    variantGroup.classList.add('variant-group', 'mb-4');
    
    var sizeInput = document.createElement('input');
    sizeInput.type = 'text';
    sizeInput.name = 'variant_sizes[]';
    sizeInput.classList.add('w-full', 'p-4', 'border', 'border-gray-300', 'rounded-lg', 'mb-2');
    sizeInput.placeholder = 'Enter variant size (e.g., S, M, L)';
    variantGroup.appendChild(sizeInput);
    
    var stockInput = document.createElement('input');
    stockInput.type = 'number';
    stockInput.name = 'variant_stocks[]';
    stockInput.classList.add('w-full', 'p-4', 'border', 'border-gray-300', 'rounded-lg', 'mb-2');
    stockInput.placeholder = 'Enter stock for this size';
    stockInput.min = '0';
    variantGroup.appendChild(stockInput);
    
    document.querySelector('.variant-inputs').appendChild(variantGroup);
}
</script>

</body>
</html>

