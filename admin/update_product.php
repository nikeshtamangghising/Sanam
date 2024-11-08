<?php
include '../components/connect.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
    header('location:admin_login.php');
    exit();
}

// Handle product update
if (isset($_POST['update_product'])) {
    $pid = filter_var($_POST['pid'], FILTER_SANITIZE_NUMBER_INT);
    $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
    $price = filter_var($_POST['price'], FILTER_SANITIZE_STRING);
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

    // Update product details
    $update_product = $conn->prepare("UPDATE `products` SET name = ?, price = ?, category = ?, color = ?, gender = ?, tags = ?, rating = ?, stock_quantity = ?, weight = ?, shipping_cost = ?, slug = ?, meta_description = ?, seo_keywords = ? WHERE id = ?");
    $update_product->execute([$name, $price, $category, $color, $gender, $tags, $rating, $stock_quantity, $weight, $shipping_cost, $slug, $meta_description, $seo_keywords, $pid]);

    // Success message
    $message[] = 'Product updated successfully!';

    // Handle image update if a new image is uploaded
    $old_image = $_POST['old_image'];
    $image = $_FILES['image']['name'];
    $image_size = $_FILES['image']['size'];
    $image_tmp_name = $_FILES['image']['tmp_name'];
    $image_folder = '../uploaded_img/' . $image;

    if (!empty($image)) {
        // Validate image size and type
        if ($image_size > 2000000) { // Max size 2MB
            $message[] = 'Image size is too large! Max size: 2MB.';
        } else {
            $image_extension = strtolower(pathinfo($image, PATHINFO_EXTENSION));
            $allowed_extensions = ['jpg', 'jpeg', 'png', 'webp'];

            if (!in_array($image_extension, $allowed_extensions)) {
                $message[] = 'Invalid image format! Allowed formats: JPG, JPEG, PNG, WEBP.';
            } else {
                // Update image in the database and move the new image
                $update_image = $conn->prepare("UPDATE `products` SET image = ? WHERE id = ?");
                $update_image->execute([$image, $pid]);
                move_uploaded_file($image_tmp_name, $image_folder);

                // Delete old image
                if ($old_image != '') {
                    unlink('../uploaded_img/' . $old_image);
                }

                $message[] = 'Image updated successfully!';
            }
        }
    }

    // Handle variants update
    if (isset($_POST['variant_sizes']) && isset($_POST['variant_stocks'])) {
        $variant_sizes = $_POST['variant_sizes'];
        $variant_stocks = $_POST['variant_stocks'];

        // Clear existing variants for this product
        $delete_variants = $conn->prepare("DELETE FROM `product_variants` WHERE product_id = ?");
        $delete_variants->execute([$pid]);

        // Insert updated variants
        for ($i = 0; $i < count($variant_sizes); $i++) {
            $size = filter_var($variant_sizes[$i], FILTER_SANITIZE_STRING);
            $stock = filter_var($variant_stocks[$i], FILTER_SANITIZE_NUMBER_INT);

            $insert_variant = $conn->prepare("INSERT INTO `product_variants` (product_id, size, stock_quantity) VALUES (?, ?, ?)");
            $insert_variant->execute([$pid, $size, $stock]);
        }

        $message[] = 'Variants updated successfully!';
    }
}
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
<body class="bg-gray-50">

<?php include '../components/admin_header.php'; ?>

<?php
if (isset($message)) {
    foreach ($message as $msg) {
        echo "<p class='text-center text-white bg-green-600 p-4 mb-6 rounded-md'>{$msg}</p>";
    }
}
?>

