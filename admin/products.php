<?php
include '../components/connect.php';
session_start();

$admin_id = $_SESSION['admin_id'];
if (!isset($admin_id)) {
    header('location:admin_login.php');
    exit();
}

// Function to handle image upload
function uploadImage($image_name, $image_tmp_name, $upload_dir = '../uploaded_img/') {
    $target_file = $upload_dir . basename($image_name);
    $image_size = $_FILES['image']['size'];

    if ($image_size > 2000000) {
        return ['error' => 'Image size is too large'];
    } elseif (move_uploaded_file($image_tmp_name, $target_file)) {
        return ['path' => basename($image_name)];
    } else {
        return ['error' => 'Failed to upload image'];
    }
}

// Handle product variant deletion
if (isset($_GET['delete_variant']) && !empty($_GET['delete_variant'])) {
    $delete_variant_id = $_GET['delete_variant'];
    $delete_variant = $conn->prepare("DELETE FROM product_variants WHERE id = ?");
    $delete_variant->execute([$delete_variant_id]);
    header('location:products.php');
    exit();
}

// Add new product
if (isset($_POST['add_product'])) {
    $name = htmlspecialchars($_POST['name']);
    $category = htmlspecialchars($_POST['category']);
    $gender = htmlspecialchars($_POST['gender']);
    $tags = htmlspecialchars($_POST['tags']);
    $stock_quantity = filter_var($_POST['stock_quantity'], FILTER_SANITIZE_NUMBER_INT);
    $weight = filter_var($_POST['weight'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    $slug = htmlspecialchars($_POST['slug']);
    $meta_description = htmlspecialchars($_POST['meta_description']);
    $seo_keywords = htmlspecialchars($_POST['seo_keywords']);
    $image = $_FILES['image']['name'];
    $image_tmp_name = $_FILES['image']['tmp_name'];

    $select_products = $conn->prepare("SELECT * FROM `products` WHERE name = ?");
    $select_products->execute([$name]);

    if ($select_products->rowCount() > 0) {
        $message[] = 'Product name already exists!';
    } else {
        $image_upload = uploadImage($image, $image_tmp_name);
        if (isset($image_upload['error'])) {
            $message[] = $image_upload['error'];
        } else {
            $insert_product = $conn->prepare("INSERT INTO `products` 
                (name, category, gender, stock_quantity, weight, slug, meta_description, seo_keywords, tags, image, popularity) 
                VALUES (?,?,?,?,?,?,?,?,?,?,0)");
            $insert_product->execute([
                $name, $category, $gender, $stock_quantity, $weight, $slug, 
                $meta_description, $seo_keywords, $tags, $image_upload['path']
            ]);

            $product_id = $conn->lastInsertId();

            if (!empty($_POST['variant_sizes']) && !empty($_POST['variant_colors']) && !empty($_POST['variant_prices']) && !empty($_POST['variant_stocks'])) {
                $variant_sizes = $_POST['variant_sizes'];
                $variant_colors = $_POST['variant_colors'];
                $variant_prices = $_POST['variant_prices'];
                $variant_stocks = $_POST['variant_stocks'];

                foreach ($variant_sizes as $index => $size) {
                    $variant_size = htmlspecialchars($size);
                    $variant_color = htmlspecialchars($variant_colors[$index]);
                    $variant_price = filter_var($variant_prices[$index], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
                    $variant_stock = filter_var($variant_stocks[$index], FILTER_SANITIZE_NUMBER_INT);

                    $insert_variant = $conn->prepare("INSERT INTO `product_variants` 
                        (product_id, size, color, price, stock_quantity) 
                        VALUES (?,?,?,?,?)");
                    $insert_variant->execute([$product_id, $variant_size, $variant_color, $variant_price, $variant_stock]);
                }
            }
            $message[] = 'Product added successfully!';
        }
    }
}

// Delete product and its associated variants and cart entries
if (isset($_GET['delete']) && !empty($_GET['delete'])) {
    $delete_id = $_GET['delete'];
    $delete_product = $conn->prepare("SELECT * FROM `products` WHERE id = ?");
    $delete_product->execute([$delete_id]);

    if ($delete_product->rowCount() > 0) {
        $product = $delete_product->fetch(PDO::FETCH_ASSOC);
        if (file_exists('../uploaded_img/' . $product['image'])) {
            unlink('../uploaded_img/' . $product['image']);
        }

        $delete_variants = $conn->prepare("DELETE FROM `product_variants` WHERE product_id = ?");
        $delete_variants->execute([$delete_id]);

        $delete_product = $conn->prepare("DELETE FROM `products` WHERE id = ?");
        $delete_product->execute([$delete_id]);

        $delete_cart = $conn->prepare("DELETE FROM `cart` WHERE product_id = ?");
        $delete_cart->execute([$delete_id]);

        header('location:products.php');
        exit();
    } else {
        $message[] = "Product not found.";
    }
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
         <!-- Product Basic Details -->
         <input type="text" name="name" class="w-full p-4 mb-4 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Enter product name" maxlength="100" required>
         
         <select name="category" class="w-full p-4 mb-4 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            <option value="" disabled selected>Select Category --</option>
            <option value="hat">Hat</option>
            <option value="one-piece">One Piece</option>
            <option value="saree">Saree</option>
            <option value="kurta">Kurta</option>
            <option value="shawl">Shawl</option>
            <option value="top">Top</option>
            <option value="t-shirt">T-Shirt</option>
            <option value="pant">Pant</option>
            <option value="socks">Socks</option>
            <option value="heels">Heels</option>
            <option value="boots">Boots</option>
            <option value="bra-panty">Bra & Panty</option>
            <option value="handkerchief">Handkerchief</option>
            <option value="muffler">Muffler</option>
         </select>

         <select name="gender" class="w-full p-4 mb-4 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            <option value="male">Male</option>
            <option value="female">Female</option>
            <option value="child">Child</option>
         </select>

         <input type="number" name="stock_quantity" class="w-full p-4 mb-4 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Enter stock quantity" min="0">
         <input type="number" name="weight" class="w-full p-4 mb-4 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Enter product weight" min="0" step="0.1">
         <input type="text" name="slug" class="w-full p-4 mb-4 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Enter product slug" maxlength="255">
         <textarea name="meta_description" class="w-full p-4 mb-4 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" rows="4" placeholder="Enter meta description"></textarea>
         <input type="text" name="seo_keywords" class="w-full p-4 mb-4 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Enter SEO keywords" maxlength="255">
         <input type="text" name="tags" class="w-full p-4 mb-4 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Enter product tags (comma separated)" maxlength="255">
         <input type="file" name="image" class="w-full p-4 mb-4 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required accept="image/*">

         <!-- Product Variants Section -->
         <h3 class="text-xl font-semibold mb-4">Product Variants</h3>
         <div id="variant-inputs" class="variant-inputs">
            <div class="variant-group mb-4">
               <select name="variant_sizes[]" class="w-full p-4 mb-4 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                  <option value="" disabled selected>Select Size --</option>
                  <option value="xs">XS</option>
                  <option value="s">S</option>
                  <option value="m">M</option>
                  <option value="l">L</option>
                  <option value="xl">XL</option>
               </select>

               <select name="variant_colors[]" class="w-full p-4 mb-4 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                  <option value="" disabled selected>Select Color --</option>
                  <option value="red">Red</option>
                  <option value="blue">Blue</option>
                  <option value="green">Green</option>
                  <option value="yellow">Yellow</option>
                  <option value="black">Black</option>
                  <option value="white">White</option>
                  <option value="purple">Purple</option>
                  <option value="pink">Pink</option>
                  <option value="orange">Orange</option>
                  <option value="gray">Gray</option>
                  <option value="brown">Brown</option>
                  <option value="indigo">Indigo</option>
               </select>

               <input type="number" name="variant_prices[]" class="w-full p-4 mb-4 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Enter variant price" min="0" step="0.01" required>
               <input type="number" name="variant_stocks[]" class="w-full p-4 mb-4 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Enter variant stock quantity" min="0" required>
            </div>
         </div>
         <button type="button" onclick="addVariant()" class="w-full p-4 mb-4 bg-blue-600 text-white rounded-lg">Add Another Variant</button>

         <button type="submit" name="add_product" class="w-full p-4 mt-6 bg-blue-600 text-white rounded-lg">Add Product</button>
      </form>
   </div>
</section>
<!-- Show Products -->
<section class="py-12">
   <div class="container mx-auto px-4">
      <h3 class="text-3xl font-semibold text-center mb-8">All Products</h3>
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
         <?php
         $select_products = $conn->prepare("SELECT * FROM products LIMIT 10");
         $select_products->execute();
         while ($product = $select_products->fetch(PDO::FETCH_ASSOC)) {
            $product_id = $product['id'];
         ?>
            <div class="bg-white p-6 rounded-lg shadow-lg hover:shadow-xl transition-all">
               <!-- Product Image -->
               <img src="../uploaded_img/<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>" class="w-full h-48 object-cover rounded-lg mb-4">
               
               <!-- Product Details -->
               <h4 class="text-xl font-semibold"><?php echo $product['name']; ?></h4>
               <p class="text-sm text-gray-500">Category: <?php echo ucfirst($product['category']); ?></p>
               <p class="text-sm text-gray-500">Gender: <?php echo ucfirst($product['gender']); ?></p>
               <p class="text-sm text-gray-500">Stock Quantity: <?php echo $product['stock_quantity']; ?></p>
               <p class="text-sm text-gray-500">Weight: <?php echo $product['weight']; ?> kg</p>
               <p class="text-sm text-gray-500">Slug: <?php echo $product['slug']; ?></p>
               <p class="text-sm text-gray-500">Meta Description: <?php echo $product['meta_description']; ?></p>
               <p class="text-sm text-gray-500">SEO Keywords: <?php echo $product['seo_keywords']; ?></p>
               <p class="text-sm text-gray-500">Tags: <?php echo $product['tags']; ?></p>

               <!-- Product Variants -->
               <div class="mt-4">
                  <h5 class="font-semibold">Variants:</h5>
                  <?php
                  $select_variants = $conn->prepare("SELECT * FROM product_variants WHERE product_id = ?");
                  $select_variants->execute([$product_id]);

                  if ($select_variants->rowCount() > 0) {
                     while ($variant = $select_variants->fetch(PDO::FETCH_ASSOC)) {
                  ?>
                        <div class="mt-2 text-sm text-gray-700 flex justify-between items-center">
                           <span>Size: <?php echo ucfirst($variant['size']); ?>, Color: <?php echo ucfirst($variant['color']); ?></span>
                           <span>Price: $<?php echo number_format($variant['price'], 2); ?>, Stock: <?php echo $variant['stock_quantity']; ?></span>
                           <a href="?delete_variant=<?php echo $variant['id']; ?>" class="text-red-500 hover:underline" onclick="return confirm('Are you sure you want to delete this variant?')">Delete</a>
                        </div>
                  <?php
                     }
                  } else {
                     echo "<p class='text-sm text-gray-500'>No variants available.</p>";
                  }
                  ?>
               </div>

               <!-- Product Update/Delete Links -->
               <div class="mt-4 flex justify-between">
                  <a href="update_product.php?id=<?php echo $product['id']; ?>" class="text-blue-500 hover:underline">Update</a>
                  <a href="?delete=<?php echo $product['id']; ?>" class="text-red-500 hover:underline" onclick="return confirm('Are you sure you want to delete this product?')">Delete</a>
               </div>
            </div>
         <?php } ?>
      </div>
   </div>
</section>


<script>
function addVariant() {
    const container = document.getElementById('variant-inputs');
    const newVariant = `
        <div class="variant-group mb-4">
            <select name="variant_sizes[]" class="w-full p-4 mb-4 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                <option value="" disabled selected>Select Size --</option>
                <option value="xs">XS</option>
                <option value="s">S</option>
                <option value="m">M</option>
                <option value="l">L</option>
                <option value="xl">XL</option>
            </select>

            <select name="variant_colors[]" class="w-full p-4 mb-4 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                <option value="" disabled selected>Select Color --</option>
                <option value="red">Red</option>
                <option value="blue">Blue</option>
                <option value="green">Green</option>
                <option value="yellow">Yellow</option>
                <option value="black">Black</option>
                <option value="white">White</option>
                <option value="purple">Purple</option>
                <option value="pink">Pink</option>
                <option value="orange">Orange</option>
                <option value="gray">Gray</option>
                <option value="brown">Brown</option>
                <option value="indigo">Indigo</option>
            </select>

            <input type="number" name="variant_prices[]" class="w-full p-4 mb-4 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Enter variant price" min="0" step="0.01" required>
            <input type="number" name="variant_stocks[]" class="w-full p-4 mb-4 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Enter variant stock quantity" min="0" required>

            <button type="button" class="w-full p-4 mt-2 bg-red-600 text-white rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 delete-variant-btn">Delete Variant</button>
        </div>`;
    
    container.insertAdjacentHTML('beforeend', newVariant);

    // Attach event listener for the "Delete Variant" button
    const deleteButtons = container.querySelectorAll('.delete-variant-btn');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function() {
            this.parentElement.remove();
        });
    });
}
</script>


</body>
</html>
