<?php
include '../components/connect.php';
session_start();

$admin_id = $_SESSION['admin_id'];
if (!isset($admin_id)) {
    header('location:admin_login.php');
    exit();
}

// Function to handle image upload (same as in your original code)
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

// Get product ID from URL
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $product_id = $_GET['id'];

    // Fetch the product details
    $select_product = $conn->prepare("SELECT * FROM products WHERE id = ?");
    $select_product->execute([$product_id]);
    $product = $select_product->fetch(PDO::FETCH_ASSOC);

    if (!$product) {
        header('location:products.php');
        exit();
    }

    // Handle product update
    if (isset($_POST['update_product'])) {
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

        $image_upload = !empty($image) ? uploadImage($image, $image_tmp_name) : ['path' => $product['image']];

        if (isset($image_upload['error'])) {
            $message[] = $image_upload['error'];
        } else {
            // Update product details
            $update_product = $conn->prepare("UPDATE `products` SET 
                name = ?, category = ?, gender = ?, stock_quantity = ?, weight = ?, slug = ?, 
                meta_description = ?, seo_keywords = ?, tags = ?, image = ? WHERE id = ?");
            $update_product->execute([
                $name, $category, $gender, $stock_quantity, $weight, $slug, 
                $meta_description, $seo_keywords, $tags, $image_upload['path'], $product_id
            ]);

            // Update variants
            if (!empty($_POST['variant_ids'])) {
                $variant_ids = $_POST['variant_ids'];
                $variant_sizes = $_POST['variant_sizes'];
                $variant_colors = $_POST['variant_colors'];
                $variant_prices = $_POST['variant_prices'];
                $variant_stocks = $_POST['variant_stocks'];

                // Update existing variants
                foreach ($variant_ids as $index => $variant_id) {
                    $variant_size = htmlspecialchars($variant_sizes[$index]);
                    $variant_color = htmlspecialchars($variant_colors[$index]);
                    $variant_price = filter_var($variant_prices[$index], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
                    $variant_stock = filter_var($variant_stocks[$index], FILTER_SANITIZE_NUMBER_INT);

                    $update_variant = $conn->prepare("UPDATE `product_variants` SET 
                        size = ?, color = ?, price = ?, stock_quantity = ? WHERE id = ?");
                    $update_variant->execute([$variant_size, $variant_color, $variant_price, $variant_stock, $variant_id]);
                }
            }

            // Add new variants
            if (!empty($_POST['new_variant_sizes'])) {
                $new_variant_sizes = $_POST['new_variant_sizes'];
                $new_variant_colors = $_POST['new_variant_colors'];
                $new_variant_prices = $_POST['new_variant_prices'];
                $new_variant_stocks = $_POST['new_variant_stocks'];

                foreach ($new_variant_sizes as $index => $size) {
                    $color = htmlspecialchars($new_variant_colors[$index]);
                    $price = filter_var($new_variant_prices[$index], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
                    $stock = filter_var($new_variant_stocks[$index], FILTER_SANITIZE_NUMBER_INT);

                    $insert_variant = $conn->prepare("INSERT INTO `product_variants` (product_id, size, color, price, stock_quantity) 
                        VALUES (?, ?, ?, ?, ?)");
                    $insert_variant->execute([$product_id, $size, $color, $price, $stock]);
                }
            }

            // Delete variants
            if (!empty($_POST['delete_variant_ids'])) {
                $delete_variant_ids = $_POST['delete_variant_ids'];
                foreach ($delete_variant_ids as $variant_id) {
                    $delete_variant = $conn->prepare("DELETE FROM `product_variants` WHERE id = ?");
                    $delete_variant->execute([$variant_id]);
                }
            }

            $message[] = 'Product updated successfully!';
            header('location:products.php');
        }
    }
}

// Fetch the product variants
$select_variants = $conn->prepare("SELECT * FROM product_variants WHERE product_id = ?");
$select_variants->execute([$product_id]);
$variants = $select_variants->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Update Product</title>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 text-gray-800">
<?php include '../components/admin_header.php'; ?>

<section class="py-12">
   <div class="container mx-auto px-4">
      <h3 class="text-3xl font-semibold text-center mb-8">Update Product</h3>
      <form action="" method="POST" enctype="multipart/form-data" class="max-w-lg mx-auto bg-white p-8 rounded-lg shadow-md">
         <!-- Product Basic Details -->
         <input type="text" name="name" value="<?php echo $product['name']; ?>" class="w-full p-4 mb-4 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Enter product name" maxlength="100" required>

         <!-- Category Select -->
<select name="category" class="w-full p-4 mb-4 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
    <option value="" disabled selected>Select Category --</option>
    <option value="hat" <?php echo ($product['category'] == 'hat') ? 'selected' : ''; ?>>Hat</option>
    <option value="one-piece" <?php echo ($product['category'] == 'one-piece') ? 'selected' : ''; ?>>One Piece</option>
    <option value="saree" <?php echo ($product['category'] == 'saree') ? 'selected' : ''; ?>>Saree</option>
    <option value="kurta" <?php echo ($product['category'] == 'kurta') ? 'selected' : ''; ?>>Kurta</option>
    <option value="shawl" <?php echo ($product['category'] == 'shawl') ? 'selected' : ''; ?>>Shawl</option>
    <option value="top" <?php echo ($product['category'] == 'top') ? 'selected' : ''; ?>>Top</option>
    <option value="t-shirt" <?php echo ($product['category'] == 't-shirt') ? 'selected' : ''; ?>>T-Shirt</option>
    <option value="pant" <?php echo ($product['category'] == 'pant') ? 'selected' : ''; ?>>Pant</option>
    <option value="socks" <?php echo ($product['category'] == 'socks') ? 'selected' : ''; ?>>Socks</option>
    <option value="heels" <?php echo ($product['category'] == 'heels') ? 'selected' : ''; ?>>Heels</option>
    <option value="boots" <?php echo ($product['category'] == 'boots') ? 'selected' : ''; ?>>Boots</option>
    <option value="bra-panty" <?php echo ($product['category'] == 'bra-panty') ? 'selected' : ''; ?>>Bra & Panty</option>
    <option value="handkerchief" <?php echo ($product['category'] == 'handkerchief') ? 'selected' : ''; ?>>Handkerchief</option>
    <option value="muffler" <?php echo ($product['category'] == 'muffler') ? 'selected' : ''; ?>>Muffler</option>
</select>

         <select name="gender" class="w-full p-4 mb-4 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            <option value="male" <?php echo ($product['gender'] == 'male') ? 'selected' : ''; ?>>Male</option>
            <option value="female" <?php echo ($product['gender'] == 'female') ? 'selected' : ''; ?>>Female</option>
            <option value="child" <?php echo ($product['gender'] == 'child') ? 'selected' : ''; ?>>Child</option>
         </select>


         <input type="number" name="stock_quantity" value="<?php echo $product['stock_quantity']; ?>" class="w-full p-4 mb-4 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Enter stock quantity" min="0">
         <input type="number" name="weight" value="<?php echo $product['weight']; ?>" class="w-full p-4 mb-4 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Enter product weight" min="0" step="0.1">
         <input type="text" name="slug" value="<?php echo $product['slug']; ?>" class="w-full p-4 mb-4 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Enter product slug" maxlength="100">
         <textarea name="meta_description" class="w-full p-4 mb-4 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Enter meta description"><?php echo $product['meta_description']; ?></textarea>
         <textarea name="tags" class="w-full p-4 mb-4 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Enter tags "><?php echo $product['tags']; ?></textarea>
         <textarea name="seo_keywords" class="w-full p-4 mb-4 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Enter SEO keywords"><?php echo $product['seo_keywords']; ?></textarea>
         <input type="file" name="image" class="w-full p-4 mb-4 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">

         <h3 class="text-2xl font-semibold mb-4">Variants</h3>
         <!-- Display existing variants -->
         <?php foreach ($variants as $variant): ?>
            <div class="variant mb-4 flex gap-4 items-center">
                <input type="hidden" name="variant_ids[]" value="<?php echo $variant['id']; ?>">
                <input type="text" name="variant_sizes[]" value="<?php echo $variant['size']; ?>" class="w-1/4 p-4 border border-gray-300 rounded-lg" placeholder="Size" required>
                <input type="text" name="variant_colors[]" value="<?php echo $variant['color']; ?>" class="w-1/4 p-4 border border-gray-300 rounded-lg" placeholder="Color" required>
                <input type="number" name="variant_prices[]" value="<?php echo $variant['price']; ?>" class="w-1/4 p-4 border border-gray-300 rounded-lg" placeholder="Price" min="0" step="0.01" required>
                <input type="number" name="variant_stocks[]" value="<?php echo $variant['stock_quantity']; ?>" class="w-1/4 p-4 border border-gray-300 rounded-lg" placeholder="Stock" min="0" required>
                <button type="button" class="remove-variant text-red-600 ml-2">Remove</button>
            </div>
         <?php endforeach; ?>

         <!-- Add new variant form -->
         <div id="new-variants-container"></div>
         <button type="button" id="add-variant" class="w-full p-4 bg-green-500 text-white font-semibold rounded-lg hover:bg-green-600 mb-4">Add Variant</button>

         <button type="submit" name="update_product" class="w-full p-4 bg-blue-500 text-white font-semibold rounded-lg hover:bg-blue-600">Update Product</button>
      </form>
   </div>
</section>

<script>
document.getElementById('add-variant').addEventListener('click', function() {
    const variantContainer = document.getElementById('new-variants-container');
    const variantForm = document.createElement('div');
    variantForm.classList.add('variant', 'mb-4', 'flex', 'gap-4', 'items-center');

    variantForm.innerHTML = `        
        <select name="new_variant_sizes[]" class="w-full p-4 mb-4 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                <option value="" disabled selected>Select Size --</option>
                <option value="xs">XS</option>
                <option value="s">S</option>
                <option value="m">M</option>
                <option value="l">L</option>
                <option value="xl">XL</option>
            </select>        
        <select name="new_variant_colors[]" class="w-full p-4 mb-4 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
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
            <input type="number" name="new_variant_prices[]" class="w-1/4 p-4 border border-gray-300 rounded-lg" placeholder="Price" min="0" step="0.01" required>
        <input type="number" name="new_variant_stocks[]" class="w-1/4 p-4 border border-gray-300 rounded-lg" placeholder="Stock" min="0" required>
        <button type="button" class="remove-variant text-red-600 ml-2">Remove</button>
    `;
    variantContainer.appendChild(variantForm);

    // Add remove functionality to the new variant
    variantForm.querySelector('.remove-variant').addEventListener('click', function() {
        variantForm.remove();
    });
});

// Remove variant functionality for existing variants
document.querySelectorAll('.remove-variant').forEach(function(button) {
    button.addEventListener('click', function() {
        this.closest('.variant').remove();
    });
});
</script>

</body>
</html>