<section class="py-12">
    <div class="container mx-auto px-4">
        <h1 class="text-3xl font-semibold text-center mb-8 text-gray-700">Update Product</h1>

        <?php
        $update_id = $_GET['id'];
        $show_products = $conn->prepare("SELECT * FROM `products` WHERE id = ?");
        $show_products->execute([$update_id]);

        if ($show_products->rowCount() > 0) {
            while ($fetch_products = $show_products->fetch(PDO::FETCH_ASSOC)) {
        ?>

        <form action="" method="POST" enctype="multipart/form-data" class="max-w-lg mx-auto bg-white p-8 rounded-lg shadow-lg">
            <input type="hidden" name="pid" value="<?= $fetch_products['id']; ?>">
            <input type="hidden" name="old_image" value="<?= $fetch_products['image']; ?>">

            <!-- Product details fields -->
            <div class="mb-6">
                <img src="../uploaded_img/<?= $fetch_products['image']; ?>" alt="Current Image" class="w-full h-48 object-cover rounded-lg shadow-sm mb-4">
            </div>

            <input type="text" name="name" class="w-full p-4 mb-4 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Enter product name" maxlength="100" required value="<?= $fetch_products['name']; ?>">
            <input type="number" name="price" class="w-full p-4 mb-4 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Enter product price" min="0" max="9999999999" required value="<?= $fetch_products['price']; ?>">

            <select name="category" class="w-full p-4 mb-4 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                <option value="<?= $fetch_products['category']; ?>" selected><?= ucfirst($fetch_products['category']); ?></option>
                <option value="t-shirts">T-Shirts</option>
                <option value="dresses">Dresses</option>
                <option value="jeans">Jeans</option>
                <option value="jackets">Jackets</option>
                <option value="accessories">Accessories</option>
            </select>

            <input type="text" name="color" class="w-full p-4 mb-4 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Enter color" maxlength="50" value="<?= $fetch_products['color']; ?>">

            <select name="gender" class="w-full p-4 mb-4 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                <option value="male" <?= ($fetch_products['gender'] == 'male') ? 'selected' : ''; ?>>Male</option>
                <option value="female" <?= ($fetch_products['gender'] == 'female') ? 'selected' : ''; ?>>Female</option>
                <option value="child" <?= ($fetch_products['gender'] == 'child') ? 'selected' : ''; ?>>Child</option>
            </select>

            <input type="text" name="tags" class="w-full p-4 mb-4 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Enter product tags (comma separated)" maxlength="255" value="<?= $fetch_products['tags']; ?>">
            <input type="number" name="rating" class="w-full p-4 mb-4 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Enter product rating (1-5)" min="1" max="5" step="0.1" value="<?= $fetch_products['rating']; ?>">
            <input type="number" name="stock_quantity" class="w-full p-4 mb-4 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Enter stock quantity" min="0" value="<?= $fetch_products['stock_quantity']; ?>">
            <input type="number" name="weight" class="w-full p-4 mb-4 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Enter product weight" min="0" step="0.1" value="<?= $fetch_products['weight']; ?>">
            <input type="number" name="shipping_cost" class="w-full p-4 mb-4 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Enter shipping cost" min="0" step="0.1" value="<?= $fetch_products['shipping_cost']; ?>">
            <input type="text" name="slug" class="w-full p-4 mb-4 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Enter SEO slug" maxlength="100" value="<?= $fetch_products['slug']; ?>">
            <input type="text" name="meta_description" class="w-full p-4 mb-4 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Enter meta description" maxlength="160" value="<?= $fetch_products['meta_description']; ?>">
            <input type="text" name="seo_keywords" class="w-full p-4 mb-4 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Enter SEO keywords" maxlength="255" value="<?= $fetch_products['seo_keywords']; ?>">

            <!-- Variant Section -->
            <h3 class="text-xl font-semibold text-gray-700 mb-4">Variants</h3>

            <div class="variant-inputs">
                <?php
                // Fetch existing variants
                $show_variants = $conn->prepare("SELECT * FROM `product_variants` WHERE product_id = ?");
                $show_variants->execute([$update_id]);

                while ($variant = $show_variants->fetch(PDO::FETCH_ASSOC)) {
                ?>
                    <div class="variant-group mb-4">
                        <input type="text" name="variant_sizes[]" class="w-full p-4 mb-2 border border-gray-300 rounded-lg" placeholder="Enter variant size (e.g., S, M, L)" value="<?= $variant['size']; ?>" required>
                        <input type="number" name="variant_stocks[]" class="w-full p-4 mb-2 border border-gray-300 rounded-lg" placeholder="Enter stock for this size" value="<?= $variant['stock_quantity']; ?>" required min="0">
                    </div>
                <?php
                }
                ?>
            </div>

            <button type="button" onclick="addVariant()" class="w-full p-4 bg-blue-600 text-white rounded-lg hover:bg-blue-700 mb-4">Add More Variants</button>

            <div class="mb-6">
                <label for="image" class="block mb-2 text-gray-600 font-semibold">Update Image (optional)</label>
                <input type="file" name="image" accept="image/*" class="w-full p-4 border border-gray-300 rounded-lg">
            </div>

            <button type="submit" name="update_product" class="w-full p-4 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Update Product</button>
        </form>

        <?php
            }
        }
        ?>
    </div>
</section>

<script>
function addVariant() {
    const variantContainer = document.querySelector('.variant-inputs');
    const variantGroup = document.createElement('div');
    variantGroup.classList.add('variant-group', 'mb-4');
    variantGroup.innerHTML = `
        <input type="text" name="variant_sizes[]" class="w-full p-4 mb-2 border border-gray-300 rounded-lg" placeholder="Enter variant size (e.g., S, M, L)" required>
        <input type="number" name="variant_stocks[]" class="w-full p-4 mb-2 border border-gray-300 rounded-lg" placeholder="Enter stock for this size" required min="0">
    `;
    variantContainer.appendChild(variantGroup);
}
</script>

</body>
</html>
